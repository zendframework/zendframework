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
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Validator;

use Zend\I18n\Translator\Translator;
use Zend\Validator\AbstractValidator;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /** @var AbstractValidator */
    public $validator;

    /**
     * Whether an error occurred
     *
     * @var boolean
     */
    protected $errorOccurred = false;

    public function setUp()
    {
        $this->validator = new TestAsset\ConcreteValidator();
    }

    public function testTranslatorNullByDefault()
    {
        $this->assertNull($this->validator->getTranslator());
    }

    public function testCanSetTranslator()
    {
        $this->testTranslatorNullByDefault();
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $translator = new Translator();
        restore_error_handler();
        $this->validator->setTranslator($translator);
        $this->assertSame($translator, $this->validator->getTranslator());
    }

    public function testCanSetTranslatorToNull()
    {
        $this->testCanSetTranslator();
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $this->validator->setTranslator(null);
        restore_error_handler();
        $this->assertNull($this->validator->getTranslator());
    }

    public function testGlobalDefaultTranslatorNullByDefault()
    {
        $this->assertNull(AbstractValidator::getDefaultTranslator());
    }

    public function testErrorMessagesAreTranslatedWhenTranslatorPresent()
    {
        $loader = new TestAsset\ArrayTranslator();
        $loader->translations = array(
            'fooMessage' => 'This is the translated message for %value%',
        );
        $translator = new Translator();
        $translator->getPluginManager()->setService('default', $loader);
        $translator->addTranslationFile('default', null);

        $this->validator->setTranslator($translator);
        $this->assertFalse($this->validator->isValid('bar'));
        $messages = $this->validator->getMessages();
        $this->assertTrue(array_key_exists('fooMessage', $messages));
        $this->assertContains('bar', $messages['fooMessage'], var_export($messages, 1));
        $this->assertContains('This is the translated message for ', $messages['fooMessage']);
    }

    public function testCanTranslateMessagesInsteadOfKeys()
    {
        $loader = new TestAsset\ArrayTranslator();
        $loader->translations = array(
            '%value% was passed' => 'This is the translated message for %value%',
        );
        $translator = new Translator();
        $translator->getPluginManager()->setService('default', $loader);
        $translator->addTranslationFile('default', null);

        $this->validator->setTranslator($translator);
        $this->assertFalse($this->validator->isValid('bar'));
        $messages = $this->validator->getMessages();
        $this->assertTrue(array_key_exists('fooMessage', $messages));
        $this->assertContains('bar', $messages['fooMessage'], var_export($messages, 1));
        $this->assertContains('This is the translated message for ', $messages['fooMessage']);
    }

    public function testObscureValueFlagFalseByDefault()
    {
        $this->assertFalse($this->validator->isValueObscured());
    }

    public function testCanSetValueObscuredFlag()
    {
        $this->testObscureValueFlagFalseByDefault();
        $this->validator->setValueObscured(true);
        $this->assertTrue($this->validator->isValueObscured());
        $this->validator->setValueObscured(false);
        $this->assertFalse($this->validator->isValueObscured());
    }

    public function testValueIsObfuscatedWheObscureValueFlagIsTrue()
    {
        $this->validator->setValueObscured(true);
        $this->assertFalse($this->validator->isValid('foobar'));
        $messages = $this->validator->getMessages();
        $this->assertTrue(isset($messages['fooMessage']));
        $message = $messages['fooMessage'];
        $this->assertNotContains('foobar', $message);
        $this->assertContains('******', $message);
    }

    /**
     * @group ZF-4463
     */
    public function testDoesNotFailOnObjectInput()
    {
        $this->assertFalse($this->validator->isValid(new \stdClass()));
        $messages = $this->validator->getMessages();
        $this->assertTrue(array_key_exists('fooMessage', $messages));
    }

    public function testTranslatorEnabledPerDefault()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $translator = new Translator();
        $this->validator->setTranslator($translator);
        $this->assertFalse($this->validator->isTranslatorDisabled());
    }

    public function testCanDisableTranslator()
    {
        $loader = new TestAsset\ArrayTranslator();
        $loader->translations = array(
            '%value% was passed' => 'This is the translated message for %value%',
        );
        $translator = new Translator();
        $translator->getPluginManager()->setService('default', $loader);
        $translator->addTranslationFile('default', null);
        $this->validator->setTranslator($translator);

        $this->assertFalse($this->validator->isValid('bar'));
        $messages = $this->validator->getMessages();
        $this->assertTrue(array_key_exists('fooMessage', $messages));
        $this->assertContains('bar', $messages['fooMessage']);
        $this->assertContains('This is the translated message for ', $messages['fooMessage']);

        $this->validator->setTranslatorDisabled(true);
        $this->assertTrue($this->validator->isTranslatorDisabled());

        $this->assertFalse($this->validator->isValid('bar'));
        $messages = $this->validator->getMessages();
        $this->assertTrue(array_key_exists('fooMessage', $messages));
        $this->assertContains('bar', $messages['fooMessage']);
        $this->assertContains('bar was passed', $messages['fooMessage']);
    }

    public function testGetMessageTemplates()
    {
        $messages = $this->validator->getMessageTemplates();
        $this->assertEquals(
            array('fooMessage' => '%value% was passed'), $messages);

        $this->assertEquals(
            array(TestAsset\ConcreteValidator::FOO_MESSAGE => '%value% was passed'),
            $messages
            );
    }

    public function testInvokeProxiesToIsValid()
    {
        $validator = new TestAsset\ConcreteValidator;
        $this->assertFalse($validator('foo'));
        $this->assertContains("foo was passed", $validator->getMessages());
    }

    /**
     * Ignores a raised PHP error when in effect, but throws a flag to indicate an error occurred
     *
     * @param  integer $errno
     * @param  string  $errstr
     * @param  string  $errfile
     * @param  integer $errline
     * @param  array   $errcontext
     * @return void
     */
    public function errorHandlerIgnore($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        $this->errorOccurred = true;
    }
}
