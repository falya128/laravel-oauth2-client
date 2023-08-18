# Laravel OAuth2 Client

## 概要

OAuth 2.0 を用いた認証機能の構築手順を理解するために作成した Laravel プロジェクトです。  
詳細についてはこちらの Qiita 記事をご参照ください。  
https://qiita.com/falya128/items/652a0d150faaaeb58b2b

本プロジェクトはクライアントアプリの役割を担っております。  
動作確認を行うためには、別リポジトリのリソースサーバ 兼 認証サーバと併せてご利用ください。  
https://github.com/falya128/laravel-oauth2-server

## 開始手順

### 各種ライブラリのインストール

```powershell
cd laravel-oauth2-client
composer install
npm install
```

### 環境設定

```powershell
cp .env.example .env
php artisan key:generate
```

以下の箇所を変更

```
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

LARAVELPASSPORT_CLIENT_ID=[OAuth2Server Authorization Code Grant の Client ID]
```

### データベース準備

```powershell
php artisan migrate
```

### 起動

```powershell
npm run build
php artisan serve
```
