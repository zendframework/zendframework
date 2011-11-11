<?php

set_time_limit(0);

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Bootstrap.php';

use Zend\Version;
use Zend\Service\LiveDocx\MailMerge;
use Zend\Service\LiveDocx\Helper;

// -----------------------------------------------------------------------------

define('TEST_PASS',         'PASS');
define('TEST_FAIL',         'FAIL');

define('MIN_PHP_VERSION',   '5.3');
define('MIN_ZF_VERSION',    '2.0.0dev1');

define('GEOIP_SERVICE_URI', 'http://api.ipinfodb.com/v2/ip_query.php?key=332bde528d94fe578455e18ad225a01cba8dd359ee915ee46b70ca5e67137252');

// -----------------------------------------------------------------------------

$failed  = false;
$counter = 1;

// -----------------------------------------------------------------------------

echo PHP_EOL;

Helper::printLine('Environment Checker for Zend Framework LiveDocx Component' . PHP_EOL . PHP_EOL);

Helper::printLine('If requested by technical support, please send the entire output of this script to the support engineers. The information contained within is vital to debug your environment. Thank you.' . PHP_EOL . PHP_EOL);

// -----------------------------------------------------------------------------

Helper::printLineToc($counter, sprintf('Checking OS (%s)', PHP_OS), TEST_PASS);

$counter++;

// -----------------------------------------------------------------------------

if (1 === version_compare(PHP_VERSION, MIN_PHP_VERSION)) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

Helper::printLineToc($counter, sprintf('Checking PHP version (%s)', PHP_VERSION), $result);

$counter++;

// -----------------------------------------------------------------------------

Helper::printLineToc($counter, sprintf('Checking memory limit (%s)', ini_get('memory_limit')), TEST_PASS);

$counter++;

// -----------------------------------------------------------------------------

if (in_array('http', stream_get_wrappers())) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

Helper::printLineToc($counter, 'Checking HTTP stream wrapper', $result);

$counter++;

// -----------------------------------------------------------------------------

if (in_array('https', stream_get_wrappers())) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

Helper::printLineToc($counter, 'Checking HTTPS stream wrapper', $result);

$counter++;

// -----------------------------------------------------------------------------

if (true === method_exists('\Zend\Debug', 'dump')) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

Helper::printLineToc($counter, 'Checking Zend Framework path', $result);

$counter++;

// -----------------------------------------------------------------------------

if (1 === Version::compareVersion(PHP_VERSION, MIN_PHP_VERSION)) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

Helper::printLineToc($counter, sprintf('Checking Zend Framework version (%s)', Version::VERSION), $result);

$counter++;

// -----------------------------------------------------------------------------

if (extension_loaded('openssl')) {
    $version = OPENSSL_VERSION_TEXT;
    $result  = TEST_PASS;
} else {
    $version = 'N/A';
    $result  = TEST_FAIL;
    $failed  = true;
}

Helper::printLineToc($counter, sprintf('Checking OpenSSL extension (%s)', $version), $result);

$counter++;

unset($version);

// -----------------------------------------------------------------------------

if (extension_loaded('soap')) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

Helper::printLineToc($counter, 'Checking SOAP extension', $result);

$counter++;

// -----------------------------------------------------------------------------

if (extension_loaded('dom')) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

Helper::printLineToc($counter, 'Checking DOM extension', $result);

$counter++;

// -----------------------------------------------------------------------------

if (extension_loaded('simplexml')) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

Helper::printLineToc($counter, 'Checking SimpleXML extension', $result);

$counter++;

// -----------------------------------------------------------------------------

if (extension_loaded('libxml')) {
    $version = LIBXML_DOTTED_VERSION;
    $result  = TEST_PASS;
} else {
    $version = 'N/A';
    $result  = TEST_FAIL;
    $failed  = true;
}

Helper::printLineToc($counter, sprintf('Checking libXML extension (%s)', $version), $result);

$counter++;

unset($version);

// -----------------------------------------------------------------------------

$results = @file_get_contents(GEOIP_SERVICE_URI);

if (false != $results) {
    $keys = array(
                 'Ip' => 'IP address',
               'City' => 'city',
         'RegionName' => 'region',
        'CountryName' => 'country'
    );
    $simplexml = new SimpleXMLElement($results);
    foreach ($keys as $key => $value) {
        Helper::printLineToc($counter, sprintf('Checking your %s (%s)', $keys[$key], $simplexml->$key), TEST_PASS);
        $counter++;
    }
} else {
    Helper::printLineToc($counter, 'Checking your geo data', TEST_FAIL);
    $failed = true;
}

// -----------------------------------------------------------------------------

$microtime = microtime(true);

$results = @file_get_contents(MailMerge::WSDL);

if (false != $results) {
    $duration = microtime(true) - $microtime;
    $result   = TEST_PASS;
} else {
    $duration = -1;
    $result = TEST_FAIL;
    $failed = true;
}

Helper::printLineToc($counter, sprintf('Checking backend WSDL (%01.2fs)', $duration), $result);

$counter++;

// -----------------------------------------------------------------------------

if (defined('DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME') &&
        defined('DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD')) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

Helper::printLineToc($counter, 'Checking backend credentials are defined', $result);

$counter++;

// -----------------------------------------------------------------------------

$errorMessage = null;

try {
    $microtime = microtime(true);

    $mailMerge = new MailMerge();
    $mailMerge->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME)
              ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD)
              ->listTemplates();
    $duration = microtime(true) - $microtime;
    unset($mailMerge);
} catch (Exception $e) {
    $duration = -1;
    $errorMessage = $e->getMessage();
}

if (is_null($errorMessage)) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

Helper::printLineToc($counter, sprintf('Logging into backend service (%01.2fs)', $duration), $result);

$counter++;

// -----------------------------------------------------------------------------

if (defined('DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_WSDL') &&
        false !== DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_WSDL) {

    $microtime = microtime(true);

    $results = @file_get_contents(DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_WSDL);

    if (false != $results) {
        $duration = microtime(true) - $microtime;
        $result   = TEST_PASS;
    } else {
        $duration = -1;
        $result   = TEST_FAIL;
        $failed   = true;
    }

    Helper::printLineToc($counter, sprintf('[PREMIUM] Checking backend WSDL (%01.2fs)', $duration), $result);

    $counter++;
}

// -----------------------------------------------------------------------------

if (defined('DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_USERNAME')     &&
        defined('DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_PASSWORD') &&
        defined('DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_WSDL')     &&
        false !== DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_USERNAME  &&
        false !== DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_PASSWORD  &&
        false !== DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_WSDL) {

    $errorMessage = null;

    try {
        $microtime = microtime(true);
        $mailMerge = new MailMerge();
        $mailMerge->setWsdl(DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_WSDL)
                  ->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_USERNAME)
                  ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_PASSWORD)
                  ->listTemplates();
        $duration = microtime(true) - $microtime;
        unset($mailMerge);
    } catch (Exception $e) {
        $duration = -1;
        $errorMessage = $e->getMessage();
    }

    if (is_null($errorMessage)) {
        $result = TEST_PASS;
    } else {
        $result = TEST_FAIL;
        $failed = true;
    }

    Helper::printLineToc($counter, sprintf('[PREMIUM] Logging into backend service (%01.2fs)', $duration), $result);

    $counter++;
}

// -----------------------------------------------------------------------------

if (true === $failed) {
    $message = 'One or more tests failed. The web server environment, in which this script is running, does not meet the requirements for the Zend Framework LiveDocx component.';
} else {
    $message = 'Congratulations! All tests passed. The server environment, in which this script is running, is suitable for the Zend Framework LiveDocx component.';
}

Helper::printLine(PHP_EOL . $message . PHP_EOL . PHP_EOL);

// -----------------------------------------------------------------------------