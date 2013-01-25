<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Di
 */

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
