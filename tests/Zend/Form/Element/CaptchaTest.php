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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Form_Element_CaptchaTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Form_Element_CaptchaTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

/** Zend_Form_Element_Captcha */
require_once 'Zend/Form/Element/Captcha.php';

/** Zend_Captcha_Dumb */
require_once 'Zend/Captcha/Dumb.php';

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Form_Element_CaptchaTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('Zend_Form_Element_CaptchaTest');
        PHPUnit_TextUI_TestRunner::run($suite);
    }
    
    public function setUp()
    {
        $this->element = new Zend_Form_Element_Captcha(
            'foo',
            array(
                'captcha' => 'Dumb',
                'captchaOptions' => array(
                    'sessionClass' => 'Zend_Form_Element_CaptchaTest_SessionContainer',
                ),
            )
        );
    }

    public function getCaptcha()
    {
        $captcha = new Zend_Captcha_Dumb(array(
            'sessionClass' => 'Zend_Form_Element_CaptchaTest_SessionContainer',
        ));
        return $captcha;
    }

    /**
     * @expectedException Zend_Form_Exception
     */
    public function testConstructionShouldRequireCaptchaDetails()
    {
        $this->element = new Zend_Form_Element_Captcha('foo');
    }

    public function testShouldAllowSettingCaptcha()
    {
        $captcha = $this->getCaptcha();
        $this->assertNotSame($this->element->getCaptcha(), $captcha);
        $this->element->setCaptcha($captcha);
        $this->assertSame($captcha, $this->element->getCaptcha());
    }

    public function testShouldAllowAddingCaptchaPrefixPath()
    {
        $this->element->addPrefixPath('My_Captcha', 'My/Captcha/', 'captcha');
        $loader = $this->element->getPluginLoader('captcha');
        $paths  = $loader->getPaths('My_Captcha');
        $this->assertTrue(is_array($paths));
    }

    public function testAddingNullPrefixPathShouldAddCaptchaPrefixPath()
    {
        $this->element->addPrefixPath('My', 'My');
        $loader = $this->element->getPluginLoader('captcha');
        $paths  = $loader->getPaths('My_Captcha');
        $this->assertTrue(is_array($paths));
    }

    /**
     * @see   ZF-4038
     * @group ZF-4038
     */
    public function testCaptchaShouldRenderFullyQualifiedElementName()
    {
        require_once 'Zend/Form.php';
        require_once 'Zend/View.php';
        $form = new Zend_Form();
        $form->addElement($this->element)
             ->setElementsBelongTo('bar');
        $html = $form->render(new Zend_View);
        $this->assertContains('name="bar[foo', $html, $html);
        $this->assertContains('id="bar-foo-', $html, $html);
        $this->form = $form;
    }

    /**
     * @see   ZF-4038
     * @group ZF-4038
     */
    public function testCaptchaShouldValidateUsingFullyQualifiedElementName()
    {
        $this->testCaptchaShouldRenderFullyQualifiedElementName();
        $word = $this->element->getCaptcha()->getWord();
        $id   = $this->element->getCaptcha()->getId();
        $data = array(
            'bar' => array(
                'foo' => array(
                    'id'    => $id,
                    'input' => $word,
                )
            )
        );
        $valid = $this->form->isValid($data);
        $this->assertTrue($valid, var_export($this->form->getMessages(), 1));
    }

    /**
     * @group ZF-4822
     */
    public function testDefaultDecoratorsShouldIncludeErrorsDescriptionHtmlTagAndLabel()
    {
        $decorators = $this->element->getDecorators();
        $this->assertTrue(is_array($decorators));
        $this->assertTrue(array_key_exists('Zend_Form_Decorator_Errors', $decorators), 'Missing Errors decorator' . var_export(array_keys($decorators), 1));
        $this->assertTrue(array_key_exists('Zend_Form_Decorator_Description', $decorators), 'Missing Description decorator' . var_export(array_keys($decorators), 1));
        $this->assertTrue(array_key_exists('Zend_Form_Decorator_HtmlTag', $decorators), 'Missing HtmlTag decorator' . var_export(array_keys($decorators), 1));
        $this->assertTrue(array_key_exists('Zend_Form_Decorator_Label', $decorators), 'Missing Label decorator' . var_export(array_keys($decorators), 1));
    }

    /**
     * @group ZF-5855
     */
    public function testHelperDoesNotShowUpInAttribs()
    {
        require_once 'Zend/View.php';
        $this->assertFalse(array_key_exists('helper', $this->element->getAttribs()));
    }
}

class Zend_Form_Element_CaptchaTest_SessionContainer
{
    protected static $_word;

    public function __get($name)
    {
        if ('word' == $name) {
            return self::$_word;
        }

        return null;
    }

    public function __set($name, $value)
    {
        if ('word' == $name) {
            self::$_word = $value;
        } else {
            $this->$name = $value;
        }
    }

    public function __isset($name)
    {
        if (('word' == $name) && (null !== self::$_word))  {
            return true;
        }

        return false;
    }

    public function __call($method, $args)
    {
        switch ($method) {
            case 'setExpirationHops':
            case 'setExpirationSeconds':
                $this->$method = array_shift($args);
                break;
            default:
        }
    }
}

// Call Zend_Form_Element_CaptchaTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Form_Element_CaptchaTest::main") {
    Zend_Form_Element_CaptchaTest::main();
}
