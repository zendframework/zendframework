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

use Zend\Validator\Identical;

/**
 * @category   Zend
 * @package    Zend
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class IdenticalTest extends \PHPUnit_Framework_TestCase
{
    /** @var Identical */
    public $validator;

    public function setUp()
    {
        $this->validator = new Identical;
    }

    public function testTokenInitiallyNull()
    {
        $this->assertNull($this->validator->getToken());
    }

    public function testCanSetToken()
    {
        $this->testTokenInitiallyNull();
        $this->validator->setToken('foo');
        $this->assertEquals('foo', $this->validator->getToken());
    }

    public function testCanSetTokenViaConstructor()
    {
        $validator = new Identical('foo');
        $this->assertEquals('foo', $validator->getToken());
    }

    public function testValidatingWhenTokenNullReturnsFalse()
    {
        $this->assertFalse($this->validator->isValid('foo'));
    }

    public function testValidatingWhenTokenNullSetsMissingTokenMessage()
    {
        $this->testValidatingWhenTokenNullReturnsFalse();
        $messages = $this->validator->getMessages();
        $this->assertTrue(array_key_exists('missingToken', $messages));
    }

    public function testValidatingAgainstTokenWithNonMatchingValueReturnsFalse()
    {
        $this->validator->setToken('foo');
        $this->assertFalse($this->validator->isValid('bar'));
    }

    public function testValidatingAgainstTokenWithNonMatchingValueSetsNotSameMessage()
    {
        $this->testValidatingAgainstTokenWithNonMatchingValueReturnsFalse();
        $messages = $this->validator->getMessages();
        $this->assertTrue(array_key_exists('notSame', $messages));
    }

    public function testValidatingAgainstTokenWithMatchingValueReturnsTrue()
    {
        $this->validator->setToken('foo');
        $this->assertTrue($this->validator->isValid('foo'));
    }

    /**
     * @group ZF-6953
     */
    public function testValidatingAgainstEmptyToken()
    {
        $this->validator->setToken('');
        $this->assertTrue($this->validator->isValid(''));
    }

    /**
     * @group ZF-7128
     */
    public function testValidatingAgainstNonStrings()
    {
        $this->validator->setToken(true);
        $this->assertTrue($this->validator->isValid(true));
        $this->assertFalse($this->validator->isValid(1));

        $this->validator->setToken(array('one' => 'two', 'three'));
        $this->assertTrue($this->validator->isValid(array('one' => 'two', 'three')));
        $this->assertFalse($this->validator->isValid(array()));
    }

    public function testValidatingTokenArray()
    {
        $validator = new Identical(array('token' => 123));
        $this->assertTrue($validator->isValid(123));
        $this->assertFalse($validator->isValid(array('token' => 123)));
    }

    public function testValidatingNonStrictToken()
    {
        $validator = new Identical(array('token' => 123, 'strict' => false));
        $this->assertTrue($validator->isValid('123'));

        $validator->setStrict(true);
        $this->assertFalse($validator->isValid(array('token' => '123')));
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

    public function testValidatingStringTokenInContext()
    {
        $this->validator->setToken('email');

        $this->assertTrue($this->validator->isValid(
            'john@doe.com',
            array('email' => 'john@doe.com')
        ));

        $this->assertFalse($this->validator->isValid(
            'john@doe.com',
            array('email' => 'harry@hoe.com')
        ));

        $this->assertFalse($this->validator->isValid(
            'harry@hoe.com',
            array('email' => 'john@doe.com')
        ));
    }

    public function testValidatingArrayTokenInContext()
    {
        $this->validator->setToken(array('user' => 'email'));

        $this->assertTrue($this->validator->isValid(
            'john@doe.com',
            array(
                'user' => array(
                    'email' => 'john@doe.com'
                )
            )
        ));

        $this->assertFalse($this->validator->isValid(
            'john@doe.com',
            array(
                'user' => array(
                    'email' => 'harry@hoe.com'
                )
            )
        ));

        $this->assertFalse($this->validator->isValid(
            'harry@hoe.com',
            array(
                'user' => array(
                    'email' => 'john@doe.com'
                )
            )
        ));
    }
}
