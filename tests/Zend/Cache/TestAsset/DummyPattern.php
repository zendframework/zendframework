<?php

namespace ZendTest\Cache\TestAsset;
use Zend\Cache;

class DummyPattern extends Cache\Pattern\AbstractPattern
{

    public $_dummyOption = 'dummyOption';

    public function setDummyOption($value)
    {
        $this->_dummyOption = $value;
        return $this;
    }

    public function getDummyOption()
    {
        return $this->_dummyOption;
    }

}
