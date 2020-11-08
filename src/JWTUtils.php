<?php

namespace Gigya\PHP;

use Exception;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use InvalidArgumentException;
use stdClass;
use UnexpectedValueException;

class JWTUtils
{
    /**
     * Composes a JWT to be used as a bearer token for authentication with Gigya
     *
     * @param string $privateKey
     * @param string $userKey
     * @param string $nonce
     *
     * @return string
     *
     * @throws Exception
     */
    public static function getBearerToken(string $privateKey, string $userKey, $nonce = null)
    {
        $jti = $nonce ?? SigUtils::currentTimeMillis() . rand(); /* PHP 7.0+ */
        $payload = [
            'iat' => time(),
            'jti' => $jti,
        ];

        return JWT::encode($payload, $privateKey, 'RS256', $userKey);
    }

    /**
     * Validates JWT signature
     *
     * @param string $jwt
     * @param string $apiKey
     * @param string $apiDomain
     *
     * @return stdClass|bool
     *
     * @throws Exception
     */
    public static function validateSignature(string $jwt, string $apiKey, string $apiDomain)
    {
        /* Validate input and get KID */
        if (!$jwt) {
            throw new InvalidArgumentException('No JWT provided');
        }
        if (substr_count($jwt, '.') !== 2) {
            throw new InvalidArgumentException('Invalid JWT format');
        }
        $jwtHeader = explode('.', $jwt)[0];
        if (empty($jwtArray = json_decode(base64_decode($jwtHeader), true))) {
            throw new InvalidArgumentException('Invalid JWT format');
        }
        if (empty($kid = $jwtArray['kid'])) {
            throw new InvalidArgumentException('No KID field found in JWT');
        }

        try {
            $jwk = self::getJWKByKid($apiKey, $apiDomain, $kid);
        } catch (GSException $e) {
            return false;
        }

        try {
            $jwtInfo = JWT::decode($jwt, $jwk, ['RS256']);

            return $jwtInfo ?? false; /* PHP 7.0+ */
        } catch (UnexpectedValueException $e) {
            return false;
        }
    }

    /**
     * @param $apiKey
     * @param $apiDomain
     * @param $kid
     *
     * @return string|resource
     *
     * @throws GSException
     */
    private static function getJWKByKid($apiKey, $apiDomain, $kid) {
        if (($jwks = self::readPublicKeyCache($apiDomain)) === false) {
            $jwks = self::fetchPublicJWKs($apiKey, $apiDomain);
        }

        if (isset($jwks[$kid])) {
            $jwk = $jwks[$kid];
        } else {
            $jwks = self::fetchPublicJWKs($apiKey, $apiDomain);

            if (isset($jwks[$kid])) {
                $jwk = $jwks[$kid];
            } else {
                return false;
            }
        }

        return $jwk;
    }

    /**
     * @param $apiKey
     * @param $apiDomain
     *
     * @return array|null
     *
     * @throws GSException
     */
    private static function fetchPublicJWKs($apiKey, $apiDomain)
    {
        $request = new GSRequest($apiKey, null, 'accounts.getJWTPublicKey');
        $request->setAPIDomain($apiDomain);
        $request->setParam('V2', true);

        $result = $request->send();

        $keys = $result->getData()->serialize();
        if (!empty($keys)) {
            try {
                $publicKeys = JWK::parseKeySet($keys);
            } catch (Exception $e) {
                throw new GSException('Unable to retrieve public key: ' . $e->getMessage());
            }

            self::addToPublicKeyCache($publicKeys, $apiDomain);

            return $publicKeys;
        }

        return null;
    }

    /**
     * @param array  $publicKeys
     * @param string $apiDomain
     *
     * @return int|false Bytes written to cache file or false on failure
     */
    private static function addToPublicKeyCache($publicKeys, $apiDomain)
    {
        foreach ($publicKeys as $kid => $key) {
            if (!empty($pem = openssl_pkey_get_details($key)['key'])) {
                $publicKeys[$kid] = $pem;
            } else {
                return false;
            }
        }

        $filename = __DIR__ . '/keys/' . $apiDomain . '_keys.txt';

        return file_put_contents($filename, json_encode($publicKeys));
    }

    /**
     * @param string $apiDomain Data center
     *
     * @return array|false Returns false if the cache file does not exist, or if reading the file or decoding the JSON array in it fails
     */
    private static function readPublicKeyCache($apiDomain)
    {
        $filename = __DIR__ . '/keys/' . $apiDomain . '_keys.txt';

        if (!file_exists($filename)) {
            return false;
        }

        return json_decode(file_get_contents($filename), true);
    }
}
