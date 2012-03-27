<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\View\Helper;

use ArrayObject,
    Iterator,
    Zend\Controller\Front as FrontController,
    Zend\View\PhpRenderer as View,
    Zend\View\Helper\PartialLoop;

/**
 * Test class for Zend_View_Helper_PartialLoop.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class PartialLoopTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_View_Helper_PartialLoop
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
        $this->helper = new PartialLoop();
        FrontController::getInstance()->resetInstance();
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
    public function testPartialLoopIteratesOverArray()
    {
        $data = array(
            array('message' => 'foo'),
            array('message' => 'bar'),
            array('message' => 'baz'),
            array('message' => 'bat')
        );

        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);

        $result = $this->helper->__invoke('partialLoop.phtml', $data);
        foreach ($data as $item) {
            $string = 'This is an iteration: ' . $item['message'];
            $this->assertContains($string, $result);
        }
    }

    /**
     * @return void
     */
    public function testPartialLoopIteratesOverIterator()
    {
        $data = array(
            array('message' => 'foo'),
            array('message' => 'bar'),
            array('message' => 'baz'),
            array('message' => 'bat')
        );
        $o = new IteratorTest($data);

        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);

        $result = $this->helper->__invoke('partialLoop.phtml', $o);
        foreach ($data as $item) {
            $string = 'This is an iteration: ' . $item['message'];
            $this->assertContains($string, $result);
        }
    }

    /**
     * @return void
     */
    public function testPartialLoopIteratesOverRecursiveIterator()
    {
        $rIterator = new RecursiveIteratorTest();
        for ($i = 0; $i < 5; ++$i) {
            $data = array(
                'message' => 'foo' . $i,
            );
            $rIterator->addItem(new IteratorTest($data));
        }

        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);

        $result = $this->helper->__invoke('partialLoop.phtml', $rIterator);
        foreach ($rIterator as $item) {
            foreach ($item as $key => $value) {
                $this->assertContains($value, $result, var_export($value, 1));
            }
        }
    }

    /**
     * @return void
     */
    public function testPartialLoopThrowsExceptionWithBadIterator()
    {
        $data = array(
            array('message' => 'foo'),
            array('message' => 'bar'),
            array('message' => 'baz'),
            array('message' => 'bat')
        );
        $o = new BogusIteratorTest($data);

        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);

        try {
            $result = $this->helper->__invoke('partialLoop.phtml', $o);
            $this->fail('PartialLoop should only work with arrays and iterators');
        } catch (\Exception $e) {
        }
    }

    /**
     * @return void
     */
    public function testPartialLoopFindsModule()
    {
        FrontController::getInstance()->addModuleDirectory($this->basePath);
        $data = array(
            array('message' => 'foo'),
            array('message' => 'bar'),
            array('message' => 'baz'),
            array('message' => 'bat')
        );

        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);

        $result = $this->helper->__invoke('partialLoop.phtml', 'foo', $data);
        foreach ($data as $item) {
            $string = 'This is an iteration in the foo module: ' . $item['message'];
            $this->assertContains($string, $result);
        }
    }

    public function testPassingNoArgsReturnsHelperInstance()
    {
        $test = $this->helper->__invoke();
        $this->assertSame($this->helper, $test);
    }

    public function testShouldAllowIteratingOverTraversableObjects()
    {
        $data = array(
            array('message' => 'foo'),
            array('message' => 'bar'),
            array('message' => 'baz'),
            array('message' => 'bat')
        );
        $o = new ArrayObject($data);

        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);

        $result = $this->helper->__invoke('partialLoop.phtml', $o);
        foreach ($data as $item) {
            $string = 'This is an iteration: ' . $item['message'];
            $this->assertContains($string, $result);
        }
    }

    public function testShouldAllowIteratingOverObjectsImplementingToArray()
    {
        $data = array(
            array('message' => 'foo'),
            array('message' => 'bar'),
            array('message' => 'baz'),
            array('message' => 'bat')
        );
        $o = new ToArrayTest($data);

        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);

        $result = $this->helper->__invoke('partialLoop.phtml', $o);
        foreach ($data as $item) {
            $string = 'This is an iteration: ' . $item['message'];
            $this->assertContains($string, $result, $result);
        }
    }

    /**
     * @group ZF-3350
     * @group ZF-3352
     */
    public function testShouldNotCastToArrayIfObjectIsTraversable()
    {
        $data = array(
            new IteratorWithToArrayTestContainer(array('message' => 'foo')),
            new IteratorWithToArrayTestContainer(array('message' => 'bar')),
            new IteratorWithToArrayTestContainer(array('message' => 'baz')),
            new IteratorWithToArrayTestContainer(array('message' => 'bat')),
        );
        $o = new IteratorWithToArrayTest($data);

        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);
        $this->helper->setObjectKey('obj');

        $result = $this->helper->__invoke('partialLoopObject.phtml', $o);
        foreach ($data as $item) {
            $string = 'This is an iteration: ' . $item->message;
            $this->assertContains($string, $result, $result);
        }
    }

    /**
     * @group ZF-3083
     */
    public function testEmptyArrayPassedToPartialLoopShouldNotThrowException()
    {
        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);

        try {
            $result = $this->helper->__invoke('partialLoop.phtml', array());
        } catch (\Exception $e) {
            $this->fail('Empty array should not cause partialLoop to throw exception');
        }

        try {
            $result = $this->helper->__invoke('partialLoop.phtml', null, array());
        } catch (\Exception $e) {
            $this->fail('Empty array should not cause partialLoop to throw exception');
        }
    }

    /**
     * @group ZF-2737
     */
    public function testPartialLoopIncramentsPartialCounter()
    {
        $data = array(
            array('message' => 'foo'),
            array('message' => 'bar'),
            array('message' => 'baz'),
            array('message' => 'bat')
        );

        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);

        $result = $this->helper->__invoke('partialLoopCouter.phtml', $data);
        foreach ($data as $key => $item) {
            $string = sprintf(
                'This is an iteration: %s, pointer at %d',
                $item['message'],
                $key + 1
            );
            $this->assertContains($string, $result, $result);
        }
    }

    /**
     * @group ZF-5174
     */
    public function testPartialLoopPartialCounterResets()
    {
        $data = array(
            array('message' => 'foo'),
            array('message' => 'bar'),
            array('message' => 'baz'),
            array('message' => 'bat')
        );

        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);

        $result = $this->helper->__invoke('partialLoopCouter.phtml', $data);
        foreach ($data as $key=>$item) {
            $string = 'This is an iteration: ' . $item['message'] . ', pointer at ' . ($key+1);
            $this->assertContains($string, $result);
        }

        $result = $this->helper->__invoke('partialLoopCouter.phtml', $data);
        foreach ($data as $key=>$item) {
            $string = 'This is an iteration: ' . $item['message'] . ', pointer at ' . ($key+1);
            $this->assertContains($string, $result);
        }
    }
}

class IteratorTest implements Iterator
{
    public $items;

    public function __construct(array $array)
    {
        $this->items = $array;
    }

    public function current()
    {
        return current($this->items);
    }

    public function key()
    {
        return key($this->items);
    }

    public function next()
    {
        return next($this->items);
    }

    public function rewind()
    {
        return reset($this->items);
    }

    public function valid()
    {
        return (current($this->items) !== false);
    }

    public function toArray()
    {
        return $this->items;
    }
}

class RecursiveIteratorTest implements Iterator
{
    public $items;

    public function __construct()
    {
        $this->items = array();
    }

    public function addItem(Iterator $iterator)
    {
        $this->items[] = $iterator;
        return $this;
    }

    public function current()
    {
        return current($this->items);
    }

    public function key()
    {
        return key($this->items);
    }

    public function next()
    {
        return next($this->items);
    }

    public function rewind()
    {
        return reset($this->items);
    }

    public function valid()
    {
        return (current($this->items) !== false);
    }
}

class BogusIteratorTest
{
}

class ToArrayTest
{
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function toArray()
    {
        return $this->data;
    }
}

class IteratorWithToArrayTest implements Iterator
{
    public $items;

    public function __construct(array $array)
    {
        $this->items = $array;
    }

    public function toArray()
    {
        return $this->items;
    }

    public function current()
    {
        return current($this->items);
    }

    public function key()
    {
        return key($this->items);
    }

    public function next()
    {
        return next($this->items);
    }

    public function rewind()
    {
        return reset($this->items);
    }

    public function valid()
    {
        return (current($this->items) !== false);
    }
}

class IteratorWithToArrayTestContainer
{
    protected $_info;

    public function __construct(array $info)
    {
        foreach ($info as $key => $value) {
            $this->$key = $value;
        }
        $this->_info = $info;
    }

    public function toArray()
    {
        return $this->_info;
    }
}

