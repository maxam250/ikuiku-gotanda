# さくらオブジェクトストレージ 自動バックアップ 設定メモ（びくびくサークル五反田店）

## 構成ファイル

```
scripts/
├── backup.sh          # 汎用バックアップスクリプト（編集不要）
├── restore.sh         # 復元スクリプト（編集不要）
├── backup_config.sh   # プロジェクト固有設定（ここだけ編集する）
└── setup_s3cmd.sh     # s3cmd 初回セットアップ（サーバーで1回だけ）
```

Laravel は `public/` がドキュメントルートのため、`scripts/` は Web から見えない（.htaccess 不要）。

## 保存先

```
s3://backup-sites/ikuiku-gotanda/
├── files/     files_backup_YYYYMMDD_HHMMSS.tar.gz    ← storage/app/public（tar 内は public/）
└── database/  database_backup_YYYYMMDD_HHMMSS.sql    ← mysqldump
```

各 7 世代を保持し、超過分は古い順に削除する。

## このプロジェクトの設定値

| 項目 | 値 |
|---|---|
| サーバー | さくらインターネット マネージドサーバー |
| APP_DIR | `/home/cariver18/www/ikuiku-gotanda` |
| DB 接続情報 | `.env`（dotenv 形式：DB_HOST / DB_DATABASE / DB_USERNAME / DB_PASSWORD / DB_PORT） |
| バックアップ対象 | `storage/app/public`（FILE_BACKUP_MODE="directory"） |
| S3 バケット / サイト名 | `backup-sites` / `ikuiku-gotanda` |
| S3 エンドポイント | `s3.tky01.sakurastorage.jp`（東京第1） |
| ログ | `~/logs/backup/backup.log` / `~/logs/backup/restore.log` |

## 初回セットアップ

```bash
cd /home/cariver18/www/ikuiku-gotanda/scripts
chmod +x backup.sh restore.sh setup_s3cmd.sh

# s3cmd のインストールと ~/.s3cfg 作成（同じサーバーで設定済みなら不要）
bash setup_s3cmd.sh

# 手動テスト
bash backup.sh
cat ~/logs/backup/backup.log
s3cmd ls s3://backup-sites/ikuiku-gotanda/files/
```

## cron 登録（毎日 02:00）

さくらのコントロールパネルの「CRON設定」または `crontab -e` で登録:

```
0 2 * * * /bin/bash /home/cariver18/www/ikuiku-gotanda/scripts/backup.sh
```

同じサーバーの他店舗と時刻をずらすとサーバー負荷が分散できる。

## 復元

```bash
bash restore.sh                    # 最新のバックアップから復元
bash restore.sh 20261102_020000    # 指定したタイムスタンプのバックアップから復元
```

復元前に現在のファイルと DB を `/tmp/restore_*/pre_restore/` に退避する。
復元に失敗した場合はそのまま残るので、ログに出るパスから手動で戻せる。

## トラブルシューティング

- **s3cmd が見つからない**: `pip3 install --user s3cmd` 後、`~/.local/bin` を PATH に追加
- **S3 403 AccessDenied**: `s3cmd ls` でバケットが見えるか、`~/.s3cfg` のキーを確認
- **mysqldump の PROCESS 権限エラー**: `--no-tablespaces` で回避済み
- **復元時に `Unknown command '\"'`**: 文字コードの問題。`--default-character-set=utf8mb4` を指定済み
