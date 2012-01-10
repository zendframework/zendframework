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
use Zend\Validator;


/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Zend_Validator object
     *
     * @var Zend_Validator
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validator object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_validator = new Validator\ValidatorChain();
    }

    /**
     * Ensures expected results from empty validator chain
     *
     * @return void
     */
    public function testEmpty()
    {
        $this->assertEquals(array(), $this->_validator->getMessages());
        $this->assertEquals(array(), $this->_validator->getErrors());
        $this->assertTrue($this->_validator->isValid('something'));
        $this->assertEquals(array(), $this->_validator->getErrors());
    }

    /**
     * Ensures expected behavior from a validator known to succeed
     *
     * @return void
     */
    public function testTrue()
    {
        $this->_validator->addValidator(new ValidatorTrue());
        $this->assertTrue($this->_validator->isValid(null));
        $this->assertEquals(array(), $this->_validator->getMessages());
        $this->assertEquals(array(), $this->_validator->getErrors());
    }

    /**
     * Ensures expected behavior from a validator known to fail
     *
     * @return void
     */
    public function testFalse()
    {
        $this->_validator->addValidator(new ValidatorFalse());
        $this->assertFalse($this->_validator->isValid(null));
        $this->assertEquals(array('error' => 'validation failed'), $this->_validator->getMessages());
    }

    /**
     * Ensures that a validator may break the chain
     *
     * @return void
     */
    public function testBreakChainOnFailure()
    {
        $this->_validator->addValidator(new ValidatorFalse(), true)
                         ->addValidator(new ValidatorFalse());
        $this->assertFalse($this->_validator->isValid(null));
        $this->assertEquals(array('error' => 'validation failed'), $this->_validator->getMessages());
    }

    /**
     * Ensures that we can call the static method is()
     * to instantiate a named validator by its class basename
     * and it returns the result of isValid() with the input.
     */
    public function testStaticFactory()
    {
        $this->markTestSkipped('is() method should not try to implement its own plugin loader- refactor this');
        $this->assertTrue(Validator\ValidatorChain::execute('1234', 'Digits'));
        $this->assertFalse(Validator\ValidatorChain::execute('abc', 'Digits'));
    }

    /**
     * Ensures that a validator with constructor arguments can be called
     * with the static method is().
     */
    public function testStaticFactoryWithConstructorArguments()
    {
        $this->markTestSkipped('is() method should not try to implement its own plugin loader - refactor this');
        $this->assertTrue(Validator\ValidatorChain::execute('12', 'Between', array('min' => 1, 'max' => 12)));
        $this->assertFalse(Validator\ValidatorChain::execute('24', 'Between', array('min' => 1, 'max' => 12)));
    }

    /**
     * Ensures that if we specify a validator class basename that doesn't
     * exist in the namespace, is() throws an exception.
     *
     * Refactored to conform with ZF-2724.
     *
     * @group  ZF-2724
     * @return void
     */
    public function testStaticFactoryClassNotFound()
    {
        $this->setExpectedException('Zend\Loader\Exception\RuntimeException', 'unknownvalidator');
        Validator\StaticValidator::execute('1234', 'UnknownValidator');
    }

    public function testIsValidWithParameters()
    {
        $this->assertTrue(Validator\StaticValidator::execute(5, 'Between', array(1, 10)));
        $this->assertTrue(Validator\StaticValidator::execute(5, 'Between', array('min' => 1, 'max' => 10)));
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

    /**
     * Handle file not found errors
     *
     * @group  ZF-2724
     * @param  int $errnum
     * @param  string $errstr
     * @return void
     */
    public function handleNotFoundError($errnum, $errstr)
    {
        if (strstr($errstr, 'No such file')) {
            $this->error = true;
        }
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


/**
 * Validator to return true to any input.
 */
class ValidatorTrue extends Validator\AbstractValidator
{
    public function isValid($value)
    {
        return true;
    }
}


/**
 * Validator to return false to any input.
 */
class ValidatorFalse extends Validator\AbstractValidator
{
    protected $_messageTemplates = array(
        'error' => 'validation failed',
    );

    public function isValid($value)
    {
        $this->error('error');
        return false;
    }
}
