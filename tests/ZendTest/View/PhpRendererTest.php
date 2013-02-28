<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View;

use Zend\View\Renderer\PhpRenderer;
use Zend\View\Model\ViewModel;
use Zend\View\Resolver\TemplateMapResolver;
use Zend\View\Resolver\TemplatePathStack;
use Zend\View\Variables;
use Zend\Filter\FilterChain;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @group      Zend_View
 */
class PhpRendererTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->renderer = new PhpRenderer();
    }

    public function testEngineIsIdenticalToRenderer()
    {
        $this->assertSame($this->renderer, $this->renderer->getEngine());
    }

    public function testUsesTemplatePathStackAsDefaultResolver()
    {
        $this->assertInstanceOf('Zend\View\Resolver\TemplatePathStack', $this->renderer->resolver());
    }

    public function testCanSetResolverInstance()
    {
        $resolver = new TemplatePathStack();
        $this->renderer->setResolver($resolver);
        $this->assertSame($resolver, $this->renderer->resolver());
    }

    public function testPassingNameToResolverReturnsScriptName()
    {
        $this->renderer->resolver()->addPath(__DIR__ . '/_templates');
        $filename = $this->renderer->resolver('test.phtml');
        $this->assertEquals(realpath(__DIR__ . '/_templates/test.phtml'), $filename);
    }

    public function testUsesVariablesObjectForVarsByDefault()
    {
        $this->assertInstanceOf('Zend\View\Variables', $this->renderer->vars());
    }

    public function testCanSpecifyArrayAccessForVars()
    {
        $a = new \ArrayObject;
        $this->renderer->setVars($a);
        $this->assertSame($a->getArrayCopy(), $this->renderer->vars()->getArrayCopy());
    }

    public function testCanSpecifyArrayForVars()
    {
        $vars = array('foo' => 'bar');
        $this->renderer->setVars($vars);
        $this->assertEquals($vars, $this->renderer->vars()->getArrayCopy());
    }

    public function testPassingArgumentToVarsReturnsValueFromThatKey()
    {
        $this->renderer->vars()->assign(array('foo' => 'bar'));
        $this->assertEquals('bar', $this->renderer->vars('foo'));
    }

    public function testUsesHelperPluginManagerByDefault()
    {
        $this->assertInstanceOf('Zend\View\HelperPluginManager', $this->renderer->getHelperPluginManager());
    }

    public function testPassingArgumentToPluginReturnsHelperByThatName()
    {
        $helper = $this->renderer->plugin('doctype');
        $this->assertInstanceOf('Zend\View\Helper\Doctype', $helper);
    }

    public function testPassingStringOfUndefinedClassToSetHelperPluginManagerRaisesException()
    {
        $this->setExpectedException('Zend\View\Exception\ExceptionInterface', 'Invalid');
        $this->renderer->setHelperPluginManager('__foo__');
    }

    public function testPassingValidStringClassToSetHelperPluginManagerCreatesIt()
    {
        $this->renderer->setHelperPluginManager('Zend\View\HelperPluginManager');
        $this->assertInstanceOf('Zend\View\HelperPluginManager', $this->renderer->getHelperPluginManager());
    }

    public function invalidPluginManagers()
    {
        return array(
            array(true),
            array(1),
            array(1.0),
            array(array('foo')),
            array(new \stdClass),
        );
    }

    /**
     * @dataProvider invalidPluginManagers
     */
    public function testPassingInvalidArgumentToSetHelperPluginManagerRaisesException($plugins)
    {
        $this->setExpectedException('Zend\View\Exception\ExceptionInterface', 'must extend');
        $this->renderer->setHelperPluginManager($plugins);
    }

    public function testInjectsSelfIntoHelperPluginManager()
    {
        $plugins = $this->renderer->getHelperPluginManager();
        $this->assertSame($this->renderer, $plugins->getRenderer());
    }

    public function testUsesFilterChainByDefault()
    {
        $this->assertInstanceOf('Zend\Filter\FilterChain', $this->renderer->getFilterChain());
    }

    public function testMaySetExplicitFilterChainInstance()
    {
        $filterChain = new FilterChain();
        $this->renderer->setFilterChain($filterChain);
        $this->assertSame($filterChain, $this->renderer->getFilterChain());
    }

    public function testRenderingAllowsVariableSubstitutions()
    {
        $expected = 'foo INJECT baz';
        $this->renderer->vars()->assign(array('bar' => 'INJECT'));
        $this->renderer->resolver()->addPath(__DIR__ . '/_templates');
        $test = $this->renderer->render('test.phtml');
        $this->assertContains($expected, $test);
    }

    public function testRenderingFiltersContentWithFilterChain()
    {
        $expected = 'foo bar baz';
        $this->renderer->getFilterChain()->attach(function ($content) {
            return str_replace('INJECT', 'bar', $content);
        });
        $this->renderer->vars()->assign(array('bar' => 'INJECT'));
        $this->renderer->resolver()->addPath(__DIR__ . '/_templates');
        $test = $this->renderer->render('test.phtml');
        $this->assertContains($expected, $test);
    }

    public function testCanAccessHelpersInTemplates()
    {
        $this->renderer->resolver()->addPath(__DIR__ . '/_templates');
        $content = $this->renderer->render('test-with-helpers.phtml');
        foreach (array('foo', 'bar', 'baz') as $value) {
            $this->assertContains("<li>$value</li>", $content);
        }
    }

    /**
     * @group ZF2-68
     */
    public function testCanSpecifyArrayForVarsAndGetAlwaysArrayObject()
    {
        $vars = array('foo' => 'bar');
        $this->renderer->setVars($vars);
        $this->assertTrue($this->renderer->vars() instanceof Variables);
    }

    /**
     * @group ZF2-68
     */
    public function testPassingVariablesObjectToSetVarsShouldUseItDirectory()
    {
        $vars = new Variables(array('foo' => '<p>Bar</p>'));
        $this->renderer->setVars($vars);
        $this->assertSame($vars, $this->renderer->vars());
    }

    /**
     * @group ZF2-86
     */
    public function testNestedRenderingRestoresVariablesCorrectly()
    {
        $expected = "inner\n<p>content</p>";
        $this->renderer->resolver()->addPath(__DIR__ . '/_templates');
        $test = $this->renderer->render('testNestedOuter.phtml', array('content' => '<p>content</p>'));
        $this->assertEquals($expected, $test);
    }

    /**
     * @group convenience-api
     */
    public function testPropertyOverloadingShouldProxyToVariablesContainer()
    {
        $this->renderer->foo = '<p>Bar</p>';
        $this->assertEquals($this->renderer->vars('foo'), $this->renderer->foo);
    }

    /**
     * @group convenience-api
     */
    public function testMethodOverloadingShouldReturnHelperInstanceIfNotInvokable()
    {
        $helpers = $this->renderer->getHelperPluginManager();
        $helpers->setInvokableClass('uninvokable', 'ZendTest\View\TestAsset\Uninvokable');
        $helper = $this->renderer->uninvokable();
        $this->assertInstanceOf('ZendTest\View\TestAsset\Uninvokable', $helper);
    }

    /**
     * @group convenience-api
     */
    public function testMethodOverloadingShouldInvokeHelperIfInvokable()
    {
        $helpers = $this->renderer->getHelperPluginManager();
        $helpers->setInvokableClass('invokable', 'ZendTest\View\TestAsset\Invokable');
        $return = $this->renderer->invokable('it works!');
        $this->assertEquals('ZendTest\View\TestAsset\Invokable::__invoke: it works!', $return);
    }

    /**
     * @group convenience-api
     */
    public function testGetMethodShouldRetrieveVariableFromVariableContainer()
    {
        $this->renderer->foo = '<p>Bar</p>';
        $foo = $this->renderer->get('foo');
        $this->assertSame($this->renderer->vars()->foo, $foo);
    }

    /**
     * @group convenience-api
     */
    public function testRenderingLocalVariables()
    {
        $expected = '10 > 9';
        $this->renderer->vars()->assign(array('foo' => '10 > 9'));
        $this->renderer->resolver()->addPath(__DIR__ . '/_templates');
        $test = $this->renderer->render('testLocalVars.phtml');
        $this->assertContains($expected, $test);
    }

    public function testRendersTemplatesInAStack()
    {
        $resolver = new TemplateMapResolver(array(
            'layout' => __DIR__ . '/_templates/layout.phtml',
            'block'  => __DIR__ . '/_templates/block.phtml',
        ));
        $this->renderer->setResolver($resolver);

        $content = $this->renderer->render('block');
        $this->assertRegexp('#<body>\s*Block content\s*</body>#', $content);
    }

    /**
     * @group view-model
     */
    public function testCanRenderViewModel()
    {
        $resolver = new TemplateMapResolver(array(
            'empty' => __DIR__ . '/_templates/empty.phtml',
        ));
        $this->renderer->setResolver($resolver);

        $model = new ViewModel();
        $model->setTemplate('empty');

        $content = $this->renderer->render($model);
        $this->assertRegexp('/\s*Empty view\s*/s', $content);
    }

    /**
     * @group view-model
     */
    public function testViewModelWithoutTemplateRaisesException()
    {
        $model = new ViewModel();
        $this->setExpectedException('Zend\View\Exception\DomainException');
        $content = $this->renderer->render($model);
    }

    /**
     * @group view-model
     */
    public function testRendersViewModelWithVariablesSpecified()
    {
        $resolver = new TemplateMapResolver(array(
            'test' => __DIR__ . '/_templates/test.phtml',
        ));
        $this->renderer->setResolver($resolver);

        $model = new ViewModel();
        $model->setTemplate('test');
        $model->setVariable('bar', 'bar');

        $content = $this->renderer->render($model);
        $this->assertRegexp('/\s*foo bar baz\s*/s', $content);
    }

    /**
     * @group view-model
     */
    public function testRenderedViewModelIsRegisteredAsCurrentViewModel()
    {
        $resolver = new TemplateMapResolver(array(
            'empty' => __DIR__ . '/_templates/empty.phtml',
        ));
        $this->renderer->setResolver($resolver);

        $model = new ViewModel();
        $model->setTemplate('empty');

        $content = $this->renderer->render($model);
        $helper  = $this->renderer->plugin('view_model');
        $this->assertTrue($helper->hasCurrent());
        $this->assertSame($model, $helper->getCurrent());
    }

    public function testRendererRaisesExceptionInCaseOfExceptionInView()
    {
        $resolver = new TemplateMapResolver(array(
            'exception' => __DIR__ . '../../Mvc/View/_files/exception.phtml',
        ));
        $this->renderer->setResolver($resolver);

        $model = new ViewModel();
        $model->setTemplate('exception');

        try {
            $this->renderer->render($model);
            $this->fail('Exception from renderer should propagate');
        } catch (\Exception $e) {
            $this->assertInstanceOf('Exception', $e);
        }
    }

    public function testRendererRaisesExceptionIfResolverCannotResolveTemplate()
    {
        $expected = '10 &gt; 9';
        $this->renderer->vars()->assign(array('foo' => '10 > 9'));
        $this->setExpectedException('Zend\View\Exception\RuntimeException', 'could not resolve');
        $test = $this->renderer->render('should-not-find-this');
    }

    /**
     * @group view-model
     */
    public function testDoesNotRenderTreesOfViewModelsByDefault()
    {
        $this->assertFalse($this->renderer->canRenderTrees());
    }

    /**
     * @group view-model
     */
    public function testRenderTreesOfViewModelsCapabilityIsMutable()
    {
        $this->renderer->setCanRenderTrees(true);
        $this->assertTrue($this->renderer->canRenderTrees());
        $this->renderer->setCanRenderTrees(false);
        $this->assertFalse($this->renderer->canRenderTrees());
    }

    /**
     * @group view-model
     */
    public function testIfViewModelComposesVariablesInstanceThenRendererUsesIt()
    {
        $model = new ViewModel();
        $model->setTemplate('template');
        $vars  = $model->getVariables();
        $vars['foo'] = 'BAR-BAZ-BAT';

        $resolver = new TemplateMapResolver(array(
            'template' => __DIR__ . '/_templates/view-model-variables.phtml',
        ));
        $this->renderer->setResolver($resolver);
        $test = $this->renderer->render($model);
        $this->assertContains('BAR-BAZ-BAT', $test);
    }
}
