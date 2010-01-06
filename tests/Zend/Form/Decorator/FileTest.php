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
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Form_Decorator_FileTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Form_Decorator_FileTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Form/Decorator/File.php';

require_once 'Zend/Form/Element/File.php';
require_once 'Zend/View.php';

/**
 * Test class for Zend_Form_Decorator_Errors
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class Zend_Form_Decorator_FileTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Form_Decorator_FileTest");
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
        $this->decorator = new Zend_Form_Decorator_File();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
    }

    public function testRenderReturnsInitialContentIfNoViewPresentInElement()
    {
        $element = new Zend_Form_Element_File('foo');
        $this->decorator->setElement($element);
        $content = 'test content';
        $this->assertSame($content, $this->decorator->render($content));
    }

    public function getView()
    {
        $view = new Zend_View();
        $view->addHelperPath(dirname(__FILE__) . '/../../../../library/Zend/View/Helper');
        return $view;
    }

    public function setupSingleElement()
    {
        $element = new Zend_Form_Element_File('foo');
        $element->addValidator('Count', 1)
                ->setView($this->getView());
        $this->element = $element;
        $this->decorator->setElement($element);
    }

    public function setupMultiElement()
    {
        $element = new Zend_Form_Element_File('foo');
        $element->addValidator('Count', 1)
                ->setMultiFile(2)
                ->setView($this->getView());
        $this->element = $element;
        $this->decorator->setElement($element);
    }

    public function testRenderSingleFiles()
    {
        $this->setupSingleElement();
        $test = $this->decorator->render(null);
        $this->assertRegexp('#foo#s', $test);
    }

    public function testRenderMultiFiles()
    {
        $this->setupMultiElement();
        $test = $this->decorator->render(null);
        $this->assertRegexp('#foo\[\]#s', $test);
    }

    public function setupElementWithMaxFileSize()
    {
        $max = $this->_convertIniToInteger(trim(ini_get('upload_max_filesize')));

        $element = new Zend_Form_Element_File('foo');
        $element->addValidator('Count', 1)
                ->setView($this->getView())
                ->setMaxFileSize($max);
        $this->element = $element;
        $this->decorator->setElement($element);
    }

    public function testRenderMaxFileSize()
    {
        $max = $this->_convertIniToInteger(trim(ini_get('upload_max_filesize')));

        $this->setupElementWithMaxFileSize();
        $test = $this->decorator->render(null);
        $this->assertRegexp('#MAX_FILE_SIZE#s', $test);
        $this->assertRegexp('#' . $max . '#s', $test);
    }

    public function testPlacementInitiallyAppends()
    {
        $this->assertEquals(Zend_Form_Decorator_Abstract::APPEND, $this->decorator->getPlacement());
    }

    public function testRenderReturnsOriginalContentWhenNoViewPresentInElement()
    {
        $element = new Zend_Form_Element('foo');
        $this->decorator->setElement($element);
        $content = 'test content';
        $this->assertSame($content, $this->decorator->render($content));
    }

    public function testCanPrependFileToContent()
    {
        $element = new Zend_Form_Element_File('foo');
        $element->setValue('foobar')
                ->setView($this->getView());
        $this->decorator->setElement($element)
                        ->setOption('placement', 'prepend');

        $file = $this->decorator->render('content');
        $this->assertRegexp('#<input[^>]*>.*?(content)#s', $file, $file);
    }

    private function _convertIniToInteger($setting)
    {
        if (!is_numeric($setting)) {
            $type = strtoupper(substr($setting, -1));
            $setting = (integer) substr($setting, 0, -1);

            switch ($type) {
                case 'M' :
                    $setting *= 1024;
                    break;

                case 'G' :
                    $setting *= 1024 * 1024;
                    break;

                default :
                    break;
            }
        }

        return (integer) $setting;
    }
}

// Call Zend_Form_Decorator_FileTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Form_Decorator_FileTest::main") {
    Zend_Form_Decorator_FileTest::main();
}
