FROM cr.codeboys.pt/geral/dockerbuilds:latest

#ARG cb_enc_key
ARG lara_env

RUN echo "building for ${lara_env}"

# Copy Composer binary from the Composer official Docker image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
##
ENV WEB_DOCUMENT_ROOT /app/public
ENV APP_ENV ${lara_env}


WORKDIR /app

COPY . .

RUN rm /app/.env

COPY "./envs/.env.${lara_env}.encrypted" /app/.env.$lara_env.encrypted

RUN rm /app/.env.$lara_env || true

RUN php artisan env:decrypt --key=g2SQwZDpD4QQgvL5qCfxANEjrnzCa93j --env=$lara_env

RUN mv /app/.env.$lara_env /app/.env

RUN composer install --no-interaction --optimize-autoloader

#NPM
#RUN npm install

#RUN npm run build

## Optimizing Configuration loading
RUN php artisan migrate:fresh --seed
RUN php artisan optimize:clear

RUN #rm /app/.env

RUN chown -R application:application .
