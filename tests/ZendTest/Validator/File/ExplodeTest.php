<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Validator
 */

namespace ZendTest\Validator\File;

use Zend\Validator\File\Explode as FileExplode;

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
        $validator = new FileExplode();
        $this->setExpectedException('Zend\Validator\Exception\RuntimeException', 'validator');

        $file = array(array(
            'name'     => "test.jpg",
            'type'     => 'image/jpeg',
            'tmp_name' => "/private/tmp/php0Pnzdi",
            'error'    => 0,
            'size'     => 344215,
        ));
        $validator->isValid($file);
    }

    public function getExpectedData()
    {
        $files = array();
        for ($i = 0; $i < 3; $i++) {
            $files[] = array(
                'name'     => "test_$i.jpg",
                'type'     => 'image/jpeg',
                'tmp_name' => "/private/tmp/php0Pnzdi$i",
                'error'    => 0,
                'size'     => 344215,
            );
        }
        $file = array($files[0]);

        return array(
            //    value   break  N  valid  messages                   expects
            array($files, false, 3, true,  array(),              true),
            array($files, true,  1, false, array('X'),           false),
            array($files, false, 3, false, array('X', 'X', 'X'), false),
            array($file,  false, 1, true,  array(),              true),
            array($file,  false, 1, false, array('X'),           false),
            array($file,  true,  1, false, array('X'),           false),
            array('foo',  false, 0, true,  array(FileExplode::INVALID => 'Invalid'), false),
            array(1,      false, 0, true,  array(FileExplode::INVALID => 'Invalid'), false),
        );
    }

    /**
     * @dataProvider getExpectedData
     */
    public function testExpectedBehavior($value, $breakOnFirst, $numIsValidCalls, $isValidReturn, $messages, $expects)
    {
        $mockValidator = $this->getMock('Zend\Validator\ValidatorInterface');
        $mockValidator->expects($this->exactly($numIsValidCalls))->method('isValid')->will($this->returnValue($isValidReturn));
        $mockValidator->expects($this->any())->method('getMessages')->will($this->returnValue('X'));

        $validator = new FileExplode(array(
            'validator'           => $mockValidator,
            'breakOnFirstFailure' => $breakOnFirst,
        ));
        $validator->setMessage('Invalid', FileExplode::INVALID);

        $this->assertEquals($expects,  $validator->isValid($value));
        $this->assertEquals($messages, $validator->getMessages());
    }

    public function testGetMessagesReturnsDefaultValue()
    {
        $validator = new FileExplode();
        $this->assertEquals(array(), $validator->getMessages());
    }

    public function testEqualsMessageTemplates()
    {
        $validator = new FileExplode(array());
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }

    public function testEqualsMessageVariables()
    {
        $validator = new FileExplode(array());
        $this->assertAttributeEquals($validator->getOption('messageVariables'),
                                     'messageVariables', $validator);
    }
}
