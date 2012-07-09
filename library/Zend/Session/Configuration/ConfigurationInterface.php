<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Session
 */

namespace Zend\Session\Configuration;

/**
 * Standard session configuration
 *
 * @category   Zend
 * @package    Zend_Session
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
