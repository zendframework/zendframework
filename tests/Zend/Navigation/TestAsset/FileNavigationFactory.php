<?php

namespace ZendTest\Navigation\TestAsset;

use \Zend\Navigation\Service\AbstractNavigationFactory;

class FileNavigationFactory extends AbstractNavigationFactory
{
    protected function getName()
    {
        return 'file';
    }
}