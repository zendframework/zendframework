<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Text
 */

namespace ZendTest\Text;

use Zend\Text\Figlet;


/**
 * @category   Zend
 * @package    Zend_Text
 * @subpackage UnitTests
 * @group      Zend_Text
 */
class FigletTest extends \PHPUnit_Framework_TestCase
{

    public function testStandardAlignLeft()
    {
        $figlet = new Figlet\Figlet();

        $this->_equalAgainstFile($figlet->render('Dummy'), 'StandardAlignLeft.figlet');
    }

    public function testStandardAlignCenter()
    {
        $figlet = new Figlet\Figlet(array('justification' => Figlet\Figlet::JUSTIFICATION_CENTER));

        $this->_equalAgainstFile($figlet->render('Dummy'), 'StandardAlignCenter.figlet');
    }

    public function testStandardAlignRight()
    {
        $figlet = new Figlet\Figlet(array('justification' => Figlet\Figlet::JUSTIFICATION_RIGHT));

        $this->_equalAgainstFile($figlet->render('Dummy'), 'StandardAlignRight.figlet');
    }

    public function testStandardRightToLeftAlignLeft()
    {
        $figlet = new Figlet\Figlet(array('justification' => Figlet\Figlet::JUSTIFICATION_LEFT,
                                             'rightToLeft'   => Figlet\Figlet::DIRECTION_RIGHT_TO_LEFT));

        $this->_equalAgainstFile($figlet->render('Dummy'), 'StandardRightToLeftAlignLeft.figlet');
    }

    public function testStandardRightToLeftAlignCenter()
    {
        $figlet = new Figlet\Figlet(array('justification' => Figlet\Figlet::JUSTIFICATION_CENTER,
                                             'rightToLeft'   => Figlet\Figlet::DIRECTION_RIGHT_TO_LEFT));

        $this->_equalAgainstFile($figlet->render('Dummy'), 'StandardRightToLeftAlignCenter.figlet');
    }

    public function testStandardRightToLeftAlignRight()
    {
        $figlet = new Figlet\Figlet(array('rightToLeft' => Figlet\Figlet::DIRECTION_RIGHT_TO_LEFT));

        $this->_equalAgainstFile($figlet->render('Dummy'), 'StandardRightToLeftAlignRight.figlet');
    }

    public function testWrongParameter()
    {
        $figlet = new Figlet\Figlet();

        $this->setExpectedException('Zend\Text\Figlet\Exception\InvalidArgumentException', 'must be a string');
        $figlet->render(1);
    }

    public function testCorrectEncodingUTF8()
    {
        $figlet = new Figlet\Figlet();

        $this->_equalAgainstFile($figlet->render('Ömläüt'), 'CorrectEncoding.figlet');
    }

    public function testCorrectEncodingISO885915()
    {
        if (PHP_OS == 'AIX') {
            $this->markTestSkipped('Test case cannot run on AIX');
        }

        $figlet = new Figlet\Figlet();

        $isoText = iconv('UTF-8', 'ISO-8859-15', 'Ömläüt');
        $this->_equalAgainstFile($figlet->render($isoText, 'ISO-8859-15'), 'CorrectEncoding.figlet');
    }

    public function testIncorrectEncoding()
    {
        $this->setExpectedException('Zend\Text\Figlet\Exception\UnexpectedValueException',
                                    'text is not encoded with UTF-8');
        $isoText = iconv('UTF-8', 'ISO-8859-15', 'Ömläüt');

        $figlet  = new Figlet\Figlet();
        $figlet->render($isoText);
    }

    public function testNonExistentFont()
    {
        $this->setExpectedException('Zend\Text\Figlet\Exception\RuntimeException', 'not found');
        $figlet = new Figlet\Figlet(array('font' => __DIR__ . '/Figlet/NonExistentFont.flf'));

    }

    public function testInvalidFont()
    {
        $this->setExpectedException('Zend\Text\Figlet\Exception\UnexpectedValueException', 'Not a FIGlet');
        $figlet = new Figlet\Figlet(array('font' => __DIR__ . '/Figlet/InvalidFont.flf'));
    }

    public function testGzippedFont()
    {
        $figlet = new Figlet\Figlet(array('font' => __DIR__ . '/Figlet/GzippedFont.gz'));
        $this->_equalAgainstFile($figlet->render('Dummy'), 'StandardAlignLeft.figlet');
    }

    public function testConfig()
    {
        $config = new \Zend\Config\Config(array('justification' => Figlet\Figlet::JUSTIFICATION_RIGHT));
        $figlet = new Figlet\Figlet($config);

        $this->_equalAgainstFile($figlet->render('Dummy'), 'StandardAlignRight.figlet');
    }

    public function testOutputWidth()
    {
        $figlet = new Figlet\Figlet(array('outputWidth'   => 50,
                                             'justification' => Figlet\Figlet::JUSTIFICATION_RIGHT));

        $this->_equalAgainstFile($figlet->render('Dummy'), 'OutputWidth50AlignRight.figlet');
    }

    public function testSmushModeRemoved()
    {
        $figlet = new Figlet\Figlet(array('smushMode' => -1));

        $this->_equalAgainstFile($figlet->render('Dummy'), 'NoSmush.figlet');
    }

    public function testSmushModeRemovedRightToLeft()
    {
        $figlet = new Figlet\Figlet(array('smushMode'     => -1,
                                             'rightToLeft'   => Figlet\Figlet::DIRECTION_RIGHT_TO_LEFT));

        $this->_equalAgainstFile($figlet->render('Dummy'), 'NoSmushRightToLeft.figlet');
    }

    public function testSmushModeInvalid()
    {
        $figlet = new Figlet\Figlet(array('smushMode' => -5));

        $this->_equalAgainstFile($figlet->render('Dummy'), 'StandardAlignLeft.figlet');
    }

    public function testSmushModeTooSmall()
    {
        $figlet = new Figlet\Figlet(array('smushMode' => -2));

        $this->_equalAgainstFile($figlet->render('Dummy'), 'StandardAlignLeft.figlet');
    }

    public function testSmushModeDefault()
    {
        $figlet = new Figlet\Figlet(array('smushMode' => 0));

        $this->_equalAgainstFile($figlet->render('Dummy'), 'SmushDefault.figlet');
    }

    public function testSmushModeForced()
    {
        $figlet = new Figlet\Figlet(array('smushMode' => 5));

        $this->_equalAgainstFile($figlet->render('Dummy'), 'SmushForced.figlet');
    }

    public function testWordWrapLeftToRight()
    {
        $figlet = new Figlet\Figlet();

        $this->_equalAgainstFile($figlet->render('Dummy Dummy Dummy'), 'WordWrapLeftToRight.figlet');
    }

    public function testWordWrapRightToLeft()
    {
        $figlet = new Figlet\Figlet(array('rightToLeft' => Figlet\Figlet::DIRECTION_RIGHT_TO_LEFT));

        $this->_equalAgainstFile($figlet->render('Dummy Dummy Dummy'), 'WordWrapRightToLeft.figlet');
    }

    public function testCharWrapLeftToRight()
    {
        $figlet = new Figlet\Figlet();

        $this->_equalAgainstFile($figlet->render('DummyDumDummy'), 'CharWrapLeftToRight.figlet');
    }

    public function testCharWrapRightToLeft()
    {
        $figlet = new Figlet\Figlet(array('rightToLeft' => Figlet\Figlet::DIRECTION_RIGHT_TO_LEFT));

        $this->_equalAgainstFile($figlet->render('DummyDumDummy'), 'CharWrapRightToLeft.figlet');
    }

    public function testParagraphOff()
    {
        $figlet = new Figlet\Figlet();

        $this->_equalAgainstFile($figlet->render("Dum\nDum\n\nDum\n"), 'ParagraphOff.figlet');
    }

    public function testParagraphOn()
    {
        $figlet = new Figlet\Figlet(array('handleParagraphs' => true));

        $this->_equalAgainstFile($figlet->render("Dum\nDum\n\nDum\n"), 'ParagraphOn.figlet');
    }

    public function testEmptyString()
    {
        $figlet = new Figlet\Figlet();

        $this->assertEquals('', $figlet->render(''));
    }

    protected function _equalAgainstFile($output, $file)
    {
        $compareString = file_get_contents(__DIR__ . '/Figlet/' . $file);

        $this->assertEquals($compareString, $output);
    }
}
