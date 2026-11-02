#!/bin/bash
# ============================================================
# s3cmd セットアップスクリプト（初回のみ実行）
#
# 処理内容:
#   1. s3cmd の存在確認（なければ pip でユーザーローカルにインストール）
#   2. ~/.s3cfg にさくらオブジェクトストレージの設定を書き込む
#   3. 接続テスト
#
# 実行方法:
#   chmod +x setup_s3cmd.sh
#   bash setup_s3cmd.sh
#
# 注意:
#   - root 権限不要（pip --user でユーザーローカルにインストール）
#   - 既存の ~/.s3cfg はバックアップしてから上書きします
#   - このスクリプトは1回だけ実行すれば OK です
# ============================================================

set -euo pipefail

echo "========== s3cmd セットアップ開始 =========="

# ============================================================
# [1/3] s3cmd のインストール確認・インストール
# ============================================================
echo ""
echo "[1/3] s3cmd の確認..."

if command -v s3cmd &>/dev/null; then
    echo "  [OK] s3cmd はすでにインストールされています: $(which s3cmd)"
    S3CMD_BIN="s3cmd"
elif [ -x "${HOME}/.local/bin/s3cmd" ]; then
    echo "  [OK] s3cmd は ~/.local/bin にインストールされています"
    S3CMD_BIN="${HOME}/.local/bin/s3cmd"
else
    echo "  s3cmd が見つかりません。pip でインストールします..."

    # pip3 → pip の順に試す
    PIP_CMD=""
    if command -v pip3 &>/dev/null; then
        PIP_CMD="pip3"
    elif command -v pip &>/dev/null; then
        PIP_CMD="pip"
    else
        echo ""
        echo "ERROR: pip が見つかりません"
        echo "  Python が利用可能か確認してください:"
        echo "    which python3 && python3 --version"
        echo "    which python  && python  --version"
        echo ""
        echo "  Python がある場合は get-pip.py でインストールできます:"
        echo "    curl https://bootstrap.pypa.io/get-pip.py | python3 --user"
        exit 1
    fi

    echo "  ${PIP_CMD} install --user s3cmd を実行中..."
    ${PIP_CMD} install --user s3cmd

    # ~/.local/bin が PATH に含まれているか確認・追加
    if ! command -v s3cmd &>/dev/null && [ -x "${HOME}/.local/bin/s3cmd" ]; then
        echo ""
        echo "  [INFO] ~/.local/bin を PATH に追加します"
        echo "  （~/.bashrc または ~/.bash_profile にも追加することを推奨します）"
        export PATH="${HOME}/.local/bin:${PATH}"
    fi

    if command -v s3cmd &>/dev/null; then
        S3CMD_BIN="s3cmd"
        echo "  [OK] s3cmd のインストールが完了しました: $(which s3cmd)"
    elif [ -x "${HOME}/.local/bin/s3cmd" ]; then
        S3CMD_BIN="${HOME}/.local/bin/s3cmd"
        echo "  [OK] s3cmd のインストールが完了しました: ${S3CMD_BIN}"
    else
        echo ""
        echo "ERROR: s3cmd のインストールに失敗しました"
        echo "  以下を確認してください:"
        echo "    ls ~/.local/bin/s3cmd"
        echo "    echo \$PATH"
        exit 1
    fi
fi

# ============================================================
# [2/3] ~/.s3cfg の設定
# ============================================================
echo ""
echo "[2/3] ~/.s3cfg の設定..."
echo ""
echo "  さくらオブジェクトストレージのアクセスキーを入力してください。"
echo "  （入力内容は画面に表示されません）"
echo ""

read -rp  "  エンドポイント（例: s3.isk01.sakurastorage.jp）: " S3_ENDPOINT_INPUT
read -rsp "  アクセスキー: " S3_ACCESS_KEY
echo
read -rsp "  シークレットキー: " S3_SECRET_KEY
echo

# 入力値の検証
if [ -z "${S3_ACCESS_KEY}" ] || [ -z "${S3_SECRET_KEY}" ] || [ -z "${S3_ENDPOINT_INPUT}" ]; then
    echo ""
    echo "ERROR: エンドポイント・アクセスキー・シークレットキーはすべて必須です"
    exit 1
fi

S3CFG="${HOME}/.s3cfg"

# 既存の設定をバックアップ
if [ -f "${S3CFG}" ]; then
    BACKUP_NAME="${S3CFG}.bak_$(date +%Y%m%d_%H%M%S)"
    cp "${S3CFG}" "${BACKUP_NAME}"
    echo "  [INFO] 既存の ~/.s3cfg をバックアップしました: ${BACKUP_NAME}"
fi

# ~/.s3cfg に書き込み
cat > "${S3CFG}" << EOF
[default]
host_base = ${S3_ENDPOINT_INPUT}
host_bucket = %(bucket)s.${S3_ENDPOINT_INPUT}
access_key = ${S3_ACCESS_KEY}
secret_key = ${S3_SECRET_KEY}
use_https = True
signature_v2 = False
check_ssl_certificate = True
EOF

# 所有者のみ読み書き可能に設定（セキュリティ対策）
chmod 600 "${S3CFG}"
echo "  [OK] ~/.s3cfg を設定しました（パーミッション: 600）"

# ============================================================
# [3/3] 接続テスト
# ============================================================
echo ""
echo "[3/3] 接続テスト..."

if ${S3CMD_BIN} ls 2>&1; then
    echo ""
    echo "  [OK] さくらオブジェクトストレージへの接続に成功しました"
else
    echo ""
    echo "  WARNING: 接続テストに失敗しました"
    echo "  アクセスキー・シークレットキー・エンドポイントを確認してください"
    echo "  ~/.s3cfg の内容:"
    cat "${S3CFG}"
    exit 1
fi

# ============================================================
# 完了
# ============================================================
echo ""
echo "========== セットアップ完了 =========="
echo ""
echo "  動作確認コマンド:"
echo "    ${S3CMD_BIN} ls                         # バケット一覧"
echo "    ${S3CMD_BIN} ls s3://バケット名/         # バケット内のファイル一覧"
echo ""
echo "  次のステップ:"
echo "    1. backup_config.sh の APP_DIR・BUCKET_NAME を確認する"
echo "    2. bash backup.sh でバックアップの手動実行テストをする"
echo "    3. crontab -e でスケジュール登録する"
echo "       例: 0 2 * * * $(dirname "${S3CMD_BIN}"/../../../)/scripts/backup.sh"
echo ""

# PATH に ~/.local/bin が含まれていない場合の案内
if [[ ":${PATH}:" != *":${HOME}/.local/bin:"* ]]; then
    echo "  [注意] ~/.local/bin が PATH に含まれていません"
    echo "  ~/.bashrc に以下を追加してください:"
    echo "    export PATH=\"\${HOME}/.local/bin:\${PATH}\""
    echo ""
fi

exit 0
