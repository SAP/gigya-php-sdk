<?php
/*
 * Gigya PHP SDK
 *
 * version 2.17.1
 *
 * http://developers.gigya.com/display/GD/PHP
 */
if (!function_exists('curl_init')) {
    throw new Exception('Gigya PHP SDK needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
    throw new Exception('Gigya PHP SDK needs the JSON PHP extension.');
}
require_once('GSSDK.lib/GSException_Class.php');
require_once('GSSDK.lib/GSKeyNotFoundException_Class.php');
require_once('GSSDK.lib/GSRequest_Class.php');
require_once('GSSDK.lib/GSResponse_Class.php');
require_once('GSSDK.lib/GSObject_Class.php');
require_once('GSSDK.lib/GSArray_Class.php');
require_once('GSSDK.lib/SigUtils_Class.php');