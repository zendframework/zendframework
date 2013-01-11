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

use Zend\Captcha\ReCaptcha;
use Zend\Form\Element\Captcha as CaptchaElement;
use Zend\Form\View\Helper\Captcha\ReCaptcha as ReCaptchaHelper;
use ZendTest\Form\View\Helper\CommonTestCase;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
 */
class ReCaptchaTest extends CommonTestCase
{
    protected $publicKey  = TESTS_ZEND_FORM_RECAPTCHA_PUBLIC_KEY;
    protected $privateKey = TESTS_ZEND_FORM_RECAPTCHA_PRIVATE_KEY;

    public function setUp()
    {
        if (!constant('TESTS_ZEND_FORM_RECAPTCHA_SUPPORT')) {
            $this->markTestSkipped('Enable TESTS_ZEND_FORM_RECAPTCHA_SUPPORT to test PDF render');
        }

        $this->helper  = new ReCaptchaHelper();
        $this->captcha = new ReCaptcha(array(
            'sessionClass' => 'ZendTest\Captcha\TestAsset\SessionContainer',
        ));
        $service = $this->captcha->getService();
        $service->setPublicKey($this->publicKey);
        $service->setPrivateKey($this->privateKey);
        parent::setUp();
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

    public function testRendersHiddenInputForChallengeField()
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertRegExp('#(type="hidden").*?(name="' . $element->getName() . '\[recaptcha_challenge_field\]")#', $markup);
        $this->assertRegExp('#(type="hidden").*?(id="' . $element->getName() . '-challenge")#', $markup);
    }

    public function testRendersHiddenInputForResponseField()
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertRegExp('#(type="hidden").*?(name="' . $element->getName() . '\[recaptcha_response_field\]")#', $markup);
        $this->assertRegExp('#(type="hidden").*?(id="' . $element->getName() . '-response")#', $markup);
    }

    public function testRendersReCaptchaMarkup()
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertContains($this->captcha->getService()->getHtml($element->getName()), $markup);
    }

    public function testRendersJsEventScripts()
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertContains('function zendBindEvent', $markup);
        $this->assertContains('document.getElementById("' . $element->getName() . '-challenge")', $markup);
        $this->assertContains('document.getElementById("' . $element->getName() . '-response")', $markup);
    }
}
