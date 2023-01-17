<?php

namespace Gigya\PHP\Test;

use Gigya\PHP\GSKeyNotFoundException;
use Gigya\PHP\GSRequest;
use Gigya\PHP\GSResponse;
use PHPUnit\Framework\TestCase;

class GSRequestWithUserKeyPrivateKeyTest extends TestCase
{
    /**
     * @param $apiKey
     * @param $apiDomain
     * @param $userKey
     * @param $privateKey
     *
     * @dataProvider \Gigya\PHP\Test\JwtAuthDataProvider::provideAuthDetails
     *
     * @throws GSKeyNotFoundException
     */
    public function testGigyaCallWithBearerToken($apiKey, $apiDomain, $userKey, $privateKey)
    {
        $response = $this->sendAccountsSearchRequest($apiKey, $apiDomain, $userKey, $privateKey);

        $this->assertEquals(200, $response->getInt('statusCode'));
        $this->assertEquals(0, $response->getErrorCode());
    }

    /**
     * Sends a Gigya request related to a specific account, namely accounts.getAccountInfo.
     * This is useful because account methods have particular requirements so validation might fail on the Gigya side in some cases when a generic call succeeds
     *
     * @param $apiKey
     * @param $apiDomain
     * @param $userKey
     * @param $privateKey
     * @param $uid
     *
     * @dataProvider \Gigya\PHP\Test\JwtAuthDataProvider::provideAuthDetails
     *
     * @throws GSKeyNotFoundException
     */
    public function testGigyaAccountCallWithBearerToken($apiKey, $apiDomain, $userKey, $privateKey, $uid)
    {
        $response = $this->sendGetAccountInfoRequest($apiKey, $apiDomain, $userKey, $privateKey, $uid);

        $this->assertEquals(200, $response->getInt('statusCode'));
        $this->assertEquals(0, $response->getErrorCode());
    }

    /**
     * @param $apiKey
     * @param $apiDomain
     * @param $userKey
     * @param $privateKey
     *
     * @dataProvider \Gigya\PHP\Test\JwtAuthDataProvider::provideIncorrectAuthDetails
     *
     * @throws GSKeyNotFoundException
     */
    public function testGigyaCallWithIncorrectBearerToken($apiKey, $apiDomain, $userKey, $privateKey)
    {
        $response = $this->sendAccountsSearchRequest($apiKey, $apiDomain, $userKey, $privateKey);

        $this->assertEquals(400008, $response->getErrorCode());
    }

    /**
     * @param string $apiKey
     * @param string $apiDomain
     * @param string $userKey
     * @param string $privateKey
     *
     * @return GSResponse
     *
     * @throws GSKeyNotFoundException
     */
    private function sendAccountsSearchRequest(string $apiKey, string $apiDomain, string $userKey, string $privateKey): GSResponse {
        return $this->sendRequest($apiKey, $apiDomain, $userKey, $privateKey, 'accounts.search', array('query' => 'SELECT * FROM user LIMIT 1'));
    }

    /**
     * @param string $apiKey
     * @param string $apiDomain
     * @param string $userKey
     * @param string $privateKey
     * @param string $uid
     *
     * @return GSResponse
     * @throws GSKeyNotFoundException
     */
    private function sendGetAccountInfoRequest(string $apiKey, string $apiDomain, string $userKey, string $privateKey, string $uid): GSResponse {
        return $this->sendRequest($apiKey, $apiDomain, $userKey, $privateKey, 'accounts.getAccountInfo', array('uid' => $uid));
    }

    /**
     * @param string $apiKey
     * @param string $apiDomain
     * @param string $userKey
     * @param string $privateKey
     * @param string $apiMethod
     * @param array $params
     *
     * @return GSResponse
     * @throws GSKeyNotFoundException
     */
    private function sendRequest(string $apiKey, string $apiDomain, string $userKey, string $privateKey, string $apiMethod, array $params): GSResponse {
        $request = new GSRequest($apiKey,
            null,
            $apiMethod,
            null,
            true,
            $userKey,
            $privateKey
        );
        $request->setAPIDomain($apiDomain);
        foreach ($params as $param => $value) {
            $request->setParam($param, $value);
        }

        return $request->send();
    }
}

