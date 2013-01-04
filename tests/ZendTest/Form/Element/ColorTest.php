<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ZendTest\Form\Element;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Element\Color as ColorElement;

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

    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes()
    {
        $element = new ColorElement();

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedClasses = array(
            'Zend\Validator\Regex'
        );
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertTrue(in_array($class, $expectedClasses), $class);
            switch ($class) {
                case 'Zend\Validator\Regex':
                    $this->assertEquals('/^#[0-9a-fA-F]{6}$/', $validator->getPattern());
                    break;
                default:
                    break;
            }
        }
    }
}
