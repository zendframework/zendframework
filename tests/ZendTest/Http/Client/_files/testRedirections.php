<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Http
 */

if (! isset($_GET['redirection'])) {
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
