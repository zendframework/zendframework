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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_View_Helper_PartialLoopTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_PartialLoopTest::main");
}

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/TestHelper.php';
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

/** Zend_View_Helper_PartialLoop */
require_once 'Zend/View/Helper/PartialLoop.php';

/** Zend_View */
require_once 'Zend/View.php';

/** Zend_Controller_Front */
require_once 'Zend/Controller/Front.php';

/**
 * Test class for Zend_View_Helper_PartialLoop.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class Zend_View_Helper_PartialLoopTest extends PHPUnit_Framework_TestCase
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
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_PartialLoopTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->basePath = dirname(__FILE__) . '/_files/modules';
        $this->helper = new Zend_View_Helper_PartialLoop();
        Zend_Controller_Front::getInstance()->resetInstance();
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

        $view = new Zend_View(array(
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ));
        $this->helper->setView($view);

        $result = $this->helper->partialLoop('partialLoop.phtml', $data);
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
        $o = new Zend_View_Helper_PartialLoop_IteratorTest($data);

        $view = new Zend_View(array(
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ));
        $this->helper->setView($view);

        $result = $this->helper->partialLoop('partialLoop.phtml', $o);
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
        $rIterator = new Zend_View_Helper_PartialLoop_RecursiveIteratorTest();
        for ($i = 0; $i < 5; ++$i) {
            $data = array(
                'message' => 'foo' . $i,
            );
            $rIterator->addItem(new Zend_View_Helper_PartialLoop_IteratorTest($data));
        }

        $view = new Zend_View(array(
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ));
        $this->helper->setView($view);

        $result = $this->helper->partialLoop('partialLoop.phtml', $rIterator);
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
        $o = new Zend_View_Helper_PartialLoop_BogusIteratorTest($data);

        $view = new Zend_View(array(
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ));
        $this->helper->setView($view);

        try {
            $result = $this->helper->partialLoop('partialLoop.phtml', $o);
            $this->fail('PartialLoop should only work with arrays and iterators');
        } catch (Exception $e) {
        }
    }

    /**
     * @return void
     */
    public function testPartialLoopFindsModule()
    {
        Zend_Controller_Front::getInstance()->addModuleDirectory($this->basePath);
        $data = array(
            array('message' => 'foo'),
            array('message' => 'bar'),
            array('message' => 'baz'),
            array('message' => 'bat')
        );

        $view = new Zend_View(array(
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ));
        $this->helper->setView($view);

        $result = $this->helper->partialLoop('partialLoop.phtml', 'foo', $data);
        foreach ($data as $item) {
            $string = 'This is an iteration in the foo module: ' . $item['message'];
            $this->assertContains($string, $result);
        }
    }

    public function testPassingNoArgsReturnsHelperInstance()
    {
        $test = $this->helper->partialLoop();
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

        $view = new Zend_View(array(
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ));
        $this->helper->setView($view);

        $result = $this->helper->partialLoop('partialLoop.phtml', $o);
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
        $o = new Zend_View_Helper_PartialLoop_ToArrayTest($data);

        $view = new Zend_View(array(
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ));
        $this->helper->setView($view);

        $result = $this->helper->partialLoop('partialLoop.phtml', $o);
        foreach ($data as $item) {
            $string = 'This is an iteration: ' . $item['message'];
            $this->assertContains($string, $result, $result);
        }
    }

    /**
     * @see ZF-3350
     * @see ZF-3352
     */
    public function testShouldNotCastToArrayIfObjectIsTraversable()
    {
        $data = array(
            new Zend_View_Helper_PartialLoop_IteratorWithToArrayTestContainer(array('message' => 'foo')),
            new Zend_View_Helper_PartialLoop_IteratorWithToArrayTestContainer(array('message' => 'bar')),
            new Zend_View_Helper_PartialLoop_IteratorWithToArrayTestContainer(array('message' => 'baz')),
            new Zend_View_Helper_PartialLoop_IteratorWithToArrayTestContainer(array('message' => 'bat')),
        );
        $o = new Zend_View_Helper_PartialLoop_IteratorWithToArrayTest($data);

        $view = new Zend_View(array(
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ));
        $this->helper->setView($view);
        $this->helper->setObjectKey('obj');

        $result = $this->helper->partialLoop('partialLoopObject.phtml', $o);
        foreach ($data as $item) {
            $string = 'This is an iteration: ' . $item->message;
            $this->assertContains($string, $result, $result);
        }
    }

    /**
     * @see ZF-3083
     */
    public function testEmptyArrayPassedToPartialLoopShouldNotThrowException()
    {
        $view = new Zend_View(array(
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ));
        $this->helper->setView($view);

        try {
            $result = $this->helper->partialLoop('partialLoop.phtml', array());
        } catch (Exception $e) {
            $this->fail('Empty array should not cause partialLoop to throw exception');
        }

        try {
            $result = $this->helper->partialLoop('partialLoop.phtml', null, array());
        } catch (Exception $e) {
            $this->fail('Empty array should not cause partialLoop to throw exception');
        }
    }

    /**
     * @see ZF-2737
     * @link http://framework.zend.com/issues/browse/ZF-2737
     */
    public function testPartialLoopIncramentsPartialCounter()
    {
        $data = array(
            array('message' => 'foo'),
            array('message' => 'bar'),
            array('message' => 'baz'),
            array('message' => 'bat')
        );

        $view = new Zend_View(array(
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ));
        $this->helper->setView($view);

        $result = $this->helper->partialLoop('partialLoopCouter.phtml', $data);
        foreach ($data as $key=>$item) {
            $string = 'This is an iteration: ' . $item['message'] . ', pointer at ' . ($key+1);
            $this->assertContains($string, $result);
        }
    }

    /**
     * @see ZF-5174
     * @link http://framework.zend.com/issues/browse/ZF-5174
     */
    public function testPartialLoopPartialCounterResets()
    {
        $data = array(
            array('message' => 'foo'),
            array('message' => 'bar'),
            array('message' => 'baz'),
            array('message' => 'bat')
        );

        $view = new Zend_View(array(
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ));
        $this->helper->setView($view);

        $result = $this->helper->partialLoop('partialLoopCouter.phtml', $data);
        foreach ($data as $key=>$item) {
            $string = 'This is an iteration: ' . $item['message'] . ', pointer at ' . ($key+1);
            $this->assertContains($string, $result);
        }

        $result = $this->helper->partialLoop('partialLoopCouter.phtml', $data);
        foreach ($data as $key=>$item) {
            $string = 'This is an iteration: ' . $item['message'] . ', pointer at ' . ($key+1);
            $this->assertContains($string, $result);
        }
    }
}

class Zend_View_Helper_PartialLoop_IteratorTest implements Iterator
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

class Zend_View_Helper_PartialLoop_RecursiveIteratorTest implements Iterator
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

class Zend_View_Helper_PartialLoop_BogusIteratorTest
{
}

class Zend_View_Helper_PartialLoop_ToArrayTest
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

class Zend_View_Helper_PartialLoop_IteratorWithToArrayTest implements Iterator
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

class Zend_View_Helper_PartialLoop_IteratorWithToArrayTestContainer
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

// Call Zend_View_Helper_PartialLoopTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_PartialLoopTest::main") {
    Zend_View_Helper_PartialLoopTest::main();
}
