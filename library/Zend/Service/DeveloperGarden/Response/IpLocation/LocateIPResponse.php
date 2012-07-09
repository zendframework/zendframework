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
class Zend_Service_DeveloperGarden_Response_IpLocation_LocateIPResponse
    extends Zend_Service_DeveloperGarden_Response_BaseType
{
    /**
     * internal data object array of
     * elements
     *
     * @var array
     */
    public $ipAddressLocation = array();

    /**
     * constructor
     *
     * @param Zend_Service_DeveloperGarden_Response_IpLocation_LocateIPResponseType $response
     */
    public function __construct(
        Zend_Service_DeveloperGarden_Response_IpLocation_LocateIPResponseType $response
    ) {
        if ($response->ipAddressLocation instanceof Zend_Service_DeveloperGarden_Response_IpLocation_IPAddressLocationType) {
            if (is_array($response->ipAddressLocation)) {
                foreach ($response->ipAddressLocation as $location) {
                    $this->ipAddressLocation[] = $location;
                }

            } else {
                $this->ipAddressLocation[] = $response->ipAddressLocation;
            }
        } elseif (is_array($response->ipAddressLocation)) {
            $this->ipAddressLocation = $response->ipAddressLocation;
        }

        $this->errorCode     = $response->getErrorCode();
        $this->errorMessage  = $response->getErrorMessage();
        $this->statusCode    = $response->getStatusCode();
        $this->statusMessage = $response->getStatusMessage();
    }

    /**
     * implement own parsing mechanism to fix broken wsdl implementation
     */
    public function parse()
    {
        parent::parse();
        if (is_array($this->ipAddressLocation)) {
            foreach ($this->ipAddressLocation as $address) {
                $address->parse();
            }
        } elseif ($this->ipAddressLocation instanceof Zend_Service_DeveloperGarden_Response_IpLocation_IPAddressLocationType) {
            $this->ipAddressLocation->parse();
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getIpAddressLocation()
    {
        return $this->ipAddressLocation;
    }
}
