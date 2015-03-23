<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator;

use Zend\Validator\Explode;
use Zend\Validator\Regex;
use Zend\Validator\Callback;

/**
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
            array('foo,bar,dev,null', ',', false, 4, false, array('X'),                false),
            array('foo,bar,dev,null', ';', false, 1, true,  array(),                   true),
            array('foo;bar,dev;null', ',', false, 2, true,  array(),                   true),
            array('foo;bar,dev;null', ',', false, 2, false, array('X'),                false),
            array('foo;bar;dev;null', ';', false, 4, true,  array(),                   true),
            array('foo',              ',', false, 1, true,  array(),                   true),
            array('foo',              ',', false, 1, false, array('X'),                false),
            array('foo',              ',', true,  1, false, array('X'),                false),
            array(array('a', 'b'),   null, false, 2, true,  array(),                   true),
            array(array('a', 'b'),   null, false, 2, false, array('X'),                false),
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

    public function testSetValidatorAsArray()
    {
        $validator = new Explode();
        $validator->setValidator(
            array(
                'name' => 'inarray',
                'options' => array(
                    'haystack' => array(
                        'a', 'b', 'c'
                    )
                )
            )
        );

        /** @var $inArrayValidator \Zend\Validator\InArray */
        $inArrayValidator = $validator->getValidator();
        $this->assertInstanceOf('Zend\Validator\InArray', $inArrayValidator);
        $this->assertSame(
            array('a', 'b', 'c'), $inArrayValidator->getHaystack()
        );
    }

    /**
     * @expectedException \Zend\Validator\Exception\RuntimeException
     */
    public function testSetValidatorMissingName()
    {
        $validator = new Explode();
        $validator->setValidator(
            array(
                'options' => array()
            )
        );
    }

    /**
     * @expectedException \Zend\Validator\Exception\RuntimeException
     */
    public function testSetValidatorInvalidParam()
    {
        $validator = new Explode();
        $validator->setValidator('inarray');
    }

    /**
     * @group ZF2-5796
     */
    public function testGetMessagesMultipleInvalid()
    {
        $validator = new Explode(array(
            'validator'           => new Regex(
                '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/'
            ),
            'valueDelimiter'      => ',',
            'breakOnFirstFailure' => false,
        ));

        $messages = array(
            0 => array(
                'regexNotMatch' => "The input does not match against pattern '/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/'",
            ),
        );

        $this->assertFalse($validator->isValid('zf-devteam@zend.com,abc,defghij'));
        $this->assertEquals($messages, $validator->getMessages());
    }

    /**
     * Assert context is passed to composed validator
     */
    public function testIsValidPassContext()
    {
        $context       = 'context';
        $contextSame   = false;
        $validator = new Explode(array(
            'validator'           => new Callback(function ($v, $c) use ($context, &$contextSame) {
                $contextSame = ($context === $c);
                return true;
            }),
            'valueDelimiter'      => ',',
            'breakOnFirstFailure' => false,
        ));
        $this->assertTrue($validator->isValid('a,b,c', $context));
        $this->assertTrue($contextSame);
    }
}
