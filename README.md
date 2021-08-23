# Gigya SDK for PHP 

[![REUSE status](https://api.reuse.software/badge/github.com/SAP/gigya-php-sdk)](https://api.reuse.software/info/github.com/SAP/gigya-php-sdk)

Learn more: https://developers.gigya.com/display/GD/PHP

## Description
The PHP SDK, provides a PHP interface for the Gigya API. 
The library makes it simple to integrate Gigya services in your PHP application.

## Requirements
[PHP 7.x.](https://www.php.net/downloads) 

## Download and Installation
### Standalone
* Clone the repo
* Run `composer update`

### In a project
* Run `composer config repositories.gigyaphpsdk git https://github.com/SAP/gigya-php-sdk.git`
* Run `composer require gigya/php-sdk`

It will now be possible to autoload Gigya PHP SDK: `use Gigya\PHP`.

## Configuration
* [Obtain a Gigya APIKey and authentication details](https://developers.gigya.com/display/GD/PHP#PHP-ObtainingGigya'sAPIKeyandSecretkey).
* Follow the installation instructions above
* Start using according to [documentation](https://developers.gigya.com/display/GD/PHP).

## Limitations
None

## Known Issues
None

## How to obtain support
Via SAP standard support.
https://developers.gigya.com/display/GD/Opening+A+Support+Incident

## Running tests
* Copy `tests/provideAuthDetails.json.dist` to `tests/provideAuthDetails.json`
* If testing JWT-related functions, create a private key file
* Enter the relevant authentication details and the private key file path in `tests/provideAuthDetails.json` 

## Contributing
Via pull request to this repository.

## To-Do (upcoming changes)
None

## Licensing
Copyright 2020-2021 SAP SE or an SAP affiliate company and gigya-php-sdk contributors. Please see our [LICENSE](LICENSE.txt) for copyright and license information. Detailed information including third-party components and their licensing/copyright information is available [via the REUSE tool](https://api.reuse.software/info/github.com/SAP/gigya-php-sdk).
