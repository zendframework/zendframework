<?php

namespace ZendTest\Cache\Storage\TestAsset;
use Zend\Cache\Storage\Adapter\AbstractAdapter;

class MockAdapter extends AbstractAdapter
{

    public function getItem($key = null, array $options = array())
    {
    } 

    public function getMetadata($key = null, array $options = array())
    {
    }

    public function setItem($value, $key = null, array $options = array())
    {
    }

    public function removeItem($key = null, array $options = array())
    {
    }

    public function getCapacity(array $options = array())
    {
    }

}
