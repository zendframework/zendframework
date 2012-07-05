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
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Form\View\Helper;

use DirectoryIterator;
use Zend\Captcha;
use Zend\Form\Element\Captcha as CaptchaElement;
use Zend\Form\View\Helper\FormCaptcha as FormCaptchaHelper;
use Zend\View\Renderer\PhpRenderer;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FormCaptchaTest extends CommonTestCase
{
    protected $publicKey  = TESTS_ZEND_SERVICE_RECAPTCHA_PUBLIC_KEY;
    protected $privateKey = TESTS_ZEND_SERVICE_RECAPTCHA_PRIVATE_KEY;
    protected $testDir    = null;
    protected $tmpDir     = null;

    public function setUp()
    {
        $this->helper = new FormCaptchaHelper();
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
        if (null !== $this->testDir) {
            foreach (new DirectoryIterator($this->testDir) as $file) {
                if (!$file->isDot() && !$file->isDir()) {
                    unlink($file->getPathname());
                }
            }
        }
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
        return $element;
    }

    public function testRaisesExceptionIfElementHasNoCaptcha()
    {
        $element = $this->getElement();
        $this->setExpectedException('Zend\Form\Exception\ExceptionInterface', 'captcha');
        $this->helper->render($element);
    }

    public function testPassingElementWithDumbCaptchaRendersCorrectly()
    {
        $captcha = new Captcha\Dumb(array(
            'sessionClass' => 'ZendTest\Captcha\TestAsset\SessionContainer',
        ));
        $element = $this->getElement();
        $element->setCaptcha($captcha);
        $markup = $this->helper->render($element);
        $this->assertContains($captcha->getLabel(), $markup);
    }

    public function testPassingElementWithFigletCaptchaRendersCorrectly()
    {
        $captcha = new Captcha\Figlet(array(
            'sessionClass' => 'ZendTest\Captcha\TestAsset\SessionContainer',
        ));
        $element = $this->getElement();
        $element->setCaptcha($captcha);
        $markup = $this->helper->render($element);
        $this->assertContains('<pre>' . $captcha->getFiglet()->render($captcha->getWord()) . '</pre>', $markup);
    }

    public function testPassingElementWithImageCaptchaRendersCorrectly()
    {
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('The GD extension is not available.');
            return;
        }
        if(!function_exists("imagepng")) {
            $this->markTestSkipped("Image CAPTCHA requires PNG support");
        }
        if(!function_exists("imageftbbox")) {
            $this->markTestSkipped("Image CAPTCHA requires FT fonts support");
        }

        $this->testDir = $this->getTmpDir() . '/ZF_test_images';
        if (!is_dir($this->testDir)) {
            @mkdir($this->testDir);
        }

        $captcha = new Captcha\Image(array(
            'sessionClass' => 'ZendTest\Captcha\TestAsset\SessionContainer',
            'imgDir'       => $this->testDir,
            'font'         => __DIR__. '/../../../Pdf/_fonts/Vera.ttf',
        ));
        $element = $this->getElement();
        $element->setCaptcha($captcha);
        $markup = $this->helper->render($element);
        $this->assertContains('<img ', $markup);
        $this->assertContains($captcha->getImgUrl(), $markup);
        $this->assertContains($captcha->getId(), $markup);
    }

    public function testPassingElementWithReCaptchaRendersCorrectly()
    {
        $captcha = new Captcha\ReCaptcha(array(
            'sessionClass' => 'ZendTest\Captcha\TestAsset\SessionContainer',
        ));
        $service = $captcha->getService();
        $service->setPublicKey($this->publicKey);
        $service->setPrivateKey($this->privateKey);

        $element = $this->getElement();
        $element->setCaptcha($captcha);
        $markup = $this->helper->render($element);
        $this->assertContains('foo-challenge', $markup);
        $this->assertContains('foo-response', $markup);
        $this->assertContains('foo[recaptcha_challenge_field]', $markup);
        $this->assertContains('foo[recaptcha_response_field]', $markup);
        $this->assertContains('zendBindEvent', $markup);
        $this->assertContains($service->getHtml('foo'), $markup);
    }
}
