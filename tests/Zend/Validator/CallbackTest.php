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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class CallbackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valid = new Validator\Callback(array($this, 'objectCallback'));
        $this->assertTrue($valid->isValid('test'));
    }

    public function testStaticCallback()
    {
        $valid = new Validator\Callback(
            array('\ZendTest\Validator\CallbackTest', 'staticCallback')
        );
        $this->assertTrue($valid->isValid('test'));
    }

    public function testSettingDefaultOptionsAfterwards()
    {
        $valid = new Validator\Callback(array($this, 'objectCallback'));
        $valid->setCallbackOptions('options');
        $this->assertEquals(array('options'), $valid->getCallbackOptions());
        $this->assertTrue($valid->isValid('test'));
    }

    public function testSettingDefaultOptions()
    {
        $valid = new Validator\Callback(array('callback' => array($this, 'objectCallback'), 'callbackOptions' => 'options'));
        $this->assertEquals(array('options'), $valid->getCallbackOptions());
        $this->assertTrue($valid->isValid('test'));
    }

    public function testGettingCallback()
    {
        $valid = new Validator\Callback(array($this, 'objectCallback'));
        $this->assertEquals(array($this, 'objectCallback'), $valid->getCallback());
    }

    public function testInvalidCallback()
    {
        $valid = new Validator\Callback(array($this, 'objectCallback'));

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'Invalid callback given');
        $valid->setCallback('invalidcallback');
    }

    public function testAddingValueOptions()
    {
        $valid = new Validator\Callback(array('callback' => array($this, 'optionsCallback'), 'callbackOptions' => 'options'));
        $this->assertEquals(array('options'), $valid->getCallbackOptions());
        $this->assertTrue($valid->isValid('test', 'something'));
    }

    public function testEqualsMessageTemplates()
    {
        $validator = new Validator\Callback(array($this, 'objectCallback'));
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
        $validator = new Validator\Callback(array($this, 'objectCallback'));
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

    public function objectCallback($value)
    {
        return true;
    }

    public static function staticCallback($value)
    {
        return true;
    }

    public function optionsCallback($value)
    {
        $args = func_get_args();
        $this->assertContains('something', $args);
        return $args;
    }
}
