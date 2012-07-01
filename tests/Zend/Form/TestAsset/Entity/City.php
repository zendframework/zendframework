<?php

namespace ZendTest\Form\TestAsset\Entity;

class City
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $zipCode;

    /**
     * @var Country
     */
    protected $country;


    /**
     * @param Country $country
     * @return City
     */
    public function setCountry(Country $country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $name
     * @return City
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $zipCode
     * @return City
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }
}
