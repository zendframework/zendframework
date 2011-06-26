<?php
namespace ZendTest\Di\TestAsset;

use Zend\Di\ServiceLocator;

class ContainerExtension extends ServiceLocator
{
    public $foo;
    public $params;

    protected $map = array(
        'foo'    => 'getFoo',
        'params' => 'getParams',
    );

    public function getFoo()
    {
        return $this->foo;
    }

    public function getParams(array $params)
    {
        $this->params = $params;
        return $this->params;
    }
}
