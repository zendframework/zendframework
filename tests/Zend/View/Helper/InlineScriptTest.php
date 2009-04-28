<?php
// Call Zend_View_Helper_InlineScriptTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_InlineScriptTest::main");
}

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/TestHelper.php';
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

/** Zend_View_Helper_InlineScript */
require_once 'Zend/View/Helper/InlineScript.php';

/** Zend_View_Helper_Placeholder_Registry */
require_once 'Zend/View/Helper/Placeholder/Registry.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/**
 * Test class for Zend_View_Helper_InlineScript.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 */
class Zend_View_Helper_InlineScriptTest extends PHPUnit_Framework_TestCase 
{
    /**
     * @var Zend_View_Helper_InlineScript
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

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_InlineScriptTest");
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
        $regKey = Zend_View_Helper_Placeholder_Registry::REGISTRY_KEY;
        if (Zend_Registry::isRegistered($regKey)) {
            $registry = Zend_Registry::getInstance();
            unset($registry[$regKey]);
        }
        $this->basePath = dirname(__FILE__) . '/_files/modules';
        $this->helper = new Zend_View_Helper_InlineScript();
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

    public function testNamespaceRegisteredInPlaceholderRegistryAfterInstantiation()
    {
        $registry = Zend_View_Helper_Placeholder_Registry::getRegistry();
        if ($registry->containerExists('Zend_View_Helper_InlineScript')) {
            $registry->deleteContainer('Zend_View_Helper_InlineScript');
        }
        $this->assertFalse($registry->containerExists('Zend_View_Helper_InlineScript'));
        $helper = new Zend_View_Helper_InlineScript();
        $this->assertTrue($registry->containerExists('Zend_View_Helper_InlineScript'));
    }

    public function testInlineScriptReturnsObjectInstance()
    {
        $placeholder = $this->helper->inlineScript();
        $this->assertTrue($placeholder instanceof Zend_View_Helper_InlineScript);
    }
}

// Call Zend_View_Helper_InlineScriptTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_InlineScriptTest::main") {
    Zend_View_Helper_InlineScriptTest::main();
}
