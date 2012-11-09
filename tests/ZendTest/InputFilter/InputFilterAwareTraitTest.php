<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InputFilter
 */

namespace ZendTest\InputFilter;

use \PHPUnit_Framework_TestCase as TestCase;
use \Zend\InputFilter\InputFilter;

class InputFilterAwareTraitTest extends TestCase
{
    /**
     * @requires PHP 5.4
     */
    public function testSetInputFilter()
    {
        $object = new TestAsset\MockInputFilterAwareTrait;

        $this->assertAttributeEquals(null, 'inputFilter', $object);

        $inputFilter = new InputFilter;

        $object->setInputFilter($inputFilter);

        $this->assertAttributeEquals($inputFilter, 'inputFilter', $object);
    }

    /**
     * @requires PHP 5.4
     */
    public function testGetInputFilter()
    {
        $object = new TestAsset\MockInputFilterAwareTrait;

        $this->assertNull($object->getInputFilter());

        $inputFilter = new InputFilter;

        $object->setInputFilter($inputFilter);

        $this->assertEquals($inputFilter, $object->getInputFilter());
    }
}
