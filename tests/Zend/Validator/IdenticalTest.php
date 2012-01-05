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
 * @package    Zend
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

/** Zend_Validator_Identical */

/**
 * Zend_Validator_Identical
 *
 * @category   Zend
 * @package    Zend
 * @subpackage UnitTests
 * @uses       Zend_Validator_Identical
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class IdenticalTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->validator = new Validator\Identical;
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
        $validator = new Validator\Identical('foo');
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
        $validator = new Validator\Identical(array('token' => 123));
        $this->assertTrue($validator->isValid(123));
        $this->assertFalse($validator->isValid(array('token' => 123)));
    }

    public function testValidatingNonStrictToken()
    {
        $validator = new Validator\Identical(array('token' => 123, 'strict' => false));
        $this->assertTrue($validator->isValid('123'));

        $validator->setStrict(true);
        $this->assertFalse($validator->isValid(array('token' => '123')));
    }
    
    public function testEqualsMessageTemplates()
    {
        $validator = new Validator\Identical();
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
        $validator = new Validator\Identical();
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
