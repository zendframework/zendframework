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
use Zend\Validator\Regex as RegexValidator;

class ColorTest extends TestCase
{
    public function colorData()
    {
        return array(
            array('#012345', 'assertTrue'),
            array('#abcdef', 'assertTrue'),
            array('#012abc', 'assertTrue'),
            array('#01a',    'assertFalse'),
            array('01abcd',  'assertFalse'),
            array('blue',    'assertFalse'),
        );
    }

    /**
     * @dataProvider colorData
     */
    public function testLazyLoadsRegexValidatorByDefault($color, $assertion)
    {
        $element   = new ColorElement();
        $validator = $element->getValidator();
        $this->assertInstanceOf('Zend\Validator\Regex', $validator);
        $this->$assertion($validator->isValid($color));
    }

    public function testCanInjectValidator()
    {
        $element   = new ColorElement();
        $validator = new RegexValidator('/^#[0-9a-z]{6}$/');
        $element->setValidator($validator);
        $this->assertSame($validator, $element->getValidator());
    }

    public function testProvidesInputSpecificationThatIncludesRegexValidator()
    {
        $element = new ColorElement();
        $validator = new RegexValidator('/^#[0-9a-z]{6}$/');
        $element->setValidator($validator);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);
        $test = array_shift($inputSpec['validators']);
        $this->assertSame($validator, $test);
    }
}
