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
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Validator;
use Zend\Validator;
use Zend\Translator;

/**
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */
class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Runs this test suite
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new \PHPUnit_Framework_TestSuite('Zend_Validate_AbstractTest');
        $result = \PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function clearRegistry()
    {
        if (\Zend\Registry::isRegistered('Zend_Translate')) {
            $registry = \Zend\Registry::getInstance();
            unset($registry['Zend_Translate']);
        }
    }

    /**
     * Creates a new validation object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->clearRegistry();
        Validator\AbstractValidator::setDefaultTranslator(null);
        $this->validator = new Concrete();
    }

    public function tearDown()
    {
        $this->clearRegistry();
        Validator\AbstractValidator::setDefaultTranslator(null);
        Validator\AbstractValidator::setMessageLength(-1);
    }

    public function testTranslatorNullByDefault()
    {
        $this->assertNull($this->validator->getTranslator());
    }

    public function testCanSetTranslator()
    {
        $this->testTranslatorNullByDefault();
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $translator = new Translator\Translator('array', array(), 'en');
        restore_error_handler();
        $this->validator->setTranslator($translator);
        $this->assertSame($translator->getAdapter(), $this->validator->getTranslator());
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
        $this->assertNull(Validator\AbstractValidator::getDefaultTranslator());
    }

    public function testCanSetGlobalDefaultTranslator()
    {
        $this->testGlobalDefaultTranslatorNullByDefault();
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $translator = new Translator\Translator('array', array(), 'en');
        restore_error_handler();
        Validator\AbstractValidator::setDefaultTranslator($translator);
        $this->assertSame($translator->getAdapter(), Validator\AbstractValidator::getDefaultTranslator());
    }

    public function testGlobalDefaultTranslatorUsedWhenNoLocalTranslatorSet()
    {
        $this->testCanSetGlobalDefaultTranslator();
        $this->assertSame(Validator\AbstractValidator::getDefaultTranslator(), $this->validator->getTranslator());
    }

    public function testGlobalTranslatorFromRegistryUsedWhenNoLocalTranslatorSet()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $translate = new Translator\Translator('array', array());
        restore_error_handler();
        \Zend\Registry::set('Zend_Translate', $translate);
        $this->assertSame($translate->getAdapter(), $this->validator->getTranslator());
    }

    public function testLocalTranslatorPreferredOverGlobalTranslator()
    {
        $this->testCanSetGlobalDefaultTranslator();
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $translator = new Translator\Translator('array', array(), 'en');
        restore_error_handler();
        $this->validator->setTranslator($translator);
        $this->assertNotSame(Validator\AbstractValidator::getDefaultTranslator(), $this->validator->getTranslator());
    }

    public function testErrorMessagesAreTranslatedWhenTranslatorPresent()
    {
        $translator = new Translator\Translator(
            'array',
            array('fooMessage' => 'This is the translated message for %value%'),
            'en'
        );
        $this->validator->setTranslator($translator);
        $this->assertFalse($this->validator->isValid('bar'));
        $messages = $this->validator->getMessages();
        $this->assertTrue(array_key_exists('fooMessage', $messages));
        $this->assertContains('bar', $messages['fooMessage']);
        $this->assertContains('This is the translated message for ', $messages['fooMessage']);
    }

    public function testCanTranslateMessagesInsteadOfKeys()
    {
        $translator = new Translator\Translator(
            'array',
            array('%value% was passed' => 'This is the translated message for %value%'),
            'en'
        );
        $this->validator->setTranslator($translator);
        $this->assertFalse($this->validator->isValid('bar'));
        $messages = $this->validator->getMessages();
        $this->assertTrue(array_key_exists('fooMessage', $messages));
        $this->assertContains('bar', $messages['fooMessage']);
        $this->assertContains('This is the translated message for ', $messages['fooMessage']);
    }

    public function testObscureValueFlagFalseByDefault()
    {
        $this->assertFalse($this->validator->getObscureValue());
    }

    public function testCanSetObscureValueFlag()
    {
        $this->testObscureValueFlagFalseByDefault();
        $this->validator->setObscureValue(true);
        $this->assertTrue($this->validator->getObscureValue());
        $this->validator->setObscureValue(false);
        $this->assertFalse($this->validator->getObscureValue());
    }

    public function testValueIsObfuscatedWheObscureValueFlagIsTrue()
    {
        $this->validator->setObscureValue(true);
        $this->assertFalse($this->validator->isValid('foobar'));
        $messages = $this->validator->getMessages();
        $this->assertTrue(isset($messages['fooMessage']));
        $message = $messages['fooMessage'];
        $this->assertNotContains('foobar', $message);
        $this->assertContains('******', $message);
    }

    /**
     * @see ZF-4463
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
        $translator = new Translator\Translator('array', array(), 'en');
        restore_error_handler();
        $this->validator->setTranslator($translator);
        $this->assertFalse($this->validator->translatorIsDisabled());
    }

    public function testCanDisableTranslator()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $translator = new Translator\Translator(
            'array',
            array('fooMessage' => 'This is the translated message for %value%'),
            'en'
        );
        restore_error_handler();
        $this->validator->setTranslator($translator);

        $this->assertFalse($this->validator->isValid('bar'));
        $messages = $this->validator->getMessages();
        $this->assertTrue(array_key_exists('fooMessage', $messages));
        $this->assertContains('bar', $messages['fooMessage']);
        $this->assertContains('This is the translated message for ', $messages['fooMessage']);

        $this->validator->setDisableTranslator(true);
        $this->assertTrue($this->validator->translatorIsDisabled());

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
            array(Concrete::FOO_MESSAGE => '%value% was passed'),
            $messages
            );
    }

    public function testMaximumErrorMessageLength()
    {
        $this->assertEquals(-1, Validator\ValidatorChain::getMessageLength());
        Validator\AbstractValidator::setMessageLength(10);
        $this->assertEquals(10, Validator\ValidatorChain::getMessageLength());

        $translator = new Translator\Translator(
            'array',
            array('fooMessage' => 'This is the translated message for %value%'),
            'en'
        );
        $this->validator->setTranslator($translator);
        $this->assertFalse($this->validator->isValid('bar'));
        $messages = $this->validator->getMessages();
        $this->assertTrue(array_key_exists('fooMessage', $messages));
        $this->assertEquals('This is...', $messages['fooMessage']);
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
        $this->_errorOccurred = true;
    }
}

class Concrete extends Validator\AbstractValidator
{
    const FOO_MESSAGE = 'fooMessage';

    protected $_messageTemplates = array(
        'fooMessage' => '%value% was passed',
    );

    public function isValid($value)
    {
        $this->_setValue($value);
        $this->_error(self::FOO_MESSAGE);
        return false;
    }
}

