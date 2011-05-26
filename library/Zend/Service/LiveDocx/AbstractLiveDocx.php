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
 * @package    Zend_Service
 * @subpackage LiveDocx
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Service\LiveDocx;


/**
 * @uses       \Zend\Config\Config
 * @category   Zend
 * @package    Zend_Service
 * @subpackage LiveDocx
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractLiveDocx
{
    /**
     * LiveDocx service version.
     * @since LiveDocx 1.0
     */
    const VERSION = '2.0';


    /**
     * SOAP client used to connect to LiveDocx service.
     * @var   \Zend\Soap\Client
     * @since LiveDocx 1.0
     */
    protected $_soapClient = null;

    /**
     * WSDL of LiveDocx service.
     * @var   string
     * @since LiveDocx 1.0
     */
    protected $_wsdl = null;

    /**
     * Array of credentials (username and password) to log into LiveDocx service.
     * @var   array
     * @since LiveDocx 1.2
     */
    protected $_credentials = array();

    /**
     * Status of connection to LiveDocx service.
     * When set to true, session is logged into LiveDocx service.
     * When set to false, session is not logged into LiveDocx service.
     * @var   boolean
     * @since LiveDocx 1.2
     */
    protected $_isLoggedIn = null;


    /**
     * Constructor.
     *
     * Optionally, pass an array of options (or \Zend\Config\Config object).
     *
     * @param  array | \Zend\Config\Config $options
     * @return void
     * @since  LiveDocx 1.0
     */
    public function __construct($options = null)
    {
        $this->_setIsLoggedIn(false);

        if ($options instanceof \Zend\Config\Config) {
            $options = $options->toArray();
        }

        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Clean up and log out of LiveDocx service.
     *
     * @return boolean
     * @since  LiveDocx 1.0
     */
    public function __destruct()
    {
        return $this->_logOut();
    }

    /**
     * Set options. Valid options are username, password and soapClient.
     * 
     * @param  $options
     * @throws \Zend\Service\LiveDocx\Exception\InvalidArgumentException
     * @return \Zend\Service\AbstractLiveDocx
     * @since  LiveDocx 1.2
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (!method_exists($this, $method)) {
                throw new Exception\InvalidArgumentException(
                    'Invalid option specified - ' . $key
                );
            }
            $this->$method($value);
        }

        return $this;
    }

    /**
     * Set SOAP client.
     *
     * @param  \Zend\Soap\Client $soapClient
     * @return \Zend\Service\AbstractLiveDocx
     * @since  LiveDocx 1.2
     */
    public function setSoapClient($soapClient)
    {
        $this->_soapClient = $soapClient;

        return $this;
    }

    /**
     * Get SOAP client.
     *
     * @return \Zend\Soap\Client
     * @since  LiveDocx 1.2
     */
    public function getSoapClient()
    {
        return $this->_soapClient;
    }

    /**
     * Instantiate SOAP client.
     *
     * @param  string $endpoint
     * @return void
     * @since  LiveDocx 1.2
     */
    protected function _initSoapClient($endpoint)
    {
        $this->_soapClient = new \Zend\Soap\Client();
        $this->_soapClient->setWsdl($endpoint);
    }

    /**
     * Set username.
     * 
     * @return \Zend\Service\AbstractLiveDocx
     * @since  LiveDocx 1.0
     */
    public function setUsername($username)
    {
        $this->_credentials['username'] = $username;

        return $this;
    }

    /**
     * Return username.
     *
     * @return string | null
     * @since  LiveDocx 1.0
     */
    public function getUsername()
    {
        if (isset($this->_credentials['username'])) {
            return $this->_credentials['username'];
        }

        return null;
    }

    /**
     * Set password.
     * 
     * @return \Zend\Service\AbstractLiveDocx
     * @since  LiveDocx 1.0
     */
    public function setPassword($password)
    {
        $this->_credentials['password'] = $password;

        return $this;
    }

    /**
     * Return password.
     *
     * @return string | null
     * @since  LiveDocx 1.0
     */
    public function getPassword()
    {
        if (isset($this->_credentials['password'])) {
            return $this->_credentials['password'];
        }

        return null;
    }

    /**
     * Set WSDL of LiveDocx service.
     * 
     * @return \Zend\Service\AbstractLiveDocx
     * @since  LiveDocx 1.0
     */
    public function setWsdl($wsdl)
    {
        $this->_wsdl = $wsdl;

        return $this;
    }

    /**
     * Return WSDL of LiveDocx service.
     *
     * @return \Zend\Service\AbstractLiveDocx
     * @since  LiveDocx 1.0
     */
    public function getWsdl()
    {
        if (null !== $this->getSoapClient()) {
            return $this->getSoapClient()->getWsdl();
        } else {
            return $this->_wsdl;
        }
    }

    /**
     * Return the document format (extension) of a filename.
     *
     * @param  string $filename
     * @return string
     * @since  LiveDocx 1.0
     */
    public function getFormat($filename)
    {
        return strtolower(substr(strrchr($filename, '.'), 1));
    }

    /**
     * Return the current API version.
     *
     * @return string
     * @since  LiveDocx 1.0
     */
    public function getVersion()
    {
        return self::VERSION;
    }

    /**
     * Compare the current API version with another version.
     *
     * @param  string $version (STRING NOT FLOAT).
     * @return int -1 (version is less than API version), 0 (versions are equal), or 1 (version is greater than API version).
     * @since  LiveDocx 1.0
     */
    public function compareVersion($version)
    {
        return version_compare($version, $this->getVersion());
    }

    // -------------------------------------------------------------------------

    /**
     * Return logged into LiveDocx service status.
     * (true = logged in, false = not logged in).
     *
     * @return boolean
     * @since  LiveDocx 1.2
     */
    protected function _getIsLoggedIn()
    {
        return $this->_isLoggedIn;
    }

    /**
     * Set logged into LiveDocx service status.
     * (true = logged in, false = not logged in).
     *
     * @throws \Zend\Service\LiveDocx\Exception\InvalidArgumentException
     * @return boolean
     * @since  LiveDocx 1.2
     */
    protected function _setIsLoggedIn($state)
    {
        if (!is_bool($state)) {
            throw new Exception\InvalidArgumentException(
                'Logged in status must be boolean.'
            );
        }

        $this->_isLoggedIn = $state;
    }

    // -------------------------------------------------------------------------

    /**
     * Log in to LiveDocx service.
     *
     * @param string $username
     * @param string $password
     * @throws \Zend\Service\LiveDocx\Exception\InvalidArgumentException
     * @throws \Zend\Service\LiveDocx\Exception\RuntimeException
     * @return boolean
     * @since  LiveDocx 1.2
     */
    protected function _logIn()
    {
        if (false === $this->_getIsLoggedIn()) {

            if (null === $this->getUsername()) {
                throw new Exception\InvalidArgumentException(
                    'Username has not been set. To set username specify the options array '
                  . 'in the constructor or call setUsername($username) after instantiation.'
                );
            }

            if (null === $this->getPassword()) {
                throw new Exception\InvalidArgumentException(
                    'Password has not been set. To set password specify the options array '
                  . 'in the constructor or call setPassword($password) after instantiation.'
                );
            }

            if (null === $this->getSoapClient()) {
                $this->_initSoapClient($this->getWsdl());
            }

            try {
                @$this->getSoapClient()->LogIn(array(
                    'username' => $this->getUsername(),
                    'password' => $this->getPassword()
                ));
                $this->_setIsLoggedIn(true);
            } catch (\Exception $e) {
                throw new Exception\RuntimeException(
                    $e->getMessage()
                );
            }
        }

        return $this->_isLoggedIn;
    }

    /**
     * Log out of the LiveDocx service.
     *
     * @throws \Zend\Service\LiveDocx\Exception\RuntimeException
     * @return boolean
     * @since  LiveDocx 1.2
     */
    protected function _logOut()
    {
        if ($this->_getIsLoggedIn()) {
            try {
                $this->getSoapClient()->LogOut();
                $this->_setIsLoggedIn(false);
            } catch (\Exception $e) {
                throw new Exception\RuntimeException(
                    $e->getMessage()
                );
            }
        }

        return $this->_isLoggedIn;
    }

    // -------------------------------------------------------------------------

}
