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
use Zend\Form\Element\Url as UrlElement;

class UrlTest extends TestCase
{
    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes()
    {
        $element = new UrlElement();
        $element->setAttributes(array(
            'allowAbsolute' => true,
            'allowRelative' => false
        ));

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedClasses = array(
            'Zend\Validator\Uri'
        );
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertTrue(in_array($class, $expectedClasses), $class);
            switch ($class) {
                case 'Zend\Validator\Uri':
                    $this->assertEquals(true, $validator->getAllowAbsolute());
                    $this->assertEquals(false, $validator->getAllowRelative());
                    break;
                default:
                    break;
            }
        }
    }
}
