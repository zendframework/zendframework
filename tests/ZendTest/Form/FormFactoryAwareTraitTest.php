<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ZendTest\Form;

use \PHPUnit_Framework_TestCase as TestCase;
use \Zend\Form\Factory;

/**
 * @requires PHP 5.4
 */
class FormFactoryAwareTraitTest extends TestCase
{
    public function testSetFormFactory()
    {
        $object = $this->getObjectForTrait('\Zend\Form\FormFactoryAwareTrait');

        $this->assertAttributeEquals(null, 'factory', $object);

        $factory = new Factory;

        $object->setFormFactory($factory);

        $this->assertAttributeEquals($factory, 'factory', $object);
    }
}
