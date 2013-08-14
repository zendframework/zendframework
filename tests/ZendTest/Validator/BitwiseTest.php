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

use Zend\Validator\Bitwise;

class BitwiseTest extends \PHPUnit_Framework_TestCase
{
    /** @var Bitwise */
    public $validator;

    public function setUp()
    {
        $this->validator = new Bitwise();
    }

    /**
     * @covers Bitwise::isvalid()
     */
    public function testBitwiseAndNotStrict()
    {
        $controlSum = 0x7; // (0x1 | 0x2 | 0x4) === 0x7

        $validator = new Bitwise();
        $validator->setControl($controlSum);
        $validator->setOperator(Bitwise::OP_AND);

        $this->assertTrue($validator->isValid(0x1));
        $this->assertTrue($validator->isValid(0x2));
        $this->assertTrue($validator->isValid(0x4));
        $this->assertFalse($validator->isValid(0x8));

        $this->assertTrue($validator->isValid(0x1 | 0x2));
        $this->assertTrue($validator->isValid(0x1 | 0x2 | 0x4));
        $this->assertTrue($validator->isValid(0x1 | 0x8));
    }

    /**
     * @covers Bitwise::isvalid()
     */
    public function testBitwiseAndStrict()
    {
        $controlSum = 0x7; // (0x1 | 0x2 | 0x4) === 0x7

        $validator = new Bitwise();
        $validator->setControl($controlSum);
        $validator->setOperator(Bitwise::OP_AND);
        $validator->setStrict(true);

        $this->assertTrue($validator->isValid(0x1));
        $this->assertTrue($validator->isValid(0x2));
        $this->assertTrue($validator->isValid(0x4));
        $this->assertFalse($validator->isValid(0x8));

        $this->assertTrue($validator->isValid(0x1 | 0x2));
        $this->assertTrue($validator->isValid(0x1 | 0x2 | 0x4));
        $this->assertFalse($validator->isValid(0x1 | 0x8));
    }

    /**
     * @covers Bitwise::isvalid()
     */
    public function testBitwiseXor()
    {
        $controlSum = 0x5; // (0x1 | 0x4) === 0x5

        $validator = new Bitwise();
        $validator->setControl($controlSum);
        $validator->setOperator(Bitwise::OP_XOR);

        $this->assertTrue($validator->isValid(0x2));
        $this->assertTrue($validator->isValid(0x8));
        $this->assertTrue($validator->isValid(0x10));
        $this->assertFalse($validator->isValid(0x1));
        $this->assertFalse($validator->isValid(0x4));

        $this->assertTrue($validator->isValid(0x8 | 0x10));
        $this->assertFalse($validator->isValid(0x1 | 0x4));
        $this->assertFalse($validator->isValid(0x1 | 0x8));
        $this->assertFalse($validator->isValid(0x4 | 0x8));
    }

    /**
     * @covers Bitwise::setOperator()
     */
    public function testSetOperator()
    {
        $validator = new Bitwise();

        $validator->setOperator(Bitwise::OP_AND);
        $this->assertSame(Bitwise::OP_AND, $validator->getOperator());

        $validator->setOperator(Bitwise::OP_XOR);
        $this->assertSame(Bitwise::OP_XOR, $validator->getOperator());
    }

    /**
     * @covers Bitwise::setStrict()
     */
    public function testSetStrict()
    {
        $validator = new Bitwise();

        $this->assertFalse($validator->getStrict(), 'Strict false by default');

        $validator->setStrict(false);
        $this->assertFalse($validator->getStrict());

        $validator->setStrict(true);
        $this->assertTrue($validator->getStrict());

        $validator = new Bitwise(0x1, Bitwise::OP_AND, false);
        $this->assertFalse($validator->getStrict());

        $validator = new Bitwise(0x1, Bitwise::OP_AND, true);
        $this->assertTrue($validator->getStrict());
    }
}
