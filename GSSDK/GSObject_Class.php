<?php
/**
 * Used for passing parameters when issueing requests e.g. GSRequest.send
 * As well as returning response data e.g. GSResponse.getData
 *
 * @version 1.0
 */
require_once('GSException_Class.php');
require_once('GSKeyNotFoundException_Class.php');
require_once('GSRequest_Class.php');
require_once('GSResponse_Class.php');
require_once('GSArray_Class.php');
require_once('SigUtils_Class.php');
class GSObject
{
    private $map;
    /* PUBLIC INTERFACE */
    /**
     * Construct a GSObject from json string, throws excpetion.
     *
     * @param string|object the json formatted string
     * @throws Exception if unable to parse json
     */
    public function __construct($json = null)
    {
        $this->map = array();
        if (!empty($json)) {
            //parse json string.
            if (gettype($json) == 'string') {
                $obj = json_decode($json, false);
                if ($obj == null) {
                    throw new GSException();
                }
            } else {
                $obj = $json;
            }
            self::processJsonObject($obj, $this);
        }
    }
    public function serialize()
    {
        $arr = Array();
        if (empty($this->map)) return $arr;
        $arr = $this->serializeGSObject($this);
        return $arr;
    }
    public static function serializeGSObject($gsd)
    {
        $arr = Array();
        foreach ($gsd->map as $name => $value) {
            $val = GSObject::serializeValue($value);
            $arr[$name] = $val;
        }
        return $arr;
    }
    public static function serializeValue($value)
    {
        //GSDictionary
        if ($value instanceof GSObject) {
            return GSObject::serializeGSObject($value);
        } //array
        else if ($value instanceof GSArray) {
            return GSArray::serializeGSArray($value);
        } //else just add
        else {
            return $value;
        }
    }
    /* Put */
    const DEFAULT_VALUE = '@@EMPTY@@';
    public function put($key, $value)
    {
        $this->map[$key] = $value;
    }
    private function get($key, $defaultValue)
    {
        if (array_key_exists($key, $this->map)) {
            return $this->map[$key];
        }
        if ($defaultValue !== GSObject::DEFAULT_VALUE) {
            return $defaultValue;
        }
        throw new GSKeyNotFoundException($key);
    }
    /* GET BOOLEAN */
    public function getBool($key, $defaultValue = GSObject::DEFAULT_VALUE)
    {
        return (bool)$this->get($key, $defaultValue);
    }
    /* GET INTEGER */
    public function getInt($key, $defaultValue = GSObject::DEFAULT_VALUE)
    {
        return (int)$this->get($key, $defaultValue);
    }
    /* GET LONG */
    public function getLong($key, $defaultValue = GSObject::DEFAULT_VALUE)
    {
        return (float)$this->get($key, $defaultValue);
    }
    /* GET DOUBLE */
    public function getDouble($key, $defaultValue = GSObject::DEFAULT_VALUE)
    {
        return (double)$this->get($key, $defaultValue);
    }
    /* GET STRING */
    public function getString($key, $defaultValue = GSObject::DEFAULT_VALUE)
    {
        $obj = $this->get($key, $defaultValue);
        return (string)$obj;
    }
    /* GET GSOBJECT */
    public function getObject($key)
    {
        return (object)$this->get($key, null);
    }
    /* GET GSOBJECT[] */
    public function getArray($key)
    {
        return $this->get($key, null);
    }
    /**
     * Parse parameters from URL into the dictionary
     *
     * @param url
     */
    public function parseURL($url)
    {
        try {
            $u = parse_url($url);
            if (isset($u["query"]))
                $this->parseQueryString($u["query"]);
            if (isset($u["fragment"]))
                $this->parseQueryString($u["fragment"]);
        } catch (Exception $e) {
        }
    }
    /**
     * Parse parameters from query string
     *
     * @param qs
     */
    public function parseQueryString($qs)
    {
        if (!isset($qs)) return;
        parse_str($qs, $this->map);
    }
    public function containsKey($key)
    {
        return array_key_exists($key, $this->map);
    }
    public function remove($key)
    {
        unset($this->map[$key]);
    }
    public function clear()
    {
        unset($this->map);
        $this->map = array();
    }
    public function getKeys()
    {
        return array_keys($this->map);
    }
    public function __toString()
    {
        return $this->toJsonString();
    }
    public function toString()
    {
        return $this->toJsonString();
    }
    public function toJsonString()
    {
        try {
            return json_encode($this->serialize());
        } catch (Exception $e) {
            return null;
        }
    }
    private static function processJsonObject($jo, $parentObj)
    {
        if (!empty($jo))
            foreach ($jo as $name => $value) {
                //array
                if (is_array(($value))) {
                    $parentObj->put($name, new GSArray($value));
                } //object
                elseif (is_object($value)) {
                    $childObj = new GSObject();
                    $parentObj->put($name, $childObj);
                    self::processJsonObject($value, $childObj);
                } //primitive
                else {
                    $parentObj->put($name, $value);
                }
            }
        return $parentObj;
    }
}