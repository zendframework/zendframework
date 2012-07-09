<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage DeveloperGarden
 * @author     Marco Kaiser
 */
class Zend_Service_DeveloperGarden_IpLocation_IpAddress
{
    /**
     * the ip version
     * ip v4 = 4
     * ip v6 = 6
     *
     * @var integer
     */
    private $_version = 4;

    /**
     * currently supported versions
     *
     * @var array
     */
    private $_versionSupported = array(
        4,
        //6, not supported yet
    );

    private $_address = null;

    /**
     * create ipaddress object
     *
     * @param string $ip
     * @param integer $version
     *
     * @return Zend_Service_Developergarde_IpLocation_IpAddress
     */
    public function __construct($ip, $version = 4)
    {
        $this->setIp($ip)
             ->setVersion($version);
    }

    /**
     * sets new ip address
     *
     * @param string $ip
     * @throws Zend_Service_DeveloperGarden_Exception
     * @return Zend_Service_DeveloperGarden_IpLocation_IpAddress
     */
    public function setIp($ip)
    {
        $validator = new Zend\Validator\Ip();

        if (!$validator->isValid($ip)) {
            $message = $validator->getMessages();
            throw new Zend_Service_DeveloperGarden_Exception($message['notIpAddress']);
        }
        $this->_address = $ip;
        return $this;
    }

    /**
     * returns the current address
     *
     * @return string
     */
    public function getIp()
    {
        return $this->_address;
    }

    /**
     * sets new ip version
     *
     * @param integer $version
     * @throws Zend_Service_DeveloperGarden_Exception
     * @return Zend_Service_DeveloperGarden_IpLocation_IpAddress
     */
    public function setVersion($version)
    {
        if (!in_array($version, $this->_versionSupported)) {
            throw new Zend_Service_DeveloperGarden_Exception('Ip Version ' . (int)$version . ' is not supported.');
        }

        $this->_version = $version;
        return $this;
    }

    /**
     * returns the ip version
     *
     * @return integer
     */
    public function getVersion()
    {
        return $this->_version;
    }
}
