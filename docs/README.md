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

This documentation provides you with examples & the list of available endpoints. You can try out these requests on any API platform such as [Postman](https://www.postman.com/), [HoppScotch](https://hoppscotch.io/fr/) or other online clients. However, you can test out the BileMo API directly from within the included SwaggerUI interface available at /docs endpoint.
Example : https://localhost:8000/docs

![SwaggerUI list of endpoints](images/swagger_ui1.png)

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


| Method | example URL                          | Headers                                | Successful response status code |
|--------|--------------------------------------|----------------------------------------|---------------------------------|
| GET    | https://localhost:8000/api/products  | 'Authorization: Bearer <yourJWTtoken>' | 200                             |

````json
[
  {
    "id": 24,
    "name": "Iphone 14",
    "createdAt": "2022-09-24T17:40:28.854Z",
    "updatedAt": "2022-09-24T17:40:28.854Z",
    "description": "Yet another Iphone, but more expensive!",
    "category": "smartphone",
    "brand": "Apple",
    "sku": "123456789",
    "available": true,
    "price": 1429
},
{
    "id": 25,
    "name": "Galaxy S22 Ultra",
    "createdAt": "2022-09-24T17:40:28.854Z",
    "updatedAt": "2022-09-24T17:40:28.854Z",
    "description": "A galaxy yet unmapped by Webb telescope",
    "category": "smartphone",
    "brand": "Samsung",
    "sku": "013652749",
    "available": true,
    "price": 699
  }
]
````

###### Filters

You can use search query filter on Products fot the following properties: ````available```` & ```category```

![SwaggerUI Products query filters](images/swagger_ui2_products_query_filters.png)

###### GET /api/products/{id}

Allows you to read a BileMo Product by providing its Product ID.

| Method | example URL                             | Headers                                   | Successful response status code  |
|--------|-----------------------------------------|-------------------------------------------|----------------------------------|
| GET    | https://localhost:8000/api/products/32  | 'Authorization: Bearer <your JWT token>'  | 200                              |

Example response: 

````json
{
"id": 24,
"name": "Iphone 14",
"createdAt": "2022-09-24T17:40:28.854Z",
"updatedAt": "2022-09-24T17:40:28.854Z",
"description": "Yet another Iphone, but more expensive!",
"category": "smartphone",
"brand": "Apple",
"sku": "123456789",
"available": true,
"price": 1429
}
````

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

| Method | example URL                             | Headers                                   | Successful response status code   |
|--------|-----------------------------------------|-------------------------------------------|-----------------------------------|
| GET    | https://localhost:8000/api/categories   | 'Authorization: Bearer <your JWT token>'  | 200                               |

Example response

````json
[
  {
    "id": 78,
    "name": "Smartphone",
    "description": null,
    "products": [
      "/api/products/377",
      "/api/products/384",
      "/api/products/391",
      "/api/products/398",
      "/api/products/405",
      "/api/products/412",
      "/api/products/419",
      "/api/products/426",
      "/api/products/433",
      "/api/products/440",
      "/api/products/447"
    ],
    "createdAt": "2012-12-27T09:53:39+00:00",
    "updatedAt": "2013-01-01T09:53:39+00:00"
  },
  {
    "id": 79,
    "name": "Smartwatch",
    "description": null,
    "products": [
      "/api/products/378",
      "/api/products/385",
      "/api/products/392",
      "/api/products/399",
      "/api/products/406",
      "/api/products/413",
      "/api/products/420",
      "/api/products/427",
      "/api/products/434",
      "/api/products/441",
      "/api/products/448"
    ],
    "createdAt": "2012-12-27T09:53:39+00:00",
    "updatedAt": "2013-01-01T09:53:39+00:00"
  },
  {
    "id": 80,
    "name": "Tv",
    "description": null,
    "products": [
      "/api/products/379",
      "/api/products/386",
      "/api/products/393",
      "/api/products/400",
      "/api/products/407",
      "/api/products/414",
      "/api/products/421",
      "/api/products/428",
      "/api/products/435",
      "/api/products/442",
      "/api/products/449"
    ],
    "createdAt": "2012-12-27T09:53:39+00:00",
    "updatedAt": "2013-01-01T09:53:39+00:00"
  },
  {
    "id": 81,
    "name": "Accessories",
    "description": null,
    "products": [
      "/api/products/380",
      "/api/products/387",
      "/api/products/394",
      "/api/products/401",
      "/api/products/408",
      "/api/products/415",
      "/api/products/422",
      "/api/products/429",
      "/api/products/436",
      "/api/products/443",
      "/api/products/450"
    ],
    "createdAt": "2012-12-27T09:53:39+00:00",
    "updatedAt": "2013-01-01T09:53:39+00:00"
  },
  {
    "id": 82,
    "name": "Smarthome",
    "description": null,
    "products": [
      "/api/products/381",
      "/api/products/388",
      "/api/products/395",
      "/api/products/402",
      "/api/products/409",
      "/api/products/416",
      "/api/products/423",
      "/api/products/430",
      "/api/products/437",
      "/api/products/444",
      "/api/products/451"
    ],
    "createdAt": "2012-12-27T09:53:39+00:00",
    "updatedAt": "2013-01-01T09:53:39+00:00"
  },
  {
    "id": 83,
    "name": "Refurbished",
    "description": null,
    "products": [
      "/api/products/382",
      "/api/products/389",
      "/api/products/396",
      "/api/products/403",
      "/api/products/410",
      "/api/products/417",
      "/api/products/424",
      "/api/products/431",
      "/api/products/438",
      "/api/products/445"
    ],
    "createdAt": "2012-12-27T09:53:39+00:00",
    "updatedAt": "2013-01-01T09:53:39+00:00"
  },
  {
    "id": 84,
    "name": "Hifi",
    "description": null,
    "products": [
      "/api/products/383",
      "/api/products/390",
      "/api/products/397",
      "/api/products/404",
      "/api/products/411",
      "/api/products/418",
      "/api/products/425",
      "/api/products/432",
      "/api/products/439",
      "/api/products/446"
    ],
    "createdAt": "2012-12-27T09:53:39+00:00",
    "updatedAt": "2013-01-01T09:53:39+00:00"
  },
  {
    "id": 92,
    "name": "Smartphone",
    "description": null,
    "products": [
      "/api/products/527",
      "/api/products/534",
      "/api/products/541",
      "/api/products/548",
      "/api/products/555",
      "/api/products/562",
      "/api/products/569",
      "/api/products/576",
      "/api/products/583",
      "/api/products/590",
      "/api/products/597"
    ],
    "createdAt": "2018-11-10T06:45:47+00:00",
    "updatedAt": "2018-11-15T06:45:47+00:00"
  },
  {
    "id": 93,
    "name": "Smartwatch",
    "description": null,
    "products": [
      "/api/products/528",
      "/api/products/535",
      "/api/products/542",
      "/api/products/549",
      "/api/products/556",
      "/api/products/563",
      "/api/products/570",
      "/api/products/577",
      "/api/products/584",
      "/api/products/591",
      "/api/products/598"
    ],
    "createdAt": "2018-11-10T06:45:47+00:00",
    "updatedAt": "2018-11-15T06:45:47+00:00"
  },
  {
    "id": 94,
    "name": "Tv",
    "description": null,
    "products": [
      "/api/products/529",
      "/api/products/536",
      "/api/products/543",
      "/api/products/550",
      "/api/products/557",
      "/api/products/564",
      "/api/products/571",
      "/api/products/578",
      "/api/products/585",
      "/api/products/592",
      "/api/products/599"
    ],
    "createdAt": "2018-11-10T06:45:47+00:00",
    "updatedAt": "2018-11-15T06:45:47+00:00"
  }
]
````


###### GET /api/categories/{id}

  Allows you to read a BileMo product Category by providing its Category ID. 


| Method | example URL                              | Headers                                    | Successful response status code  | Error status codes     |
|--------|------------------------------------------|--------------------------------------------|----------------------------------|------------------------|
| GET    | https://localhost:8000/api/categories/44 | 'Authorization: Bearer <your JWT token>'   | 200                              | 404 Resource not found |

````json
{
  "id": 78,
  "name": "Smartphone",
  "description": null,
  "products": [
    "/api/products/377",
    "/api/products/384",
    "/api/products/391",
    "/api/products/398",
    "/api/products/405",
    "/api/products/412",
    "/api/products/419",
    "/api/products/426",
    "/api/products/433",
    "/api/products/440",
    "/api/products/447"
  ],
  "createdAt": "2012-12-27T09:53:39+00:00",
  "updatedAt": "2013-01-01T09:53:39+00:00"
}
````

##### Customers

###### GET /api/customers

Allows you to retrieve the list of your website's Customers (which are linked to your Account).


| Method | example URL                          | Headers                                    | Successful response status code  | 
|--------|--------------------------------------|--------------------------------------------|----------------------------------|
| GET    | https://localhost:8000/api/customers | 'Authorization: Bearer <your JWT token>'   | 200                              |

###### GET /api/customers/{id}

Allows you to read the personal details of one of your website's Customer (which is linked to your Account) by providing their Customer ID.


| Method | example URL                             | Headers                                    | Successful response status code  | Error status codes     |
|--------|-----------------------------------------|--------------------------------------------|----------------------------------|------------------------|
| GET    | https://localhost:8000/api/customers/44 | 'Authorization: Bearer <your JWT token>'   | 200                              | 404 Resource not found |

###### POST /api/customers

Allows you to create a Customer (which is linked to your Account).


| Method | example URL                          | Headers                                   | Successful response status code | Error status codes                              |
|--------|--------------------------------------|-------------------------------------------|---------------------------------|-------------------------------------------------|
| POST   | https://localhost:8000/api/customers | 'Authorization: Bearer <your JWT token>'  | 201   Customer resource created | 400 Invalid input<br/> 422 Unprocessable entity |



###### DELETE /api/customers/{id}

Allows you to delete a Customer (which is linked to your Account) by providing their Customer ID.


| Method | example URL                            | Headers                                   | Successful response status code | Error status code       |
|--------|----------------------------------------|-------------------------------------------|---------------------------------|-------------------------|
| DELETE | https://localhost:8000/api/customers/3 | 'Authorization: Bearer <your JWT token>'  | 204 Customer resource deleted   | 404 Resource not found  |

##### Users

###### GET /api/users

Allows you to retrieve the list of Users related to your Account.


| Method | example URL                       | Headers                                    | Successful response status code  | 
|--------|-----------------------------------|--------------------------------------------|----------------------------------|
| GET    | https://localhost:8000/api/users  | 'Authorization: Bearer <your JWT token>'   | 200                              |

Example response :

````json
[
  {
    "email": "estelle.gaits@gmail.com",
    "account": {
      "name": "Fnac",
      "description": "Fnac is a large French retail chain selling cultural and electronic products, founded by André Essel and Max Théret in 1954. Its head office is in Le Flavia in Ivry-sur-Seine near Paris. Fnac is an abbreviation of Fédération Nationale d'Achats des Cadres",
      "industry": "Retail",
      "primaryEmail": "contact@fnac.com"
    },
    "firstName": "Estelle",
    "lastName": "Gaits",
    "createdAt": "2022-06-30T18:36:49+00:00",
    "updatedAt": "2022-06-30T18:36:49+00:00"
  },
  {
    "email": "philippine32@laposte.net",
    "account": {
      "name": "Fnac",
      "description": "Fnac is a large French retail chain selling cultural and electronic products, founded by André Essel and Max Théret in 1954. Its head office is in Le Flavia in Ivry-sur-Seine near Paris. Fnac is an abbreviation of Fédération Nationale d'Achats des Cadres",
      "industry": "Retail",
      "primaryEmail": "contact@fnac.com"
    },
    "firstName": "Julien",
    "lastName": "Rousseau",
    "createdAt": "2016-09-15T20:34:38+00:00",
    "updatedAt": "2016-09-16T20:34:38+00:00"
  },
  {
    "email": "anouk.marin@marques.net",
    "account": {
      "name": "Fnac",
      "description": "Fnac is a large French retail chain selling cultural and electronic products, founded by André Essel and Max Théret in 1954. Its head office is in Le Flavia in Ivry-sur-Seine near Paris. Fnac is an abbreviation of Fédération Nationale d'Achats des Cadres",
      "industry": "Retail",
      "primaryEmail": "contact@fnac.com"
    },
    "firstName": "Odette",
    "lastName": "Leclercq",
    "createdAt": "2016-09-15T20:34:38+00:00",
    "updatedAt": "2016-09-16T20:34:38+00:00"
  },
  {
    "email": "millet.adrien@chauvin.fr",
    "account": {
      "name": "Fnac",
      "description": "Fnac is a large French retail chain selling cultural and electronic products, founded by André Essel and Max Théret in 1954. Its head office is in Le Flavia in Ivry-sur-Seine near Paris. Fnac is an abbreviation of Fédération Nationale d'Achats des Cadres",
      "industry": "Retail",
      "primaryEmail": "contact@fnac.com"
    },
    "firstName": "Jules",
    "lastName": "Leconte",
    "createdAt": "2016-09-15T20:34:38+00:00",
    "updatedAt": "2016-09-16T20:34:38+00:00"
  },
  {
    "email": "hernandez.jeanne@sfr.fr",
    "account": {
      "name": "Fnac",
      "description": "Fnac is a large French retail chain selling cultural and electronic products, founded by André Essel and Max Théret in 1954. Its head office is in Le Flavia in Ivry-sur-Seine near Paris. Fnac is an abbreviation of Fédération Nationale d'Achats des Cadres",
      "industry": "Retail",
      "primaryEmail": "contact@fnac.com"
    },
    "firstName": "Étienne",
    "lastName": "Paris",
    "createdAt": "2016-09-15T20:34:38+00:00",
    "updatedAt": "2016-09-16T20:34:38+00:00"
  },
  {
    "email": "marie16@tele2.fr",
    "account": {
      "name": "Fnac",
      "description": "Fnac is a large French retail chain selling cultural and electronic products, founded by André Essel and Max Théret in 1954. Its head office is in Le Flavia in Ivry-sur-Seine near Paris. Fnac is an abbreviation of Fédération Nationale d'Achats des Cadres",
      "industry": "Retail",
      "primaryEmail": "contact@fnac.com"
    },
    "firstName": "Céline",
    "lastName": "Dufour",
    "createdAt": "2016-09-15T20:34:38+00:00",
    "updatedAt": "2016-09-16T20:34:38+00:00"
  },
  {
    "email": "ifleury@bodin.fr",
    "account": {
      "name": "Fnac",
      "description": "Fnac is a large French retail chain selling cultural and electronic products, founded by André Essel and Max Théret in 1954. Its head office is in Le Flavia in Ivry-sur-Seine near Paris. Fnac is an abbreviation of Fédération Nationale d'Achats des Cadres",
      "industry": "Retail",
      "primaryEmail": "contact@fnac.com"
    },
    "firstName": "Élisabeth",
    "lastName": "Raymond",
    "createdAt": "2016-09-15T20:34:38+00:00",
    "updatedAt": "2016-09-16T20:34:38+00:00"
  },
  {
    "email": "caroline91@dossantos.fr",
    "account": {
      "name": "Fnac",
      "description": "Fnac is a large French retail chain selling cultural and electronic products, founded by André Essel and Max Théret in 1954. Its head office is in Le Flavia in Ivry-sur-Seine near Paris. Fnac is an abbreviation of Fédération Nationale d'Achats des Cadres",
      "industry": "Retail",
      "primaryEmail": "contact@fnac.com"
    },
    "firstName": "Martin",
    "lastName": "Charrier",
    "createdAt": "2016-09-15T20:34:38+00:00",
    "updatedAt": "2016-09-16T20:34:38+00:00"
  },
  {
    "email": "zacharie59@clerc.com",
    "account": {
      "name": "Fnac",
      "description": "Fnac is a large French retail chain selling cultural and electronic products, founded by André Essel and Max Théret in 1954. Its head office is in Le Flavia in Ivry-sur-Seine near Paris. Fnac is an abbreviation of Fédération Nationale d'Achats des Cadres",
      "industry": "Retail",
      "primaryEmail": "contact@fnac.com"
    },
    "firstName": "Martin",
    "lastName": "Blondel",
    "createdAt": "2016-09-15T20:34:38+00:00",
    "updatedAt": "2016-09-16T20:34:38+00:00"
  },
  {
    "email": "qmarie@gregoire.net",
    "account": {
      "name": "Fnac",
      "description": "Fnac is a large French retail chain selling cultural and electronic products, founded by André Essel and Max Théret in 1954. Its head office is in Le Flavia in Ivry-sur-Seine near Paris. Fnac is an abbreviation of Fédération Nationale d'Achats des Cadres",
      "industry": "Retail",
      "primaryEmail": "contact@fnac.com"
    },
    "firstName": "Marie",
    "lastName": "Bouvier",
    "createdAt": "2016-09-15T20:34:38+00:00",
    "updatedAt": "2016-09-16T20:34:38+00:00"
  }
]
````


###### GET /api/users/{id}

Allows you to read a User account by providing the User account ID.


| Method | example URL                          | Headers                                    | Successful response status code  | Error status codes     |
|--------|--------------------------------------|--------------------------------------------|----------------------------------|------------------------|
| GET    | https://localhost:8000/api/users/44  | 'Authorization: Bearer <your JWT token>'   | 200                              | 404 Resource not found |

````json
{
  "email": "lorie.lafont@gmail.com",
  "account": {
    "name": "Fnac",
    "description": "Fnac is a large French retail chain selling cultural and electronic products, founded by André Essel and Max Théret in 1954. Its head office is in Le Flavia in Ivry-sur-Seine near Paris. Fnac is an abbreviation of Fédération Nationale d'Achats des Cadres",
    "industry": "Retail",
    "primaryEmail": "contact@fnac.com"
  },
  "firstName": "Lorie",
  "lastName": "Lafont",
  "createdAt": "2022-06-30T18:36:49+00:00",
  "updatedAt": "2022-06-30T18:36:49+00:00"
}
````


###### POST /api/users

Allows you to create a User account.


| Method | example URL                      | Headers                                   | Successful response status code | Error status codes                              |
|--------|----------------------------------|-------------------------------------------|---------------------------------|-------------------------------------------------|
| POST   | https://localhost:8000/api/users | 'Authorization: Bearer <your JWT token>'  | 201   User resource created     | 400 Invalid input<br/> 422 Unprocessable entity |



###### DELETE /api/users/{id}

!> Access control: this endpoint is only accessible to Admin users and/or the actual user account which is being deleted

Allows you to delete a User account.


| Method | example URL                        | Headers                                   | Successful response status code | Error status code       |
|--------|------------------------------------|-------------------------------------------|---------------------------------|-------------------------|
| DELETE | https://localhost:8000/api/users/3 | 'Authorization: Bearer <your JWT token>'  | 204   User resource deleted     | 404 Resource not found  |


### Pagination

For all available resources, you can paginate your requests, simply by adding a page query parameter in the URL.

| Method | example URL                                | Headers                                   | Successful response status code   |
|--------|--------------------------------------------|-------------------------------------------|-----------------------------------|
| GET    | https://localhost:8000/api/products?page=3 | 'Authorization: Bearer <your JWT token>'  | 200                               |

## Credits

Created by [Estelle Gaits](http://estellegaits.fr) as the seventh project of the Openclassrooms PHP / Symfony Apps Developer training course.
