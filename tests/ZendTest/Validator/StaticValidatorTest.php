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
use Zend\Validator,
    Zend\Loader,
    Zend\Translator;

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
        Validator\StaticValidator::setPluginLoader(null);
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
        \Zend\Registry::set('Zend_Translate', $translate);
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

    public function testLazyLoadsPluginLoaderByDefault()
    {
        $loader = Validator\StaticValidator::getPluginLoader();
        $this->assertType('Zend\Loader\PluginLoader', $loader);
    }

    public function testLazyLoadedPluginLoaderRegistersZendValidatorNamespace()
    {
        $loader = Validator\StaticValidator::getPluginLoader();
        $paths = $loader->getPaths('Zend\Validator');
        $this->assertEquals(1, count($paths));
    }

    public function testCanSetCustomPluginLoader()
    {
        $loader = new Loader\PluginLoader();
        Validator\StaticValidator::setPluginLoader($loader);
        $this->assertSame($loader, Validator\StaticValidator::getPluginLoader());
    }

    public function testPassingNullWhenSettingPluginLoaderResetsPluginLoader()
    {
        $loader = new Loader\PluginLoader();
        Validator\StaticValidator::setPluginLoader($loader);
        $this->assertSame($loader, Validator\StaticValidator::getPluginLoader());
        Validator\StaticValidator::setPluginLoader(null);
        $this->assertNotSame($loader, Validator\StaticValidator::getPluginLoader());
    }
    
    public function testExecuteValidWithParameters()
    {
        $this->assertTrue(Validator\StaticValidator::execute(5, 'Between', array(1, 10)));
        $this->assertTrue(Validator\StaticValidator::execute(5, 'Between', array('min' => 1, 'max' => 10)));
    }
}
