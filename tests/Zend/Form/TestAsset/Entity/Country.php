<?php

namespace ZendTest\Form\TestAsset\Entity;

class Country
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $continent;


    /**
     * @param string $name
     * @return Country
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
     * @param string $continent
     * @return Country
     */
    public function setContinent($continent)
    {
        $this->continent = $continent;
        return $this;
    }

    /**
     * @return string
     */
    public function getContinent()
    {
        return $this->continent;
    }
}
