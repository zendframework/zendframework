<?php
// Call Zend_View_Helper_PartialTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_PartialTest::main");
}

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/TestHelper.php';
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

/** Zend_View_Helper_Partial */
require_once 'Zend/View/Helper/Partial.php';

/** Zend_View */
require_once 'Zend/View.php';

/** Zend_Controller_Front */
require_once 'Zend/Controller/Front.php';

/**
 * Test class for Zend_View_Helper_Partial.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 */
class Zend_View_Helper_PartialTest extends PHPUnit_Framework_TestCase 
{
    /**
     * @var Zend_View_Helper_Partial
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

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_PartialTest");
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
        $this->helper = new Zend_View_Helper_Partial();
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
    public function testPartialRendersScript()
    {
        $view = new Zend_View(array(
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ));
        $this->helper->setView($view);
        $return = $this->helper->partial('partialOne.phtml');
        $this->assertContains('This is the first test partial', $return);
    }

    /**
     * @return void
     */
    public function testPartialRendersScriptWithVars()
    {
        $view = new Zend_View(array(
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ));
        $view->message = 'This should never be read';
        $this->helper->setView($view);
        $return = $this->helper->partial('partialThree.phtml', array('message' => 'This message should be read'));
        $this->assertNotContains($view->message, $return);
        $this->assertContains('This message should be read', $return, $return);
    }

    /**
     * @return void
     */
    public function testPartialRendersScriptInDifferentModuleWhenRequested()
    {
        Zend_Controller_Front::getInstance()->addModuleDirectory($this->basePath);
        $view = new Zend_View(array(
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ));
        $this->helper->setView($view);
        $return = $this->helper->partial('partialTwo.phtml', 'foo');
        $this->assertContains('This is the second partial', $return, $return);
    }

    /**
     * @return void
     */
    public function testPartialThrowsExceptionWithInvalidModule()
    {
        Zend_Controller_Front::getInstance()->addModuleDirectory($this->basePath);
        $view = new Zend_View(array(
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ));
        $this->helper->setView($view);

        try {
            $return = $this->helper->partial('partialTwo.phtml', 'barbazbat');
            $this->fail('Partial should throw exception if module does not exist');
        } catch (Exception $e) {
        }
    }

    /**
     * @return void
     */
    public function testSetViewSetsViewProperty()
    {
        $view = new Zend_View();
        $this->helper->setView($view);
        $this->assertSame($view, $this->helper->view);
    }

    /**
     * @return void
     */
    public function testCloneViewReturnsDifferentViewInstance()
    {
        $view = new Zend_View();
        $this->helper->setView($view);
        $clone = $this->helper->cloneView();
        $this->assertNotSame($view, $clone);
        $this->assertTrue($clone instanceof Zend_View);
    }

    /**
     * @return void
     */
    public function testCloneViewClearsViewVariables()
    {
        $view = new Zend_View();
        $view->foo = 'bar';
        $this->helper->setView($view);

        $clone = $this->helper->cloneView();
        $clonedVars = $clone->getVars();

        $this->assertTrue(empty($clonedVars));
        $this->assertNull($clone->foo);
    }

    public function testObjectModelWithPublicPropertiesSetsViewVariables()
    {
        $model = new stdClass();
        $model->foo = 'bar';
        $model->bar = 'baz';

        $view = new Zend_View(array(
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ));
        $this->helper->setView($view);
        $return = $this->helper->partial('partialVars.phtml', $model);

        foreach (get_object_vars($model) as $key => $value) {
            $string = sprintf('%s: %s', $key, $value);
            $this->assertContains($string, $return);
        }
    }

    public function testObjectModelWithToArraySetsViewVariables()
    {
        $model = new Zend_View_Helper_PartialTest_Aggregate();

        $view = new Zend_View(array(
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ));
        $this->helper->setView($view);
        $return = $this->helper->partial('partialVars.phtml', $model);

        foreach ($model->toArray() as $key => $value) {
            $string = sprintf('%s: %s', $key, $value);
            $this->assertContains($string, $return);
        }
    }

    public function testObjectModelSetInObjectKeyWhenKeyPresent()
    {
        $this->helper->setObjectKey('foo');
        $model = new stdClass();
        $model->footest = 'bar';
        $model->bartest = 'baz';

        $view = new Zend_View(array(
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ));
        $this->helper->setView($view);
        $return = $this->helper->partial('partialObj.phtml', $model);

        $this->assertNotContains('No object model passed', $return);

        foreach (get_object_vars($model) as $key => $value) {
            $string = sprintf('%s: %s', $key, $value);
            $this->assertContains($string, $return);
        }
    }

    public function testPassingNoArgsReturnsHelperInstance()
    {
        $test = $this->helper->partial();
        $this->assertSame($this->helper, $test);
    }

    public function testObjectKeyIsNullByDefault()
    {
        $this->assertNull($this->helper->getObjectKey());
    }

    public function testCanSetObjectKey()
    {
        $this->testObjectKeyIsNullByDefault();
        $this->helper->setObjectKey('foo');
        $this->assertEquals('foo', $this->helper->getObjectKey());
    }

    public function testCanSetObjectKeyToNullValue()
    {
        $this->testCanSetObjectKey();
        $this->helper->setObjectKey(null);
        $this->assertNull($this->helper->getObjectKey());
    }

    public function testSetObjectKeyImplementsFluentInterface()
    {
        $test = $this->helper->setObjectKey('foo');
        $this->assertSame($this->helper, $test);
    }
}

class Zend_View_Helper_PartialTest_Aggregate
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

// Call Zend_View_Helper_PartialTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_PartialTest::main") {
    Zend_View_Helper_PartialTest::main();
}
