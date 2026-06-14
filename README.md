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

## Using Mutual TLS (mTLS) Authentication

For high-security server-to-server communication, you can authenticate using a client X.509 certificate instead of (or alongside) the site secret. When mTLS is configured, the SDK automatically routes the request to the datacenter-specific endpoint `mtls.{datacenter}.gigya.com` and presents the certificate during the TLS handshake.

### Example with Certificate Files

```php
use Gigya\PHP\GSRequest;

// Create a request — apiKey/secretKey are not required when using mTLS
$request = new GSRequest(
    'your-api-key',
    null,                        // no secretKey
    'accounts.getAccountInfo',
    null,
    true                         // useHTTPS — required for mTLS
);

$request->setAPIDomain('us1.gigya.com');
$request->setParam('UID', 'user123');

// Configure the client certificate
$request->setClientCertificate(
    'certs/client.pem',          // Path to client certificate
    'certs/client.key'           // Path to private key
);

$response = $request->send();

if ($response->getErrorCode() == 0) {
    echo "Success: " . $response->getResponseText();
} else {
    echo "Error: " . $response->getErrorMessage();
}
```

### Example with PEM Content

You can also pass certificate and key content directly as PEM strings, which is useful when loading from environment variables or a secret store. PEM strings are written to short-lived temp files for the duration of the request.

```php
$certPem = getenv('MTLS_CERT_PEM');
$keyPem  = getenv('MTLS_KEY_PEM');

$request->setClientCertificate($certPem, $keyPem);
```

If the private key is encrypted, pass the password as the third argument:

```php
$request->setClientCertificate('certs/client.pem', 'certs/client.key', 'key-password');
```

**Note:** Both the certificate and key must be provided together. Each can be either a file path or PEM content.

## Limitations
None

## Known Issues
None

## How to obtain support
[Learn more](https://help.sap.com/viewer/8b8d6fffe113457094a17701f63e3d6a/GIGYA/en-US/4167e8a470b21014bbc5a10ce4041860.html)

## Contributing
Via pull request to this repository.

## Code of Conduct
See [CODE_OF_CONDUCT](https://github.com/SAP/gigya-php-sdk/blob/main/CODE_OF_CONDUCT.md)

## To-Do (upcoming changes)
None

## Licensing
Please see our [LICENSE](https://github.com/SAP/gigya-php-sdk/blob/main/LICENSE.txt) for copyright and license information.

[![REUSE status](https://api.reuse.software/badge/github.com/SAP/gigya-php-sdk)](https://api.reuse.software/info/github.com/SAP/gigya-php-sdk)
