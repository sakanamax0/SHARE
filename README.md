## 環境構築

### バックエンド（Laravel API）

#### 1. プロジェクトへ移動

```bash
cd share-api
```

#### 2. 環境変数ファイルを作成

```bash
cp src/.env.example src/.env
```

#### 3. Dockerコンテナを起動

```bash
docker compose up -d --build
```

#### 4. Laravelコンテナへ入る

```bash
docker compose exec app sh
```

#### 5. Composer依存関係をインストール

```bash
composer install
```

#### 6. アプリケーションキーを作成

```bash
php artisan key:generate
```

#### 7. 設定キャッシュをクリア

```bash
php artisan config:clear
```

#### 8. マイグレーションを実行

```bash
php artisan migrate
```

#### 9. Laravel API動作確認


```text
http://127.0.0.1:8000
```

---

### 補足

#### コンテナ停止方法

```bash
docker compose down
```

#### 再起動方法

```bash
docker compose up -d
```

#### 初回セットアップ後の再起動時

```bash
cd share-api
docker compose up -d
```
