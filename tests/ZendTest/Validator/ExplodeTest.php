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

use Zend\Validator\Explode;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class ExplodeTest extends \PHPUnit_Framework_TestCase
{
    public function testRaisesExceptionWhenValidatorIsMissing()
    {
        $validator = new Explode();
        $this->setExpectedException('Zend\Validator\Exception\RuntimeException', 'validator');
        $validator->isValid('foo,bar');
    }

    public function getExpectedData()
    {
        return array(
            //    value              delim break  N  valid  messages                   expects
            array('foo,bar,dev,null', ',', false, 4, true,  array(),                   true),
            array('foo,bar,dev,null', ',', true,  1, false, array('X'),                false),
            array('foo,bar,dev,null', ',', false, 4, false, array('X', 'X', 'X', 'X'), false),
            array('foo,bar,dev,null', ';', false, 1, true,  array(),                   true),
            array('foo;bar,dev;null', ',', false, 2, true,  array(),                   true),
            array('foo;bar,dev;null', ',', false, 2, false, array('X', 'X'),           false),
            array('foo;bar;dev;null', ';', false, 4, true,  array(),                   true),
            array('foo',              ',', false, 1, true,  array(),                   true),
            array('foo',              ',', false, 1, false, array('X'),                false),
            array('foo',              ',', true,  1, false, array('X'),                false),
            array(array('a', 'b'),   null, false, 2, true,  array(),                   true),
            array(array('a', 'b'),   null, false, 2, false, array('X', 'X'),           false),
            array('foo',             null, false, 1, true,  array(),                   true),
            array(1,                  ',', false, 1, true,  array(),                   true),
            array(null,               ',', false, 1, true,  array(),                   true),
            array(new \stdClass(),    ',', false, 1, true,  array(),                   true),
            array(new \ArrayObject(array('a', 'b')), null, false, 2, true,  array(),   true),
        );
    }

    /**
     * @dataProvider getExpectedData
     */
    public function testExpectedBehavior($value, $delimiter, $breakOnFirst, $numIsValidCalls, $isValidReturn, $messages, $expects)
    {
        $mockValidator = $this->getMock('Zend\Validator\ValidatorInterface');
        $mockValidator->expects($this->exactly($numIsValidCalls))->method('isValid')->will($this->returnValue($isValidReturn));
        $mockValidator->expects($this->any())->method('getMessages')->will($this->returnValue('X'));

        $validator = new Explode(array(
            'validator'           => $mockValidator,
            'valueDelimiter'      => $delimiter,
            'breakOnFirstFailure' => $breakOnFirst,
        ));

        $this->assertEquals($expects,  $validator->isValid($value));
        $this->assertEquals($messages, $validator->getMessages());
    }

    public function testGetMessagesReturnsDefaultValue()
    {
        $validator = new Explode();
        $this->assertEquals(array(), $validator->getMessages());
    }

    public function testEqualsMessageTemplates()
    {
        $validator = new Explode(array());
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }

    public function testEqualsMessageVariables()
    {
        $validator = new Explode(array());
        $this->assertAttributeEquals($validator->getOption('messageVariables'),
                                     'messageVariables', $validator);
    }
}
