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
 * @group      Zend_Validator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IsbnTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $validator = new Validator\Isbn();

        // Brave New World by Aldous Huxley
        $this->assertTrue($validator->isValid('0060929871'));
        $this->assertFalse($validator->isValid('006092987X'));

        // Time Rations by Benjamin Friedlander
        $this->assertTrue($validator->isValid('188202205X'));
        $this->assertFalse($validator->isValid('1882022059'));

        // Towards The Primeval Lighting Field by Will Alexander
        $this->assertTrue($validator->isValid('1882022300'));
        $this->assertFalse($validator->isValid('1882022301'));

        //  ISBN-13 for dummies by Zoë Wykes
        $this->assertTrue($validator->isValid('9780555023402'));
        $this->assertFalse($validator->isValid('97805550234029'));

        // Change Your Brain, Change Your Life Daniel G. Amen
        $this->assertTrue($validator->isValid('9780812929980'));
        $this->assertFalse($validator->isValid('9780812929981'));
    }

    /**
     * Ensures that setSeparator() works as expected
     *
     * @return void
     */
    public function testType()
    {
        $validator = new Validator\Isbn();

        $validator->setType(Validator\Isbn::AUTO);
        $this->assertTrue($validator->getType() == Validator\Isbn::AUTO);

        $validator->setType(Validator\Isbn::ISBN10);
        $this->assertTrue($validator->getType() == Validator\Isbn::ISBN10);

        $validator->setType(Validator\Isbn::ISBN13);
        $this->assertTrue($validator->getType() == Validator\Isbn::ISBN13);

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'Invalid ISBN type');
        $validator->setType('X');
    }

    /**
     * Ensures that setSeparator() works as expected
     *
     * @return void
     */
    public function testSeparator()
    {
        $validator = new Validator\Isbn();
    
        $validator->setSeparator('-');
        $this->assertTrue($validator->getSeparator() == '-');
        
        $validator->setSeparator(' ');
        $this->assertTrue($validator->getSeparator() == ' ');

        $validator->setSeparator('');
        $this->assertTrue($validator->getSeparator() == '');

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'Invalid ISBN separator');
        $validator->setSeparator('X');
    }
        

    /**
     * Ensures that __construct() works as expected
     *
     * @return void
     */
    public function testInitialization()
    {
        $options = array('type'      => Validator\Isbn::AUTO,
                         'separator' => ' ');
        $validator = new Validator\Isbn($options);
        $this->assertTrue($validator->getType() == Validator\Isbn::AUTO);
        $this->assertTrue($validator->getSeparator() == ' ');

        $options = array('type'      => Validator\Isbn::ISBN10,
                         'separator' => '-');
        $validator = new Validator\Isbn($options);
        $this->assertTrue($validator->getType() == Validator\Isbn::ISBN10);
        $this->assertTrue($validator->getSeparator() == '-');

        $options = array('type'      => Validator\Isbn::ISBN13,
                         'separator' => '');
        $validator = new Validator\Isbn($options);
        $this->assertTrue($validator->getType() == Validator\Isbn::ISBN13);
        $this->assertTrue($validator->getSeparator() == '');
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testTypeAuto()
    {
        $validator = new Validator\Isbn();

        $this->assertTrue($validator->isValid('0060929871'));
        $this->assertFalse($validator->isValid('0-06-092987-1'));
        $this->assertFalse($validator->isValid('0 06 092987 1'));

        $this->assertTrue($validator->isValid('9780555023402'));
        $this->assertFalse($validator->isValid('978-0-555023-40-2'));
        $this->assertFalse($validator->isValid('978 0 555023 40 2'));

        $validator->setSeparator('-');

        $this->assertFalse($validator->isValid('0060929871'));
        $this->assertTrue($validator->isValid('0-06-092987-1'));
        $this->assertFalse($validator->isValid('0 06 092987 1'));

        $this->assertFalse($validator->isValid('9780555023402'));
        $this->assertTrue($validator->isValid('978-0-555023-40-2'));
        $this->assertFalse($validator->isValid('978 0 555023 40 2'));

        $validator->setSeparator(' ');

        $this->assertFalse($validator->isValid('0060929871'));
        $this->assertFalse($validator->isValid('0-06-092987-1'));
        $this->assertTrue($validator->isValid('0 06 092987 1'));

        $this->assertFalse($validator->isValid('9780555023402'));
        $this->assertFalse($validator->isValid('978-0-555023-40-2'));
        $this->assertTrue($validator->isValid('978 0 555023 40 2'));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testType10()
    {
        $validator = new Validator\Isbn();
        $validator->setType(Validator\Isbn::ISBN10);

        $this->assertTrue($validator->isValid('0060929871'));
        $this->assertFalse($validator->isValid('9780555023402'));

        $validator->setSeparator('-');

        $this->assertTrue($validator->isValid('0-06-092987-1'));
        $this->assertFalse($validator->isValid('978-0-555023-40-2'));

        $validator->setSeparator(' ');

        $this->assertTrue($validator->isValid('0 06 092987 1'));
        $this->assertFalse($validator->isValid('978 0 555023 40 2'));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testType13()
    {
        $validator = new Validator\Isbn();
        $validator->setType(Validator\Isbn::ISBN13);

        $this->assertFalse($validator->isValid('0060929871'));
        $this->assertTrue($validator->isValid('9780555023402'));

        $validator->setSeparator('-');

        $this->assertFalse($validator->isValid('0-06-092987-1'));
        $this->assertTrue($validator->isValid('978-0-555023-40-2'));

        $validator->setSeparator(' ');

        $this->assertFalse($validator->isValid('0 06 092987 1'));
        $this->assertTrue($validator->isValid('978 0 555023 40 2'));
    }

    /**
     * @group ZF-9605
     */
    public function testInvalidTypeGiven()
    {
        $validator = new Validator\Isbn();
        $validator->setType(Validator\Isbn::ISBN13);

        $this->assertFalse($validator->isValid((float) 1.2345));
        $this->assertFalse($validator->isValid((object) 'Test'));
    }
    
    public function testEqualsMessageTemplates()
    {
        $validator = new Validator\Isbn();
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
        $validator = new Validator\Isbn();
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
