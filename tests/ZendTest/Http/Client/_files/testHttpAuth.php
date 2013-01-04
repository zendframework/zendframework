<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Http
 */

$user = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null;
$pass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : null;
$guser = isset($_GET['user']) ? $_GET['user'] : null;
$gpass = isset($_GET['pass']) ? $_GET['pass'] : null;
$method = isset($_GET['method']) ? $_GET['method'] : 'Basic';

if (! $user || ! $pass || $user != $guser || $pass != $gpass) {
    header('WWW-Authenticate: ' . $method . ' realm="ZendTest"');
    header('HTTP/1.0 401 Unauthorized');
}

echo serialize($_GET), "\n", $user, "\n", $pass, "\n";
