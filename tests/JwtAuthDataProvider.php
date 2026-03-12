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
    public function provideAuthDetails(): array
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

        return $returnData ?? [
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
        $data = array_merge($this->provideAuthDetails()[0], $this->provideFakeValidKeyPair()[0]);

        return [
            $data,
        ];
    }

    /**
     * Provides a valid public/private key pair that's not related to Gigya.
	 * This pair is fake, and contains dummy data.
	 * It does not contain any personally identifiable information (PII).
     *
     * @return array
     */
    public function provideFakeValidKeyPair()
    {
        $privateKey = <<<EOD
-----BEGIN PRIVATE KEY-----
MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDC7N3Lum92V+ol
CRDZcH0DfBbdV4hS1ARd3PcFKoEk/7fkfveyqcwHoNVBA6Tznb+1s/7G5pUcGD4w
M/DNK7AKjVza/0KJwGcsvJRGTVOiYFkf20t/vkgKS+OB194IRGj3FPKtjtsfGb46
RGySoivHj2C7E3ubeASkl077idADj62thTM8iYVz+y3VI+oidcosx90DYr767S5w
Lq0NECLdDqh8420K5DiUUTmzD9JNBGiYEnP+EoR2VpDNlaHvQi1J3ORi5N9gYG4P
zGfdLaG6fz/6AFIuT9rnn5x+J8EuOxu4GNxq7MBCBioYBpUKM3IIhQYIh9WMkiE5
ppz6x+PbAgMBAAECggEAHavgtzpR7TnEso6GuhKddQJWmKrSXYlYgNE56NdngTJL
08RgABpIrICW9aaQXWUN4aLcjXpcCELIdJ3zccfMrE6Ia+2fO0jGa9Do4Sq0KGty
JZ9EBtX4MVkL8J/jdG61a6cMqc6tHDbW9hekT03QTDhPCSsWDbxfOI6vvypjN6dK
4YDCCEVGPIvbJS/CCdmnKXdQcV3X5ZiCosXQXW9yc+L28OMc/xUiR05tWutypp/A
pySw+KfTPCy4Rb6z+fOM4WExC71gtjTQD+lAtKs2JVz2khWWvueWvA8OarULzxdv
xsAzskkVwnuNapZkPxR4Tt60UHF9B/C3cASll8ncwQKBgQD51maotdd8kjFtqm6h
68CkSnsCpHQmj6cRR+CWPc8CG9rh6B30vQHybdY6suo2xw9KJeDRNUxvdT06jVcN
UdtjbH1k5PBF/A6KWXj1uulJtsrgrYJ1Qrrr/0dg50EMsu32lKm9SC7jtaEXmA2P
EyJaqj74TvUSGrpJpjImwhR/HQKBgQDHu7jUnETbdy/+jI7piFHCpccokeN6Jxsz
p8e/RYIA2UmrYjd2MejO93NH2Eys64QFbb7f0wSYsxbCYIBvcKjsOjb+PIHufxJL
DvqJy7z70sPfoZTVnqPGXw9+4VVlVnh9qoRoxZ8RAwU0AqV/xMBtTNlfP3sDqiaw
wb8LWv+lVwKBgDssHpBi0TC951sT/LP5BF1lDpFnpeLkLyuWnIi5Buh29HaA5Wdr
xKRIg27PpU1oBCUJp5+lQf88A05032NWOeHodiKJXqcUtdTqsA1zQYGl+5MPRVDE
h0UR5zP6UZvSXS4Ds9gS2pgwEoFmEAANBhv3KobhHIY3MlvzMvmfCf+5AoGAUO+s
BVPRv1E7/J1+p/makBLXcoQztuMz7am2kraa6LckWDOzn8y3t26ko19uKsBBPmXr
bA25lhs0RM4QHZh8i0VH1Zw1Vqzdf9bxBXeu/Ci7QNrcUfg4C4Gn6NVs4mS47Mt1
XnN+cslTiXDtGapkA6aZizRRJ/oCNaw9O1/Dqn8CgYEA590ZumHK2oj1Owh0GneW
hC0YkMsidQ110YxxO5y1GpZpbRIN7x1L5CxKmjoFzfvMSRzmnv2jyE+L8QbF8AXM
jaw1sBmEkaEE/A1dNPV8bq3OSKVLR4wxTKLIjQTwooCSaj5UPvb38Z/gR5P36svU
4BwzxgzFcO51XRiaUz298D0=
-----END PRIVATE KEY-----
EOD;

        $publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwuzdy7pvdlfqJQkQ2XB9
A3wW3VeIUtQEXdz3BSqBJP+35H73sqnMB6DVQQOk852/tbP+xuaVHBg+MDPwzSuw
Co1c2v9CicBnLLyURk1TomBZH9tLf75ICkvjgdfeCERo9xTyrY7bHxm+OkRskqIr
x49guxN7m3gEpJdO+4nQA4+trYUzPImFc/st1SPqInXKLMfdA2K++u0ucC6tDRAi
3Q6ofONtCuQ4lFE5sw/STQRomBJz/hKEdlaQzZWh70ItSdzkYuTfYGBuD8xn3S2h
un8/+gBSLk/a55+cfifBLjsbuBjcauzAQgYqGAaVCjNyCIUGCIfVjJIhOaac+sfj
2wIDAQAB
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
