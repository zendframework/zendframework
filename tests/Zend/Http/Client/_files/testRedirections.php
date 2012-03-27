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

if (! isset($_GET['redirection'])){
    $_GET['redirection'] = 0;

    /**
     * Create session cookie, but only on first redirect
     */
    setcookie('zf2testSessionCookie','positive');

    /**
     * Create a long living cookie
     */
    setcookie('zf2testLongLivedCookie','positive',time()+2678400);

    /**
     * Create a cookie that should be invalid on arrival
     */
    setcookie('zf2testExpiredCookie','negative',time()-2400);
}

$_GET['redirection']++;
$https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';

if (!isset($_GET['redirection']) || $_GET['redirection'] < 4) {
    $target = 'http' . ($https ? 's://' : '://')  . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    header('Location: ' . $target . '?redirection=' . $_GET['redirection']);
} else {
    var_dump($_GET);
    var_dump($_POST);
}
