# PHP SDK  
[Learn more](https://help.sap.com/viewer/8b8d6fffe113457094a17701f63e3d6a/GIGYA/en-US/4168a6ba70b21014bbc5a10ce4041860.html)

## Description
The PHP SDK provides a PHP interface for the Gigya API. 
The library makes it simple to integrate Gigya services in your PHP application.

## Requirements
[PHP 8.0.x, PHP 8.1.x, PHP 8.2.x](https://www.php.net/downloads)

## Download and Installation
### Standalone
* Clone the repo.
* Run `composer update`.
### In a project
* Run `composer config repositories.gigyaphpsdk git https://github.com/SAP/gigya-php-sdk.git`
* Run `composer require gigya/php-sdk`

It will now be possible to autoload Gigya PHP SDK: `use Gigya\PHP`.

Note: If the project does not use Composer natively / as part of a framework, it is necessary to include `vendor/autoload.php` in your project.

## Configuration
* [Obtain a Gigya APIKey and authentication details](https://developers.gigya.com/display/GD/PHP#PHP-ObtainingGigya'sAPIKeyandSecretkey).
* Follow the installation instructions above.
* Start using according to [documentation](https://developers.gigya.com/display/GD/PHP).


## Running tests
1. Copy `tests/provideAuthDetails.json.dist` to `tests/provideAuthDetails.json`
2. If testing JWT-related functions, create a private key file.
3. Enter the relevant authentication details and the private key file path in `tests/provideAuthDetails.json`.

## Limitations
None

## Known Issues
None

## How to obtain support
[Learn more](https://help.sap.com/viewer/8b8d6fffe113457094a17701f63e3d6a/GIGYA/en-US/4167e8a470b21014bbc5a10ce4041860.html)


## Contributing
Via pull request to this repository.

## To-Do (upcoming changes)
None

## Licensing
Please see our [LICENSE](https://github.com/SAP/gigya-php-sdk/blob/main/LICENSE.txt) for copyright and license information.

[![REUSE status](https://api.reuse.software/badge/github.com/SAP/gigya-php-sdk)](https://api.reuse.software/info/github.com/SAP/gigya-php-sdk)
