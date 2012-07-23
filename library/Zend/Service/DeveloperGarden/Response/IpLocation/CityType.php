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
class Zend_Service_DeveloperGarden_Response_IpLocation_CityType
    extends Zend_Service_DeveloperGarden_Response_BaseType
{
    /**
     * @var string
     */
    public $countryCode = null;

    /**
     * @var string
     */
    public $cityCode = null;

    /**
     * @var string
     */
    public $cityName = null;

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
    public function getCityCode()
    {
        return $this->cityCode;
    }

    /**
     * @return string
     */
    public function getCityName()
    {
        return $this->cityName;
    }
}
