<?php

include __DIR__ . '/../library//Zend/Loader/AutoloaderFactory.php';
Zend\Loader\AutoloaderFactory::factory(array(
    'Zend\Loader\StandardAutoloader' => array(
        'autoregister_zf' => true
    )
));

use Zend\Http\ClientStatic;
use Zend\Validator\Hostname;

define('IANA_URL', 'https://data.iana.org/TLD/tlds-alpha-by-domain.txt', true);
define('ZF2_HOSTNAME_VALIDATOR_FILE', __DIR__.'/../library/Zend/Validator/Hostname.php', true);

if (!file_exists(ZF2_HOSTNAME_VALIDATOR_FILE) || !is_readable(ZF2_HOSTNAME_VALIDATOR_FILE)) {
    printf('Error: cannont read file "%s"'.PHP_EOL, ZF2_HOSTNAME_VALIDATOR_FILE);
    exit(1);
}

if (!is_writable(ZF2_HOSTNAME_VALIDATOR_FILE)) {
    printf('Error: Cannot update file "%s"'.PHP_EOL, ZF2_HOSTNAME_VALIDATOR_FILE);
    exit(1);
}

/** get online page of official TLDs **/
$response = ClientStatic::get(IANA_URL);
if (!$response->isSuccess()) {
    printf('Error: cannot get "%s"'.PHP_EOL, IANA_URL);
    exit(1);
}

/**
 * TLDs are puny encoded. We need a decodePunycode function to
 * translate TLDs ot UTF-8 :
 * - use idn_to_utf8 if available
 * - otherwise, use Zend\Validator\Hostname::decodePunycode()
 */
$decodePunycode = null;
if (function_exists('idn_to_utf8')) {
    $decodePunycode = 'idn_to_utf8';
}
if ($decodePunycode == null) {
    $hostnameValidator = new Hostname();
    $reflection = new \ReflectionClass(get_class($hostnameValidator));
    $method = $reflection->getMethod('decodePunycode');
    $method->setAccessible(true);

    $decodePunycode = function ($encode) use ($hostnameValidator, $method) {
        if (strpos($encode, 'xn--') === 0) {
            return $method->invokeArgs($hostnameValidator, array(substr($encode, 4)));
        }
        return $encode;
    };
}

/** Get new TLDs from the fetched page **/
$newValidTlds = array();
foreach (preg_grep('/^[^#]/', explode(PHP_EOL, $response->getBody())) as $line) {
    $newValidTlds []= str_repeat(' ', 8) ."'". call_user_func($decodePunycode, strtolower($line)) ."',".PHP_EOL;
}

/** Get file content **/
$fileLines = file(ZF2_HOSTNAME_VALIDATOR_FILE);

$newFileContent = array();  /** new file content **/
$insertDone = false;        /** become 'true' when we found $validTlds declaration **/
$insertFinish = false;      /** become 'true' when we found end of $validTlds declaration **/
foreach ($fileLines as $line) {
    /** outside of $validTlds definition, keep file lines **/
    if ($insertDone === $insertFinish) {
        $newFileContent []= $line;
    }
    if (!$insertFinish) {
        /** Find the line where $validTlds is initialized **/
        if (!$insertDone) {
            if (preg_match('/^\s+protected\s+\$validTlds\s+=\s+array\(\s*$/', $line)) {
                $newFileContent = array_merge($newFileContent, $newValidTlds);
                $insertDone = true;
            }
        } else {
            /** find end of $validTlds declaration **/
            if (preg_match('/^\s+\);\s*$/', $line)) {
                $newFileContent []= $line;
                $insertFinish = true;
            }
        }
    }
}

if (!$insertDone) {
    echo 'Error: cannot find line with "protected $validTlds"'.PHP_EOL;
    exit(1);
}

if (!$insertFinish) {
    echo 'Error: cannot find end of $validTlds declaration'.PHP_EOL;
    exit(1);
}

if (false === @file_put_contents(ZF2_HOSTNAME_VALIDATOR_FILE, $newFileContent)) {
    sprintf('Error: cannot write info file "%s"'.PHP_EOL, ZF2_HOSTNAME_VALIDATOR_FILE);
    exit(1);
}

echo 'Nice work done :-)'.PHP_EOL;
exit(0);
