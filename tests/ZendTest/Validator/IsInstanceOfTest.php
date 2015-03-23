<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
namespace ZendTest\Validator;

use DateTime;
use ReflectionClass;
use Zend\Validator;

/**
 * @covers     Zend\Validator\IsInstanceOf
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class IsInstanceOfTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $validator = new Validator\IsInstanceOf('DateTime');
        $this->assertTrue($validator->isValid(new DateTime())); // True
        $this->assertFalse($validator->isValid(null)); // False
        $this->assertFalse($validator->isValid($this)); // False

        $validator = new Validator\IsInstanceOf('Exception');
        $this->assertTrue($validator->isValid(new \Exception())); // True
        $this->assertFalse($validator->isValid(null)); // False
        $this->assertFalse($validator->isValid($this)); // False

        $validator = new Validator\IsInstanceOf('PHPUnit_Framework_TestCase');
        $this->assertTrue($validator->isValid($this)); // True
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $validator = new Validator\IsInstanceOf('DateTime');
        $this->assertEquals(array(), $validator->getMessages());
    }

    /**
     * Ensures that getClassName() returns expected value
     *
     * @return void
     */
    public function testGetClassName()
    {
        $validator = new Validator\IsInstanceOf('DateTime');
        $this->assertEquals('DateTime', $validator->getClassName());
    }

    public function testEqualsMessageTemplates()
    {
        $validator  = new Validator\IsInstanceOf('DateTime');
        $reflection = new ReflectionClass($validator);

        $property = $reflection->getProperty('messageTemplates');
        $property->setAccessible(true);

        $this->assertEquals(
            $property->getValue($validator),
            $validator->getOption('messageTemplates')
        );
    }

    public function testEqualsMessageVariables()
    {
        $validator  = new Validator\IsInstanceOf('\DateTime');
        $reflection = new ReflectionClass($validator);

        $property = $reflection->getProperty('messageVariables');
        $property->setAccessible(true);

        $this->assertEquals(
            $property->getValue($validator),
            $validator->getOption('messageVariables')
        );
    }

    public function testPassTraversableToConstructor()
    {
        $validator = new Validator\IsInstanceOf(new \ArrayIterator(array('className' => 'DateTime')));
        $this->assertEquals('DateTime', $validator->getClassName());
        $this->assertTrue($validator->isValid(new DateTime()));
        $this->assertFalse($validator->isValid(null));
        $this->assertFalse($validator->isValid($this));
    }

    public function testPassOptionsWithoutClassNameKey()
    {
        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'Missing option "className"');

        $options   = array('NotClassNameKey' => 'DateTime');
        $validator = new Validator\IsInstanceOf($options);
    }
}
