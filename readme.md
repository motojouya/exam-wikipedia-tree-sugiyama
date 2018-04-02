
# Wikitree

## 機能
### artisanコマンド
詳細は以下参照。  
[artisanコマンド](app/Console/Commands/readme.md)

### Webページ
docker環境であれば、以下URLからアクセス可能。
[Wikitree](http://http://192.168.99.100/)


## SetUp

1. まず、ディレクトリを切って、本プロジェクトをcloneします。

```
mkdir develop
cd develop
git clone https://github.com/motojouya/exam-wikipedia-tree-sugiyama.git
```

2. 次にLaraDockをcloneします。

```
git clone https://github.com/LaraDock/laradock.git
```

※Docker Tool boxの方は以下のブランチを取得してください。
```
git clone -b LaraDock-ToolBox https://github.com/LaraDock/laradock.git
```

3. 2でcloneしたlaradockディレクトリに入り設定を書き換えます。

```
cd laradock/
cp env-example .env
vi .env
```

```.env
APPLICATION=../exam-wikipedia-tree-sugiyama/
```

4. docker-composeでビルドし、workspaceに入ります。

```
docker-compose up -d nginx php-fpm
docker-compose exec workspace bash
```

5. コンテナ内でcomposerから必要モジュールをインストール

```
composer install
curl -sL https://deb.nodesource.com/setup_8.x | bash -
apt-get install -y nodejs
npm install
npm run dev
# ? php artisan key:generate
```

6. サーバの起動

```
php artisan serve
```

