<?php
namespace Gigya\PHP;

class SigUtils
{
    public static function validateUserSignature($UID, $timestamp, $secret, $signature)
    {
        $now = time();
        if (abs($now - $timestamp) > 180) {
            return false;
        }
        $baseString = $timestamp . "_" . $UID;
        $expectedSig = self::calcSignature($baseString, $secret);
        return $expectedSig == $signature;
    }

    public static function validateFriendSignature($UID, $timestamp, $friendUID, $secret, $signature)
    {
        $now = time();
        if (abs($now - $timestamp) > 180) {
            return false;
        }
        $baseString = $timestamp . "_" . $friendUID . "_" . $UID;
        $expectedSig = self::calcSignature($baseString, $secret);
        return $expectedSig == $signature;
    }

    /**
     * @return float
     */
    public static function currentTimeMillis()
    {
        // get utc time in ms
        return floor(microtime(true) * 1000);
    }

    public static function getDynamicSessionSignature($glt_cookie, $timeoutInSeconds, $secret)
    {
        // cookie format:
        // <expiration time in unix time format>_BASE64(HMACSHA1(secret key, <login token>_<expiration time in unix time format>))
        $expirationTimeUnix = (string)(time() + $timeoutInSeconds);
        $unsignedExpString = $glt_cookie . "_" . $expirationTimeUnix;
        $signedExpString = SigUtils::calcSignature($unsignedExpString, $secret); // sign the base string using the secret key

        return $expirationTimeUnix . '_' . $signedExpString;   // return the cookie value
    }

    public static function getDynamicSessionSignatureUserSigned($glt_cookie, $timeoutInSeconds, $userKey, $secret)
    {
        // cookie format:
        // <expiration time in unix time format>_<User Key>_BASE64(HMACSHA1(secret key, <login token>_<expiration time in unix time format>_<User Key>))
        $expirationTimeUnix = (string)(time() + $timeoutInSeconds);
        $unsignedExpString = $glt_cookie . "_" . $expirationTimeUnix . "_" . $userKey;
        $signedExpString = SigUtils::calcSignature($unsignedExpString, $secret); // sign the base string using the secret key

        return $expirationTimeUnix . "_" . $userKey . "_" . $signedExpString;   // return the cookie value
    }

    static function calcSignature($baseString, $key)
    {
        $baseString = utf8_encode($baseString);
        $rawHmac = hash_hmac("sha1", utf8_encode($baseString), base64_decode($key), true);

        return base64_encode($rawHmac);
    }
}