<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-webat this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Session
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Session\Configuration;

/**
 * Standard session configuration
 *
 * @category   Zend
 * @package    Zend_Session
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface ConfigurationInterface
{
    public function setOptions(array $options);
    public function setOption($option, $value);
    public function hasOption($option);
    public function getOption($option);
    public function toArray();

    public function setSavePath($savePath);
    public function getSavePath();

    public function setName($name);
    public function getName();

    public function setCookieLifetime($cookieLifetime);
    public function getCookieLifetime();
    public function setCookiePath($cookiePath);
    public function getCookiePath();
    public function setCookieDomain($cookieDomain);
    public function getCookieDomain();
    public function setCookieSecure($cookieSecure);
    public function getCookieSecure();
    public function setCookieHttpOnly($cookieHttpOnly);
    public function getCookieHttpOnly();
    public function setUseCookies($useCookies);
    public function getUseCookies();
    public function setRememberMeSeconds($rememberMeSeconds);
    public function getRememberMeSeconds();
}
