# mock-case1  
coachtechフリマ

## 概要  
本プロジェクトはプロテスト１回目として、模擬案件1回目に作成されたフリマアプリケーションに機能追加を行なったものです。

---

## インストール方法
### Dockerビルド・Laravel環境構築

1. DockerDesktopアプリを立ち上げる。
2. リポジトリをクローン  
   ```bash
   git clone git@github.com:Ami-3110/mock-case1.git
   cd mock-case1/src
   ```
    ※ このプロジェクトはアプリ本体が src/ に配置されています。以降のコマンドはすべて src/ 内で実行してください。
3. Composerパッケージをインストール
    ```bash
   composer install
   ```
4. .env.example をコピーして .env にリネーム
    ```bash
    cp .env.example .env
    ```
5. .env のデータベース接続設定を修正
    ```bash
    DB_CONNECTION=mysql
    DB_HOST=mysql
    DB_PORT=3306
    DB_DATABASE=laravel
    DB_USERNAME=sail
    DB_PASSWORD=password
    ```
6. Dockerコンテナをビルド＆起動
    ```bash
    ./vendor/bin/sail up -d --build
    ```
    ※ 既存のDockerコンテナやDBデータが残っている状態で再構築する場合は、以下を実行してから手順 6（コンテナ起動）に進んでください。
    ```bash
    ./vendor/bin/sail down -v
    ```
7. アプリケーションキーの作成
    ```bash
    ./vendor/bin/sail artisan key:generate
    ```
8. ストレージリンクの作成
    ```bash
    ./vendor/bin/sail artisan storage:link
    ```
9. マイグレーション・シーディングの実行
    ```bash
    ./vendor/bin/sail artisan migrate --seed
    ```

#### メール送信（開発環境用）
本プロジェクトでは開発用に MailHog を使用しています。  
メール送信処理を確認したい場合は、以下のURLから確認できます。

- MailHog: http://localhost:8025

.env の設定（Sail 既定）
    ```bash
    MAIL_MAILER=smtp
    MAIL_HOST=mailpit
    MAIL_PORT=1025
    MAIL_USERNAME=null
    MAIL_PASSWORD=null
    MAIL_ENCRYPTION=null
    MAIL_FROM_ADDRESS="noreply@example.com"
    MAIL_FROM_NAME="${APP_NAME}"
    ```
※ MailHog を使う場合は MAIL_HOST=mailhog に変更し、docker-compose に mailhog サービスを追加してください。

### フロントエンドビルド（Vite）
このプロジェクトは Laravel 10 + Vite を使用しています。初回セットアップ時には以下の手順を実行してください。

1. Node.js / npmのインストール  
   Node.js（v18以上推奨）が必要です。  
   - macOSの場合：  
     ```bash
     brew install node
     ```
   - Windowsの場合：  
     Node.js公式サイトからインストーラを利用

2. Vite 開発サーバー起動（開発環境の場合）
   ```bash
   npm run dev

   ```
   ※ ポート5173が他で使われていないことを確認してください

3. 本番用ビルド  
   ```bash
   npm run build
   ```

## URL
    Laravelアプリが正しく起動していると、以下のURLからアクセスできます。
    - 一覧画面（ダッシュボード）: http://localhost/
    - ログイン画面: http://localhost/login
    - 新規登録画面: http://localhost/register


## 使用技術
- Laravel 10.18.0  
- Vite 5.4.19
- Node.js 20.15.1
- npm 10.9.1
- PHP 8.2.20
- MySQL 8.0.42
- Stripe API（Checkoutセッションを利用して、購入時にStripeへ遷移するクレジットカード決済機能を実装済み。実際の支払い処理はStripe側で完結）
- PHPUnit（テスト用フレームワーク）


## ER図
![ER図](./images/ER_mock-case1.png)
または別途提出のスプレッドシート(テーブル仕様書)参照


## ログイン情報
| ユーザー名     | メールアドレス                                       | パスワード    | 備考                  |
| --------- | --------------------------------------------- | -------- | ------------------- |
| 正内正       | [user1@example.com](mailto:user1@example.com) | masa0000 | アイコン：banana.png     |
| 胡麻斑ごま     | [user2@example.com](mailto:user2@example.com) | goma0000 | アイコン：grapes.png     |
| 正内小正      | [user3@example.com](mailto:user3@example.com) | komasa00 | アイコン：kiwi.png       |

    ※ 全ユーザーに対してメール認証は既に完了済みの状態です（email_verified_at 設定済み）。
    ※ 住所・建物名はすべて架空の値で統一されています。

## 機能一覧
- 会員登録 / ログイン / ログアウト
- 商品の出品（※一度出品すると編集・削除はできません）
- 商品の一覧 / 詳細表示
- 商品の購入（Stripe決済連携）
- いいね機能（マイリストとして保存）
- コメント機能（商品ごと）
- マイページ（プロフィール編集、出品/購入商品一覧）
- 商品画像 / プロフィール画像のアップロード（ストレージ保存）

## 修正履歴
- 模擬案件１件目の講評を踏まえ、下記の修正を行いました。
  - Docker 環境を Laravel Sail ベースに統一  
  - 商品画像をアップロードできるよう修正（物理ファイル管理対応）  
  - PHPUnit を用いた各種機能テストを追加 (.env.testing含む)

## 追加機能
- 取引中商品の表示
- 取引中商品のチャット機能
- チャット未読件数バッジ表示（100件超は99+）

## テスト実行方法
以下のコマンドで機能テストを実行できます。
```bash
./vendor/bin/sail artisan test
```