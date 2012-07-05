<?php

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
     * @param $street
     * @return Address
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
     * @return Address
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
}
