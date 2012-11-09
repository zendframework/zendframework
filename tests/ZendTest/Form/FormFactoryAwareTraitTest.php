<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ZendTest\Form;

use \PHPUnit_Framework_TestCase as TestCase;
use \Zend\Form\Factory;
use \ZendTest\Form\TestAsset\MockFormFactoryAwareTrait;

class FormFactoryAwareTraitTest extends TestCase
{
    /**
     * @requires PHP 5.4
     */
    public function testSetFormFactory()
    {
        $object = new MockFormFactoryAwareTrait;

        $this->assertAttributeEquals(null, 'factory', $object);

        $factory = new Factory;

        $object->setFormFactory($factory);

        $this->assertAttributeEquals($factory, 'factory', $object);
    }
}
