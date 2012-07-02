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

namespace ZendTest\Form\Element;

use PHPUnit_Framework_TestCase as TestCase;
use ArrayObject;
use Zend\Captcha;
use Zend\Form\Element\Captcha as CaptchaElement;
use Zend\Form\Factory;

class CaptchaTest extends TestCase
{
    public function testCaptchaIsUndefinedByDefault()
    {
        $element = new CaptchaElement();
        $this->assertNull($element->getCaptcha());
    }

    public function testCaptchaIsMutable()
    {
        $element = new CaptchaElement();

        // by instance
        $captcha = new Captcha\Dumb(array(
            'sessionClass' => 'ZendTest\Captcha\TestAsset\SessionContainer',
        ));
        $element->setCaptcha($captcha);
        $this->assertSame($captcha, $element->getCaptcha());

        // by array
        $captcha = array(
            'class'   => 'dumb',
            'options' => array(
                'sessionClass' => 'ZendTest\Captcha\TestAsset\SessionContainer',
            )
        );
        $element->setCaptcha($captcha);
        $this->assertInstanceOf('Zend\Captcha\Dumb', $element->getCaptcha());

        // by traversable
        $captcha = new ArrayObject(array(
            'class'   => 'dumb',
            'options' => array(
                    'sessionClass' => 'ZendTest\Captcha\TestAsset\SessionContainer',
            )
        ));
        $element->setCaptcha($captcha);
        $this->assertInstanceOf('Zend\Captcha\Dumb', $element->getCaptcha());
    }

    public function testSettingCaptchaSetsCaptchaAttribute()
    {
        $element = new CaptchaElement();
        $captcha = new Captcha\Dumb(array(
            'sessionClass' => 'ZendTest\Captcha\TestAsset\SessionContainer',
        ));
        $element->setCaptcha($captcha);
        $this->assertSame($captcha, $element->getCaptcha());
    }

    public function testCreatingCaptchaElementViaFormFactoryWillCreateCaptcha()
    {
        $factory = new Factory();
        $element = $factory->createElement(array(
            'type'       => 'Zend\Form\Element\Captcha',
            'name'       => 'foo',
            'options'    => array(
                'captcha' => array(
                    'class'   => 'dumb',
                    'options' => array(
                        'sessionClass' => 'ZendTest\Captcha\TestAsset\SessionContainer',
                    )
                )
            )
        ));
        $this->assertInstanceOf('Zend\Form\Element\Captcha', $element);
        $captcha = $element->getCaptcha();
        $this->assertInstanceOf('Zend\Captcha\Dumb', $captcha);
    }

    public function testProvidesInputSpecificationThatIncludesCaptchaAsValidator()
    {
        $element = new CaptchaElement();
        $captcha = new Captcha\Dumb(array(
            'sessionClass' => 'ZendTest\Captcha\TestAsset\SessionContainer',
        ));
        $element->setCaptcha($captcha);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);
        $test = array_shift($inputSpec['validators']);
        $this->assertSame($captcha, $test);
    }
}
