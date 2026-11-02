#!/bin/bash
# ============================================================
# バックアップ設定ファイル（プロジェクトごとに編集するのはここだけ）
#
# びくびくサークル五反田店（Laravel）/ さくらインターネット マネージドサーバー
#
# 他プロジェクトへの導入方法:
#   1. このファイルをプロジェクトの scripts/ にコピー
#   2. 下記の各項目をそのプロジェクトに合わせて編集
#   3. backup.sh / restore.sh / setup_s3cmd.sh はそのままコピーするだけでOK
# ============================================================

# ============================================================
# アプリケーション設定
# ============================================================
# サーバー上の Laravel ルートディレクトリ（artisan がある場所・絶対パス）
# 例: さくらマネージド → /home/ユーザー名/www/ディレクトリ名
APP_DIR="/home/cariver18/www/ikuiku-gotanda"

# ============================================================
# DB接続情報の読み込み設定
# ============================================================
# 設定タイプ: "php_define" または "dotenv"
#   php_define → define("KEY", "VALUE") 形式 (config.php など)
#   dotenv     → KEY=VALUE 形式 (.env など)
CONFIG_TYPE="dotenv"
CONFIG_FILE="${APP_DIR}/.env"

# 各DBキーに対応する環境変数名（dotenv の場合）
# または定数名（php_define の場合）
DB_HOST_KEY="DB_HOST"
DB_NAME_KEY="DB_DATABASE"
DB_USER_KEY="DB_USERNAME"
DB_PASS_KEY="DB_PASSWORD"
DB_PORT_KEY="DB_PORT"   # 未設定の場合はデフォルト: 3306

# ============================================================
# バックアップ対象ファイルの取得方式
# ============================================================
# "directory"       : FILE_SRC_DIR をまるごとバックアップ（Laravel はこれ）
# "pattern"         : FILE_BACKUP_PATTERN にマッチする最新ディレクトリをそのままバックアップ
# "active_items_db" : 旧サイト（item_data / item_img_data）の在籍キャスト画像のみを収集してバックアップ
FILE_BACKUP_MODE="directory"

# アップロード画像・動画の保存先（storage/app/public → tar.gz 内では public/ になる）
FILE_SRC_DIR="${APP_DIR}/storage/app/public"

# pattern モード設定（FILE_BACKUP_MODE="pattern" の場合のみ使用）
FILE_BACKUP_PATTERN="file_copy[0-9]*"

# ============================================================
# さくらオブジェクトストレージ設定
# ============================================================
# S3 バケット名（全サイト共通）
BUCKET_NAME="backup-sites"

# サイト識別子（バケット内のサブディレクトリ名になる）
# 構成: s3://{BUCKET_NAME}/{SITE_NAME}/files/ , s3://{BUCKET_NAME}/{SITE_NAME}/database/
SITE_NAME="ikuiku-gotanda"

# エンドポイント（さくら東京第1サイト）
# TKY01: s3.tky01.sakurastorage.jp  ← 東京第1
# ISK01: s3.isk01.sakurastorage.jp  ← 石狩第1
# ISK02: s3.isk02.sakurastorage.jp  ← 石狩第2
# ※ 実際の接続先は ~/.s3cfg（setup_s3cmd.sh で設定）が使われる
S3_ENDPOINT="https://s3.tky01.sakurastorage.jp"

# ============================================================
# 世代管理
# ============================================================
# S3に保持するバックアップ世代数
GENERATIONS=7

# ============================================================
# ログ設定
# ============================================================
# ログ出力先（マネージドサーバーは root 権限がないためホームディレクトリ以下）
LOG_DIR="${HOME}/logs/backup"

# 一時作業ディレクトリの親（tar.gz・SQLダンプを一時的に置く）
# /tmp は容量制限が厳しいサーバーがあるため、ホーム配下を推奨
TMP_BASE="${HOME}/tmp"

# ============================================================
# cron 設定例（サーバーの crontab -e で登録）
# ============================================================
# 毎日 02:00 に実行:
#   0 2 * * * /bin/bash /home/cariver18/www/ikuiku-gotanda/scripts/backup.sh
