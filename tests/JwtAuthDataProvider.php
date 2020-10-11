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
MIIEogIBAAKCAQBmVip5+cav2sM7aCZR7UYdwwIibfGacFDjPSQQg9yec5jOqJBv
c9foISgHjE1gZg+04TD5cRsb65uj3XcACmGOJxkYKpFBr8b5kYrr2/YBsoHHhzlv
IuOSP4iSRERvhOXkDpi1Ymfwxo10nLimv1Ub+sukeu8dCJhUOfSKLmKfOM7X7sNU
h8Lpwigqlo5/SrCWV98VQbCZ2gi2VUt/UyY3lHZ0VLzMzY/iZEQx3XWaQX2jpLqu
7I6SfrjZwLQH0Mpg0cDJw5bnyMZQj5l5FonZSJwK0NwkD2jjnIK/5ueUdwS+8bj1
pWc8Cqo58MhnlwhFbptj87ejFLsa97W0KMQvAgMBAAECggEAO4MgcI6w3NN7dbC7
mClD/UrKkvsc5ZMrdvq3XmNQiVTwKD4ewLG9fWDKxpjw5n5z49yRfn4oJbR+bmz8
JyniUairD0Kx9mEidUAOadsg1RvaHQ6md9ryPLp10Zmhsri2eEmExPbVO1A8MvRw
7YeXFw5iHuTe6SN2coTBkRoogC7r3pe69CZrjWVTJtL2XEqgZQ4anJasqxhIMqe2
5wzusNW0cde6Cf6TxZ7ffTISN3x4hmrzCA8imtPr2GHr5IGSOryc4g2QSS6sp3/y
3hVlUVGcoP7PWhUmehnyWVpVum32HqSfXeogFM7uZxBa+BbgmiHKm8zxhZjDgsE5
3GsfAQKBgQCzPZB+r9Bh6nMiIulykcWICG0p9wHtxHzArI0f/MyQKlIqsok9003z
mXni4XP2lNr0Ye4nppewQ7H+7T/1eee3MKm13LSXQo03c5O0Hsyk6kNquiQlH75K
sqQFoirQY7VQPficnfIJGhEUlrcL1f7mI5xHymxUUw2abdeH98UgpQKBgQCSKX9p
v8pLzK7isoRJ0tw7y0s4YMQa8v5cYnKOtyleZVtouVSNJkG4oSIERObruCLvjSK8
Y6s0bxrr7GkSVwJJKgnKqpH9/O/3LYD8Jm5pgoilQrvdwTlBVwNeCMzBSaphPDnS
mWkCkrhvOsU49yTBuOmCQlFY3uW/+x57WNYFQwKBgQCUvNHbTyotFtDT0wTF/hFd
ASEVYdRH6nVbrdSuZqmaOIRWyjD5DQ/yxblPfKcuR/gqM9ndLXFS49ohId+hZLfU
XJ14PN3fZ2qoSVCYd7Z6Y6vuNOkppV3Tvso9ooTEyPj8zXAxByCCTQ8/dpIPUFVl
xMc7C885anIvZFcmjYUXwQKBgD4tuS5bkMu+P4fhObXOJjKpwU4ynDp6tQrINsPP
16F4rPJYJUgGxl6s7bOzPCl7JCGUgPpzzBl6SpGi+O/8Y9SMNa4p8gGC4PLeQMkD
8GSiS09CnHkyAhFx2tTeGX0AwaKr5E1bRfj6QcvPzKb3PIPNDkmDvOd5q3OIeOcw
W5vRAoGAWG/cxq5tG7Yxa+yKYhrG31KCTCfREoCOayEkU7dOkiyVKBbAfQXf4VTv
Xh2zcbcJ/qsxJzw4BHvZ2XrWPePWQyF4v+0OZSvL1LefDnFYLKD5v+19gumCC+6U
DIif0pU8bLmfhpxAirE0XluIaNXWJ8lfzcaXIirepCB05GOI6Ss=
-----END RSA PRIVATE KEY-----
EOD;

        $publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIIBITANBgkqhkiG9w0BAQEFAAOCAQ4AMIIBCQKCAQBmVip5+cav2sM7aCZR7UYd
wwIibfGacFDjPSQQg9yec5jOqJBvc9foISgHjE1gZg+04TD5cRsb65uj3XcACmGO
JxkYKpFBr8b5kYrr2/YBsoHHhzlvIuOSP4iSRERvhOXkDpi1Ymfwxo10nLimv1Ub
+sukeu8dCJhUOfSKLmKfOM7X7sNUh8Lpwigqlo5/SrCWV98VQbCZ2gi2VUt/UyY3
lHZ0VLzMzY/iZEQx3XWaQX2jpLqu7I6SfrjZwLQH0Mpg0cDJw5bnyMZQj5l5FonZ
SJwK0NwkD2jjnIK/5ueUdwS+8bj1pWc8Cqo58MhnlwhFbptj87ejFLsa97W0KMQv
AgMBAAE=
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