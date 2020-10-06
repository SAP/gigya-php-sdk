<?php

namespace Gigya\PHP\Test;

class JwtAuthDataProvider
{
    /**
     * Provides, in the following order:
     * * API key
     * * API domain
     * * User key
     * * Private key
     * * UID
     *
     * @return array
     */
    public function provideAuthDetails()
    {
        $jsonDataProvider = __DIR__ . '/' . __FUNCTION__ . '.json';

        if (file_exists($jsonDataProvider)) {
            $data = json_decode(file_get_contents($jsonDataProvider), true);

            /* Inserts private key from file, if applicable */
            foreach ($data as $i => $dataSet) {
                foreach ($dataSet as $key => $value) {
                    if (($key === 'privateKeyFile') && file_exists(__DIR__ . '/' . $value)) {
                        $key = 'privateKey';
                        $value = file_get_contents(__DIR__ . '/' . $value);
                    }

                    $returnData[$i][$key] = $value;
                }
            }
        }

        return $returnData ?? [ /* PHP 7.0+ */
                [
                    'apiKey' => '',
                    'apiDomain' => '',
                    'userKey' => '',
                    'privateKey' => '',
                    'UID' => '',
                ],
            ];
    }

    public function provideIncorrectAuthDetails()
    {
        $data = array_merge($this->provideAuthDetails()[0], $this->provideValidKeyPair()[0]);

        return [
            $data,
        ];
    }

    /**
     * Provides a valid public/private key pair that's not related to Gigya
     *
     * @return array
     */
    public function provideValidKeyPair()
    {
        $privateKey = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
----
-----END RSA PRIVATE KEY-----
EOD;

        $publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
----
-----END PUBLIC KEY-----
EOD;

        return [
            [
                'privateKey' => $privateKey,
                'publicKey' => $publicKey,
            ]
        ];
    }
}
