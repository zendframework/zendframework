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
 * @package    Zend_CodeGenerator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

/**
 * @see TestHelper
 */


/**
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group Zend_CodeGenerator
 * @group Zend_CodeGenerator_Php
 */
class Zend_CodeGenerator_Php_Property_DefaultValueTest extends PHPUnit_Framework_TestCase
{

    public function testPropertyDefaultValueConstructor()
    {
        $propDefaultValue = new Zend_CodeGenerator_Php_Property_DefaultValue();
        $this->isInstanceOf($propDefaultValue, 'Zend_CodeGenerator_Php_Property_DefaultValue');
    }

    public function testPropertyDefaultValueIsSettable()
    {
        $propDefaultValue = new Zend_CodeGenerator_Php_Property_DefaultValue();
        $propDefaultValue->setValue('foo');
        $this->assertEquals('foo', $propDefaultValue->getValue());
        //$this->assertEquals('\'foo\';', $propDefaultValue->generate());
    }

    public function testPropertyDefaultValueCanHandleStrings()
    {
        $propDefaultValue = new Zend_CodeGenerator_Php_Property_DefaultValue();
        $propDefaultValue->setValue('foo');
        $this->assertEquals('\'foo\';', $propDefaultValue->generate());
    }

    public function testPropertyDefaultValueCanHandleArray()
    {
        $propDefaultValue = new Zend_CodeGenerator_Php_Property_DefaultValue();
        $propDefaultValue->setValue(array('foo'));
        $this->assertEquals('array(\'foo\');', $propDefaultValue->generate());
    }

    public function testPropertyDefaultValueCanHandleUnquotedString()
    {
        $propDefaultValue = new Zend_CodeGenerator_Php_Property_DefaultValue();
        $propDefaultValue->setValue('PHP_EOL');
        $propDefaultValue->setType('constant');
        $this->assertEquals('PHP_EOL;', $propDefaultValue->generate());

        $propDefaultValue = new Zend_CodeGenerator_Php_Property_DefaultValue();
        $propDefaultValue->setValue(5);
        $this->assertEquals('5;', $propDefaultValue->generate());

        $propDefaultValue = new Zend_CodeGenerator_Php_Property_DefaultValue();
        $propDefaultValue->setValue(5.25);
        $this->assertEquals('5.25;', $propDefaultValue->generate());
    }

    public function testPropertyDefaultValueCanHandleComplexArrayOfTypes()
    {
        $targetValue = array(
            5,
            'one' => 1,
            'two' => '2',
            array(
                'foo',
                'bar',
                array(
                    'baz1',
                    'baz2'
                    )
                ),
            new Zend_CodeGenerator_Php_Property_DefaultValue(array('value' => 'PHP_EOL', 'type' => 'constant'))
            );

        $expectedSource = <<<EOS
array(
        5,
        'one' => 1,
        'two' => '2',
        array(
            'foo',
            'bar',
            array(
                'baz1',
                'baz2'
                )
            ),
        PHP_EOL
        );
EOS;

        $propDefaultValue = new Zend_CodeGenerator_Php_Property_DefaultValue();
        $propDefaultValue->setValue($targetValue);
        $generatedTargetSource = $propDefaultValue->generate();
        $this->assertEquals($expectedSource, $generatedTargetSource);

    }


}
