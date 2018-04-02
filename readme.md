
# Wikitree

Wikipediaから関連するキーワードを再帰的に取得して表示します。
artisanコマンドとWebのインタフェースがあります。

## 機能
### artisanコマンド
詳細は以下参照。  
[artisanコマンド](app/Console/Commands/readme.md)

### Webページ
docker環境であれば、以下URLからアクセス可能。  
[http://http://192.168.99.100/](http://http://192.168.99.100/)


## SetUp

### 1. まず、ディレクトリを切って、本プロジェクトをcloneします。

```
mkdir develop
cd develop
git clone https://github.com/motojouya/exam-wikipedia-tree-sugiyama.git
```

### 2. 次にLaraDockをcloneします。

```
git clone https://github.com/LaraDock/laradock.git
```

※Docker Tool boxの方は以下のブランチを取得してください。
```
git clone -b LaraDock-ToolBox https://github.com/LaraDock/laradock.git
```

### 3. 2でcloneしたlaradockディレクトリに入り設定を書き換えます。

```
cd laradock/
cp env-example .env
vi .env
```

```.env
APPLICATION=../exam-wikipedia-tree-sugiyama/
```

### 4. docker-composeでビルドし、workspaceに入ります。

```
docker-compose up -d nginx php-fpm
docker-compose exec workspace bash
```

### 5. コンテナ内でアプリケーションの設定を書き換えます。

```
cp .env.example .env
vi .env
```

データベースをローカルのsqliteに変更します。

```.env
DB_CONNECTION=sqlite
# DB_DATABASE=database/db.sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=homestead
# DB_USERNAME=homestead
# DB_PASSWORD=secret
```

### 6. コンテナ内で必要モジュールをインストールします。

phpモジュールのインストール

```
composer install
```

nodeのインストール、モジュールインストール、ビルド

```
curl -sL https://deb.nodesource.com/setup_8.x | bash -
apt-get install -y nodejs
npm install
npm run dev
```

もし`npm install`で、`errno -30`や`syscall symlink`エラーでるようであれば、以下のようにしてください。

```
npm install --no-bin-links
```

sqliteデータベースの作成、マイグレーション

```
touch database/database.sqlite
php artisan migrate
```

### 7. APP_KEYの作成

```
php artisan key:generate
```

### 8. サーバの起動

```
php artisan serve
```

