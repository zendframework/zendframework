<?php

set_time_limit(0);

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Bootstrap.php';

use Zend\Version;
use Zend\Http\Client as HttpClient;
use Zend\Service\LiveDocx\MailMerge;
use Zend\Service\LiveDocx\Helper;

// -----------------------------------------------------------------------------

define('TEST_PASS',         'PASS');
define('TEST_FAIL',         'FAIL');

define('MIN_PHP_VERSION',   '5.3');
define('MIN_ZF_VERSION',    '2.0.0dev1');

define('GEOIP_SERVICE_URI', 'http://api.ipinfodb.com/v2/ip_query.php?key=332bde528d94fe578455e18ad225a01cba8dd359ee915ee46b70ca5e67137252');

// -----------------------------------------------------------------------------

$httpClientOptions = array(
    'maxredirects' => 3,
         'timeout' => 5,  // seconds
       'keepalive' => false
);

$httpClient = new HttpClient(null, $httpClientOptions);

// -----------------------------------------------------------------------------

$failed  = false;
$counter = 1;

// -----------------------------------------------------------------------------

printf('%sEnvironment Checker for Zend Framework LiveDocx Component%s%s', PHP_EOL, PHP_EOL, PHP_EOL);

// -----------------------------------------------------------------------------

Helper::printCheckEnvironmentLine($counter, sprintf('Checking OS (%s)', PHP_OS), TEST_PASS);

$counter++;

// -----------------------------------------------------------------------------

if (1 === version_compare(PHP_VERSION, MIN_PHP_VERSION)) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

Helper::printCheckEnvironmentLine($counter, sprintf('Checking PHP version (%s)', PHP_VERSION), $result);

$counter++;

// -----------------------------------------------------------------------------

Helper::printCheckEnvironmentLine($counter, sprintf('Checking memory limit (%s)', ini_get('memory_limit')), TEST_PASS);

$counter++;

// -----------------------------------------------------------------------------

if (in_array('http', stream_get_wrappers())) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

Helper::printCheckEnvironmentLine($counter, 'Checking HTTP stream wrapper', $result);

$counter++;

// -----------------------------------------------------------------------------

if (in_array('https', stream_get_wrappers())) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

Helper::printCheckEnvironmentLine($counter, 'Checking HTTPS stream wrapper', $result);

$counter++;

// -----------------------------------------------------------------------------

if (true === method_exists('\Zend\Debug', 'dump')) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

Helper::printCheckEnvironmentLine($counter, 'Checking Zend Framework path', $result);

$counter++;

// -----------------------------------------------------------------------------

if (1 === Version::compareVersion(PHP_VERSION, MIN_PHP_VERSION)) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

Helper::printCheckEnvironmentLine($counter, sprintf('Checking Zend Framework version (%s)', Version::VERSION), $result);

$counter++;

// -----------------------------------------------------------------------------

if (extension_loaded('openssl')) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

Helper::printCheckEnvironmentLine($counter, 'Checking OpenSSL extension', $result);

$counter++;

// -----------------------------------------------------------------------------

if (extension_loaded('soap')) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

Helper::printCheckEnvironmentLine($counter, 'Checking SOAP extension', $result);

$counter++;

// -----------------------------------------------------------------------------

if (extension_loaded('dom')) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

Helper::printCheckEnvironmentLine($counter, 'Checking DOM extension', $result);

$counter++;

// -----------------------------------------------------------------------------

if (extension_loaded('simplexml')) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

Helper::printCheckEnvironmentLine($counter, 'Checking SimpleXML extension', $result);

$counter++;

// -----------------------------------------------------------------------------

if (extension_loaded('libxml')) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

Helper::printCheckEnvironmentLine($counter, 'Checking libXML extension', $result);

$counter++;

// -----------------------------------------------------------------------------

$httpClient->setUri(GEOIP_SERVICE_URI);

$httpResponse = $httpClient->request();

if ($httpResponse->isSuccessful()) {

    $keys = array(
                 'Ip' => 'IP address',
               'City' => 'city',
         'RegionName' => 'region',
        'CountryName' => 'country'
    );

    $simplexml = new SimpleXMLElement($httpResponse->getBody());
    foreach ($keys as $key => $value) {
        Helper::printCheckEnvironmentLine($counter, sprintf('Checking your %s (%s)', $keys[$key], $simplexml->$key), TEST_PASS);
        $counter++;
    }
} else {
    Helper::printCheckEnvironmentLine($counter, 'Checking your geo data', TEST_FAIL);
    $failed = true;
}

// -----------------------------------------------------------------------------

$microtime = microtime(true);

$httpClient->setUri(MailMerge::WSDL);

if ($httpClient->request()->isSuccessful()) {
    $duration = microtime(true) - $microtime;
    $result   = TEST_PASS;
} else {
    $duration = -1;
    $result = TEST_FAIL;
    $failed = true;
}

Helper::printCheckEnvironmentLine($counter, sprintf('Checking backend WSDL (%01.2fs)', $duration), $result);

$counter++;

// -----------------------------------------------------------------------------

if (defined('DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME') &&
        defined('DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD')) {
    $result = TEST_PASS;
} else {
    $result = TEST_FAIL;
    $failed = true;
}

Helper::printCheckEnvironmentLine($counter, 'Checking backend credentials are defined', $result);

$counter++;

// -----------------------------------------------------------------------------

$errorMessage = null;

try {
    $microtime = microtime(true);

    $mailMerge = new MailMerge();
    $mailMerge->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME)
              ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD)
              ->logIn();
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

Helper::printCheckEnvironmentLine($counter, sprintf('Logging into backend service (%01.2fs)', $duration), $result);

$counter++;

// -----------------------------------------------------------------------------

if (defined('DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_WSDL') &&
        false !== DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_WSDL) {

    $microtime = microtime(true);

    $httpClient->setUri(DEMOS_ZEND_SERVICE_LIVEDOCX_PREMIUM_WSDL);

    if ($httpClient->request()->isSuccessful()) {
        $duration = microtime(true) - $microtime;
        $result   = TEST_PASS;
    } else {
        $duration = -1;
        $result   = TEST_FAIL;
        $failed   = true;
    }

    Helper::printCheckEnvironmentLine($counter, sprintf('[PREMIUM] Checking backend WSDL (%01.2fs)', $duration), $result);

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
                  ->logIn();
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

    Helper::printCheckEnvironmentLine($counter, sprintf('[PREMIUM] Logging into backend service (%01.2fs)', $duration), $result);

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