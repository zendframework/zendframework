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
 * @package    Zend_Text
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Text_FigletTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Text_FigletTest::main");
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * Zend_Text_Figlet
 */
require_once 'Zend/Text/Figlet.php';

/**
 * Zend_Config
 */
require_once 'Zend/Config.php';

/**
 * @category   Zend
 * @package    Zend_Text
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Text_FigletTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Text_FigletTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testStandardAlignLeft()
    {
        $figlet = new Zend_Text_Figlet();

        $this->_equalAgainstFile($figlet->render('Dummy'), 'StandardAlignLeft.figlet');
    }

    public function testStandardAlignCenter()
    {
        $figlet = new Zend_Text_Figlet(array('justification' => Zend_Text_Figlet::JUSTIFICATION_CENTER));

        $this->_equalAgainstFile($figlet->render('Dummy'), 'StandardAlignCenter.figlet');
    }

    public function testStandardAlignRight()
    {
        $figlet = new Zend_Text_Figlet(array('justification' => Zend_Text_Figlet::JUSTIFICATION_RIGHT));

        $this->_equalAgainstFile($figlet->render('Dummy'), 'StandardAlignRight.figlet');
    }

    public function testStandardRightToLeftAlignLeft()
    {
        $figlet = new Zend_Text_Figlet(array('justification' => Zend_Text_Figlet::JUSTIFICATION_LEFT,
                                             'rightToLeft'   => Zend_Text_Figlet::DIRECTION_RIGHT_TO_LEFT));

        $this->_equalAgainstFile($figlet->render('Dummy'), 'StandardRightToLeftAlignLeft.figlet');
    }

    public function testStandardRightToLeftAlignCenter()
    {
        $figlet = new Zend_Text_Figlet(array('justification' => Zend_Text_Figlet::JUSTIFICATION_CENTER,
                                             'rightToLeft'   => Zend_Text_Figlet::DIRECTION_RIGHT_TO_LEFT));

        $this->_equalAgainstFile($figlet->render('Dummy'), 'StandardRightToLeftAlignCenter.figlet');
    }

    public function testStandardRightToLeftAlignRight()
    {
        $figlet = new Zend_Text_Figlet(array('rightToLeft' => Zend_Text_Figlet::DIRECTION_RIGHT_TO_LEFT));

        $this->_equalAgainstFile($figlet->render('Dummy'), 'StandardRightToLeftAlignRight.figlet');
    }

    public function testWrongParameter()
    {
        $figlet = new Zend_Text_Figlet();

        try {
            $figlet->render(1);
            $this->fail('An expected InvalidArgumentException has not been raised');
        } catch (InvalidArgumentException $expected) {
            $this->assertContains('$text must be a string', $expected->getMessage());
        }
    }

    public function testCorrectEncodingUTF8()
    {
        $figlet = new Zend_Text_Figlet();

        $this->_equalAgainstFile($figlet->render('Ömläüt'), 'CorrectEncoding.figlet');
    }

    public function testCorrectEncodingISO885915()
    {
        if (PHP_OS == 'AIX') {
            $this->markTestSkipped('Test case cannot run on AIX');
        }
        
        $figlet = new Zend_Text_Figlet();

        $isoText = iconv('UTF-8', 'ISO-8859-15', 'Ömläüt');
        $this->_equalAgainstFile($figlet->render($isoText, 'ISO-8859-15'), 'CorrectEncoding.figlet');
    }

    /**
     * @expectedException Zend_Text_Figlet_Exception
     */
    public function testIncorrectEncoding()
    {
        $this->markTestSkipped('Test case not reproducible on all setups');
        $figlet  = new Zend_Text_Figlet();
        
        if (PHP_OS == 'AIX') {
            $isoText = iconv('UTF-8', 'ISO-8859-15', 'Ömläüt');
        } else {
            $isoText = iconv('UTF-8', 'ISO-8859-15', 'Ömläüt');
        }
        
        $figlet->render($isoText);
    }

    public function testNonExistentFont()
    {
        try {
            $figlet = new Zend_Text_Figlet(array('font' => dirname(__FILE__) . '/Figlet/NonExistentFont.flf'));
            $this->fail('An expected Zend_Text_Figlet_Exception has not been raised');
        } catch (Zend_Text_Figlet_Exception $expected) {
            $this->assertContains('Font file not found', $expected->getMessage());
        }
    }

    public function testInvalidFont()
    {
        try {
            $figlet = new Zend_Text_Figlet(array('font' => dirname(__FILE__) . '/Figlet/InvalidFont.flf'));
            $this->fail('An expected Zend_Text_Figlet_Exception has not been raised');
        } catch (Zend_Text_Figlet_Exception $expected) {
            $this->assertContains('Not a FIGlet 2 font file', $expected->getMessage());
        }
    }

    public function testGzippedFont()
    {
        $figlet = new Zend_Text_Figlet(array('font' => dirname(__FILE__) . '/Figlet/GzippedFont.gz'));
        $this->_equalAgainstFile($figlet->render('Dummy'), 'StandardAlignLeft.figlet');
    }

    public function testConfig()
    {
        $config = new Zend_Config(array('justification' => Zend_Text_Figlet::JUSTIFICATION_RIGHT));
        $figlet = new Zend_Text_Figlet($config);

        $this->_equalAgainstFile($figlet->render('Dummy'), 'StandardAlignRight.figlet');
    }

    public function testOutputWidth()
    {
        $figlet = new Zend_Text_Figlet(array('outputWidth'   => 50,
                                             'justification' => Zend_Text_Figlet::JUSTIFICATION_RIGHT));

        $this->_equalAgainstFile($figlet->render('Dummy'), 'OutputWidth50AlignRight.figlet');
    }

    public function testSmushModeRemoved()
    {
        $figlet = new Zend_Text_Figlet(array('smushMode' => -1));

        $this->_equalAgainstFile($figlet->render('Dummy'), 'NoSmush.figlet');
    }

    public function testSmushModeRemovedRightToLeft()
    {
        $figlet = new Zend_Text_Figlet(array('smushMode'     => -1,
                                             'rightToLeft'   => Zend_Text_Figlet::DIRECTION_RIGHT_TO_LEFT));

        $this->_equalAgainstFile($figlet->render('Dummy'), 'NoSmushRightToLeft.figlet');
    }

    public function testSmushModeInvalid()
    {
        $figlet = new Zend_Text_Figlet(array('smushMode' => -5));

        $this->_equalAgainstFile($figlet->render('Dummy'), 'StandardAlignLeft.figlet');
    }

    public function testSmushModeTooSmall()
    {
        $figlet = new Zend_Text_Figlet(array('smushMode' => -2));

        $this->_equalAgainstFile($figlet->render('Dummy'), 'StandardAlignLeft.figlet');
    }

    public function testSmushModeDefault()
    {
        $figlet = new Zend_Text_Figlet(array('smushMode' => 0));

        $this->_equalAgainstFile($figlet->render('Dummy'), 'SmushDefault.figlet');
    }

    public function testSmushModeForced()
    {
        $figlet = new Zend_Text_Figlet(array('smushMode' => 5));

        $this->_equalAgainstFile($figlet->render('Dummy'), 'SmushForced.figlet');
    }

    public function testWordWrapLeftToRight()
    {
        $figlet = new Zend_Text_Figlet();

        $this->_equalAgainstFile($figlet->render('Dummy Dummy Dummy'), 'WordWrapLeftToRight.figlet');
    }

    public function testWordWrapRightToLeft()
    {
        $figlet = new Zend_Text_Figlet(array('rightToLeft' => Zend_Text_Figlet::DIRECTION_RIGHT_TO_LEFT));

        $this->_equalAgainstFile($figlet->render('Dummy Dummy Dummy'), 'WordWrapRightToLeft.figlet');
    }

    public function testCharWrapLeftToRight()
    {
        $figlet = new Zend_Text_Figlet();

        $this->_equalAgainstFile($figlet->render('DummyDumDummy'), 'CharWrapLeftToRight.figlet');
    }

    public function testCharWrapRightToLeft()
    {
        $figlet = new Zend_Text_Figlet(array('rightToLeft' => Zend_Text_Figlet::DIRECTION_RIGHT_TO_LEFT));

        $this->_equalAgainstFile($figlet->render('DummyDumDummy'), 'CharWrapRightToLeft.figlet');
    }

    public function testParagraphOff()
    {
        $figlet = new Zend_Text_Figlet();

        $this->_equalAgainstFile($figlet->render("Dum\nDum\n\nDum\n"), 'ParagraphOff.figlet');
    }

    public function testParagraphOn()
    {
        $figlet = new Zend_Text_Figlet(array('handleParagraphs' => true));

        $this->_equalAgainstFile($figlet->render("Dum\nDum\n\nDum\n"), 'ParagraphOn.figlet');
    }

    public function testEmptyString()
    {
        $figlet = new Zend_Text_Figlet();
        
        $this->assertEquals('', $figlet->render(''));
    }
    
    protected function _equalAgainstFile($output, $file)
    {
        $compareString = file_get_contents(dirname(__FILE__) . '/Figlet/' . $file);

        $this->assertEquals($compareString, $output);
    }
}

// Call Zend_Text_FigletTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Text_FigletTest::main") {
    Zend_Text_FigletTest::main();
}
