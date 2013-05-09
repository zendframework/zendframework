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
use Zend\Form\Element\Email as EmailElement;

class EmailTest extends TestCase
{
    public function testProvidesInputSpecificationThatIncludesDefaultValidators()
    {
        $element = new EmailElement();

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedValidators = array(
            'Zend\Validator\Regex'
        );
        foreach ($inputSpec['validators'] as $i => $validator) {
            $class = get_class($validator);
            $this->assertEquals($expectedValidators[$i], $class);
        }
    }

    public function emailAttributesDataProvider()
    {
        return array(
                  // attributes               // expectedValidators
            array(array('multiple' => true),  array('Zend\Validator\Explode')),
            array(array('multiple' => false), array('Zend\Validator\Regex')),
        );
    }

    /**
     * @dataProvider emailAttributesDataProvider
     */
    public function testProvidesInputSpecificationBasedOnAttributes($attributes, $expectedValidators)
    {
        $element = new EmailElement();
        $element->setAttributes($attributes);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        foreach ($inputSpec['validators'] as $i => $validator) {
            $class = get_class($validator);
            $this->assertEquals($expectedValidators[$i], $class);
            switch ($class) {
                case 'Zend\Validator\Explode':
                    $this->assertInstanceOf('Zend\Validator\Regex', $validator->getValidator());
                    break;
                default:
                    break;
            }
        }
    }
}
