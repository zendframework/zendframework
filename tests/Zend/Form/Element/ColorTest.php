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
 * @package    Zend_Form
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Form\Element;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Element\Color as ColorElement;
use Zend\Form\Factory;

class ColorTest extends TestCase
{
    public function colorData()
    {
        return array(
            array('#012345',     true),
            array('#abcdef',     true),
            array('#012abc',     true),
            array('#012abcd',    false),
            array('#012abcde',   false),
            array('#ABCDEF',     true),
            array('#012ABC',     true),
            array('#bcdefg',     false),
            array('#01a',        false),
            array('01abcd',      false),
            array('blue',        false),
            array('transparent', false),
        );
    }

    /**
     * @dataProvider colorData
     */
    public function testLazyLoadsRegexValidatorByDefaultAndValidatesColors($color, $expected)
    {
        $element   = new ColorElement();
        $validator = $element->getValidator();
        $this->assertInstanceOf('Zend\Validator\Regex', $validator);
        $this->assertEquals($expected, $validator->isValid($color));
    }

    public function testCanInjectValidator()
    {
        $element   = new ColorElement();
        $validator = $this->getMock('Zend\Validator\ValidatorInterface');
        $element->setValidator($validator);
        $this->assertSame($validator, $element->getValidator());
    }

    public function testProvidesInputSpecificationThatIncludesValidator()
    {
        $element = new ColorElement();
        $validator = $this->getMock('Zend\Validator\ValidatorInterface');
        $element->setValidator($validator);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);
        $test = array_shift($inputSpec['validators']);
        $this->assertSame($validator, $test);
    }
}
