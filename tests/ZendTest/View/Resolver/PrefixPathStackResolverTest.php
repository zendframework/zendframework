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
use Zend\View\Resolver\TemplatePathStack;

class PrefixPathStackResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testSetLfiProtection()
    {
        $resolver = new PrefixPathStackResolver;
        $resolver->setLfiProtection(true);
        $this->assertTrue($resolver->isLfiProtectionOn());
        $resolver->setLfiProtection(false);
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
}
