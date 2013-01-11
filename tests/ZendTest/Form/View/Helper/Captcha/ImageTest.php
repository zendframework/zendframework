<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ZendTest\Form\View\Helper\Captcha;

use DirectoryIterator;
use Zend\Captcha\Image as ImageCaptcha;
use Zend\Form\Element\Captcha as CaptchaElement;
use Zend\Form\View\Helper\Captcha\Image as ImageCaptchaHelper;
use ZendTest\Form\View\Helper\CommonTestCase;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
 */
class ImageTest extends CommonTestCase
{
    protected $tmpDir;
    protected $testDir;

    public function setUp()
    {
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('The GD extension is not available.');
            return;
        }
        if (!function_exists("imagepng")) {
            $this->markTestSkipped("Image CAPTCHA requires PNG support");
        }
        if (!function_exists("imageftbbox")) {
            $this->markTestSkipped("Image CAPTCHA requires FT fonts support");
        }

        $this->testDir = $this->getTmpDir() . '/ZF_test_images';
        if (!is_dir($this->testDir)) {
            @mkdir($this->testDir);
        }


        $this->helper  = new ImageCaptchaHelper();
        $this->captcha = new ImageCaptcha(array(
            'sessionClass' => 'ZendTest\Captcha\TestAsset\SessionContainer',
            'imgDir'       => $this->testDir,
            'font'         => __DIR__. '/_files/Vera.ttf',
        ));
        parent::setUp();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        // remove captcha images
        if (!isset($this->testDir)) {
            parent::tearDown();
            return;
        }

        foreach (new DirectoryIterator($this->testDir) as $file) {
            if (!$file->isDot() && !$file->isDir()) {
                unlink($file->getPathname());
            }
        }
        parent::tearDown();
    }

    /**
     * Determine system TMP directory
     *
     * @return string
     * @throws Zend_File_Transfer_Exception if unable to determine directory
     */
    protected function getTmpDir()
    {
        if (null === $this->tmpDir) {
            $this->tmpDir = sys_get_temp_dir();
        }
        return $this->tmpDir;
    }

    public function getElement()
    {
        $element = new CaptchaElement('foo');
        $element->setCaptcha($this->captcha);
        return $element;
    }

    public function testMissingCaptchaAttributeThrowsDomainException()
    {
        $element = new CaptchaElement('foo');

        $this->setExpectedException('Zend\Form\Exception\DomainException');
        $this->helper->render($element);
    }

    public function testRendersHiddenInputForId()
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertRegExp('#(name="' . $element->getName() . '\[id\]").*?(type="hidden")#', $markup);
        $this->assertRegExp('#(name="' . $element->getName() . '\[id\]").*?(value="' . $this->captcha->getId() . '")#', $markup);
    }

    public function testRendersTextInputForInput()
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertRegExp('#(name="' . $element->getName() . '\[input\]").*?(type="text")#', $markup);
    }

    public function testRendersImageTagPriorToInputByDefault()
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertRegexp('#<img[^>]+><input#', $markup);
    }

    public function testCanRenderImageTagFollowingInput()
    {
        $this->helper->setCaptchaPosition('prepend');
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertRegexp('#<input[^>]+><img#', $markup);
    }

}
