<?php
require_once('GSException_Class.php');
require_once('GSKeyNotFoundException_Class.php');
require_once('GSRequest_Class.php');
require_once('GSResponse_Class.php');
require_once('GSObject_Class.php');
require_once('SigUtils_Class.php');
class GSArray
{
    private $map;
    const NO_INDEX_EX = "GSArray does not contain a value at index ";
    public function __construct($value = null)
    {
        $this->map = array();
        if (!empty($value)) {
            $obj = $value;
            //parse json string.
            if (gettype($value) == 'string') {
                $obj = json_decode($value, false);
                if ($obj == null) {
                    throw new GSException();
                }
            }
            $this->processJsonObject($obj, $this);
        }
    }
    private static function processJsonObject($value, $gsarr)
    {
        if (!empty($value)) {
            foreach ($value as $val) {
                if ($val == null) {
                    $gsarr->add($val);
                } elseif (is_object($val)) {
                    $gsobj = new GSObject($val);
                    $gsarr->add($gsobj);
                } else if (is_array(($val))) {
                    $newGsarr = new GSArray($val);
                    $gsarr->add($newGsarr);
                } else {
                    $gsarr->add($val);
                }
            }
        }
    }
    public function add($value)
    {
        array_push($this->map, $value);
    }
    public function getString($inx)
    {
        $obj = $this->map[$inx];
        if ($obj === null)
            return null;
        else
            return strval($obj);
    }
    public function getBool($inx)
    {
        $obj = $this->map[$inx];
        if ($obj === null)
            throw new Exception(GSArray::NO_INDEX_EX + $inx);
        if (is_bool($obj)) {
            return (Boolean)$obj;
        } else {
            $val = strtolower(strval($obj));
            return $val == "true" || $val == "1";
        }
    }
    public function getInt($inx)
    {
        $obj = $this->map[$inx];
        if ($obj === null)
            throw new Exception(GSArray::NO_INDEX_EX + $inx);
        if (is_int($obj)) {
            return (int)$obj;
        } else {
            return intval($this->getString($inx));
        }
    }
    public function getLong($inx)
    {
        $obj = $this->map[$inx];
        if ($obj === null)
            throw new Exception(GSArray::NO_INDEX_EX + $inx);
        if (is_float($obj)) {
            return (float)$obj;
        } else {
            return floatval($this->getString($inx));
        }
    }
    public function getDouble($inx)
    {
        $obj = $this->map[$inx];
        if ($obj === null)
            throw new Exception(GSArray::NO_INDEX_EX + $inx);
        if (is_double($obj)) {
            return (double)$obj;
        } else {
            return doubleval($this->getString($inx));
        }
    }
    public function getObject($inx)
    {
        return $this->map[$inx];
    }
    public function getArray($inx)
    {
        return $this->map[$inx];
    }
    public function length()
    {
        return sizeof($this->map);
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
    public function serialize()
    {
        $arr = Array();
        if (empty($this->map)) return $arr;
        $arr = GSArray::serializeGSArray($this);
        return $arr;
    }
    public static function serializeGSArray($gsarr)
    {
        $arr = Array();
        for ($i = 0; $i < $gsarr->length(); $i++) {
            $val = $gsarr->getObject($i);
            $val = GSObject::serializeValue($val);
            array_push($arr, $val);
        }
        return $arr;
    }
}