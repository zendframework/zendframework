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
class Zend_Service_DeveloperGarden_IpLocation
    extends Zend_Service_DeveloperGarden_Client_AbstractClient
{
    /**
     * wsdl file
     *
     * @var string
     */
    protected $_wsdlFile = 'https://gateway.developer.telekom.com/p3gw-mod-odg-iplocation/services/IPLocation?wsdl';

    /**
     * wsdl file local
     *
     * @var string
     */
    protected $_wsdlFileLocal = 'Wsdl/IPLocation.wsdl';

    /**
     * Response, Request Classmapping
     *
     * @var array
     *
     */
    protected $_classMap = array(
        'LocateIPResponseType'  => 'Zend_Service_DeveloperGarden_Response_IpLocation_LocateIPResponseType',
        'IPAddressLocationType' => 'Zend_Service_DeveloperGarden_Response_IpLocation_IPAddressLocationType',
        'RegionType'            => 'Zend_Service_DeveloperGarden_Response_IpLocation_RegionType',
        'GeoCoordinatesType'    => 'Zend_Service_DeveloperGarden_Response_IpLocation_GeoCoordinatesType',
        'CityType'              => 'Zend_Service_DeveloperGarden_Response_IpLocation_CityType',
    );

    /**
     * locate the given Ip address or array of addresses
     *
     * @param Zend_Service_DeveloperGarden_IpLocation_IpAddress|string $ip
     * @return Zend_Service_DeveloperGarden_Response_IpLocation_LocateIPResponse
     */
    public function locateIP($ip)
    {
        $request = new Zend_Service_DeveloperGarden_Request_IpLocation_LocateIPRequest(
            $this->getEnvironment(),
            $ip
        );

        $result = $this->getSoapClient()->locateIP($request);

        $response = new Zend_Service_DeveloperGarden_Response_IpLocation_LocateIPResponse($result);
        return $response->parse();
    }
}
