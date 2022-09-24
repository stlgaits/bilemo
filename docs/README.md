# BileMo

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/7085e4ffbf864523b6fd8227845a9531)](https://www.codacy.com/gh/EstelleMyddleware/bilemo/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=EstelleMyddleware/bilemo&amp;utm_campaign=Badge_Grade)
![GitHub last commit](https://img.shields.io/github/last-commit/EstelleMyddleware/bilemo)
[![GitHub issues](https://img.shields.io/github/issues/EstelleMyddleware/bilemo)](https://github.com/EstelleMyddleware/bilemo/issues)
[![GitHub closed issues](https://img.shields.io/github/issues-closed/EstelleMyddleware/bilemo)](https://github.com/EstelleMyddleware/bilemo/issues?q=is%3Aissue+is%3Aclosed)
![GitHub repo size](https://img.shields.io/github/repo-size/EstelleMyddleware/bilemo)
[![GitHub release (latest by date)](https://img.shields.io/github/v/release/EstelleMyddleware/bilemo)](https://github.com/EstelleMyddleware/bilemo)
[![Estelle's GitHub stats](https://github-readme-stats.vercel.app/api?username=EstelleMyddleware)](https://github.com/EstelleMyddleware/github-readme-stats)

API for B2B mobile phones catalog. Full documentation for the API is available [here](https://estellemyddleware.github.io/bilemo/)

## API Resources definition

For the sake of clarity, you will find below the list & description of available API resources.

### Products

Products sold by BileMo (mobile phones, tablets, headphones and other accessories).

### Categories

Each product belongs  to a Category (e.g. phone, tablet, accessory).

### Customers

A customer is a physical person, someone who's purchased a product. Each Customer belongs to an Account.

### Users

A user is someone who has been granted access to the BileMo API. Each user belongs to an Account. A user can 

### Accounts (NOT an API Resource)

An account is a BileMo customer - it is usually a company, a retailer. Although each User & Customer is assigned to an Account, accounts are
not actually API Resources. The assignation of a Customer or a User to an Account is automatically handled by BileMo code in the background.



## Downloading the project

If you would like to install this project on your computer, you will first need to [clone the repo](https://github.com/EstelleMyddleware/bilemo) of this project using Git.

At the root of your project, you need to create a .env.local file (same level as .env) in which you need to configure the appropriate values for your blog to run. Specifically, you need to override the following variables :

```text
DATABASE_URL="mysql://root:password@localhost:3306/bilemo"
ADMIN_EMAIL=youremail@example.com
ADMIN_PASSWORD=ChooseAStrongPersonalPasswordHere
ADMIN_USERNAME=youradminusername
 ```

## Requirements

  * PHP 8.1.4 or above
  * [composer](https://getcomposer.org/download/)
  * Download the [Symfony CLI](https://symfony.com/download).
  * Run this command will guide you in cases there are missing extensions or parameters you need to tweek on your machine

```bash
symfony check:requirements  
```

## Install dependencies

Before running the project, you need to run the following commands in order to install the appropriate dependencies.

```bash
composer install
```

## Create a database

Now let's create our database. This will use the DATABASE_URL you've provided in .env.local file.

```bash
php bin/console doctrine:database:create
```

## Generating the database schema

```bash
 php bin/console doctrine:schema:update --force
 ```

## Load fixtures (initial dummy data)

```bash
 php bin/console doctrine:fixtures:load --append
 ```

## Running the webserver

Now you should be ready to launch the dev webserver using

```bash
symfony serve -d
```

The ```symfony serve``` command will start a PHP webserver.
You can now go to your localhost URL : <http://127.0.0.1:8000> where the blog should be displayed.

>NB: alternatively, if you do not wish to use the Symfony webserver, you can always use WAMP / Laragon / MAMP or a similar webserver suite.

## Start using the API

### Authenticate

#### Get your JWT token

To be able to make requests to the BileMo API, you will first need to authenticate yourself via JWT.
To do so, you need to have your BileMO API user credentials ready with you (if unsure where to find them, please ask your administrator).

| Method | URL                                    | Body                                                                          | Headers                          | Response body                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
|--------|----------------------------------------|-------------------------------------------------------------------------------|----------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| POST   | https://localhost:8000/api/login_check | ```{ "username": "jane.doe@email.com", "password": "yourprivatepassword"} ``` | 'Content-Type: application/json' | ``` {"token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2NjI2NTY0MTgsImV4cCI6MTY2MjY2MDAxOCwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoiZXN0ZWxsZS5nYWl0c0BnbWFpbC5jb20ifQ.MOel3xEsnEtnKtnwEg5jcoCw3MVE3DNXt-DmFhz_CZPwouoIExc-FxXHLkJwvdlQwMl0slOYgmk95OBSqNkCb7j35qGiwgV-0k9mmKc1HfCXbHMcWqZg6kElcp9uXdsMHjYdwnJfX2ZYC37aYlgZz-Frkb3DkyE0Bw-tdr8O1rXUkzA2H1ueOYqUZFL_M-rXplNOMNXcOoL5HVexud_6cVbZeXOPYF0IR19vnZ_0AFxpm2y8JssTaQ6NFYTct1ojCNEfRevdAIBseClDd8O8uXPtLS60nUDUSLLpSQuOZOkD1Tn1JJm1ORoCqz3zFqnH5p0a7te7TxXX6fWUM22Y7A", "refresh_token": "c840e1eded75fb2be4b66d33a92cede0f1ea6a8787460718db4dbb454c4950cb495858ae96539856e0e16b56d66b4ccd0d3703f815a42d00dffa35e3dbe85769"} ``` |


![Postman - POST login_check credentials request to get JWT](/images/postman_login_check.png)

If all went well, you should get a JSON response containing a **token** entry. You need to keep this token.
You will now need to send it as a header with each of your API requests in order to authenticate yourself.

#### Send your JWT token with each request

For each API request you make, you must send your JWT token too. 

Here is an example using curl : 

```curl
curl --location --request GET 'https://localhost:8000/api/products' \
--header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2NjI2NTY0MTgsImV4cCI6MTY2MjY2MDAxOCwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoiZXN0ZWxsZS5nYWl0c0BnbWFpbC5jb20ifQ.MOel3xEsnEtnKtnwEg5jcoCw3MVE3DNXt-DmFhz_CZPwouoIExc-FxXHLkJwvdlQwMl0slOYgmk95OBSqNkCb7j35qGiwgV-0k9mmKc1HfCXbHMcWqZg6kElcp9uXdsMHjYdwnJfX2ZYC37te7TxXX6fWUM22Y7A'

```

| Method | URL                                  | Headers                                                                                                                                                                                                                                                                                                                                                       | Response body                                                                                                                                                                                                                                                                                                                                                                                                | Successful response status code  |
|--------|--------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|----------------------------------|
| GET    | https://localhost:8000/api/products  | 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2NjI2NTY0MTgsImV4cCI6MTY2MjY2MDAxOCwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoiZXN0ZWxsZS5nYWl0c0BnbWFpbC5jb20ifQ.MOel3xEsnEtnKtnwEg5jcoCw3MVE3DNXt-DmFhz_CZPwouoIExc-FxXHLkJwvdlQwMl0slOYgmk95OBSqNkCb7j35qGiwgV-0k9mmKc1HfCXbHMcWqZg6kElcp9uXdsMHjYdwnJfX2ZYC37te7TxXX6fWUM22Y7A'  | ```[{"id": 377,"name": "Sed","createdAt": "2022-07-06T22:46:31+00:00","updatedAt": "2022-07-07T22:46:31+00:00","description": "Neque deleniti culpa sequi itaque eos magnam esse. Ut deserunt incidunt expedita est quia tempora veniam voluptatem. Id quia vitae nihil fuga.","category": "/api/categories/78","brand": "Masson","sku": "2120490525444","available": false,"price": 330.8}, ..... ]```      | 200                              |


![GET Products](images/products.png)

#### Endpoints

##### Logging into the API

###### POST /api/login_check

Allows you to get a JWT token, which you will need to provide to authenticate on other API requests.

##### Products

###### GET /api/products

Allows you to retrieve the complete list of Products available on the BileMo catalog.

###### GET /api/products/{id}

Allows you to read a BileMo Product by providing its Product ID.

##### Categories

###### GET /api/categories

Allows you to retrieve the complete list of Products available on the BileMo catalog. Categories include (non-exhaustive list) :

- smartphone
- smartwatch
- tv
- accessories
- smarthome
- refurbished
- hifi

###### GET /api/categories/{id}

  Allows you to read a BileMo product Category by providing its Category ID. 

##### Customers

###### GET /api/customers

Allows you to retrieve the list of your website's Customers (which are linked to your Account).

###### GET /api/customers/{id}

Allows you to read the personal details of one of your website's Customer (which is linked to your Account) by providing their Customer ID.

###### POST /api/customers

Allows you to create a Customer (which is linked to your Account).

###### DELETE /api/customers/{id}

Allows you to delete a Customer (which is linked to your Account) by providing their Customer ID.

##### Users

###### GET /api/users

Allows you to retrieve the list of Users related to your Account.

###### GET /api/users/{id}

Allows you to read a User account by providing the User account ID.

###### POST /api/users

Allows you to create a User account.

###### DELETE /api/users/{id}

!> Access control: this endpoint is only accessible to Admin users and/or the actual user account which is being deleted

Allows you to delete a User account.

## Credits

Created by [Estelle Gaits](http://estellegaits.fr) as the seventh project of the Openclassrooms PHP / Symfony Apps Developer training course.
