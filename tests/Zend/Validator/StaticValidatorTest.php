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
class StaticValidatorTest extends \PHPUnit_Framework_TestCase
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
        $this->markTestSkipped('Skipping static tests until refactoring of loading.');
        $this->clearRegistry();
        Validator\AbstractValidator::setDefaultTranslator(null);
    }
    
    public function tearDown()
    {
        $this->clearRegistry();
        Validator\AbstractValidator::setDefaultTranslator(null);
        Validator\AbstractValidator::setMessageLength(-1);
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
     * Testing Namespaces
     *
     * @return void
     */
    public function testNamespaces()
    {
        $this->assertEquals(array(), Validator\ValidatorChain::getDefaultNamespaces());
        $this->assertFalse(Validator\ValidatorChain::hasDefaultNamespaces());

        Validator\ValidatorChain::setDefaultNamespaces('TestDir');
        $this->assertEquals(array('TestDir'), Validator\ValidatorChain::getDefaultNamespaces());

        Validator\ValidatorChain::setDefaultNamespaces('OtherTestDir');
        $this->assertEquals(array('OtherTestDir'), Validator\ValidatorChain::getDefaultNamespaces());

        $this->assertTrue(Validator\ValidatorChain::hasDefaultNamespaces());

        Validator\ValidatorChain::setDefaultNamespaces(array());

        $this->assertEquals(array(), Validator\ValidatorChain::getDefaultNamespaces());
        $this->assertFalse(Validator\ValidatorChain::hasDefaultNamespaces());

        Validator\ValidatorChain::addDefaultNamespaces(array('One', 'Two'));
        $this->assertEquals(array('One', 'Two'), Validator\ValidatorChain::getDefaultNamespaces());

        Validator\ValidatorChain::addDefaultNamespaces('Three');
        $this->assertEquals(array('One', 'Two', 'Three'), Validator\ValidatorChain::getDefaultNamespaces());

        Validator\ValidatorChain::setDefaultNamespaces(array());
    }
    

    public function testIsValidWithParameters()
    {
        $this->markTestSkipped('is() method should not try to implement its own plugin loader - refactor this');
        $this->assertTrue(Validator\ValidatorChain::is(5, 'Between', array(1, 10)));
        $this->assertTrue(Validator\ValidatorChain::is(5, 'Between', array('min' => 1, 'max' => 10)));
    }

    public function testSetGetMessageLengthLimitation()
    {
        Validator\ValidatorChain::setMessageLength(5);
        $this->assertEquals(5, Validator\ValidatorChain::getMessageLength());

        $valid = new Validator\Between(1, 10);
        $this->assertFalse($valid->isValid(24));
        $message = current($valid->getMessages());
        $this->assertTrue(strlen($message) <= 5);
    }

    public function testSetGetDefaultTranslator()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $translator = new \Zend\Translator\Translator('array', array(), 'en');
        restore_error_handler();
        Validator\AbstractValidator::setDefaultTranslator($translator);
        $this->assertSame($translator->getAdapter(), Validator\AbstractValidator::getDefaultTranslator());
    }
    
}