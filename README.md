# Laravel OAuth BlanK

This is a starting install of Laravel, with OAuth enabled

## Getting Started

Clone the repository and enter in the created folder
```
git clone git@github.com:aruberutochan/laravel-oauth-blank.git
cd laravel-oauth-blank
```

### Prerequisites

You need to run this command to have a full installation

#### Configure Database
Make sure you have a `.env` file with the database confiuration set.
You can use a copy of the `.env.example` file

```
cp .env.example .env
```
Edit .env to setup database

```
(...)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=YourDataBaseName
DB_USERNAME=YourUserName
DB_PASSWORD=secret
(...)

```

#### Configure Laravel Emails
If you want to handle password recovery by email you should config laravel email sending:

Edit your `.env` file and add the following information
```
(...)
MAIL_DRIVER=smtp
MAIL_HOST=your.email.host.url
MAIL_PORT=YourPort
MAIL_USERNAME=your_user_name@domain.com
MAIL_PASSWORD=secret
(...)
```
#### Install dependencies
Make sure you have `composer` and `npm` installed, then run:

```
composer update
npm install
npm run dev
```

#### Artisan configuration commands 
Generate OAuth Keys and database schema

```
php artisan passport:install
php artisan migrate
```
Passport will generate 2 keys IDs and 2 Key Secret, one for personal access, the other for password access. Save them to use in your future conections

## Conection Example

To conect to your Api you will need the OAuth client ID and the Client Secret generated by passport

You can use it to conect to the endpoints generated:


|Method      | End Point                 | Description                              |
| ---------- | ------------------------- | ---------------------------------------- |
|POST        | oauth/token               | Request for user token                   |
|POST        | oauth/token/refresh       | Refresh token                            |
|GET / HEAD  | oauth/tokens              | Get authorized access tokens for user    |
|DELETE      | oauth/tokens/{token_id}   | Delete token                             |
|POST        | api/user/register         | Register user                            |

#### Connect using php
Get token for a user registered
```php
<?php

$client = new http\Client;
$request = new http\Client\Request;

$body = new http\Message\Body;
$body->addForm(array(
  'client_secret' => 'YourClientSecretHere',  
  'grant_type' => 'password',
  'username' => 'YourUserName',
  'password' => 'YourUserPass',
  'client_id' => 'YourClientID'
), NULL);

$request->setRequestUrl('https://example.domain.com/oauth/token');
$request->setRequestMethod('POST');
$request->setBody($body);

$client->enqueue($request)->send();
$response = $client->getResponse();

echo $response->getBody();
```

Register user via Api
```php
<?php

$client = new http\Client;
$request = new http\Client\Request;

$body = new http\Message\Body;
$body->addForm(array(
  'name' => 'Name',
  'email' => 'yourUser@email',
  'password' => 'userPassword',
  'password_confirmation' => 'userPasswordConfirmation'
), NULL);

$request->setRequestUrl('https://example.domain.com/api/user/register');
$request->setRequestMethod('POST');
$request->setBody($body);

$client->enqueue($request)->send();
$response = $client->getResponse();

echo $response->getBody();
```