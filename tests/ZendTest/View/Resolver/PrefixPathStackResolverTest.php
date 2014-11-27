<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\View\Resolver;

use Zend\View\Resolver\PrefixPathStackResolver;

class PrefixPathStackResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $resolver = new PrefixPathStackResolver(array(), true);
        $this->assertTrue($resolver->isLfiProtectionOn());
        $resolver = new PrefixPathStackResolver(array(), false);
        $this->assertFalse($resolver->isLfiProtectionOn());
    }

    public function testSetDefaultSuffix()
    {
        $resolver = new PrefixPathStackResolver;
        $resolver->setDefaultSuffix('php');
        $this->assertEquals('php', $resolver->getDefaultSuffix());
    }

    public function testGetDefaultSuffix()
    {
        $resolver = new PrefixPathStackResolver;
        $this->assertEquals('phtml', $resolver->getDefaultSuffix());
    }

    public function testSetTemplatePathStackResolver()
    {
        $templatePathStackResolver = $this->getMock('Zend\View\Resolver\TemplatePathStack');
        $resolver = new PrefixPathStackResolver;
        $resolver->setTemplatePathStackResolver('album/', $templatePathStackResolver);
        $this->assertEquals($templatePathStackResolver, $resolver->getTemplatePathStackResolver('album/'));
    }

    public function testGetDefaultTemplatePathStackResolver()
    {
        $resolver = new PrefixPathStackResolver;
        $resolver->set('album/', 'view');
        $this->assertInstanceOf('Zend\View\Resolver\TemplatePathStack', $resolver->getTemplatePathStackResolver('album/'));
    }

    public function testGetExceptionWhenPrefixIsNotSet()
    {
        $resolver = new PrefixPathStackResolver;
        $this->setExpectedException('Zend\View\Exception\InvalidArgumentException');
        $resolver->getTemplatePathStackResolver('bla-bla');
    }

    public function testResolve()
    {
        $resolver = new PrefixPathStackResolver(array(), false);
        $resolver->add('album/', 'path/to/view1');
        $resolver->add('album/', 'path/to/view2');
        $resolver->add('album/', 'path/to/view0', true);
        $templatePathStackResolver = $this->getMock('Zend\View\Resolver\TemplatePathStack');
        $resolver->setTemplatePathStackResolver('album/', $templatePathStackResolver);
        $templatePathStackResolver->expects($this->once())
            ->method('setPaths')
            ->with(array('path/to/view0', 'path/to/view1', 'path/to/view2'));
        $resolver->setDefaultSuffix('php');
        $templatePathStackResolver->expects($this->once())
            ->method('setDefaultSuffix')
            ->with('php');
        $templatePathStackResolver->expects($this->once())
            ->method('setLfiProtection')
            ->with(false);
        $templatePathStackResolver->expects($this->once())
            ->method('resolve')
            ->with('settings/add')
            ->will($this->returnValue('path/to/view1/settings/add.php'));
        $this->assertEquals('path/to/view1/settings/add.php', $resolver->resolve('album/settings/add'));
    }
}
