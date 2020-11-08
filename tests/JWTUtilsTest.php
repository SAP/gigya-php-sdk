<?php

namespace Gigya\PHP\Test;

use Exception;
use Firebase\JWT\JWT;
use Gigya\PHP\GSKeyNotFoundException;
use Gigya\PHP\GSRequest;
use PHPUnit\Framework\Error\Warning;
use PHPUnit\Framework\TestCase;
use Gigya\PHP\JWTUtils;

class JWTUtilsTest extends TestCase
{
    /**
     * @param $privateKey
     * @param $publicKey
     *
     * @dataProvider \Gigya\PHP\Test\JwtAuthDataProvider::provideFakeValidKeyPair
     *
     * @throws Exception
     */
    public function testGetBearerToken($privateKey, $publicKey)
    {
        $userKey = 'myUserKey';

        $bearerToken = JWTUtils::getBearerToken($privateKey, $userKey);
        $jwtDetails = JWT::decode($bearerToken, $publicKey, ['RS256']);

        $this->assertTrue($jwtDetails instanceof \stdClass);
        $this->assertObjectHasAttribute('iat', $jwtDetails);
        $this->assertObjectHasAttribute('jti', $jwtDetails);
    }

    /**
     * @throws Exception
     */
    public function testGetBearerTokenIncorrectPrivateKey()
    {
        $incorrectPrivateKey = rand();
        $userKey = rand();

        $this->expectException(Warning::class);

        JWTUtils::getBearerToken($incorrectPrivateKey, $userKey);
    }

    /**
     * @param string $apiKey
     * @param string $apiDomain
     * @param string $userKey
     * @param string $privateKey
     * @param string $uid
     *
     * @dataProvider \Gigya\PHP\Test\JwtAuthDataProvider::provideAuthDetails
     *
     * @throws Exception
     */
    public function testValidateSignature($apiKey, $apiDomain, $userKey, $privateKey, $uid)
    {
        $jwt = $this->getJWT($apiKey, $apiDomain, $userKey, $privateKey, $uid);
        $this->assertNotFalse($jwt);

        $claims = JWTUtils::validateSignature($jwt, $apiKey, $apiDomain);
        $this->assertEquals($claims->apiKey, $apiKey);
        $this->assertEquals($claims->sub, $uid);
        $this->assertNotEmpty($claims->email);
    }

    /**
     * @param string $apiKey
     * @param string $apiDomain
     * @param string $userKey
     * @param string $privateKey
     * @param string $uid
     *
     * @return string|false
     *
     * @throws GSKeyNotFoundException
     */
    private function getJWT($apiKey, $apiDomain, $userKey, $privateKey, $uid)
    {
        $request = new GSRequest($apiKey,
            null,
            'accounts.getJWT',
            null,
            true,
            $userKey,
            $privateKey
        );
        $request->setAPIDomain($apiDomain);
        $request->setParam('fields', 'profile.firstName,profile.lastName,email');
        $request->setParam('targetUID', $uid);

        $response = $request->send();

        if ($response->getErrorCode() === 0) {
            return (!empty($response) && !empty($response->getData())) ? $response->getData()->getString('id_token') : false;
        } else {
            return false;
        }
    }
}

