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
 * @package    Zend_Session
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Session\Validator;

use Zend\Session\Validator as SessionValidator;

/**
 * @uses       Zend\Session\Validator
 * @category   Zend
 * @package    Zend_Session
 * @subpackage Validator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class RemoteAddr implements SessionValidator
{
    /**
     * Internal data.
     *
     * @var int
     */
    protected $data;

    /**
     * Whether to use proxy addresses or not.
     *
     * As default this setting is disabled - IP address is mostly needed to increase
     * security. HTTP_* are not reliable since can easily be spoofed. It can be enabled
     * just for more flexibility, but if user uses proxy to connect to trusted services
     * it's his/her own risk, only reliable field for IP address is $_SERVER['REMOTE_ADDR'].
     *
     * @var bool
     */
    protected static $useProxy = false;

    /**
     * Constructor - get the current user IP and store it in the session
     * as 'valid data'
     *
     * @return void
     */
    public function __construct($data = null)
    {
        if (empty($data)) {
            $data = $this->getIpAddress();
        }
        $this->data = $data;
    }

    /**
     * isValid() - this method will determine if the current user IP matches the
     * IP we stored when we initialized this variable.
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->getIpAddress() === $this->getData();
    }

    /**
     * Changes proxy handling setting.
     *
     * This must be static method, since validators are recovered automatically
     * at session read, so this is the only way to switch setting.
     *
     * @param bool  $useProxy Whether to check also proxied IP addresses.
     * @return void
     */
    public static function setUseProxy($useProxy = true)
    {
        self::$useProxy = $useProxy;
    }

    /**
     * Checks proxy handling setting.
     *
     * @return bool Current setting value.
     */
    public static function getUseProxy()
    {
        return self::$useProxy;
    }

    /**
     * Returns client IP address.
     *
     * @return string IP address.
     */
    protected function getIpAddress()
    {
        if (self::$useProxy) {
            // proxy IP address
            if (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP']) {
                $ips = explode(',', $_SERVER['HTTP_CLIENT_IP']);
                return trim($ips[0]);
            }

            // proxy IP address
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
                $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                return trim($ips[0]);
            }
        }

        // direct IP address
        if (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }

        return '';
    }

    /**
     * Retrieve token for validating call
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Return validator name
     *
     * @return string
     */
    public function getName()
    {
        return __CLASS__;
    }
}
