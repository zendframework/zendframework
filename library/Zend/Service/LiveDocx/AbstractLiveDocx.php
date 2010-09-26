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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Service\LiveDocx;
use Zend\Service\LiveDocx\Exception;
use Zend\Soap\Client\Client;

/**
 * @uses       Exception
 * @uses       Zend\Soap\Client\Client
 * @category   Zend
 * @package    Zend_Service
 * @subpackage LiveDocx
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @since      LiveDocx 1.0
 */
abstract class AbstractLiveDocx
{
    /**
     * LiveDocx service version
     * @since LiveDocx 1.0
     */
    const VERSION = '1.2';

    /**
     * SOAP client used to connect to LiveDocx service
     * @var   Zend\Soap\Client\Client
     * @since LiveDocx 1.0
     */
    protected $_soapClient;
        
    /**
     * WSDL of LiveDocx web service
     * @var   string
     * @since LiveDocx 1.0
     */
    protected $_wsdl;
        
    /**
     * Array of credentials (username and password) to log into backend server
     * @var   array
     * @since LiveDocx 1.2
     */
    protected $_credentials;
    
    /**
     * Set to true, when session is logged into backend server
     * @var   boolean
     * @since LiveDocx 1.2
     */
    protected $_loggedIn;

    
    /**
     * Constructor
     *
     * Optionally, pass an array of options (or Zend\Config\Config object).
     *
     * @param  array|Zend\Config\Config $options
     */
    public function __construct($options = null)
    {
        $this->_credentials = array();
        $this->_loggedIn = false;

        if ($options instanceof \Zend\Config\Config) {
            $options = $options->toArray();
        }
        
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }
    
    /**
     * Set options
     * One or more of username, password, soapClient
     * 
     * @param  $options
     * @return Zend\Service\AbstractLiveDocx
     * @since  LiveDocx 1.2
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $method = 'set' . $key;
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        
        return $this;
    }
        
    /**
     * Clean up and log out of LiveDocx service
     *
     * @return boolean
     * @since  LiveDocx 1.0
     */
    public function __destruct()
    {
        return $this->logOut();
    }
    
    /**
     * Init Soap client - connect to SOAP service
     *
     * @param  string $endpoint
     * @throws Zend\Service\LiveDocx\Exception
     * @return void
     * @since  LiveDocx 1.2
     */
    protected function _initSoapClient($endpoint)
    {
        $this->_soapClient = new Client();
        $this->_soapClient->setWSDL($endpoint);
    }
    
    /**
     * Get SOAP client
     *
     * @return Zend\Soap\Client\Client
     * @since  LiveDocx 1.2
     */
    public function getSoapClient()
    {
        return $this->_soapClient;
    }
    
    /**
     * Set SOAP client
     *
     * @param  Zend\Soap\Client\Client $soapClient
     * @return Zend\Service\AbstractLiveDocx
     * @since  LiveDocx 1.2
     */
    public function setSoapClient($soapClient)
    {
        $this->_soapClient = $soapClient;
        return $this;
    }

    /**
     * Log in to LiveDocx service
     *
     * @param string $username
     * @param string $password
     *
     * @throws Zend\Service\LiveDocx\Exception
     * @return boolean
     * @since  LiveDocx 1.2
     */
    public function logIn()
    {
        if (!$this->isLoggedIn()) {
            
            if (null === $this->getUsername()) {
                throw new Exception(
                    'Username has not been set. To set username specify the options array in the constructor or call setUsername($username) after instantiation.'
                );
            }
            
            if (null === $this->getPassword()) {
                throw new Exception(
                    'Password has not been set. To set password specify the options array in the constructor or call setPassword($password) after instantiation.'
                );
            }
            
            if (null === $this->getSoapClient()) {
                $this->_initSoapClient($this->getWSDL());
            }            
            
            try {
                $this->getSoapClient()->LogIn(array(
                    'username' => $this->getUsername(),
                    'password' => $this->getPassword(),
                ));
                $this->_loggedIn = true;
            } catch (\SoapFault $e) {
                throw new Exception(
                    'Cannot login into LiveDocx service. Please check that your server can download the WSDL (' . $this->getWSDL() . ') and that your username and password are valid.', 0, $e
                );
            }            
        }
        
        return $this->_loggedIn;
    }

    /**
     * Log out of the LiveDocx service
     *
     * @throws Zend\Service\LiveDocx\Exception
     * @return boolean
     * @since  LiveDocx 1.2
     */
    public function logOut()
    {
        if ($this->isLoggedIn()) {
            try {
                $this->getSoapClient()->LogOut();
                $this->_loggedIn = false;
            } catch (Exception $e) {
                throw new Exception(
                    'Cannot log out of LiveDocx service.', 0, $e
                );
            }            
        }
        
        return $this->_loggedIn;
    }
    
    /**
     * Return true, if session is currently logged into the backend server
     * 
     * @return boolean
     * @since  LiveDocx 1.2
     */
    public function isLoggedIn()
    {
        return $this->_loggedIn;
    }
    
    /**
     * Set username
     * 
     * @return Zend\Service\AbstractLiveDocx
     * @since  LiveDocx 1.0
     */
    public function setUsername($username)
    {
        $this->_credentials['username'] = $username;
        return $this;
    }
    
    /**
     * Set password
     * 
     * @return Zend\Service\AbstractLiveDocx
     * @since  LiveDocx 1.0
     */    
    public function setPassword($password)
    {
        $this->_credentials['password'] = $password;
        return $this;
    }

    /**
     * Set WSDL of LiveDocx web service
     * 
     * @return Zend\Service\AbstractLiveDocx
     * @since  LiveDocx 1.0
     */      
    public function setWSDL($wsdl) 
    {
        $this->_wsdl = $wsdl;
        return $this;
    }
      
    /**
     * Return current username
     * 
     * @return string|null
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
     * Return current password
     * 
     * @return string|null
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
     * Return WSDL of LiveDocx web service
     * 
     * @return Zend\Service\AbstractLiveDocx
     * @since  LiveDocx 1.0
     */      
    public function getWSDL() 
    {
        if (null !== $this->getSoapClient()) {
            return $this->getSoapClient()->getWSDL();
        } else {
            return $this->_wsdl;
        }
    }    

    /**
     * Return the document format (extension) of a filename
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
     * Return the current API version
     *
     * @return string
     * @since  LiveDocx 1.0
     */
    public function getVersion()
    {
        return self::VERSION;
    }
    
    /**
     * Compare the current API version with another version
     *
     * @param  string $version (STRING NOT FLOAT)
     * @return int -1 (version is less than API version), 0 (versions are equal), or 1 (version is greater than API version)
     * @since  LiveDocx 1.0
     */
    public function compareVersion($version)
    {
        return version_compare($version, $this->getVersion());
    }
}
