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

namespace ZendTest\Form\Annotation;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Annotation;
use ZendTest\Form\TestAsset;

class AnnotationBuilderTest extends TestCase
{
    public function testCanCreateFormFromStandardEntity()
    {
        $entity  = new TestAsset\Annotation\Entity();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $this->assertTrue($form->has('username'));
        $this->assertTrue($form->has('password'));

        $username = $form->get('username');
        $this->assertInstanceOf('Zend\Form\Element', $username);
        $password = $form->get('password');
        $this->assertInstanceOf('Zend\Form\Element', $password);
        $attributes = $password->getAttributes();
        $this->assertEquals(array('type' => 'password', 'label' => 'Enter your password', 'name' => 'password'), $attributes);

        $filter = $form->getInputFilter();
        $this->assertTrue($filter->has('username'));
        $this->assertTrue($filter->has('password'));

        $username = $filter->get('username');
        $this->assertTrue($username->isRequired());
        $this->assertEquals(1, count($username->getFilterChain()));
        $this->assertEquals(2, count($username->getValidatorChain()));

        $password = $filter->get('password');
        $this->assertTrue($password->isRequired());
        $this->assertEquals(1, count($password->getFilterChain()));
        $this->assertEquals(1, count($password->getValidatorChain()));
    }

    public function testCanCreateFormWithClassAnnotations()
    {
        $entity  = new TestAsset\Annotation\ClassEntity();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $this->assertTrue($form->has('keeper'));
        $this->assertFalse($form->has('keep'));
        $this->assertFalse($form->has('omit'));
        $this->assertEquals('some_name', $form->getName());

        $attributes = $form->getAttributes();
        $this->assertArrayHasKey('legend', $attributes);
        $this->assertEquals('Some Fieldset', $attributes['legend']);

        $filter = $form->getInputFilter();
        $this->assertInstanceOf('ZendTest\Form\TestAsset\Annotation\InputFilter', $filter);

        $keeper     = $form->get('keeper');
        $attributes = $keeper->getAttributes();
        $this->assertArrayHasKey('type', $attributes);
        $this->assertEquals('text', $attributes['type']);
    }
}
