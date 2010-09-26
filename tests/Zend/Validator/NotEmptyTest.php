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
use Zend\Validator;

/**
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */
class NotEmptyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Zend_Validate_NotEmpty object
     *
     * @var Zend_Validate_NotEmpty
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validate_NotEmpty object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_validator = new Validator\NotEmpty();
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * ZF-6708 introduces a change for validating integer 0; it is a valid
     * integer value. '0' is also valid.
     *
     * @group ZF-6708
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            array('word', true),
            array('', false),
            array('    ', false),
            array('  word  ', true),
            array('0', true),
            array(1, true),
            array(0, true),
            array(true, true),
            array(false, false),
            array(null, false),
            array(array(), false),
            array(array(5), true),
        );
        foreach ($valuesExpected as $i => $element) {
            $this->assertEquals($element[1], $this->_validator->isValid($element[0]),
                "Failed test #$i");
        }
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlyBoolean()
    {
        $this->_validator->setType(Validator\NotEmpty::BOOLEAN);
        $this->assertFalse($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertTrue($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertTrue($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertTrue($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertTrue($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertTrue($this->_validator->isValid(array()));
        $this->assertTrue($this->_validator->isValid(array('xxx')));
        $this->assertTrue($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlyInteger()
    {
        $this->_validator->setType(Validator\NotEmpty::INTEGER);
        $this->assertTrue($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertFalse($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertTrue($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertTrue($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertTrue($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertTrue($this->_validator->isValid(array()));
        $this->assertTrue($this->_validator->isValid(array('xxx')));
        $this->assertTrue($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlyFloat()
    {
        $this->_validator->setType(Validator\NotEmpty::FLOAT);
        $this->assertTrue($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertTrue($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertFalse($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertTrue($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertTrue($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertTrue($this->_validator->isValid(array()));
        $this->assertTrue($this->_validator->isValid(array('xxx')));
        $this->assertTrue($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlyString()
    {
        $this->_validator->setType(Validator\NotEmpty::STRING);
        $this->assertTrue($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertTrue($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertTrue($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertFalse($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertTrue($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertTrue($this->_validator->isValid(array()));
        $this->assertTrue($this->_validator->isValid(array('xxx')));
        $this->assertTrue($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlyZero()
    {
        $this->_validator->setType(Validator\NotEmpty::ZERO);
        $this->assertTrue($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertTrue($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertTrue($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertTrue($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertFalse($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertTrue($this->_validator->isValid(array()));
        $this->assertTrue($this->_validator->isValid(array('xxx')));
        $this->assertTrue($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlyArray()
    {
        $this->_validator->setType(Validator\NotEmpty::EMPTY_ARRAY);
        $this->assertTrue($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertTrue($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertTrue($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertTrue($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertTrue($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertFalse($this->_validator->isValid(array()));
        $this->assertTrue($this->_validator->isValid(array('xxx')));
        $this->assertTrue($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlyNull()
    {
        $this->_validator->setType(Validator\NotEmpty::NULL);
        $this->assertTrue($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertTrue($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertTrue($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertTrue($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertTrue($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertTrue($this->_validator->isValid(array()));
        $this->assertTrue($this->_validator->isValid(array('xxx')));
        $this->assertFalse($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlyPHP()
    {
        $this->_validator->setType(Validator\NotEmpty::PHP);
        $this->assertFalse($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertFalse($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertFalse($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertFalse($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertFalse($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertFalse($this->_validator->isValid(array()));
        $this->assertTrue($this->_validator->isValid(array('xxx')));
        $this->assertFalse($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlySpace()
    {
        $this->_validator->setType(Validator\NotEmpty::SPACE);
        $this->assertTrue($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertTrue($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertTrue($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertTrue($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertTrue($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertTrue($this->_validator->isValid(array()));
        $this->assertTrue($this->_validator->isValid(array('xxx')));
        $this->assertTrue($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlyAll()
    {
        $this->_validator->setType(Validator\NotEmpty::ALL);
        $this->assertFalse($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertFalse($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertFalse($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertFalse($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertFalse($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertFalse($this->_validator->isValid(array()));
        $this->assertTrue($this->_validator->isValid(array('xxx')));
        $this->assertFalse($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testArrayConstantNotation()
    {
        $filter = new Validator\NotEmpty(
            array(
                'type' => array(
                    Validator\NotEmpty::ZERO,
                    Validator\NotEmpty::STRING,
                    Validator\NotEmpty::BOOLEAN
                )
            )
        );

        $this->assertFalse($filter->isValid(false));
        $this->assertTrue($filter->isValid(true));
        $this->assertTrue($filter->isValid(0));
        $this->assertTrue($filter->isValid(1));
        $this->assertTrue($filter->isValid(0.0));
        $this->assertTrue($filter->isValid(1.0));
        $this->assertFalse($filter->isValid(''));
        $this->assertTrue($filter->isValid('abc'));
        $this->assertFalse($filter->isValid('0'));
        $this->assertTrue($filter->isValid('1'));
        $this->assertTrue($filter->isValid(array()));
        $this->assertTrue($filter->isValid(array('xxx')));
        $this->assertTrue($filter->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testArrayConfigNotation()
    {
        $filter = new Validator\NotEmpty(
            array(
                'type' => array(
                    Validator\NotEmpty::ZERO,
                    Validator\NotEmpty::STRING,
                    Validator\NotEmpty::BOOLEAN),
                'test' => false
            )
        );

        $this->assertFalse($filter->isValid(false));
        $this->assertTrue($filter->isValid(true));
        $this->assertTrue($filter->isValid(0));
        $this->assertTrue($filter->isValid(1));
        $this->assertTrue($filter->isValid(0.0));
        $this->assertTrue($filter->isValid(1.0));
        $this->assertFalse($filter->isValid(''));
        $this->assertTrue($filter->isValid('abc'));
        $this->assertFalse($filter->isValid('0'));
        $this->assertTrue($filter->isValid('1'));
        $this->assertTrue($filter->isValid(array()));
        $this->assertTrue($filter->isValid(array('xxx')));
        $this->assertTrue($filter->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testMultiConstantNotation()
    {
        $filter = new Validator\NotEmpty(
            Validator\NotEmpty::ZERO + Validator\NotEmpty::STRING + Validator\NotEmpty::BOOLEAN
        );

        $this->assertFalse($filter->isValid(false));
        $this->assertTrue($filter->isValid(true));
        $this->assertTrue($filter->isValid(0));
        $this->assertTrue($filter->isValid(1));
        $this->assertTrue($filter->isValid(0.0));
        $this->assertTrue($filter->isValid(1.0));
        $this->assertFalse($filter->isValid(''));
        $this->assertTrue($filter->isValid('abc'));
        $this->assertFalse($filter->isValid('0'));
        $this->assertTrue($filter->isValid('1'));
        $this->assertTrue($filter->isValid(array()));
        $this->assertTrue($filter->isValid(array('xxx')));
        $this->assertTrue($filter->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testStringNotation()
    {
        $filter = new Validator\NotEmpty(
            array(
                'type' => array('zero', 'string', 'boolean')
            )
        );

        $this->assertFalse($filter->isValid(false));
        $this->assertTrue($filter->isValid(true));
        $this->assertTrue($filter->isValid(0));
        $this->assertTrue($filter->isValid(1));
        $this->assertTrue($filter->isValid(0.0));
        $this->assertTrue($filter->isValid(1.0));
        $this->assertFalse($filter->isValid(''));
        $this->assertTrue($filter->isValid('abc'));
        $this->assertFalse($filter->isValid('0'));
        $this->assertTrue($filter->isValid('1'));
        $this->assertTrue($filter->isValid(array()));
        $this->assertTrue($filter->isValid(array('xxx')));
        $this->assertTrue($filter->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testSingleStringNotation()
    {
        $filter = new Validator\NotEmpty(
            'boolean'
        );

        $this->assertFalse($filter->isValid(false));
        $this->assertTrue($filter->isValid(true));
        $this->assertTrue($filter->isValid(0));
        $this->assertTrue($filter->isValid(1));
        $this->assertTrue($filter->isValid(0.0));
        $this->assertTrue($filter->isValid(1.0));
        $this->assertTrue($filter->isValid(''));
        $this->assertTrue($filter->isValid('abc'));
        $this->assertTrue($filter->isValid('0'));
        $this->assertTrue($filter->isValid('1'));
        $this->assertTrue($filter->isValid(array()));
        $this->assertTrue($filter->isValid(array('xxx')));
        $this->assertTrue($filter->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testConfigObject()
    {
        $options = array('type' => 'all');
        $config  = new \Zend\Config\Config($options);

        $filter = new Validator\NotEmpty(
            $config
        );

        $this->assertFalse($filter->isValid(false));
        $this->assertTrue($filter->isValid(true));
        $this->assertFalse($filter->isValid(0));
        $this->assertTrue($filter->isValid(1));
        $this->assertFalse($filter->isValid(0.0));
        $this->assertTrue($filter->isValid(1.0));
        $this->assertFalse($filter->isValid(''));
        $this->assertTrue($filter->isValid('abc'));
        $this->assertFalse($filter->isValid('0'));
        $this->assertTrue($filter->isValid('1'));
        $this->assertFalse($filter->isValid(array()));
        $this->assertTrue($filter->isValid(array('xxx')));
        $this->assertFalse($filter->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testSettingFalseType()
    {
        try {
            $this->_validator->setType(true);
            $this->fail();
        } catch (\Zend\Exception $e) {
            $this->assertContains('Unknown', $e->getMessage());
        }
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testGetType()
    {
        $this->assertEquals(493, $this->_validator->getType());
    }

    /**
     * @see ZF-3236
     */
    public function testStringWithZeroShouldNotBeTreatedAsEmpty()
    {
        $this->assertTrue($this->_validator->isValid('0'));
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals(array(), $this->_validator->getMessages());
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $v2 = new Validator\NotEmpty();
        $this->assertTrue($this->_validator->isValid($v2));
    }

    /**
     * @ZF-8767
     *
     * @return void
     */
    public function testZF8767()
    {
        $valid = new Validator\NotEmpty(Validator\NotEmpty::STRING);

        $this->assertFalse($valid->isValid(''));
        $messages = $valid->getMessages();
        $this->assertTrue(array_key_exists('isEmpty', $messages));
        $this->assertContains("can't be empty", $messages['isEmpty']);
    }

    /**
     * @return void
     */
    public function testObjects()
    {
        $valid = new Validator\NotEmpty(Validator\NotEmpty::STRING);
        $object = new ClassTest1();

        $this->assertFalse($valid->isValid($object));

        $valid = new Validator\NotEmpty(Validator\NotEmpty::OBJECT);
        $this->assertTrue($valid->isValid($object));
    }

    /**
     * @return void
     */
    public function testStringObjects()
    {
        $valid = new Validator\NotEmpty(Validator\NotEmpty::STRING);
        $object = new ClassTest2();

        $this->assertFalse($valid->isValid($object));

        $valid = new Validator\NotEmpty(Validator\NotEmpty::OBJECT_STRING);
        $this->assertTrue($valid->isValid($object));

        $object = new ClassTest3();
        $this->assertFalse($valid->isValid($object));
    }
}

class ClassTest1 {}

class ClassTest2
{
    public function __toString()
    {
        return 'Test';
    }
}

class ClassTest3
{
    public function toString()
    {
        return '';
    }
}
