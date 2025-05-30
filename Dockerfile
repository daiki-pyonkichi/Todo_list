FROM php:8.2-apache

# 必要なPHP拡張機能をインストール
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql

# Apacheの設定
RUN a2enmod rewrite

# 作業ディレクトリを設定
WORKDIR /var/www/html

# アプリケーションのファイルをコピー
COPY . /var/www/html/

# Apacheの設定ファイルをコピー
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# 権限の設定
RUN chown -R www-data:www-data /var/www/html 