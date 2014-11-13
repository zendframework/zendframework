<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form\View\Helper\Captcha;

use Zend\Captcha\Figlet as FigletCaptcha;
use Zend\Form\Element\Captcha as CaptchaElement;
use Zend\Form\View\Helper\Captcha\Figlet as FigletCaptchaHelper;
use ZendTest\Form\View\Helper\CommonTestCase;

class FigletTest extends CommonTestCase
{
    public function setUp()
    {
        $this->helper  = new FigletCaptchaHelper();
        $this->captcha = new FigletCaptcha(array(
            'sessionClass' => 'ZendTest\Captcha\TestAsset\SessionContainer',
        ));
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

    public function testRendersHiddenInputForId()
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertRegExp('#(name="' . $element->getName() . '\&\#x5B\;id\&\#x5D\;").*?(type="hidden")#', $markup);
        $this->assertRegExp('#(name="' . $element->getName() . '\&\#x5B\;id\&\#x5D\;").*?(value="' . $this->captcha->getId() . '")#', $markup);
    }

    public function testRendersTextInputForInput()
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertRegExp('#(name="' . $element->getName() . '\&\#x5B\;input\&\#x5D\;").*?(type="text")#', $markup);
    }

    public function testRendersFigletPriorToInputByDefault()
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertContains('<pre>' . $this->captcha->getFiglet()->render($this->captcha->getWord()) . '</pre>' . $this->helper->getSeparator() . '<input', $markup);
    }

    public function testCanRenderFigletFollowingInput()
    {
        $this->helper->setCaptchaPosition('prepend');
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertContains('><pre>', $markup);
    }

}
