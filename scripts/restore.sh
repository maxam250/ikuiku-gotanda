#!/bin/bash
# cron 実行時のために PATH を明示的に設定
export PATH="/usr/local/bin:/usr/bin:/bin:${HOME}/.local/bin:${PATH}"

# ============================================================
# 復元スクリプト（backup.sh で作成したバックアップから復元）
#
# 処理内容:
#   [0/4] 現在のファイル・DB を一時ディレクトリに事前バックアップ（ロールバック用）
#   [1/4] S3 から最新（または引数で指定した日時）のバックアップを特定
#   [2/4] S3 からダウンロード（s3cmd get）
#   [3/4] ファイルを FILE_SRC_DIR へ rsync で復元
#   [4/4] DB を mysql コマンドで復元
#         一時ディレクトリを削除
#
# 実行方法:
#   bash restore.sh                    # 最新のバックアップから復元
#   bash restore.sh 20261102_020000    # 指定したタイムスタンプのバックアップから復元
#
# 前提:
#   - backup_config.sh が同じディレクトリにある（FILE_BACKUP_MODE="directory"）
#   - setup_s3cmd.sh で ~/.s3cfg 設定済み
#
# 警告:
#   このスクリプトは FILE_SRC_DIR と DB を上書きします。
#   本番環境での実行前に必ず S3 にバックアップが存在することを確認してください。
# ============================================================

set -uo pipefail

# ============================================================
# 設定ファイル読み込み
# ============================================================
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CONFIG_PATH="${SCRIPT_DIR}/backup_config.sh"

[ -f "${CONFIG_PATH}" ] || {
    echo "ERROR: backup_config.sh が見つかりません: ${CONFIG_PATH}"
    exit 1
}
# shellcheck source=backup_config.sh
source "${CONFIG_PATH}"

[ "${FILE_BACKUP_MODE:-}" = "directory" ] || {
    echo "ERROR: restore.sh は FILE_BACKUP_MODE=\"directory\" のみ対応しています"
    exit 1
}

# ============================================================
# 変数初期化
# ============================================================
TARGET_STAMP="${1:-}"   # 復元するバックアップのタイムスタンプ（省略時は最新）

TIMESTAMP=$(date +%Y%m%d_%H%M%S)
TMP_DIR="/tmp/restore_${TIMESTAMP}"
PRE_RESTORE_DIR="${TMP_DIR}/pre_restore"  # 事前バックアップ保存先（ロールバック用）
DOWNLOAD_DIR="${TMP_DIR}/download"        # S3 ダウンロード先
EXTRACT_DIR="${TMP_DIR}/extract"          # tar.gz 展開先
S3_FILE_PREFIX="s3://${BUCKET_NAME}/${SITE_NAME}/files"
S3_DB_PREFIX="s3://${BUCKET_NAME}/${SITE_NAME}/database"

RESTORE_TARGET="${FILE_SRC_DIR}"
RESTORE_PARENT="$(dirname "${RESTORE_TARGET}")"
RESTORE_BASENAME="$(basename "${RESTORE_TARGET}")"

# 事前バックアップ完了フラグ（error_exit でロールバック案内に使用）
PRE_RESTORE_DONE=false

# s3cmd のパスを決定（システムインストール優先、なければ pip --user インストール先）
if command -v s3cmd &>/dev/null; then
    S3CMD="s3cmd"
elif [ -x "${HOME}/.local/bin/s3cmd" ]; then
    S3CMD="${HOME}/.local/bin/s3cmd"
else
    echo "ERROR: s3cmd が見つかりません。setup_s3cmd.sh を実行してください。"
    exit 1
fi

# ============================================================
# ログ設定
# ============================================================
mkdir -p "${LOG_DIR}"
LOG_FILE="${LOG_DIR}/restore.log"

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $*" | tee -a "${LOG_FILE}"
}

error_exit() {
    log "ERROR: $*"
    if [ "${PRE_RESTORE_DONE}" = "true" ]; then
        log "------------------------------------------------------"
        log "  ロールバック用の事前バックアップが保存されています:"
        log "    ファイル: ${PRE_RESTORE_DIR}/pre_files_${TIMESTAMP}.tar.gz"
        log "    DB      : ${PRE_RESTORE_DIR}/pre_database_${TIMESTAMP}.sql"
        log "  一時ディレクトリは削除していません: ${TMP_DIR}"
        log "------------------------------------------------------"
    else
        rm -rf "${TMP_DIR}" 2>/dev/null || true
    fi
    exit 1
}

# ============================================================
# DB接続情報の取得（.env の KEY=VALUE 形式）
# ============================================================
parse_dotenv() {
    local key="$1"
    local file="$2"
    grep "^${key}=" "${file}" \
        | head -1 \
        | cut -d'=' -f2- \
        | sed "s/^['\"]//;s/['\"]$//" \
        | tr -d '\r'
}

[ "${CONFIG_TYPE}" = "dotenv" ] || { echo "ERROR: restore.sh は CONFIG_TYPE=\"dotenv\" のみ対応しています"; exit 1; }
[ -f "${CONFIG_FILE}" ] || { echo "ERROR: DB設定ファイルが見つかりません: ${CONFIG_FILE}"; exit 1; }

DB_HOST=$(parse_dotenv "${DB_HOST_KEY}" "${CONFIG_FILE}")
DB_NAME=$(parse_dotenv "${DB_NAME_KEY}" "${CONFIG_FILE}")
DB_USER=$(parse_dotenv "${DB_USER_KEY}" "${CONFIG_FILE}")
DB_PASS=$(parse_dotenv "${DB_PASS_KEY}" "${CONFIG_FILE}")
DB_PORT=""
[ -n "${DB_PORT_KEY:-}" ] && DB_PORT=$(parse_dotenv "${DB_PORT_KEY}" "${CONFIG_FILE}")
DB_PORT="${DB_PORT:-3306}"

[ -n "${DB_NAME}" ] || { echo "ERROR: DB名を取得できませんでした（キー: ${DB_NAME_KEY}）"; exit 1; }
[ -n "${DB_USER}" ] || { echo "ERROR: DBユーザーを取得できませんでした（キー: ${DB_USER_KEY}）"; exit 1; }

# ============================================================
# 一時ディレクトリ作成
# ============================================================
log "========== 復元処理開始: ${BUCKET_NAME}/${SITE_NAME} =========="
log "一時ディレクトリ: ${TMP_DIR}"

mkdir -p "${PRE_RESTORE_DIR}" "${DOWNLOAD_DIR}" "${EXTRACT_DIR}" \
    || error_exit "一時ディレクトリの作成に失敗: ${TMP_DIR}"

# ============================================================
# [0/4] 事前バックアップ（ロールバック用）
# ============================================================
log "[0/4] 事前バックアップ開始（復元失敗時のロールバック用）"

if [ -d "${RESTORE_TARGET}" ]; then
    log "  現在のファイルをバックアップ中: ${RESTORE_TARGET}"
    tar -czf "${PRE_RESTORE_DIR}/pre_files_${TIMESTAMP}.tar.gz" \
        -C "${RESTORE_PARENT}" "${RESTORE_BASENAME}" \
        || error_exit "ファイルの事前バックアップに失敗しました"
    log "  完了: pre_files_${TIMESTAMP}.tar.gz ($(du -sh "${PRE_RESTORE_DIR}/pre_files_${TIMESTAMP}.tar.gz" | cut -f1))"
else
    log "  WARNING: ${RESTORE_TARGET} が存在しないためファイル事前バックアップをスキップ"
fi

log "  現在の DB をバックアップ中: ${DB_NAME}"
MYSQL_PWD="${DB_PASS}" mysqldump \
    --user="${DB_USER}" \
    --host="${DB_HOST}" \
    --port="${DB_PORT}" \
    --default-character-set=utf8mb4 \
    --single-transaction \
    --quick \
    --lock-tables=false \
    --no-tablespaces \
    "${DB_NAME}" > "${PRE_RESTORE_DIR}/pre_database_${TIMESTAMP}.sql" \
    || error_exit "DB の事前バックアップに失敗しました"

[ -s "${PRE_RESTORE_DIR}/pre_database_${TIMESTAMP}.sql" ] \
    || error_exit "DB 事前バックアップファイルが空です"

log "  完了: pre_database_${TIMESTAMP}.sql ($(du -sh "${PRE_RESTORE_DIR}/pre_database_${TIMESTAMP}.sql" | cut -f1))"

PRE_RESTORE_DONE=true
log "[0/4] 事前バックアップ完了: ${PRE_RESTORE_DIR}"

# ============================================================
# [1/4] S3 から復元するバックアップを特定
# s3cmd ls の出力: YYYY-MM-DD HH:MM:SS  バイト数  s3://bucket/path/file
# ファイル名にタイムスタンプが入っているため sort → tail -n1 で最新になる
# ============================================================
log "[1/4] S3 から復元対象を特定中${TARGET_STAMP:+（指定: ${TARGET_STAMP}）}"

find_backup() {
    local PREFIX="$1"
    local EXT_PATTERN="$2"
    ${S3CMD} ls "${PREFIX}/" 2>/dev/null \
        | awk '{print $4}' \
        | { grep -E "${EXT_PATTERN}" || true; } \
        | { grep -F "${TARGET_STAMP}" || true; } \
        | sort \
        | tail -n1
}

LATEST_FILE_S3=$(find_backup "${S3_FILE_PREFIX}" "\.tar\.gz$")
[ -n "${LATEST_FILE_S3}" ] \
    || error_exit "S3 にファイルバックアップが見つかりません: ${S3_FILE_PREFIX}/"

LATEST_DB_S3=$(find_backup "${S3_DB_PREFIX}" "\.sql$")
[ -n "${LATEST_DB_S3}" ] \
    || error_exit "S3 に DB バックアップが見つかりません: ${S3_DB_PREFIX}/"

log "  ファイルバックアップ: ${LATEST_FILE_S3}"
log "  DB バックアップ     : ${LATEST_DB_S3}"
log "[1/4] 特定完了"

# ============================================================
# [2/4] S3 からダウンロード（s3cmd get）
# ============================================================
log "[2/4] S3 からダウンロード開始"

FILE_BACKUP_LOCAL="${DOWNLOAD_DIR}/$(basename "${LATEST_FILE_S3}")"
DB_BACKUP_LOCAL="${DOWNLOAD_DIR}/$(basename "${LATEST_DB_S3}")"

${S3CMD} get "${LATEST_FILE_S3}" "${FILE_BACKUP_LOCAL}" \
    || error_exit "ファイルバックアップのダウンロードに失敗: ${LATEST_FILE_S3}"
log "  完了: $(basename "${FILE_BACKUP_LOCAL}") ($(du -sh "${FILE_BACKUP_LOCAL}" | cut -f1))"

${S3CMD} get "${LATEST_DB_S3}" "${DB_BACKUP_LOCAL}" \
    || error_exit "DB バックアップのダウンロードに失敗: ${LATEST_DB_S3}"
log "  完了: $(basename "${DB_BACKUP_LOCAL}") ($(du -sh "${DB_BACKUP_LOCAL}" | cut -f1))"

log "[2/4] ダウンロード完了"

# ============================================================
# [3/4] ファイル復元（rsync）
# ============================================================
log "[3/4] ファイル復元開始"

tar -xzf "${FILE_BACKUP_LOCAL}" -C "${EXTRACT_DIR}" \
    || error_exit "tar.gz の展開に失敗しました"

EXTRACT_SRC="${EXTRACT_DIR}/${RESTORE_BASENAME}"
[ -d "${EXTRACT_SRC}" ] \
    || error_exit "展開後に ${RESTORE_BASENAME} ディレクトリが見つかりません: ${EXTRACT_SRC}"

mkdir -p "${RESTORE_TARGET}"

# --delete: バックアップに存在しないファイルを復元先から削除
# -a     : パーミッション・タイムスタンプなどを保持
log "  rsync で復元中: ${EXTRACT_SRC}/ → ${RESTORE_TARGET}/"
rsync -a --delete \
    "${EXTRACT_SRC}/" \
    "${RESTORE_TARGET}/" \
    || error_exit "rsync による復元に失敗しました"

log "[3/4] ファイル復元完了: ${RESTORE_TARGET}"

# ============================================================
# [4/4] DB 復元（mysql）
# ============================================================
log "[4/4] DB 復元開始: ${DB_NAME}"

[ -s "${DB_BACKUP_LOCAL}" ] \
    || error_exit "DB バックアップファイルが空です: ${DB_BACKUP_LOCAL}"

MYSQL_PWD="${DB_PASS}" mysql \
    --user="${DB_USER}" \
    --host="${DB_HOST}" \
    --port="${DB_PORT}" \
    --default-character-set=utf8mb4 \
    "${DB_NAME}" < "${DB_BACKUP_LOCAL}" \
    || error_exit "DB 復元に失敗しました"

log "[4/4] DB 復元完了: ${DB_NAME}"

# ============================================================
# 一時ディレクトリ削除（復元が成功したため事前バックアップも不要）
# ============================================================
rm -rf "${TMP_DIR}" \
    && log "一時ディレクトリを削除しました: ${TMP_DIR}" \
    || log "WARNING: 一時ディレクトリの削除に失敗しました（手動削除してください）: ${TMP_DIR}"

log "========== 復元正常完了 =========="
log "  復元ソース（ファイル）: ${LATEST_FILE_S3}"
log "  復元ソース（DB）      : ${LATEST_DB_S3}"
exit 0
