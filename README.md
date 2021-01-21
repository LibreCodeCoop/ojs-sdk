![Test Status](https://github.com/lyseontech/ojs-sdk/workflows/ci/badge.svg?branch=main)

# OJS-SDK

SDK package for the [OJS](https://pkp.sfu.ca/ojs/)

## Install

```bash
composer require lyseontech/ojs-sdk
```

## Configure

OJS installed in the same server is necessary. Inform the path of OJS for all dependencies and the login URL if you need make login and get a user token after login.

| Environment     | Suggested value                                 |
| --------------- | ----------------------------------------------- |
| OJS_VERSION     | 3_2_1-1                                         |
| DB_PASSWD       | root                                            |
| OJS_WEB_BASEDIR | /app/ojs                                        |
| OJS_LOGIN_URL   | http://localhost/ojs/index.php/csp/login/signIn |

## How to use?

* Create or update user:
```php
use OjsSdk\Services\OJSService\Users\OJSUserService;

$OjsClient = new OJSUserService();
$return = $OjsClient->createUpdateUser([
    'username' => 'jhonusername',
    'password' => '123password',
    'email' => 'jhon.doe@test.coop',
    'mailingAddress' => 'Street 55',
    'locales' => ['en_US'],
    'groups' => [1], // administrator
    'givenName'     => ['en_US' => 'Jhon'],
    'familyName'    => ['en_US' => 'Doe'],
    'phone'         => '+123456789',
    'lattes'        => 'https://lattes.com'
]);
```
* changePassword

```php
use OjsSdk\Services\OJSService\Users\OJSUserService;

$OjsClient = new OJSUserService();
$OjsClient->changePassword('jhonusername', '123password');
```
* getUniqueUsername

```php
use OjsSdk\Services\OJSService\Users\OJSUserService;

$OjsClient = new OJSUserService();
$uniqueUsername = $OjsClient->getUniqueUsername('jhonusername', '123password');
```

* Login

```php
use OjsSdk\Services\OJSService\Users\OJSUserService;

$OjsClient = new OJSUserService();
$loginResponse = $OjsClient->login('jhonusername', '123password');
```

### Make anyfing

The method `OjsProvider::getApplication();` make all necessary to use all classes of OJS.

For more informations see [OJSUserServiceTest.php](tests/Services/OJSService/Users/OJSUserServiceTest.php) or [OJSUserService.php](src/Services/OJSService/Users/OJSUserService.php).


## Development

Use Docker for create a development environment.

If you don't use Docker, read the [entrypoint.sh](.docker/php7/entrypoint.sh) and [Dockerfile](.docker/php7/Dockerfile).

Running tests:

```bash
composer test
```