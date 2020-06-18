

![](https://github.com/BuildForSDG/Team-209-Backend/workflows/Laravel-CI%2FCD/badge.svg)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/dea34bcf3c61497d8593eba3f0b714c3)](https://app.codacy.com/gh/BuildForSDG/Team-209-Backend?utm_source=github.com&utm_medium=referral&utm_content=BuildForSDG/Team-209-Backend&utm_campaign=Badge_Grade_Settings)
[![codecov](https://codecov.io/gh/BuildForSDG/Team-209-Backend/branch/develop/graph/badge.svg)](https://codecov.io/gh/BuildForSDG/Team-209-Backend)


## About Project
This project addresses **SDG 3** Problem Statement 2. 
The Solution seeks to provide a platform where victims of road accidents or bystanders to report incidents with relevant data attached and receive timely adequate response.
As well as providing for emergency responders a platform for managing, responding and monitoring incidents.


## About Repo
This is the Backend API repo, for frontend repos, check the `app.properties` file for details.

## Installation
This is a PHP based application with Laravel Framework.

### Requirements
- LEMP stack with PHP 7.2 minimum
- <a href="https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos">Composer</a> installed
- _*Note:* CDNs are used for Frontend dependencies and thus no node or npm installations are required._

### Steps
   1. Download or Clone the repo into your web directory and point your webserver to the `public` folder as the app's root directory
   2. Rename `.env.example` to `.env`
   3. Create a new mysql database and update `.env` with it's name and access credentials
   4. OPTIONAL : If you'd prefer to use `S3` for file storage, add this configuration line `FILESYSTEM_DRIVER=s3` to your `.env` file and update the AWS section of the `.env`
   5. Run `php artisan storage:link` to create a public accessible point to the app's file storage.
   6. From a terminal, change directory to the root folder of the project and run `composer install` to install the dependencies of the app, and run `composer artisan key:generate` when successfully completed to generate an encryption key for the app.
   7. Run `php artisan migrate --seed` to create the database structure of the app in mysql, and seed it with dummy data.
   8. Visit the frontend app to test.

