<?php

namespace ZendTest\Cache\TestAsset;
use Zend\Cache\Storage\Plugin\AbstractPlugin;

class DummyStoragePlugin extends AbstractPlugin
{

    /**
     * Overwrite constructor: do not check internal storage
     */
    public function __construct($options = array())
    {
        $this->setOptions($options);
    }
}
