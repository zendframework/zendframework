<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mvc\Service;

use Zend\Mvc\Service\ViewPrefixPathStackResolverFactory;

class ViewPrefixPathStackResolverFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateService()
    {
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->expects($this->once())
            ->method('get')
            ->with('Config')
            ->will($this->returnValue(array(
                'view_manager' => array(
                    'prefix_template_path_stack' => array(
                        'album/' => array(),
                    ),
                    'default_template_suffix' => 'php',
                ),
            )));

        $factory = new ViewPrefixPathStackResolverFactory();
        $resolver = $factory->createService($serviceLocator);
        $this->assertInstanceOf('Zend\View\Resolver\PrefixPathStackResolver', $resolver);
        $this->assertEquals('php', $resolver->getDefaultSuffix());
    }
}
