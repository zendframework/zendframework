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
    ReflectionClass;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Default instance created for all test methods
     *
     * @var Zend_Validator_StringLength
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validator_StringLength object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_validator = new Validator\StringLength(4, 8);
    }

    /**
     * Ensures that we can change a specified message template by its key
     * and that this message is returned when the input is invalid.
     *
     * @return void
     */
    public function testSetMessage()
    {
        $inputInvalid = 'abcdefghij';
        $this->assertFalse($this->_validator->isValid($inputInvalid));
        $messages = $this->_validator->getMessages();
        $this->assertEquals("'$inputInvalid' is more than 8 characters long", current($messages));

        $this->_validator->setMessage(
            'Your value is too long',
            Validator\StringLength::TOO_LONG
        );

        $this->assertFalse($this->_validator->isValid('abcdefghij'));
        $messages = $this->_validator->getMessages();
        $this->assertEquals('Your value is too long', current($messages));
    }

    /**
     * Ensures that if we don't specify the message key, it uses
     * the first one in the list of message templates.
     * In the case of Zend_Validate_StringLength, TOO_SHORT is
     * the one we should expect to change.
     *
     * @return void
     */
    public function testSetMessageDefaultKey()
    {
        $this->_validator->setMessage(
            'Your value is too short', Validator\StringLength::TOO_SHORT
        );

        $this->assertFalse($this->_validator->isValid('abc'));
        $messages = $this->_validator->getMessages();
        $this->assertEquals('Your value is too short', current($messages));
        $errors = array_keys($this->_validator->getMessages());
        $this->assertEquals(Validator\StringLength::TOO_SHORT, current($errors));
    }

    /**
     * Ensures that we can include the %value% parameter in the message,
     * and that it is substituted with the value we are validating.
     *
     * @return void
     */
    public function testSetMessageWithValueParam()
    {
        $this->_validator->setMessage(
            "Your value '%value%' is too long",
            Validator\StringLength::TOO_LONG
        );

        $inputInvalid = 'abcdefghij';
        $this->assertFalse($this->_validator->isValid($inputInvalid));
        $messages = $this->_validator->getMessages();
        $this->assertEquals("Your value '$inputInvalid' is too long", current($messages));
    }

    /**
     * Ensures that we can include another parameter, defined on a
     * class-by-class basis, in the message string.
     * In the case of Zend_Validate_StringLength, one such parameter
     * is %max%.
     *
     * @return void
     */
    public function testSetMessageWithOtherParam()
    {
        $this->_validator->setMessage(
            'Your value is too long, it should be no longer than %max%',
            Validator\StringLength::TOO_LONG
        );

        $inputInvalid = 'abcdefghij';
        $this->assertFalse($this->_validator->isValid($inputInvalid));
        $messages = $this->_validator->getMessages();
        $this->assertEquals('Your value is too long, it should be no longer than 8', current($messages));
    }

    /**
     * Ensures that if we set a parameter in the message that is not
     * known to the validator class, it is not changed; %shazam% is
     * left as literal text in the message.
     *
     * @return void
     */
    public function testSetMessageWithUnknownParam()
    {
        $this->_validator->setMessage(
            'Your value is too long, and btw, %shazam%!',
            Validator\StringLength::TOO_LONG
        );

        $inputInvalid = 'abcdefghij';
        $this->assertFalse($this->_validator->isValid($inputInvalid));
        $messages = $this->_validator->getMessages();
        $this->assertEquals('Your value is too long, and btw, %shazam%!', current($messages));
    }

    /**
     * Ensures that the validator throws an exception when we
     * try to set a message for a key that is unknown to the class.
     *
     * @return void
     */
    public function testSetMessageExceptionInvalidKey()
    {
        $keyInvalid = 'invalidKey';

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'No message template exists for key');
        $this->_validator->setMessage(
            'Your value is too long',
            $keyInvalid
        );
    }

    /**
     * Ensures that we can set more than one message at a time,
     * by passing an array of key/message pairs.  Both messages
     * should be defined.
     *
     * @return void
     */
    public function testSetMessages()
    {
        $this->_validator->setMessages(
            array(
                Validator\StringLength::TOO_LONG  => 'Your value is too long',
                Validator\StringLength::TOO_SHORT => 'Your value is too short'
            )
        );

        $this->assertFalse($this->_validator->isValid('abcdefghij'));
        $messages = $this->_validator->getMessages();
        $this->assertEquals('Your value is too long', current($messages));

        $this->assertFalse($this->_validator->isValid('abc'));
        $messages = $this->_validator->getMessages();
        $this->assertEquals('Your value is too short', current($messages));
    }

    /**
     * Ensures that the magic getter gives us access to properties
     * that are permitted to be substituted in the message string.
     * The access is by the parameter name, not by the protected
     * property variable name.
     *
     * @return void
     */
    public function testGetProperty()
    {
        $this->_validator->setMessage(
            'Your value is too long',
            Validator\StringLength::TOO_LONG
        );

        $inputInvalid = 'abcdefghij';

        $this->assertFalse($this->_validator->isValid($inputInvalid));
        $messages = $this->_validator->getMessages();
        $this->assertEquals('Your value is too long', current($messages));

        $this->assertEquals($inputInvalid, $this->_validator->value);
        $this->assertEquals(8, $this->_validator->max);
        $this->assertEquals(4, $this->_validator->min);
    }

    /**
     * Ensures that the class throws an exception when we try to
     * access a property that doesn't exist as a parameter.
     *
     * @return void
     */
    public function testGetPropertyException()
    {
        $this->_validator->setMessage(
            'Your value is too long',
            Validator\StringLength::TOO_LONG
        );

        $this->assertFalse($this->_validator->isValid('abcdefghij'));
        $messages = $this->_validator->getMessages();
        $this->assertEquals('Your value is too long', current($messages));

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'No property exists by the name ');
        $property = $this->_validator->unknownProperty;
    }

    /**
     * Ensures that getMessageVariables() returns an array of
     * strings and that these strings that can be used as variables
     * in a message.
     */
    public function testGetMessageVariables()
    {
        $vars = $this->_validator->getMessageVariables();

        $this->assertInternalType('array', $vars);
        $this->assertEquals(array('min', 'max'), $vars);
        $message = 'variables: %notvar% ';
        foreach ($vars as $var) {
            $message .= "%$var% ";
        }
        $this->_validator->setMessage($message, Validator\StringLength::TOO_SHORT);

        $this->assertFalse($this->_validator->isValid('abc'));
        $messages = $this->_validator->getMessages();
        $this->assertEquals('variables: %notvar% 4 8 ', current($messages));
    }
    
    public function testEqualsMessageTemplates()
    {
        $validator = $this->_validator;
        $reflection = new ReflectionClass($validator);
        
        if(!$reflection->hasProperty('_messageTemplates')) {
            return;
        }
        
        $property = $reflection->getProperty('_messageTemplates');
        $property->setAccessible(true);

        $this->assertEquals(
            $property->getValue($validator),
            $validator->getOption('messageTemplates')
        );
    }
    
    public function testEqualsMessageVariables()
    {
        $validator = $this->_validator;
        $reflection = new ReflectionClass($validator);
        
        if(!$reflection->hasProperty('_messageVariables')) {
            return;
        }
        
        $property = $reflection->getProperty('_messageVariables');
        $property->setAccessible(true);

        $this->assertEquals(
            $property->getValue($validator),
            $validator->getOption('messageVariables')
        );
    }

}
