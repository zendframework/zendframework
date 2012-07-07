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

namespace ZendTest\Form\View\Helper\Captcha;

use Zend\Captcha\ReCaptcha;
use Zend\Form\Element\Captcha as CaptchaElement;
use Zend\Form\View\Helper\Captcha\ReCaptcha as ReCaptchaHelper;
use Zend\Service\ReCaptcha\ReCaptcha as ReCaptchaService;
use ZendTest\Form\View\Helper\CommonTestCase;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ReCaptchaTest extends CommonTestCase
{
    protected $publicKey  = TESTS_ZEND_SERVICE_RECAPTCHA_PUBLIC_KEY;
    protected $privateKey = TESTS_ZEND_SERVICE_RECAPTCHA_PRIVATE_KEY;

    public function setUp()
    {
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

