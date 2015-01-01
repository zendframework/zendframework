<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form\TestAsset\Entity;

class Address
{
    /**
     * @var string
     */
    protected $street;

    /**
     * @var City
     */
    protected $city;

    /**
     * @var array
     */
    protected $phones = array();


    /**
     * @param $street
     * @return self
     */
    public function setStreet($street)
    {
        $this->street = $street;
        return $this;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param City $city
     * @return self
     */
    public function setCity(City $city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param array $phones
     * @return self
     */
    public function setPhones(array $phones)
    {
        $this->phones = $phones;
        return $this;
    }

    /**
     * @return array
     */
    public function getPhones()
    {
        return $this->phones;
    }
}
