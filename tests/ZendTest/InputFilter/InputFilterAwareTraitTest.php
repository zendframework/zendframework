<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InputFilter
 */

namespace ZendTest\InputFilter;

use \PHPUnit_Framework_TestCase as TestCase;
use \Zend\InputFilter\InputFilter;

/**
 * @requires PHP 5.4
 */
class InputFilterAwareTraitTest extends TestCase
{
    public function testSetInputFilter()
    {
        $object = $this->getObjectForTrait('\Zend\InputFilter\InputFilterAwareTrait');

        $this->assertAttributeEquals(null, 'inputFilter', $object);

        $inputFilter = new InputFilter;

        $object->setInputFilter($inputFilter);

        $this->assertAttributeEquals($inputFilter, 'inputFilter', $object);
    }

    public function testGetInputFilter()
    {
        $object = $this->getObjectForTrait('\Zend\InputFilter\InputFilterAwareTrait');

        $this->assertNull($object->getInputFilter());

        $inputFilter = new InputFilter;

        $object->setInputFilter($inputFilter);

        $this->assertEquals($inputFilter, $object->getInputFilter());
    }
}
