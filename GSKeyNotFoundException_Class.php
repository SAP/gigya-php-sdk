<?php
/**
 * Gigya Socialize Key Not Found Exception
 */
require_once('GSException_Class.php');
require_once('GSRequest_Class.php');
require_once('GSResponse_Class.php');
require_once('GSObject_Class.php');
require_once('GSArray_Class.php');
require_once('SigUtils_Class.php');
class GSKeyNotFoundException extends GSException
{
    public function __construct($key)
    {
        $this->errorMessage = "GSObject does not contain a value for key " . $key;
    }
}