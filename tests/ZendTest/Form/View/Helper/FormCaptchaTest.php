<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ZendTest\Form\View\Helper;

use DirectoryIterator;
use Zend\Captcha;
use Zend\Form\Element\Captcha as CaptchaElement;
use Zend\Form\View\Helper\FormCaptcha as FormCaptchaHelper;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 */
class FormCaptchaTest extends CommonTestCase
{
    protected $publicKey  = TESTS_ZEND_FORM_RECAPTCHA_PUBLIC_KEY;
    protected $privateKey = TESTS_ZEND_FORM_RECAPTCHA_PRIVATE_KEY;
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
        $element->setAttribute('id', 'foo');
        $markup = $this->helper->render($element);
        $this->assertContains($captcha->getLabel(), $markup);
        $this->assertRegExp('#<[^>]*(id="' . $element->getAttribute('id') . '")[^>]*(type="text")[^>]*>#', $markup);
        $this->assertRegExp('#<[^>]*(id="' . $element->getAttribute('id') . '-hidden")[^>]*(type="hidden")[^>]*>#', $markup);
    }

    public function testPassingElementWithFigletCaptchaRendersCorrectly()
    {
        $captcha = new Captcha\Figlet(array(
            'sessionClass' => 'ZendTest\Captcha\TestAsset\SessionContainer',
        ));
        $element = $this->getElement();
        $element->setCaptcha($captcha);
        $element->setAttribute('id', 'foo');
        $markup = $this->helper->render($element);
        $this->assertContains('<pre>' . $captcha->getFiglet()->render($captcha->getWord()) . '</pre>', $markup);
        $this->assertRegExp('#<[^>]*(id="' . $element->getAttribute('id') . '")[^>]*(type="text")[^>]*>#', $markup);
        $this->assertRegExp('#<[^>]*(id="' . $element->getAttribute('id') . '-hidden")[^>]*(type="hidden")[^>]*>#', $markup);
    }

    public function testPassingElementWithImageCaptchaRendersCorrectly()
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

        $captcha = new Captcha\Image(array(
            'sessionClass' => 'ZendTest\Captcha\TestAsset\SessionContainer',
            'imgDir'       => $this->testDir,
            'font'         => __DIR__. '/Captcha/_files/Vera.ttf',
        ));
        $element = $this->getElement();
        $element->setCaptcha($captcha);
        $element->setAttribute('id', 'foo');

        $markup = $this->helper->render($element);

        $this->assertContains('<img ', $markup);
        $this->assertContains($captcha->getImgUrl(), $markup);
        $this->assertContains($captcha->getId(), $markup);
        $this->assertRegExp('#<img[^>]*(id="' . $element->getAttribute('id') . '-image")[^>]*>#', $markup);
        $this->assertRegExp('#<input[^>]*(id="' . $element->getAttribute('id') . '")[^>]*(type="text")[^>]*>#', $markup);
        $this->assertRegExp('#<input[^>]*(id="' . $element->getAttribute('id') . '-hidden")[^>]*(type="hidden")[^>]*>#', $markup);
    }

    public function testPassingElementWithReCaptchaRendersCorrectly()
    {
        if (!constant('TESTS_ZEND_FORM_RECAPTCHA_SUPPORT')) {
            $this->markTestSkipped('Enable TESTS_ZEND_FORM_RECAPTCHA_SUPPORT to test PDF render');
        }

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
