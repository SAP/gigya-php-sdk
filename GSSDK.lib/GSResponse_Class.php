<?php
/**
 * Wraps the server's response.
 * If the request was sent with the format set to "xml", the getData() will return null and you should use getResponseText() instead.
 * We only parse response text into GSObject if request format is set "json" which is the default.
 */
require_once('GSException_Class.php');
require_once('GSKeyNotFoundException_Class.php');
require_once('GSRequest_Class.php');
require_once('GSObject_Class.php');
require_once('GSArray_Class.php');
require_once('SigUtils_Class.php');
class GSResponse
{
    private $errorCode = 0;
    private $errorMessage = null;
    private $rawData = "";
    private $data; //GSObject
    private static $errorMsgDic;
    private $params = null;
    private $method = null;
    private $traceLog = null;
    public static function Init()
    {
        self::$errorMsgDic = new GSObject();
        self::$errorMsgDic->put(400002, "Required parameter is missing");
        self::$errorMsgDic->put(400003, "You must set a certificate for HTTPS requests");
        self::$errorMsgDic->put(500000, "General server error");
    }
    public function getErrorCode()
    {
        return $this->errorCode;
    }
    public function getErrorMessage()
    {
        if (isset($this->errorMessage))
            return $this->errorMessage;
        else {
            if ($this->errorCode == 0 || !self::$errorMsgDic->containsKey((int)$this->errorCode))
                return "";
            else
                return self::$errorMsgDic->getString($this->errorCode);
        }
    }
    public function getResponseText()
    {
        return $this->rawData;
    }
    public function getData()
    {
        return $this->data;
    }
    /* GET BOOLEAN */
    public function getBool($key, $defaultValue = GSObject::DEFAULT_VALUE)
    {
        return $this->data->getBool($key, $defaultValue);
    }
    /* GET INTEGER */
    public function getInt($key, $defaultValue = GSObject::DEFAULT_VALUE)
    {
        return $this->data->getInt($key, $defaultValue);
    }
    /* GET LONG */
    public function getLong($key, $defaultValue = GSObject::DEFAULT_VALUE)
    {
        return $this->data->getLong($key, $defaultValue);
    }
    /* GET INTEGER */
    public function getDouble($key, $defaultValue = GSObject::DEFAULT_VALUE)
    {
        return $this->data->getDouble($key, $defaultValue);
    }
    /* GET STRING */
    public function getString($key, $defaultValue = GSObject::DEFAULT_VALUE)
    {
        return $this->data->getString($key, $defaultValue);
    }
    /* GET GSOBJECT */
    public function getObject($key)
    {
        return $this->data->getObject($key);
    }
    /* GET GSOBJECT[] */
    public function getArray($key)
    {
        return $this->data->getArray($key);
    }
    /* C'tor */
    public function __construct($method, $responseText = null, $params = null, $errorCode = null, $errorMessage = null, $traceLog = null)
    {
        $this->traceLog = $traceLog;
        $this->method = $method;
        if (empty($params))
            $this->params = new GSObject();
        else
            $this->params = $params;
        if (!empty($responseText)) {
            $this->rawData = $responseText;
            if (strpos(ltrim($responseText), "{") !== false) {
                $this->data = new GSObject($responseText);
                if (isset($this->data)) {
                    if ($this->data->containsKey("errorCode")) {
                        $this->errorCode = $this->data->getInt("errorCode");
                    }
                    if ($this->data->containsKey("errorMessage")) {
                        $this->errorMessage = $this->data->getString("errorMessage");
                    }
                }
            } else {
                $matches = array();
                preg_match("~<errorCode\s*>([^<]+)~", $this->rawData, $matches);
                if (sizeof($matches) > 0) {
                    $errCodeStr = $matches[1];
                    if ($errCodeStr != null) {
                        $this->errorCode = (int)$errCodeStr;
                        $matches = array();
                        preg_match("~<errorMessage\s*>([^<]+)~", $this->rawData, $matches);
                        if (sizeof($matches) > 0) {
                            $this->errorMessage = $matches[1];
                        }
                    }
                }
            }
        } else {
            $this->errorCode = $errorCode;
            $this->errorMessage = $errorMessage != null ? $errorMessage : self::getErrorMessage();
            $this->populateClientResponseText();
        }
    }
    private function populateClientResponseText()
    {
        if ($this->params->getString("format", "json")) {
            $this->rawData = "{errorCode:" . $this->errorCode . ",errorMessage:\"" . $this->errorMessage . "\"}";
        } else {
            $sb = array(
                "<?xml version=\"1.0\" encoding=\"utf-8\"?>"
            , "<" . $this->method . "Response xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"urn:com:gigya:api http://socialize-api.gigya.com/schema\" xmlns=\"urn:com:gigya:api\">"
            , "<errorCode>" . $this->errorCode . "</errorCode>"
            , "<errorMessage>" . $this->errorMessage . "</errorMessager>"
            , "</" . $this->method . "Response>"
            );
            $this->rawData = implode("\r\n", $sb);
        }
    }
    public function getLog()
    {
        return implode("\r\n", $this->traceLog);
    }
    public function __toString()
    {
        $sb = "";
        $sb .= "\terrorCode:";
        $sb .= $this->errorCode;
        $sb .= "\n\terrorMessage:";
        $sb .= $this->errorMessage;
        $sb .= "\n\tdata:";
        $sb .= $this->data;
        return $sb;
    }
}
GSResponse::Init();