<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Http
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
