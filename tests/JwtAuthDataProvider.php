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
-----BEGIN RSA PRIVATE KEY-----
MIIEogIBAAKCAQB16HIHTWGIQ+pE/treHHPqAxtqscaf19AuYrVxSLaumuuBvvpW
iBkhPHctqb5Sg3+bV5U3dN8JZD5TF3Usqq4szhMBUX6Ltj3qXP2Shkd8o7ZvgmgK
v7q/m7Hns5kUqKWAArtqJr7bA0/tGbKGUgKTmwgfHuzNyXEgbA5YXH8yEi98qU/o
lhVL6VMzz02Shd4M1f5pJnzYxo2wkZHI9MDxDNF0oaCt9jxYxFlw4C3roSTglgZ4
kjRQ0Vmjsl6VmuaUtJ4XxCLgvYzMeGsOSeZnWMRH+YCt7aYU86d9GijTH4r2IhV8
3tOgHZ8hcD69fyd+TWbc0n2Sl9vDYGmLcWZpAgMBAAECggEAVIjaMEgXdEYVEXCT
ZQmLRa0CnCVnXbSySn90zQqwB1GFJhnRi/a55a1fWPrJPImRFMPfdMozGp2nXZWE
6ghZJkB9OcW+79YyX/RZzRK+8CrEIwChLYu3DadIDvIh/8DGH5hV3E55KbcfN8Pj
zg/ATymo0f8vEn+pvvAZ/NocS73qLWxdgRCkp1NFxGIeyZL59zC7s97uWPS4k+XS
ugghFFHMV0IbFVGlSKcYwMYQoOIGhd0O3HvJcVEbeqtQO29KUpUj6NOV3jEin3XT
Wq+hX+PPRlogero6xN4SipjuNOtnYZFTPCPia79yhwV/rR4PZLqF7bYotTv+Lrqy
UI1x7QKBgQC7/Id31Dn7zlyad7o+4D1aFrOW+tPflteyeyo7ELnaoDVY43U4yyoX
qkclMVqvDOqU9sNlbhQKdsTKkarotdbqj0eNvlfiV7bttOKAT27d40jGIl38DVm2
GyurAQptLu+sHgzHWr4xO3f3DV+6KPD+9dusXRFELffpiYdDHNGLMwKBgQCgkTAb
r3H8q0RUUC82vkKL79fdv6xq7TeAT50DHE+brUdJ4VI55PY+nSwZJ4Rl9ecuQ4l5
3VmZqVlZOddTOshDQM9l2C5Afi2h5Q+ZxRarkfn5qBerdUxM3Q78ing8+WrIDzMy
HC7GbikbR8vPkrAezxOgh/WfsaY1oOuN5lSn8wKBgFKv+np37GVZWRxMy6x6gCIP
WEFx5R41QH1udZ3zdny2+aA2tMode09bg93VPrk+6jRJ8pq7966+nUMpHc8spPMZ
wPSVJB0YTgtzBCpCRlbtcYZNeZ+z06EphGS8mXR7L8s6kI5j2MLZEKSnylKdTZwo
Us8XRNADlEeySb/4fidHAoGBAJ/ShHE6PBqFGxlVSvmRfcmubU7S57ry7ciw9hdP
icnqO0hFmEP6LET8yxUqqXWXw4ngPanocrQpxb6zfSniG84dZ5L3EGqvbZp01wd3
Du6t+YTQFOdcdRsY+j7TUYVXujryLkJVnRV0N0RpPWwalWXFNsMWMZ3IBSniSDTR
LDiTAoGAMNs6uFGDjmDCwpJ+2q/7exs3ZtTdjgwb0kOs5Dy+oVTOMkPYGaNTazQX
DDwIIlc3PpiqoYj1YDCUBDU5j7GU7mtcIJb3wJjcagypppR0DMApAgYHQUCzWp17
bzlIE6OO0K4/kbXVbvjkCFe3tyfxnvQTt1Hp0CyA0qsgGfwuQUc=
-----END RSA PRIVATE KEY-----
EOD;

        $publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIIBITANBgkqhkiG9w0BAQEFAAOCAQ4AMIIBCQKCAQB16HIHTWGIQ+pE/treHHPq
Axtqscaf19AuYrVxSLaumuuBvvpWiBkhPHctqb5Sg3+bV5U3dN8JZD5TF3Usqq4s
zhMBUX6Ltj3qXP2Shkd8o7ZvgmgKv7q/m7Hns5kUqKWAArtqJr7bA0/tGbKGUgKT
mwgfHuzNyXEgbA5YXH8yEi98qU/olhVL6VMzz02Shd4M1f5pJnzYxo2wkZHI9MDx
DNF0oaCt9jxYxFlw4C3roSTglgZ4kjRQ0Vmjsl6VmuaUtJ4XxCLgvYzMeGsOSeZn
WMRH+YCt7aYU86d9GijTH4r2IhV83tOgHZ8hcD69fyd+TWbc0n2Sl9vDYGmLcWZp
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
