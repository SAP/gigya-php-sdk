<?php

namespace Gigya\PHP\Test;

use Gigya\PHP\GSRequest;
use PHPUnit\Framework\TestCase;

class GSRequestMtlsDomainTest extends TestCase
{
    /**
     * @dataProvider mtlsDomainProvider
     */
    public function testGetMtlsDomain(string $apiDomain, string $expectedMtlsDomain)
    {
        $request = new GSRequest('testApiKey', null, 'accounts.getAccountInfo', null, true, 'testUserKey', 'testPrivateKey');
        $request->setAPIDomain($apiDomain);

        $this->assertEquals($expectedMtlsDomain, $request->getMtlsDomain());
    }

    public function mtlsDomainProvider(): array
    {
        return [
            'US1 datacenter'     => ['us1.gigya.com', 'mtls.us1.gigya.com'],
            'EU1 datacenter'     => ['eu1.gigya.com', 'mtls.eu1.gigya.com'],
            'EU2 datacenter'     => ['eu2.gigya.com', 'mtls.eu2.gigya.com'],
            'AU1 datacenter'     => ['au1.gigya.com', 'mtls.au1.gigya.com'],
            'Global datacenter'  => ['global.gigya.com', 'mtls.global.gigya.com'],
            'Staging datacenter' => ['us1-st1.gigya.com', 'mtls.us1-st1.gigya.com'],
        ];
    }

    public function testGetMtlsDomainDefaultsToUs1()
    {
        $request = new GSRequest('testApiKey', null, 'accounts.getAccountInfo', null, true, 'testUserKey', 'testPrivateKey');

        $this->assertEquals('mtls.us1.gigya.com', $request->getMtlsDomain());
    }

    /**
     * @dataProvider missingDcProvider
     */
    public function testGetMtlsDomainWhenDcDoesNotExist(string $apiDomain, string $expectedMtlsDomain)
    {
        $request = new GSRequest('testApiKey', null, 'accounts.getAccountInfo', null, true, 'testUserKey', 'testPrivateKey');
        $request->setAPIDomain($apiDomain);

        $this->assertEquals($expectedMtlsDomain, $request->getMtlsDomain());
    }

    public function missingDcProvider(): array
    {
        return [
            'empty string falls back to default'  => ['', 'mtls.us1.gigya.com'],
            'bare domain without dc prefix'        => ['gigya.com', 'mtls.gigya.gigya.com'],
            'leading dot (empty dc segment)'       => ['.gigya.com', 'mtls.us1.gigya.com'],
        ];
    }
}
