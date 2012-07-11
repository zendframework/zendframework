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
class Zend_Service_DeveloperGarden_Response_IpLocation_IPAddressLocationType
    extends Zend_Service_DeveloperGarden_Response_BaseType
{
    /**
     * @var Zend_Service_DeveloperGarden_Response_IpLocation_RegionType
     */
    public $isInRegion = null;

    /**
     * @var Zend_Service_DeveloperGarden_Response_IpLocation_GeoCoordinatesType
     */
    public $isInGeo = null;

    /**
     * @var Zend_Service_DeveloperGarden_Response_IpLocation_CityType
     */
    public $isInCity = null;

    /**
     * @var integer
     */
    public $ipType = null;

    /**
     * @var string
     */
    public $ipAddress = null;

    /**
     * @var integer
     */
    public $radius = 0;

    /**
     * @return Zend_Service_DeveloperGarden_Response_IpLocation_RegionType
     */
    public function getRegion()
    {
        return $this->isInRegion;
    }

    /**
     * @return Zend_Service_DeveloperGarden_Response_IpLocation_GeoCoordinatesType
     */
    public function getGeoCoordinates()
    {
        return $this->isInGeo;
    }

    /**
     * @return Zend_Service_DeveloperGarden_Response_IpLocation_CityType
     */
    public function getCity()
    {
        return $this->isInCity;
    }

    /**
     * @return integer
     */
    public function getIpType()
    {
        return $this->ipType;
    }

    /**
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @return integer
     */
    public function getRadius()
    {
        return $this->radius;
    }

    /**
     * implement parsing
     *
     */
    public function parse()
    {
        parent::parse();
        if ($this->isInCity === null) {
            $this->isInCity = new Zend_Service_DeveloperGarden_Response_IpLocation_CityType();
        }

        if ($this->isInRegion === null) {
            $this->isInRegion = new Zend_Service_DeveloperGarden_Response_IpLocation_RegionType();
        }

        if ($this->isInGeo === null) {
            $this->isInGeo = new Zend_Service_DeveloperGarden_Response_IpLocation_GeoCoordinatesType();
        }

        return $this;
    }
}
