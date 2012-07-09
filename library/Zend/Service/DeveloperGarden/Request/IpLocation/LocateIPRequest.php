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
class Zend_Service_DeveloperGarden_Request_IpLocation_LocateIPRequest
    extends Zend_Service_DeveloperGarden_Request_AbstractRequest
{
    /**
     * the ip addresses to lookup for
     *
     * @var Zend_Service_DeveloperGarden_Request_IpLocation_IpAddress
     */
    public $address = null;

    /**
     * the account
     *
     * @var string
     */
    public $account = null;

    /**
     * constructor give them the environment
     *
     * @param integer $environment
     * @param Zend_Service_DeveloperGarden_IpLocation_IpAddress|array $ip
     *
     * @return Zend_Service_DeveloperGarden_Request_AbstractRequest
     */
    public function __construct($environment, $ip = null)
    {
        parent::__construct($environment);

        if ($ip !== null) {
            $this->setIp($ip);
        }
    }

    /**
     * sets new ip or array of ips
     *
     * @param Zend_Service_DeveloperGarden_IpLocation_IpAddress|array $ip
     *
     * @return Zend_Service_DeveloperGarden_Request_IpLocation_LocateIPRequest
     */
    public function setIp($ip)
    {
        if ($ip instanceof Zend_Service_DeveloperGarden_IpLocation_IpAddress) {
            $this->address[] = array(
                'ipType'    => $ip->getVersion(),
                'ipAddress' => $ip->getIp(),
            );
            return $this;
        }

        if (is_array($ip)) {
            foreach ($ip as $ipObject) {
                if (!$ipObject instanceof Zend_Service_DeveloperGarden_IpLocation_IpAddress
                    && !is_string($ipObject)
                ) {
                    throw new Zend_Service_DeveloperGarden_Request_Exception(
                        'Not a valid Ip Address object found.'
                    );
                }
                $this->setIp($ipObject);
            }
            return $this;
        }

        if (!is_string($ip)) {
            throw new Zend_Service_DeveloperGarden_Request_Exception('Not a valid Ip Address object found.');
        }

        return $this->setIp(new Zend_Service_DeveloperGarden_IpLocation_IpAddress($ip));
    }
}
