<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Validator
 */

namespace ZendTest\Validator;

use Zend\Validator\StringLength;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StringLength
     */
    protected $validator;

    public function setUp()
    {
        $this->validator = new StringLength(4, 8);
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
        $this->assertFalse($this->validator->isValid($inputInvalid));
        $messages = $this->validator->getMessages();
        $this->assertEquals("The input is more than 8 characters long", current($messages));

        $this->validator->setMessage(
            'Your value is too long',
            StringLength::TOO_LONG
        );

        $this->assertFalse($this->validator->isValid('abcdefghij'));
        $messages = $this->validator->getMessages();
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
        $this->validator->setMessage(
            'Your value is too short', StringLength::TOO_SHORT
        );

        $this->assertFalse($this->validator->isValid('abc'));
        $messages = $this->validator->getMessages();
        $this->assertEquals('Your value is too short', current($messages));
        $errors = array_keys($this->validator->getMessages());
        $this->assertEquals(StringLength::TOO_SHORT, current($errors));
    }

    /**
     * Ensures that we can include the %value% parameter in the message,
     * and that it is substituted with the value we are validating.
     *
     * @return void
     */
    public function testSetMessageWithValueParam()
    {
        $this->validator->setMessage(
            "Your value '%value%' is too long",
            StringLength::TOO_LONG
        );

        $inputInvalid = 'abcdefghij';
        $this->assertFalse($this->validator->isValid($inputInvalid));
        $messages = $this->validator->getMessages();
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
        $this->validator->setMessage(
            'Your value is too long, it should be no longer than %max%',
            StringLength::TOO_LONG
        );

        $inputInvalid = 'abcdefghij';
        $this->assertFalse($this->validator->isValid($inputInvalid));
        $messages = $this->validator->getMessages();
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
        $this->validator->setMessage(
            'Your value is too long, and btw, %shazam%!',
            StringLength::TOO_LONG
        );

        $inputInvalid = 'abcdefghij';
        $this->assertFalse($this->validator->isValid($inputInvalid));
        $messages = $this->validator->getMessages();
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
        $this->validator->setMessage(
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
        $this->validator->setMessages(
            array(
                StringLength::TOO_LONG  => 'Your value is too long',
                StringLength::TOO_SHORT => 'Your value is too short'
            )
        );

        $this->assertFalse($this->validator->isValid('abcdefghij'));
        $messages = $this->validator->getMessages();
        $this->assertEquals('Your value is too long', current($messages));

        $this->assertFalse($this->validator->isValid('abc'));
        $messages = $this->validator->getMessages();
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
        $this->validator->setMessage(
            'Your value is too long',
            StringLength::TOO_LONG
        );

        $inputInvalid = 'abcdefghij';

        $this->assertFalse($this->validator->isValid($inputInvalid));
        $messages = $this->validator->getMessages();
        $this->assertEquals('Your value is too long', current($messages));

        $this->assertEquals($inputInvalid, $this->validator->value);
        $this->assertEquals(8, $this->validator->max);
        $this->assertEquals(4, $this->validator->min);
    }

    /**
     * Ensures that the class throws an exception when we try to
     * access a property that doesn't exist as a parameter.
     *
     * @return void
     */
    public function testGetPropertyException()
    {
        $this->validator->setMessage(
            'Your value is too long',
            StringLength::TOO_LONG
        );

        $this->assertFalse($this->validator->isValid('abcdefghij'));
        $messages = $this->validator->getMessages();
        $this->assertEquals('Your value is too long', current($messages));

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'No property exists by the name ');
        $this->validator->unknownProperty;
    }

    /**
     * Ensures that getMessageVariables() returns an array of
     * strings and that these strings that can be used as variables
     * in a message.
     */
    public function testGetMessageVariables()
    {
        $vars = $this->validator->getMessageVariables();

        $this->assertInternalType('array', $vars);
        $this->assertEquals(array('min', 'max'), $vars);
        $message = 'variables: %notvar% ';
        foreach ($vars as $var) {
            $message .= "%$var% ";
        }
        $this->validator->setMessage($message, StringLength::TOO_SHORT);

        $this->assertFalse($this->validator->isValid('abc'));
        $messages = $this->validator->getMessages();
        $this->assertEquals('variables: %notvar% 4 8 ', current($messages));
    }

    public function testEqualsMessageTemplates()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }

    public function testEqualsMessageVariables()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals($validator->getOption('messageVariables'),
                                     'messageVariables', $validator);
    }
}
