<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\View\Resolver;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver\RelativeFallbackResolver;
use Zend\View\Resolver\TemplateMapResolver;
use Zend\View\Resolver\TemplatePathStack;
use Zend\View\Resolver\AggregateResolver;

/**
 * @covers \ZendTest\View\Resolver\RelativeFallbackResolver
 */
class RelativeFallbackResolverTest extends TestCase
{
    public function testReturnsResourceFromTheSameNameSpaceWithMapResolver()
    {
        $tplMapResolver = new TemplateMapResolver(array(
            'foo/bar' => 'foo/baz',
        ));
        $resolver = new RelativeFallbackResolver($tplMapResolver);
        $renderer = new PhpRenderer();
        $view = new ViewModel();
        $view->setTemplate('foo/zaz');
        $helper = $renderer->plugin('view_model');
        $helper->setCurrent($view);

        $test = $resolver->resolve('bar', $renderer);
        $this->assertEquals('foo/baz', $test);
    }

    public function testReturnsResourceFromTheSameNameSpaceWithPathStack()
    {
        $pathStack = new TemplatePathStack();
        $pathStack->addPath(__DIR__ . '/../_templates');
        $resolver = new RelativeFallbackResolver($pathStack);
        $renderer = new PhpRenderer();
        $view = new ViewModel();
        $view->setTemplate('name-space/any-view');
        $helper = $renderer->plugin('view_model');
        $helper->setCurrent($view);

        $test = $resolver->resolve('bar', $renderer);
        $this->assertEquals(realpath(__DIR__ . '/../_templates/name-space/bar.phtml'), $test);
    }

    public function testReturnsResourceFromTopLevelIfExistsInsteadOfTheSameNameSpace()
    {
        $tplMapResolver = new TemplateMapResolver(array(
            'foo/bar' => 'foo/baz',
            'bar' => 'baz',
        ));
        $resolver = new AggregateResolver();
        $resolver->attach($tplMapResolver);
        $resolver->attach(new RelativeFallbackResolver($tplMapResolver));
        $renderer = new PhpRenderer();
        $view = new ViewModel();
        $view->setTemplate('foo/zaz');
        $helper = $renderer->plugin('view_model');
        $helper->setCurrent($view);

        $test = $resolver->resolve('bar', $renderer);
        $this->assertEquals('baz', $test);
    }
}
