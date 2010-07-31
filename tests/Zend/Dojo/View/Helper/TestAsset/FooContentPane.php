<?php

namespace ZendTest\Dojo\View\Helper\TestAsset;

use Zend\Dojo\View\Helper\CustomDijit as CustomDijitHelper;

class FooContentPane extends CustomDijitHelper
{
    protected $_defaultDojoType = 'foo.ContentPane';

    public function direct($id = null, $value = null, array $params = array(), array $attribs = array())
    {
        return parent::direct($id, $value, $params, $attribs);
    }
}
