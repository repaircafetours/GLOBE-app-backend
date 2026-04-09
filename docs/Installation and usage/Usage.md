---
sidebar_label: Usage
sidebar_position: 2
---

# Using natively

## Configuration

The API uses an environement file as a configuration file.

:::note

You may also use shell environment variables instead of an environement file.

:::

Using the app for the first time requires to initialize the `APP_KEY` configuration variable using 

```bash
php artisan key:generate
```

### Important configuration variables definition

- `APP_DEBUG` : True if you want detailed information on errors or contributing to the application. False when running in production.
- `APP_URL` : When running the php artisan serve, laravel will use this variable as the listening url **and** port.
- `DB_HOST` : Represent the mariadb database URL.
- `DB_PORT` : The database port
- `DB_DATABASE` : The name of the database laravel will use.
- `DB_USERNAME` : The username for the used database.
- `DB_PASSWORD` : The password for selected user.
- `MAIL_ENCRYPTION` : The encryption method for emails
- `MAIL_FROM_ADDRESS` : The email address which will send the emails
- `MAIL_FROM_NAME` : The name of the sender

## Run the application

### First usage

If you use the app for the first time, you will have to initialize the database.

```bash
php artisan migrate
```

### Usage

Once you configured the .env file you can run the api with a simple command.

```sh

php artisan serve

```

You can now access the API on `http://localhost:8000/api/v1` or your definition of `APP_URL`.

# Using with docker

You can simply run `docker compose up` and the app will be available on the port 8000.

:::note

The `compose.yaml` file uses the same environement variables as the `.env` file

:::

### First usage

When using the docker compose for the first time you will need to initialize the database with 

```sh

php artisan serve

```

# Reaching the documentation

You can find the REST API documentation on `<url>/docs/api`