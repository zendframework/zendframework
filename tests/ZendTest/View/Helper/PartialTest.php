<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Helper;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\View\Helper\Partial;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer as View;

/**
 * Test class for Partial view helper.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class PartialTest extends TestCase
{
    /**
     * @var Partial
     */
    public $helper;

    /**
     * @var string
     */
    public $basePath;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->basePath = __DIR__ . '/_files/modules';
        $this->helper   = new Partial();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->helper);
    }

    /**
     * @return void
     */
    public function testPartialRendersScript()
    {
        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);
        $return = $this->helper->__invoke('partialOne.phtml');
        $this->assertContains('This is the first test partial', $return);
    }

    /**
     * @return void
     */
    public function testPartialRendersScriptWithVars()
    {
        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $view->vars()->message = 'This should never be read';
        $this->helper->setView($view);
        $return = $this->helper->__invoke('partialThree.phtml', array('message' => 'This message should be read'));
        $this->assertNotContains('This should never be read', $return);
        $this->assertContains('This message should be read', $return, $return);
    }

    /**
     * @return void
     */
    public function testSetViewSetsViewProperty()
    {
        $view = new View();
        $this->helper->setView($view);
        $this->assertSame($view, $this->helper->getView());
    }

    public function testObjectModelWithPublicPropertiesSetsViewVariables()
    {
        $model = new \stdClass();
        $model->foo = 'bar';
        $model->bar = 'baz';

        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);
        $return = $this->helper->__invoke('partialVars.phtml', $model);

        foreach (get_object_vars($model) as $key => $value) {
            $string = sprintf('%s: %s', $key, $value);
            $this->assertContains($string, $return);
        }
    }

    public function testObjectModelWithToArraySetsViewVariables()
    {
        $model = new Aggregate();

        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);
        $return = $this->helper->__invoke('partialVars.phtml', $model);

        foreach ($model->toArray() as $key => $value) {
            $string = sprintf('%s: %s', $key, $value);
            $this->assertContains($string, $return);
        }
    }

    public function testPassingNoArgsReturnsHelperInstance()
    {
        $test = $this->helper->__invoke();
        $this->assertSame($this->helper, $test);
    }

    public function testCanPassViewModelAsSecondArgument()
    {
        $model = new ViewModel(array(
            'foo' => 'bar',
            'bar' => 'baz',
        ));

        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);
        $return = $this->helper->__invoke('partialVars.phtml', $model);

        foreach ($model->getVariables() as $key => $value) {
            $string = sprintf('%s: %s', $key, $value);
            $this->assertContains($string, $return);
        }
    }

    public function testCanPassViewModelAsSoleArgument()
    {
        $model = new ViewModel(array(
            'foo' => 'bar',
            'bar' => 'baz',
        ));
        $model->setTemplate('partialVars.phtml');

        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);
        $return = $this->helper->__invoke($model);

        foreach ($model->getVariables() as $key => $value) {
            $string = sprintf('%s: %s', $key, $value);
            $this->assertContains($string, $return);
        }
    }
}

class Aggregate
{
    public $vars = array(
        'foo' => 'bar',
        'bar' => 'baz'
    );

    public function toArray()
    {
        return $this->vars;
    }
}
