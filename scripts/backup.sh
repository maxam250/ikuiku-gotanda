#!/bin/bash
# cron 実行時のために PATH を明示的に設定
export PATH="/usr/local/bin:/usr/bin:/bin:${HOME}/.local/bin:${PATH}"

# ============================================================
# 汎用バックアップスクリプト
#
# 処理内容:
#   [1/4] ファイルバックアップ（FILE_BACKUP_MODE に応じて tar.gz）
#   [2/4] DB バックアップ（mysqldump）
#   [3/4] さくらオブジェクトストレージへ s3cmd でアップロード
#   [4/4] S3 上で GENERATIONS 世代保持（超過分を古い順に削除）
#
# 導入手順:
#   1. backup_config.sh をプロジェクトに合わせて編集
#   2. setup_s3cmd.sh を一度だけ実行（s3cmd のインストールと ~/.s3cfg 設定）
#   3. cron に登録（backup_config.sh 末尾の設定例を参照）
#
# 注意:
#   - このファイルは編集不要です
#   - 設定は backup_config.sh のみ編集してください
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

# ============================================================
# 変数初期化
# ============================================================
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
# 一時作業ディレクトリの親（/tmp は容量制限が厳しいサーバーがあるためホーム配下をデフォルトにする）
TMP_BASE="${TMP_BASE:-${HOME}/tmp}"
# 同時刻に複数サイトが実行されても衝突しないよう SITE_NAME と PID を含める
TMP_DIR="${TMP_BASE}/backup_${SITE_NAME}_${TIMESTAMP}_$$"
FILE_BACKUP_NAME="files_backup_${TIMESTAMP}.tar.gz"
DB_BACKUP_NAME="database_backup_${TIMESTAMP}.sql"
S3_FILE_PREFIX="s3://${BUCKET_NAME}/${SITE_NAME}/files"
S3_DB_PREFIX="s3://${BUCKET_NAME}/${SITE_NAME}/database"

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
LOG_FILE="${LOG_DIR}/backup.log"

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] [${SITE_NAME}] $*" | tee -a "${LOG_FILE}"
}

error_exit() {
    log "ERROR: $*"
    rm -rf "${TMP_DIR}" 2>/dev/null || true
    exit 1
}

# ============================================================
# DB接続情報の取得
# ============================================================

# PHP define("KEY", "VALUE") 形式のパーサー
# 対応形式:
#   define("KEY", "value")  ← ダブルクォート
#   define('KEY', 'value')  ← シングルクォート
#   define(KEY, 'value')    ← クォートなし（古いPHP5スタイル）
# コメント行を除外:
#   //define(...)           ← 行コメント
#   define(...); */         ← ブロックコメント内
parse_php_define() {
    local key="$1"
    local file="$2"
    grep -E "define\(['\"]?${key}['\"]?" "${file}" \
        | grep -vE "^[[:space:]]*//" \
        | grep -v "\*/" \
        | head -1 \
        | sed -E "s/.*define\([^,]+,[[:space:]]*['\"]([^'\"]*)['\"].*/\1/" \
        | tr -d '\r'
}

# KEY=VALUE 形式のパーサー（.env など）
parse_dotenv() {
    local key="$1"
    local file="$2"
    grep "^${key}=" "${file}" \
        | head -1 \
        | cut -d'=' -f2- \
        | sed "s/^['\"]//;s/['\"]$//" \
        | tr -d '\r'
}

[ -f "${CONFIG_FILE}" ] || error_exit "DB設定ファイルが見つかりません: ${CONFIG_FILE}"

case "${CONFIG_TYPE}" in
    php_define)
        DB_HOST=$(parse_php_define "${DB_HOST_KEY}" "${CONFIG_FILE}")
        DB_NAME=$(parse_php_define "${DB_NAME_KEY}" "${CONFIG_FILE}")
        DB_USER=$(parse_php_define "${DB_USER_KEY}" "${CONFIG_FILE}")
        DB_PASS=$(parse_php_define "${DB_PASS_KEY}" "${CONFIG_FILE}")
        DB_PORT=""
        [ -n "${DB_PORT_KEY:-}" ] && DB_PORT=$(parse_php_define "${DB_PORT_KEY}" "${CONFIG_FILE}")
        ;;
    dotenv)
        DB_HOST=$(parse_dotenv "${DB_HOST_KEY}" "${CONFIG_FILE}")
        DB_NAME=$(parse_dotenv "${DB_NAME_KEY}" "${CONFIG_FILE}")
        DB_USER=$(parse_dotenv "${DB_USER_KEY}" "${CONFIG_FILE}")
        DB_PASS=$(parse_dotenv "${DB_PASS_KEY}" "${CONFIG_FILE}")
        DB_PORT=""
        [ -n "${DB_PORT_KEY:-}" ] && DB_PORT=$(parse_dotenv "${DB_PORT_KEY}" "${CONFIG_FILE}")
        ;;
    *)
        error_exit "不明な CONFIG_TYPE: ${CONFIG_TYPE} (php_define または dotenv を指定)"
        ;;
esac

DB_PORT="${DB_PORT:-3306}"

[ -n "${DB_NAME}" ] || error_exit "DB名を取得できませんでした（キー: ${DB_NAME_KEY}）"
[ -n "${DB_USER}" ] || error_exit "DBユーザーを取得できませんでした（キー: ${DB_USER_KEY}）"

# ============================================================
# 一時ディレクトリ作成
# ============================================================
log "========== バックアップ開始: ${BUCKET_NAME}/${SITE_NAME} =========="
mkdir -p "${TMP_DIR}" || error_exit "一時ディレクトリの作成に失敗: ${TMP_DIR}"

# ============================================================
# [1/4] ファイルバックアップ
#
# FILE_BACKUP_MODE="directory"
#   FILE_SRC_DIR のディレクトリをまるごと tar.gz
#
# FILE_BACKUP_MODE="pattern"         （デフォルト）
#   FILE_BACKUP_PATTERN にマッチする最新ディレクトリをそのまま tar.gz
#
# FILE_BACKUP_MODE="active_items_db"
#   DB の item_data.item_active=1 のアイテムに紐づく画像のみを
#   一時ディレクトリに収集して tar.gz（サーバーに恒久ファイルを残さない）
# ============================================================
FILE_BACKUP_PATH="${TMP_DIR}/${FILE_BACKUP_NAME}"

case "${FILE_BACKUP_MODE:-pattern}" in

    pattern)
        # glob パターンで最新ディレクトリを取得（更新日時降順の先頭）
        # shellcheck disable=SC2086
        SRC_DIR=$(ls -td ${APP_DIR}/${FILE_BACKUP_PATTERN} 2>/dev/null | head -1)

        [ -n "${SRC_DIR}" ] || error_exit "バックアップ対象が見つかりません: ${APP_DIR}/${FILE_BACKUP_PATTERN}"
        [ -d "${SRC_DIR}" ] || error_exit "対象がディレクトリではありません: ${SRC_DIR}"

        log "[1/4] ファイルバックアップ開始（pattern）: $(basename "${SRC_DIR}")"

        SRC_BASENAME=$(basename "${SRC_DIR}")
        tar -czf "${FILE_BACKUP_PATH}" -C "${APP_DIR}" "${SRC_BASENAME}" \
            || error_exit "tar.gz の作成に失敗しました"
        ;;

    directory)
        # FILE_SRC_DIR のディレクトリをまるごと tar.gz（Laravel の storage/app/public など）
        # 展開時はディレクトリ名（例: public/）で展開される
        [ -n "${FILE_SRC_DIR:-}" ] || error_exit "FILE_SRC_DIR が設定されていません"
        [ -d "${FILE_SRC_DIR}" ] || error_exit "バックアップ対象ディレクトリが存在しません: ${FILE_SRC_DIR}"

        log "[1/4] ファイルバックアップ開始（directory）: ${FILE_SRC_DIR}"

        tar -czf "${FILE_BACKUP_PATH}" -C "$(dirname "${FILE_SRC_DIR}")" "$(basename "${FILE_SRC_DIR}")" \
            || error_exit "tar.gz の作成に失敗しました"
        ;;

    active_items_db)
        # DB 接続情報はこの時点で取得済みのものを再利用
        # FILE_SRC_DIR が未設定の場合は APP_DIR/file をデフォルトとする
        FILE_SRC="${FILE_SRC_DIR:-${APP_DIR}/file}"
        TEMP_FILE_COPY="${TMP_DIR}/file_copy"

        [ -d "${FILE_SRC}" ] \
            || error_exit "ファイルソースディレクトリが存在しません: ${FILE_SRC}"

        mkdir -p "${TEMP_FILE_COPY}" \
            || error_exit "一時コピー先ディレクトリの作成に失敗: ${TEMP_FILE_COPY}"

        log "[1/4] ファイルバックアップ開始（active_items_db）: ${FILE_SRC}"

        # item_active=1 のアイテムに紐づく画像ファイル名を取得
        # item_img_data.img_i_id = item_data.item_id で結合
        IMAGE_LIST=$(MYSQL_PWD="${DB_PASS}" mysql \
            --user="${DB_USER}" \
            --host="${DB_HOST}" \
            --port="${DB_PORT}" \
            --batch \
            --skip-column-names \
            "${DB_NAME}" \
            --execute="SELECT iid.img_name FROM item_img_data iid INNER JOIN item_data id ON iid.img_i_id = id.item_id WHERE id.item_active = 1 ORDER BY iid.img_sort ASC" \
            2>/dev/null) \
            || error_exit "DB からの画像リスト取得に失敗しました"

        [ -n "${IMAGE_LIST}" ] \
            || error_exit "在籍中アイテムの画像が取得できませんでした（item_active=1 のデータを確認してください）"

        # 一時ディレクトリへコピー
        COPY_COUNT=0
        SKIP_COUNT=0
        while IFS= read -r img_name; do
            [ -z "${img_name}" ] && continue
            src="${FILE_SRC}/${img_name}"
            if [ -f "${src}" ]; then
                cp "${src}" "${TEMP_FILE_COPY}/${img_name}" \
                    && COPY_COUNT=$((COPY_COUNT + 1)) \
                    || log "  WARNING: コピー失敗: ${img_name}"
            else
                SKIP_COUNT=$((SKIP_COUNT + 1))
                log "  WARNING: ファイル未存在（スキップ）: ${img_name}"
            fi
        done <<< "${IMAGE_LIST}"

        log "  コピー: ${COPY_COUNT}件 / スキップ: ${SKIP_COUNT}件"
        [ "${COPY_COUNT}" -gt 0 ] || error_exit "コピーできたファイルが0件でした"

        tar -czf "${FILE_BACKUP_PATH}" -C "${TMP_DIR}" "file_copy" \
            || error_exit "tar.gz の作成に失敗しました"
        ;;

    *)
        error_exit "不明な FILE_BACKUP_MODE: ${FILE_BACKUP_MODE} (directory / pattern / active_items_db を指定)"
        ;;
esac

log "[1/4] 完了: ${FILE_BACKUP_NAME} ($(du -sh "${FILE_BACKUP_PATH}" | cut -f1))"

# ============================================================
# [2/4] DB バックアップ（mysqldump）
# パスワードは MYSQL_PWD 経由で渡す（コマンド履歴・プロセスリストに残さない）
# ============================================================
DB_BACKUP_PATH="${TMP_DIR}/${DB_BACKUP_NAME}"

log "[2/4] DB バックアップ開始: ${DB_NAME}"

MYSQL_PWD="${DB_PASS}" mysqldump \
    --user="${DB_USER}" \
    --host="${DB_HOST}" \
    --port="${DB_PORT}" \
    --default-character-set=utf8mb4 \
    --single-transaction \
    --quick \
    --lock-tables=false \
    --no-tablespaces \
    "${DB_NAME}" > "${DB_BACKUP_PATH}" \
    || error_exit "mysqldump に失敗しました"

[ -s "${DB_BACKUP_PATH}" ] || error_exit "DB ダンプファイルが空です（mysqldump の出力を確認してください）"

log "[2/4] 完了: ${DB_BACKUP_NAME} ($(du -sh "${DB_BACKUP_PATH}" | cut -f1))"

# ============================================================
# [3/4] S3 アップロード
# ~/.s3cfg の認証情報を使用（setup_s3cmd.sh で設定済みであること）
# ============================================================
log "[3/4] S3 アップロード開始 → ${BUCKET_NAME}"

# エンドポイントは ~/.s3cfg の設定を使用（setup_s3cmd.sh で設定済み）
${S3CMD} put "${FILE_BACKUP_PATH}" "${S3_FILE_PREFIX}/${FILE_BACKUP_NAME}" \
    || error_exit "ファイルバックアップの S3 アップロードに失敗しました"
log "  完了: ${S3_FILE_PREFIX}/${FILE_BACKUP_NAME}"

${S3CMD} put "${DB_BACKUP_PATH}" "${S3_DB_PREFIX}/${DB_BACKUP_NAME}" \
    || error_exit "DB バックアップの S3 アップロードに失敗しました"
log "  完了: ${S3_DB_PREFIX}/${DB_BACKUP_NAME}"

log "[3/4] S3 アップロード完了"

# ============================================================
# [4/4] 世代管理（S3 上の古いバックアップを削除）
# s3cmd ls の出力: YYYY-MM-DD HH:MM:SS  バイト数  s3://bucket/path/file
# ============================================================
cleanup_old_backups() {
    local PREFIX="$1"
    local EXT_PATTERN="$2"
    local LABEL="$3"

    log "  [世代管理] ${LABEL}: ${GENERATIONS}世代保持"

    local BACKUP_LIST
    BACKUP_LIST=$(${S3CMD} ls "${PREFIX}/" 2>/dev/null \
        | awk '{print $4}' \
        | { grep -E "${EXT_PATTERN}" || true; } \
        | sort)

    if [ -z "${BACKUP_LIST}" ]; then
        log "  [世代管理] ${LABEL}: S3 にファイルなし、スキップ"
        return
    fi

    local TOTAL
    TOTAL=$(echo "${BACKUP_LIST}" | wc -l | tr -d ' ')

    if [ "${TOTAL}" -le "${GENERATIONS}" ]; then
        log "  [世代管理] ${LABEL}: ${TOTAL}件 ≦ ${GENERATIONS}件のため削除なし"
        return
    fi

    local DELETE_COUNT=$(( TOTAL - GENERATIONS ))
    log "  [世代管理] ${LABEL}: ${TOTAL}件中 古い ${DELETE_COUNT}件を削除"

    echo "${BACKUP_LIST}" | head -n "${DELETE_COUNT}" | while IFS= read -r TARGET; do
        [ -n "${TARGET}" ] || continue
        if ${S3CMD} del "${TARGET}"; then
            log "  削除: ${TARGET}"
        else
            log "  WARNING: 削除失敗（手動確認してください）: ${TARGET}"
        fi
    done
}

log "[4/4] 世代管理開始"
cleanup_old_backups "${S3_FILE_PREFIX}" "\.tar\.gz$" "ファイルバックアップ"
cleanup_old_backups "${S3_DB_PREFIX}"   "\.sql$"      "DB バックアップ"
log "[4/4] 世代管理完了"

# ============================================================
# 一時ファイル削除
# ============================================================
rm -rf "${TMP_DIR}" \
    && log "一時ディレクトリを削除しました: ${TMP_DIR}" \
    || log "WARNING: 一時ディレクトリの削除に失敗しました（手動削除してください）: ${TMP_DIR}"

log "========== バックアップ正常完了 =========="
exit 0
