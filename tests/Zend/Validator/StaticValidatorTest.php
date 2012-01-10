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

/**
 * @namespace
 */
namespace ZendTest\Validator;

use Zend\Validator,
    Zend\Validator\ValidatorBroker,
    Zend\Loader\Broker,
    Zend\Loader\PluginBroker,
    Zend\Translator;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class StaticValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function clearRegistry()
    {
        if (\Zend\Registry::isRegistered('Zend_Translator')) {
            $registry = \Zend\Registry::getInstance();
            unset($registry['Zend_Translator']);
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
        Validator\StaticValidator::setBroker(null);
        $this->validator = new Validator\Alpha();
    }
    
    public function tearDown()
    {
        $this->clearRegistry();
        Validator\AbstractValidator::setDefaultTranslator(null);
        Validator\AbstractValidator::setMessageLength(-1);
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
    
    public function testCanSetGlobalDefaultTranslator()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $translator = new Translator\Translator('ArrayAdapter', array(), 'en');
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
        $translate = new Translator\Translator('ArrayAdapter', array());
        restore_error_handler();
        \Zend\Registry::set('Zend_Translator', $translate);
        $this->assertSame($translate->getAdapter(), $this->validator->getTranslator());
    }

    public function testLocalTranslatorPreferredOverGlobalTranslator()
    {
        $this->testCanSetGlobalDefaultTranslator();
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $translator = new Translator\Translator('ArrayAdapter', array(), 'en');
        restore_error_handler();
        $this->validator->setTranslator($translator);
        $this->assertNotSame(Validator\AbstractValidator::getDefaultTranslator(), $this->validator->getTranslator());
    }
    
    public function testMaximumErrorMessageLength()
    {
        $this->assertEquals(-1, Validator\AbstractValidator::getMessageLength());
        Validator\AbstractValidator::setMessageLength(10);
        $this->assertEquals(10, Validator\AbstractValidator::getMessageLength());

        $translator = new Translator\Translator(
            'ArrayAdapter',
            array(Validator\Alpha::INVALID => 'This is the translated message for %value%'),
            'en'
        );
        $this->validator->setTranslator($translator);
        $this->assertFalse($this->validator->isValid(123));
        $messages = $this->validator->getMessages();
        $this->assertTrue(array_key_exists(Validator\Alpha::INVALID, $messages));
        $this->assertEquals('This is...', $messages[Validator\Alpha::INVALID]);
    }

    public function testSetGetMessageLengthLimitation()
    {
        Validator\AbstractValidator::setMessageLength(5);
        $this->assertEquals(5, Validator\AbstractValidator::getMessageLength());

        $valid = new Validator\Between(1, 10);
        $this->assertFalse($valid->isValid(24));
        $message = current($valid->getMessages());
        $this->assertTrue(strlen($message) <= 5);
    }

    public function testSetGetDefaultTranslator()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $translator = new \Zend\Translator\Translator('ArrayAdapter', array(), 'en');
        restore_error_handler();
        Validator\AbstractValidator::setDefaultTranslator($translator);
        $this->assertSame($translator->getAdapter(), Validator\AbstractValidator::getDefaultTranslator());
    }
    
    /* plugin loading */

    public function testLazyLoadsValidatorBrokerByDefault()
    {
        $broker = Validator\StaticValidator::getBroker();
        $this->assertInstanceOf('Zend\Validator\ValidatorBroker', $broker);
    }

    public function testCanSetCustomPluginBroker()
    {
        $broker = new PluginBroker();
        Validator\StaticValidator::setBroker($broker);
        $this->assertSame($broker, Validator\StaticValidator::getBroker());
    }

    public function testPassingNullWhenSettingBrokerResetsBroker()
    {
        $broker = new PluginBroker();
        Validator\StaticValidator::setBroker($broker);
        $this->assertSame($broker, Validator\StaticValidator::getBroker());
        Validator\StaticValidator::setBroker(null);
        $this->assertNotSame($broker, Validator\StaticValidator::getBroker());
    }
    
    public function testExecuteValidWithParameters()
    {
        $this->assertTrue(Validator\StaticValidator::execute(5, 'Between', array(1, 10)));
        $this->assertTrue(Validator\StaticValidator::execute(5, 'Between', array('min' => 1, 'max' => 10)));
    }
}
