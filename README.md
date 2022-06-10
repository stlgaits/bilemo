# BileMo

![GitHub last commit](https://img.shields.io/github/last-commit/EstelleMyddleware/bilemo)
[![GitHub issues](https://img.shields.io/github/issues/EstelleMyddleware/bilemo)](https://github.com/EstelleMyddleware/bilemo/issues)
[![GitHub closed issues](https://img.shields.io/github/issues-closed/EstelleMyddleware/bilemo)](https://github.com/EstelleMyddleware/bilemo/issues?q=is%3Aissue+is%3Aclosed)
![GitHub repo size](https://img.shields.io/github/repo-size/EstelleMyddleware/bilemo)
[![GitHub release (latest by date)](https://img.shields.io/github/v/release/EstelleMyddleware/bilemo)](https://github.com/EstelleMyddleware/bilemo)

 API for B2B mobile phones catalog. Full documentation for the API is available [here](https://estellemyddleware.github.io/bilemo/)


#### Downloading the project

If you would like to install this project on your computer, you will first need to [clone the repo](https://github.com/EstelleMyddleware/bilemo) of this project using Git.

At the root of your project, you need to create a .env.local file (same level as .env) in which you need to configure the appropriate values for your blog to run. Specifically, you need to override the following variables :

```text
DATABASE_URL="mysql://root:password@localhost:3306/bilemo"
ADMIN_EMAIL=youremail@example.com
ADMIN_PASSWORD=ChooseAStrongPersonalPasswordHere
ADMIN_USERNAME=youradminusername
 ```

#### Requirements

* PHP 8.1.4 or above
* [composer](https://getcomposer.org/download/)
* Download the [Symfony CLI](https://symfony.com/download).
* Run this command will guide you in cases there are missing extensions or parameters you need to tweek on your machine

```bash
symfony check:requirements  
```

#### Install dependencies

Before running the project, you need to run the following commands in order to install the appropriate dependencies.

```bash
composer install
```

#### Create a database

Now let's create our database. This will use the DATABASE_URL you've provided in .env.local file.

```bash
php bin/console doctrine:database:create
```

#### Generating the database schema

```bash
 php bin/console doctrine:schema:update --force
 ```

#### Running the webserver

Now you should be ready to launch the dev webserver using

```bash
symfony serve -d
```

The ```symfony serve``` command will start a PHP webserver.
You can now go to your localhost URL : <http://127.0.0.1:8000> where the blog should be displayed.

>NB: alternatively, if you do not wish to use the Symfony webserver, you can always use WAMP / Laragon / MAMP or a similar webserver suite.


## Credits

Created by [Estelle Gaits](http://estellegaits.fr) as the seventh project of the Openclassrooms PHP / Symfony Apps Developer training course.
