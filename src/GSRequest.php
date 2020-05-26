<?php

namespace Gigya\PHP;

use Exception;

/**
 * A Request to Gigya Socialize API
 */
class GSRequest
{
    private static $cafile;

    const DEFAULT_API_DOMAIN = "us1.gigya.com";
    const version = "3.0";

    private $host;
    private $domain;
    private $path;
    private $traceLog = array();
    protected $method;
    private $proxy;
    private $proxyType = CURLPROXY_HTTP;
    private $proxyUserPass = ":";
    private $curlArray = array();
    private $apiKey;
    private $userKey;
    private $secretKey;
    private $privateKey;
    private $params; // GSObject
    private $useHTTPS;
    private $apiDomain = self::DEFAULT_API_DOMAIN;

    static function __constructStatic()
    {
        GSRequest::$cafile = realpath(dirname(__FILE__).'/cacert.pem');
    }

    /**
     * Constructs a request using an apiKey and secretKey, or apiKey and userKey/secretKey combination.
     *
     * @param string   $apiKey
     * @param string   $secretKey
     * @param string   $apiMethod  The API method (including namespace) to call. for
     *                             example: accounts.getAccountInfo
     * @param GSObject $params     The request parameters
     * @param boolean  $useHTTPS   Set this to false if you DO NOT want to use HTTPS
     * @param string   $userKey    A key of an admin user with extra permissions.
     *                             If this parameter is provided, then the secretKey parameter is assumed
     *                             to be the admin user's secret key and not the site's secret key.
     * @param string   $privateKey A private key generated at Gigya for authentication
     *                             A userKey/privateKey authentication can be used in the place of userKey/secretKey authentication
     *
     * @throws GSKeyNotFoundException
     */
    public function __construct($apiKey, $secretKey, $apiMethod, $params = null, $useHTTPS = true, $userKey = null, $privateKey = null)
    {
        if (!isset($apiMethod) || strlen($apiMethod) == 0)
            return;
        if (substr($apiMethod, 0, 1) == "/")
            $apiMethod = substr($apiMethod, 1);
        if (strrpos($apiMethod, ".") == 0) {
            $this->domain = "socialize.gigya.com";
            $this->path = "/socialize." . $apiMethod;
        } else {
            $tokens = explode(".", $apiMethod);
            $this->domain = $tokens[0] . ".gigya.com";
            $this->path = "/" . $apiMethod;
        }
        $this->method = $apiMethod;
        if (empty($params)) {
            $this->params = new GSObject();
        } else {
            $this->params = clone $params;
        }
        // use "_host" to override domain, if available
        $this->domain = $this->params->getString("_host", $this->domain);
        $this->useHTTPS = $useHTTPS;
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
        $this->userKey = $userKey;
        $this->privateKey = $privateKey;
        $this->traceField("apiMethod", $apiMethod);
        $this->traceField("apiKey", $apiKey);
    }

    public function setParam($param, $val)
    {
        $this->params->put($param, $val);
    }
    public function getParams()
    {
        return $this->params;
    }

	/**
	 * Sets the domain used for making API calls. This method provides the option to override the default domain "gigya.com" and specify an alternative data center to be used.
	 *
	 * @param string $apiDomain the domain of the data center to be used. For example: "eu1.gigya.com" for Europe data center.
	 */
    public function setAPIDomain($apiDomain)
    {
        if (!isset($apiDomain) || strlen($apiDomain) == 0)
            $this->apiDomain = self::DEFAULT_API_DOMAIN;
        else
            $this->apiDomain = $apiDomain;
    }
    public static function setCAFile($filename)
    {
        GSRequest::$cafile = $filename;
    }
    public function setProxy($proxy, $proxyUserPass = ":", $proxyType = CURLPROXY_HTTP)
    {
        $this->proxy = $proxy;
        $this->proxyType = $proxyType;
        $this->proxyUserPass = $proxyUserPass;
        $this->traceField("proxy", $proxy);
        $this->traceField("proxyType", $proxyType);
        $this->traceField("proxyUserPass", $proxyUserPass);
    }
    public function setCurlOptionsArray($curlArray)
    {
        $this->curlArray = $curlArray;
        $this->traceField("curlArray", $curlArray);
    }

	/**
	 * Send the request synchronously
	 *
	 * @param int $timeout
	 *
	 * @return GSResponse
     *
	 * @throws GSKeyNotFoundException
	 */
    public function send($timeout = null)
    {
        $format = $this->params->getString("format", null);
        if (!strrpos($this->method, ".")) {
            $this->host = "socialize." . $this->apiDomain;
            $this->path = "/socialize." . $this->method;
        } else {
            $tokens = explode(".", $this->method);
            $this->host = $tokens[0] . "." . $this->apiDomain;
            $this->path = "/" . $this->method;
        }
        //set json as default format.
        if (empty($format)) {
            $format = "json";
            $this->setParam("format", $format);
        }
        if (!empty($timeout)) {
            $this->traceField("timeout", $timeout);
        }
        if (empty($this->method) || (empty($this->apiKey) and empty($this->userKey))) {
            return new GSResponse($this->method, null, $this->params, 400002, null, $this->traceLog);
        }
        if ($this->useHTTPS && empty(GSRequest::$cafile)) {
            return new GSResponse($this->method, null, $this->params, 400003, null, $this->traceLog);
        }
        try {
            $this->setParam("httpStatusCodes", "false");

            $this->traceField("userKey", $this->userKey);
            $this->traceField("apiKey", $this->apiKey);
            $this->traceField("apiMethod", $this->method);
            $this->traceField("params", $this->params);
            $this->traceField("useHTTPS", $this->useHTTPS);

            $responseStr = $this->sendRequest($this->host, $this->path, $this->params, $this->apiKey, $this->secretKey, $this->useHTTPS, $timeout, $this->userKey, $this->privateKey);

            return new GSResponse($this->method, $responseStr, null, 0, null, $this->traceLog);
        } catch (Exception $ex) {
            $errcode = 500000;
            $errMsg = $ex->getMessage();
            $length = strlen("Operation timed out");
            if ((substr($ex->getMessage(), 0, $length) === "Operation timed out")) {
                $errcode = 504002;
                $errMsg = "Request Timeout";
            }

            return new GSResponse($this->method, null, $this->params, $errcode, $errMsg, $this->traceLog);
        }
    }

    /**
     * @param string   $domain
     * @param string   $path
     * @param GSObject $params
     * @param string   $token
     * @param string   $secret
     * @param bool     $useHTTPS
     * @param int      $timeout
     * @param string   $userKey
     * @param string   $privateKey
     *
     * @return bool|string
     *
     * @throws GSKeyNotFoundException
     * @throws Exception
     */
    private function sendRequest($domain, $path, $params, $token, $secret, $useHTTPS = false, $timeout = null, $userKey = null, $privateKey = null)
    {
        $params->put("sdk", "php_" . GSRequest::version);
        //prepare query params
        $protocol = $useHTTPS || empty($secret) ? "https" : "http";
        $resourceURI = $protocol . "://" . $domain . $path;
        //UTC timestamp.
        $timestamp = (string)time();
        //timestamp in milliseconds
        $nonce = ((string)SigUtils::currentTimeMillis()) . rand();
        $httpMethod = "POST";

        $curlHeaders = array();
        if ($userKey) {
            if (empty($secret) && !empty($privateKey)) { /* Authentication with private key */
                $bearerToken = JWTUtils::getBearerToken($privateKey, $userKey, $nonce);
                $curlHeaders[] = 'Authorization: Bearer ' . $bearerToken;
            } else { /* Authentication with user key + secret key */
                $params->put("userKey", $userKey);
            }
        }

        if (!empty($secret) or !empty($privateKey)) {
            $params->put("apiKey", $token);
            $params->put("timestamp", $timestamp);

            if (!empty($secret)) {
                $params->put("nonce", $nonce);
                $signature = self::getOAuth1Signature($secret, $httpMethod, $resourceURI, $params);
                $params->put("sig", $signature);
            }
        } else {
            $params->put("oauth_token", $token);
        }

        /* Send the request to Gigya */
        return $this->curl($resourceURI, $params, $timeout, $curlHeaders);
	}

    /**
     * @param string   $url
     * @param GSObject $params
     * @param int      $timeout     Request timeout in milliseconds
     * @param array    $curlHeaders Custom HTTP headers to add to the call, if applicable
     *
     * @return bool|string
     *
     * @throws Exception
     */
    private function curl($url, $params, $timeout = null, $curlHeaders = array())
    {
        $postData = array();
        foreach ($params->getKeys() as $key) {
            $value = $params->getString($key);
            $postData[$key] = $value;
        }

        $qs = http_build_query($postData);
        $this->traceField("URL", $url);
        $this->traceField("postData", $qs);

        /* POST */
        $defaults = array(
            CURLOPT_URL => $url,
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 1,
            CURLOPT_POSTFIELDS => $qs,
            CURLOPT_HTTPHEADER => array(),
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_SSL_VERIFYPEER => TRUE,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CAINFO => GSRequest::$cafile,
            CURLOPT_PROXY => $this->proxy,
            CURLOPT_PROXYTYPE => $this->proxyType,
            CURLOPT_PROXYUSERPWD => $this->proxyUserPass,
            CURLOPT_TIMEOUT_MS => $timeout
        );
        $ch = curl_init();
        $mergedCurlArray = ($this->curlArray + $defaults); /* Merged overrides defaults with values from curlArray, if set */

        if (!empty($curlHeaders)) {
            $mergedCurlArray[CURLOPT_HTTPHEADER] = array_merge($mergedCurlArray[CURLOPT_HTTPHEADER], $curlHeaders);
        }

        curl_setopt_array($ch, $mergedCurlArray);
        if (!$result = curl_exec($ch)) {
            $err = curl_error($ch);
            throw new Exception($err);
        }
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        $header = trim(substr($result, 0, $header_size));
        $body = substr($result, $header_size);
        $curlHeaders = explode("\r\n", $header);
        foreach ($curlHeaders as $value) {
            $kvp = explode(":", $value);
            if ($kvp[0] == "x-server") {
                $this->traceField("server", $kvp[1]);
                break;
            }
        }

        return $body;
	}

	/**
	 * Converts a GSObject to a query string
	 *
	 * @param GSObject $params
	 *
	 * @return string
     *
	 * @throws GSKeyNotFoundException
	 */
	public static function buildQS($params)
    {
        $val = null;
        $ret = "";
        foreach ($params->getKeys() as $key) {
            $val = $params->getString($key);
            if (isset($val)) {
                $ret .= "$key=" . urlencode($val);
            }
            $ret .= '&';
        }
        $ret = rtrim($ret, "&");
        return $ret;
    }

    /**
     * @param $key
     * @param $httpMethod
     * @param $url
     * @param $requestParams
     *
     * @return string
     * @throws GSKeyNotFoundException
     */
    private static function getOAuth1Signature($key, $httpMethod, $url, $requestParams)
    {
        // Create the BaseString.
        $baseString = self::calcOAuth1BaseString($httpMethod, $url, $requestParams);
        return SigUtils::calcSignature($baseString, $key);
    }

    /**
     * @param          $httpMethod
     * @param string   $url
     * @param GSObject $requestParams
     *
     * @return string
     * @throws GSKeyNotFoundException
     */
    private static function calcOAuth1BaseString($httpMethod, $url, $requestParams)
    {
        $normalizedUrl = '';
        $u = parse_url($url);
        $protocol = strtolower($u['scheme']);
        if (array_key_exists('port', $u)) {
            $port = $u['port'];
        } else {
            $port = null;
        }
        $normalizedUrl .= $protocol . '://';
        $normalizedUrl .= strtolower($u['host']);
        if ($port && (($protocol == 'http' && $port != 80) || ($protocol == 'https' && $port != 443))) {
            $normalizedUrl .= ':' . $port;
        }
        $normalizedUrl .= $u['path'];

        // Create a sorted list of query parameters
        $amp = '';
        $queryString = '';
        $keys = $requestParams->getKeys();
        sort($keys);
        foreach ($keys as $key) {
            $value = $requestParams->getString($key);
            if ($value !== false && $value != '0' && empty($value)) {
                $value = '';
            }

            // Curl is sending 1 and 0 when the value is boolean.
            // So in order to create a valid signature we're changing false to 0 and true to 1.
            if ($value === false)
                $value = '0';
            if ($value === true)
                $value = '1';
            $queryString .= $amp . $key . '=' . self::UrlEncode($value);
            $amp = '&';
        }

        /* Construct the base string from the HTTP method, the URL and the parameters */
        return strtoupper($httpMethod) . '&' . self::UrlEncode($normalizedUrl) . '&' . self::UrlEncode($queryString);
    }

    public static function UrlEncode($value)
    {
        if ($value === false) {
            return $value;
        } else {
            return str_replace('%7E', '~', rawurlencode($value));
        }
    }

    private function traceField($name, $value)
    {
        array_push($this->traceLog, $name . "=" . print_r($value, true));
    }
}

GSRequest::__constructStatic();