FROM php:8.2-fpm

# 必要なパッケージをインストール
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpq-dev \
    libonig-dev \
    libzip-dev \
    zip \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql mbstring zip

# Composer をインストール
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# 作業ディレクトリを設定
WORKDIR /var/www

# プロジェクトのファイルをコピー
COPY . .

# npm install と npm run build を実行
RUN npm install && npm run build

# 権限を設定
RUN chown -R www-data:www-data /var/www