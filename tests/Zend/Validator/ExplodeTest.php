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

namespace ZendTest\Validator;

use Zend\Validator\Explode;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
            array(array(),            ',', false, 0, true,  array(Explode::INVALID => 'Invalid'), false),
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
        $validator->setMessage('Invalid', Explode::INVALID);

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
