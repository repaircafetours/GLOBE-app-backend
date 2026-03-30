FROM php:8.4-cli

WORKDIR /app

COPY . .

RUN apt update && apt install git -y

RUN docker-php-ext-install pdo pdo_mysql

RUN chmod a+x ./installComposer.sh
RUN ./installComposer.sh
RUN php ./composer.phar install --no-interaction --prefer-dist --no-dev

ARG PORT=8000
ENV PORT=${PORT}

CMD ./serve.sh

EXPOSE ${PORT}

