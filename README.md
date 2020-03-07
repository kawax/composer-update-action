# Artisan only application

[![Build Status](https://travis-ci.com/kawax/arty.svg?branch=master)](https://travis-ci.com/kawax/arty)
[![Maintainability](https://api.codeclimate.com/v1/badges/ac2d9a1669653b4f24ae/maintainability)](https://codeclimate.com/github/kawax/arty/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/ac2d9a1669653b4f24ae/test_coverage)](https://codeclimate.com/github/kawax/arty/test_coverage)

- 主にDiscord botを想定したartisanコマンドのみのアプリを作るためのテンプレート
- artisanコマンドでできることは大体何でも可能なので他の用途にも使えるけどそれならLaravelかLaravel Zeroを直接使えばいい。
- Laravel Zero https://laravel-zero.com/
- GitLab CIを使ってサーバーレスでの稼働が目標

## Create project

```
composer create-project --prefer-dist revolution/arty:dev-master discord-bot && cd $_
```

`php arty`でコマンドリスト表示。

### GitHubのTemplateから作成した場合
手動での.envコピーなどが必要。

```
cp .env.example .env
composer install
```

## Laravel Zeroから追加した機能

- Laravel Notification
  - https://github.com/laravel-notification-channels/discord

通知先を増やせばDiscord以外にも簡単に対応できる。

## Discord test
`.env`を設定後`php arty discord:test`で指定のチャンネルに投稿されれば成功。

このようにコマンド1回実行するだけであればGitLab CIで定期的に実行が可能。（最短間隔はおそらく1時間）  
Laravelのスケジュール機能は使わない。  
次回のコマンド実行時になんらかのデータを引き継ぎたい場合はキャッシュかStorageを使う。

## Discord serve
`php arty discord:serve`ではbotを起動し続ける。  
メッセージを受け取って返すようなbotを作るにはサーバー上で動かし続ける必要がある。

GitLab CIでは無理そうだけどtimeoutが1時間なので1時間毎に再実行し続ければ可能かもしれない。  
この場合はDB使ったりもっと複雑なbotを作るだろうからGitLab CIには向いてない。無料プランでは月間の制限時間もある。

## コマンドや通知の作成
Laravelと同じ。

```
php arty make:command TestCommand
php arty make:notification TestNotification
```

## Discordコマンド作成
作成場所は`app/Discord`固定。

```
php arty make:discord:command NewChannelCommand
php arty make:discord:direct NewDmCommand
```

## artyファイル名の変更
```
php arty app:rename artisan
```

```
php artisan
```

## Dockerで使う

```
docker-compose run arty {command}
```

```
docker-compose run --rm arty discord:test
```

```
docker-compose run --entrypoint '' --rm arty composer install
```

```
docker-compose run --entrypoint '' --rm arty vendor/bin/phpunit
```

最初にcomposer create-projectで作ってるならphpもcomposerも動くはずだけど。
CIで必要になるかもしれない。
