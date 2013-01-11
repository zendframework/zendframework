<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc\Controller\Plugin\TestAsset;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class SamplePluginWithConstructor extends AbstractPlugin
{
    protected $bar;

    public function __construct($bar = 'baz')
    {
        $this->bar = $bar;
    }

    public function getBar()
    {
        return $this->bar;
    }
}
