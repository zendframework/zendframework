<?php

namespace Zend\Db\Adapter\DriverStatement;

class MappedParameterContainer extends NamedParameterContainer
{
    
    protected $map = null;
    
    public function __construct($map = null, array $array = array(), $arrayMode = self::ARRAY_IS_NAMES_AND_VALUES)
    {
        if ($map) {
            $this->setMap($map);
        }
        parent::__construct($array, $arrayMode);
    }
    
    public function setMap(Array $map)
    {
        $index = 0;
        foreach ($map as $n => $v) {
            if ($n !== $index) {
                throw new \InvalidArgumentException('The provided map must be a zero indexed array of names for array positions');
            }
            $this->map[$n] = $v;
            $index++;
        }
    }
    
    public function getErrataIterator()
    {
        /* return a mapped positional iterator for errata */
    }
    
}
