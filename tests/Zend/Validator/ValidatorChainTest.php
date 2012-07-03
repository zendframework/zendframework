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
use Zend\Validator\Between;
use Zend\Validator\StaticValidator;
use Zend\Validator\ValidatorChain;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class ValidatorChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ValidatorChain
     */
    protected $validator;

    /**
     * Whether an error occurred
     *
     * @var boolean
     */
    protected $errorOccurred = false;

    public function setUp()
    {
        $this->validator = new ValidatorChain();
    }

    /**
     * Ensures expected results from empty validator chain
     *
     * @return void
     */
    public function testEmpty()
    {
        $this->assertEquals(array(), $this->validator->getMessages());
        $this->assertTrue($this->validator->isValid('something'));
    }

    /**
     * Ensures expected behavior from a validator known to succeed
     *
     * @return void
     */
    public function testTrue()
    {
        $this->validator->addValidator($this->getValidatorTrue());
        $this->assertTrue($this->validator->isValid(null));
        $this->assertEquals(array(), $this->validator->getMessages());
    }

    /**
     * Ensures expected behavior from a validator known to fail
     *
     * @return void
     */
    public function testFalse()
    {
        $this->validator->addValidator($this->getValidatorFalse());
        $this->assertFalse($this->validator->isValid(null));
        $this->assertEquals(array('error' => 'validation failed'), $this->validator->getMessages());
    }

    /**
     * Ensures that a validator may break the chain
     *
     * @return void
     */
    public function testBreakChainOnFailure()
    {
        $this->validator->addValidator($this->getValidatorFalse(), true)
            ->addValidator($this->getValidatorFalse());
        $this->assertFalse($this->validator->isValid(null));
        $this->assertEquals(array('error' => 'validation failed'), $this->validator->getMessages());
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
        $this->setExpectedException('Zend\ServiceManager\Exception\ServiceNotFoundException');
        StaticValidator::execute('1234', 'UnknownValidator');
    }

    public function testIsValidWithParameters()
    {
        $this->assertTrue(StaticValidator::execute(5, 'Between', array(1, 10)));
        $this->assertTrue(StaticValidator::execute(5, 'Between', array('min' => 1, 'max' => 10)));
    }

    public function testSetGetMessageLengthLimitation()
    {
        AbstractValidator::setMessageLength(5);
        $this->assertEquals(5, AbstractValidator::getMessageLength());

        $valid = new Between(1, 10);
        $this->assertFalse($valid->isValid(24));
        $message = current($valid->getMessages());
        $this->assertTrue(strlen($message) <= 5);
    }

    public function testSetGetDefaultTranslator()
    {
        $translator = new Translator();
        AbstractValidator::setDefaultTranslator($translator);
        $this->assertSame($translator, AbstractValidator::getDefaultTranslator());
    }

    public function testAllowsPrependingValidators()
    {
        $this->validator->addValidator($this->getValidatorTrue())
            ->prependValidator($this->getValidatorFalse(), true);
        $this->assertFalse($this->validator->isValid(true));
        $messages = $this->validator->getMessages();
        $this->assertArrayHasKey('error', $messages);
    }

    public function testAllowsPrependingValidatorsByName()
    {
        $this->validator->addValidator($this->getValidatorTrue())
            ->prependByName('NotEmpty', array(), true);
        $this->assertFalse($this->validator->isValid(''));
        $messages = $this->validator->getMessages();
        $this->assertArrayHasKey('isEmpty', $messages);
    }

    /**
     * Handle file not found errors
     *
     * @group  ZF-2724
     * @param  int    $errnum
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
        $this->errorOccurred = true;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Zend\Validator\ValidatorInterface
     */
    public function getValidatorTrue()
    {
        $validator = $this->getMock('Zend\Validator\ValidatorInterface');
        $validator->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(true));
        return $validator;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Zend\Validator\ValidatorInterface
     */
    public function getValidatorFalse()
    {
        $validator = $this->getMock('Zend\Validator\ValidatorInterface');
        $validator->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(false));
        $validator->expects($this->any())
            ->method('getMessages')
            ->will($this->returnValue(array('error' => 'validation failed')));
        return $validator;
    }
}
