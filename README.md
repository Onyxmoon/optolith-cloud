<h1 align="center">Welcome to Optolith Character Generator - Cloud 👋</h1>
<p>
  <img alt="Version" src="https://img.shields.io/badge/version-0.0.0-blue.svg?cacheSeconds=2592000" />
  <a href="https://spdx.org/licenses/MPL-2.0.html">
    <img alt="License: Mozilla Public License 2.0" src="https://img.shields.io/badge/License-Mozilla Public License 2.0-yellow.svg" target="_blank" />
  </a>
  <a href="https://twitter.com/onyxmoon_">
    <img alt="Twitter: onyxmoon_" src="https://img.shields.io/twitter/follow/onyxmoon_.svg?style=social" target="_blank" />
  </a>
</p>

> This repository contains the development data of the server for a cloud sync  infrastructure service for Optolith Character Manager (https://github.com/elyukai/optolith-client).

### 🏠 [Homepage](cloud.optolith.app)

## Install

This application needs

- PHP > *7.3*
- composer *latest*

Recommendend add-ons
- symfony development kit

To prepare the application and install all dependencies use in a terminal:

```sh
composer install
```

## Usage

**📝 Configuration**

Please add the following information to a an .env.local file
```dotenv
# App Mode
# 'prod' in production, will disable test tools and increase performance
APP_ENV=dev

# This secret string is 40 random characters that is used for CSRF protection.
# Generate one at: http://nux.net/secret
APP_SECRET=652f40901a1e2230e2b1ab86cc810835b13bc4a5

# Database configuration
# Format described at https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url
# For an SQLite database, use: "sqlite:///%kernel.project_dir%/var/data.db"
# For a PostgreSQL database, use: "postgresql://db_user:db_password@127.0.0.1:5432/db_name?serverVersion=11&charset=utf8"
# IMPORTANT: You MUST configure your server version, either here or in config/packages/doctrine.yaml
# DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7
DATABASE_URL=sqlite:///%kernel.project_dir%/var/data.db

# Mail configuration
# While testing you can disable the need for an mail dsn with 'null://null'
MAILER_DSN=smtp://<mail-adress>:<password>@<smtp-server>>
```

**🔪 Prepare database**

Prepare sql terms for database depending on current condition
```bash
php bin/console make:migration
```
Apply changes to database
```bash
php bin/console doctrine:migrations:migrate
```

**🧪 Development test run**

```sh
php bin/console server:run
```

**📨 Deploy**

For deployment on specific webservers, take a look into the [symfony documentation](https://symfony.com/doc/current/deployment.html) 

## Author

👤 **Onyxmoon (Philipp Borucki)**

* Twitter: [@onyxmoon_](https://twitter.com/onyxmoon_)
* Github: [@Onyxmoon](https://github.com/Onyxmoon)

👤 **elyukai (Lukas Obermann)**

* Twitter: [@elyukai](https://twitter.com/elyukai)
* Github: [@elyukai](https://github.com/elyukai/)

## 🤝 Contributing

Contributions, issues and feature requests are welcome!<br />Feel free to check [issues page](https://github.com/Onyxmoon/optolith-sync/issues).

## Show your support

Give a ⭐️ if this project helped you!

## 📝 License

Copyright © 2019 [Onyxmoon (Philipp Borucki)](https://github.com/Onyxmoon).<br />
This project is [Mozilla Public License 2.0](https://spdx.org/licenses/MPL-2.0.html) licensed.

***