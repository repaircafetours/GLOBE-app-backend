---
sidebar_label: Installation
sidebar_position: 1
---

# Native installation

## Prerequisite

You need php and composer in order to use this application

```bash
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.4)"
```

This will install laravel, composer and php 8.4.

### MariaDB

This app requires a [mariaDB database instance](https://mariadb.com/get-started-with-mariadb/#linux).

## Installing the app

First, we will need to pull the official repository

```bash
git clone https://github.com/repaircafetours/app-backend.git
```

We will also need some dependencies that can be installed with `composer install`

# Docker installation

:::note[Docker Image]

The docker image is not yet available with `docker pull`. Yet you may build it yourself

:::

## Building the application

Once the code is cloned with `git clone https://github.com/repaircafetours/app-backend.git`. You can build the docker image using the `docker build` command

```bash
docker build . -t repairCafeTours/app-backend:latest
```
