<?php
/**
 * Gigya Socialize Exception
 */
require_once('GSKeyNotFoundException_Class.php');
require_once('GSRequest_Class.php');
require_once('GSResponse_Class.php');
require_once('GSObject_Class.php');
require_once('GSArray_Class.php');
require_once('SigUtils_Class.php');
class GSException extends Exception
{
    public $errorMessage;
}