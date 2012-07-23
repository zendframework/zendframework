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
class Zend_Service_DeveloperGarden_Response_IpLocation_RegionType
    extends Zend_Service_DeveloperGarden_Response_BaseType
{
    /**
     * country code
     * @var string
     */
    public $countryCode = null;

    /**
     * region code
     * @var string
     */
    public $regionCode = null;

    /**
     * region Name
     * @var string
     */
    public $regionName = null;

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @return string
     */
    public function getRegionCode()
    {
        return $this->regionCode;
    }

    /**
     * @return string
     */
    public function getRegionName()
    {
        return $this->regionName;
    }
}
