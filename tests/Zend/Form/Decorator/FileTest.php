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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Form\Decorator;

use Zend\Form\Decorator\File as FileDecorator,
    Zend\Form\Decorator\AbstractDecorator,
    Zend\Form\Element\File as FileElement,
    Zend\Form\Element,
    Zend\View\Renderer\PhpRenderer as View;

/**
 * Test class for Zend_Form_Decorator_Errors
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->decorator = new FileDecorator();
    }

    /**
     * This test is obsolete, as a view is always lazy-loaded
     *
     * @group disable
     */
    public function testRenderReturnsInitialContentIfNoViewPresentInElement()
    {
        $element = new FileElement('foo');
        $this->decorator->setElement($element);
        $content = 'test content';
        $this->assertSame($content, $this->decorator->render($content));
    }

    public function getView()
    {
        $view = new View();
        return $view;
    }

    public function setupSingleElement()
    {
        $element = new FileElement('foo');
        $element->addValidator('Count', 1)
                ->setView($this->getView());
        $this->element = $element;
        $this->decorator->setElement($element);
    }

    public function setupMultiElement()
    {
        $element = new FileElement('foo');
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

        $element = new FileElement('foo');
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
        $this->assertEquals(AbstractDecorator::APPEND, $this->decorator->getPlacement());
    }

    /**
     * Test is obsolete as view is now lazy-loaded
     * @group disable
     */
    public function testRenderReturnsOriginalContentWhenNoViewPresentInElement()
    {
        $element = new Element('foo');
        $this->decorator->setElement($element);
        $content = 'test content';
        $this->assertSame($content, $this->decorator->render($content));
    }

    public function testCanPrependFileToContent()
    {
        $element = new FileElement('foo');
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
