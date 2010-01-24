#!/usr/bin/php
<?php

include_once './common.php';

define('TEST_PASS',       'PASS');
define('TEST_FAIL',       'FAIL');  

define('MIN_PHP_VERSION', '5.2.4');
define('MIN_ZF_VERSION',  '1.8.0');

define('SOCKET_TIMEOUT',   5); // seconds

$failed  = false;
$counter = 1;

// -----------------------------------------------------------------------------

ini_set('default_socket_timeout', SOCKET_TIMEOUT);

printf('%sZend_Service_LiveDocx Environment Checker%s%s', PHP_EOL, PHP_EOL, PHP_EOL);

// -----------------------------------------------------------------------------

printLine($counter, sprintf('Checking OS (%s)', PHP_OS), TEST_PASS);

$counter ++;

// -----------------------------------------------------------------------------

if ('cli' === strtolower(PHP_SAPI)) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

printLine($counter, sprintf('Checking SAPI (%s)', PHP_SAPI), $result);

$counter ++;

// -----------------------------------------------------------------------------

if (1 === version_compare(PHP_VERSION, MIN_PHP_VERSION)) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

printLine($counter, sprintf('Checking PHP version (%s)', PHP_VERSION), $result);

$counter ++;

// -----------------------------------------------------------------------------

printLine($counter, sprintf('Checking memory limit (%s)', ini_get('memory_limit')), TEST_PASS);

$counter ++;

// -----------------------------------------------------------------------------

if (in_array('http', stream_get_wrappers())) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

printLine($counter, 'Checking HTTP stream wrapper', $result);

$counter ++;

// -----------------------------------------------------------------------------

if (in_array('https', stream_get_wrappers())) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

printLine($counter, 'Checking HTTPS stream wrapper', $result);

$counter ++;

// -----------------------------------------------------------------------------

if (true === method_exists('Zend_Debug', 'dump')) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

printLine($counter, 'Checking Zend Framework path', $result);

$counter ++;

// -----------------------------------------------------------------------------

if (1 === version_compare(Zend_Version::VERSION, MIN_ZF_VERSION)) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

printLine($counter, sprintf('Checking Zend Framework version (%s)', Zend_Version::VERSION), $result);

$counter ++;

// -----------------------------------------------------------------------------

if (extension_loaded('soap')) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

printLine($counter, 'Checking SOAP extension', $result);

$counter ++;

// -----------------------------------------------------------------------------

if (extension_loaded('dom')) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

printLine($counter, 'Checking DOM extension', $result);

$counter ++;

// -----------------------------------------------------------------------------

if (extension_loaded('simplexml')) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

printLine($counter, 'Checking SimpleXML extension', $result);

$counter ++;

// -----------------------------------------------------------------------------

if (extension_loaded('libxml')) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

printLine($counter, 'Checking libXML extension', $result);

$counter ++;

// -----------------------------------------------------------------------------

$geoData = @file_get_contents('http://ipinfodb.com/ip_query.php');

$keys = array (
    'Ip'          => 'IP address',
    'City'        => 'city',
    'RegionName'  => 'region',
    'CountryName' => 'country'
);

if (false !== $geoData) {
    $simplexml = new SimpleXMLElement($geoData);
    foreach ($keys as $key => $value) {
        printLine($counter, sprintf('Checking your %s (%s)', $keys[$key], $simplexml->$key), TEST_PASS);
        $counter ++;
    }
} else {
    printLine($counter, 'Checking your geo data', TEST_FAIL);
    $failed = true;
}

// -----------------------------------------------------------------------------

$microtime = microtime(true);

if (false !== file_get_contents(Zend_Service_LiveDocx_MailMerge::WSDL)) {
    $duration = microtime(true) - $microtime;
    $result = TEST_PASS;
} else {
    $duration = -1;
    $result = TEST_FAIL;
    $failed = true;
}

printLine($counter, sprintf('Checking backend WSDL (%01.2fs)', $duration), $result);

$counter ++;

// -----------------------------------------------------------------------------

if (defined('DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME') &&
    defined('DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD')) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

printLine($counter, 'Checking backend credentials are defined', $result);

$counter ++;

// -----------------------------------------------------------------------------

$errorMessage = null;

try {
    $microtime = microtime(true);
    $phpLiveDocx = new Zend_Service_LiveDocx_MailMerge(
        array (
            'username' => DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME,
            'password' => DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD
        )
    );
    $duration = microtime(true) - $microtime;
} catch (Zend_Service_LiveDocx_Exception $e) {
    $duration = -1;
    $errorMessage = $e->getMessage();    
}

if (is_null($errorMessage)) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

printLine($counter, sprintf('Instantiating Zend_Service_LiveDocx_MailMerge object (%01.2fs)', $duration), $result);

$counter ++;

// -----------------------------------------------------------------------------

if (true === $failed) {
    $message = 'One or more tests failed. The web server environment, in which this script is running, does not meet the requirements for Zend_Service_LiveDocx_*.';
} else {
    $message = 'Congratulations! All tests passed. The server environment, in which this script is running, is suitable for Zend_Service_LiveDocx_*.';
}

print(Demos_Zend_Service_LiveDocx_Helper::wrapLine(PHP_EOL . $message . PHP_EOL . PHP_EOL));

// -----------------------------------------------------------------------------

/**
 * Print result line
 *
 * @param $counter
 * @param $testString
 * @param $testResult
 */
function printLine($counter, $testString, $testResult)
{
    $lineLength = Demos_Zend_Service_LiveDocx_Helper::LINE_LENGTH;
    
    //                        counter     result
    $padding = $lineLength - (4 + strlen(TEST_PASS));
    
    $counter    = sprintf('%2s: ', $counter);
    $testString = str_pad($testString, $padding, '.', STR_PAD_RIGHT);
    
    printf('%s%s%s%s', $counter, $testString, $testResult, PHP_EOL);
}

// -----------------------------------------------------------------------------
