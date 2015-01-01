<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace NotAutoloaderModule;

class Module
{
    public $getAutoloaderConfigCalled = false;

    public function getAutoloaderConfig()
    {
        $this->getAutoloaderConfigCalled = true;
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    'Foo' => __DIR__ . '/src/Foo',
                ),
            ),
        );
    }
}
