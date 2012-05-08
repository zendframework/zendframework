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
 * @package    Zend_Code_Generator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Code\Generator;
use Zend\Code\Generator\ValueGenerator;

/**
 * @category   Zend
 * @package    Zend_Code_Generator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group Zend_Code_Generator
 * @group Zend_Code_Generator_Php
 */
class ValueGeneratorTest extends \PHPUnit_Framework_TestCase
{

    public function testPropertyDefaultValueConstructor()
    {
        $valueGenerator = new ValueGenerator();
        $this->isInstanceOf($valueGenerator, 'Zend\Code\Generator\ValueGenerator');
    }

    public function testPropertyDefaultValueIsSettable()
    {
        $valueGenerator = new ValueGenerator();
        $valueGenerator->setValue('foo');
        $this->assertEquals('foo', $valueGenerator->getValue());
        //$this->assertEquals('\'foo\';', $valueGenerator->generate());
    }

    public function testPropertyDefaultValueCanHandleStrings()
    {
        $valueGenerator = new ValueGenerator();
        $valueGenerator->setValue('foo');
        $this->assertEquals('\'foo\'', $valueGenerator->generate());
    }

    public function testPropertyDefaultValueCanHandleArray()
    {
        $valueGenerator = new ValueGenerator();
        $valueGenerator->setValue(array('foo'));
        $this->assertEquals('array(\'foo\')', $valueGenerator->generate());
    }

    public function testPropertyDefaultValueCanHandleUnquotedString()
    {
        $valueGenerator = new ValueGenerator();
        $valueGenerator->setValue('PHP_EOL');
        $valueGenerator->setType('constant');
        $this->assertEquals('PHP_EOL', $valueGenerator->generate());

        $valueGenerator = new ValueGenerator();
        $valueGenerator->setValue(5);
        $this->assertEquals('5', $valueGenerator->generate());

        $valueGenerator = new ValueGenerator();
        $valueGenerator->setValue(5.25);
        $this->assertEquals('5.25', $valueGenerator->generate());
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
            new ValueGenerator('PHP_EOL', 'constant')
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
        )
EOS;

        $valueGenerator = new ValueGenerator();
        $valueGenerator->setValue($targetValue);
        $generatedTargetSource = $valueGenerator->generate();
        $this->assertEquals($expectedSource, $generatedTargetSource);

    }


}
