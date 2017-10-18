<?php
/*
 * Gigya PHP SDK
 *
 * version 2.16.1
 *
 *
 * http://developers.gigya.com/display/GD/PHP
 */
if (!function_exists('curl_init')) {
    throw new Exception('Gigya.Socialize needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
    throw new Exception('Gigya.Socialize needs the JSON PHP extension.');
}

require_once('GSException_Class.php');
require_once('GSKeyNotFoundException_Class.php');
require_once('GSRequest_Class.php');
require_once('GSResponse_Class.php');
require_once('GSObject_Class.php');
require_once('GSArray_Class.php');
require_once('SigUtils_Class.php');