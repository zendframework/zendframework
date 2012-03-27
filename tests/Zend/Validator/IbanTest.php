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

use ReflectionClass,
    Zend\Registry,
    Zend\Validator;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class IbanTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Registry::_unsetInstance();
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $validator = new Validator\Iban();
        $valuesExpected = array(
            'AD1200012030200359100100' => true,
            'AT611904300234573201'     => true,
            'AT61 1904 3002 3457 3201' => false,
            'AD1200012030200354100100' => false,
        );
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals($result, $validator->isValid($input),
                                "'$input' expected to be " . ($result ? '' : 'in') . 'valid');
        }
    }

    public function testSettingAndGettingLocale()
    {
        $validator = new Validator\Iban();

        $validator->setLocale('de_DE');
        $this->assertEquals('de_DE', $validator->getLocale());

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'IBAN validation');
        $validator->setLocale('de_QA');

    }

    public function testInstanceWithLocale()
    {
        $validator = new Validator\Iban('de_AT');
        $this->assertTrue($validator->isValid('AT611904300234573201'));
    }

    public function testIbanNotSupported()
    {
        $validator = new Validator\Iban('en_US');
        $this->assertFalse($validator->isValid('AT611904300234573201'));
    }

    /**
     * @group ZF-10556
     */
    public function testIbanDetectionWithoutLocale()
    {
        $validator = new Validator\Iban(false);
        $this->assertTrue($validator->isValid('AT611904300234573201'));
    }
    
    public function testEqualsMessageTemplates()
    {
        $validator = new Validator\Iban();
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
        $validator = new Validator\Iban();
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
