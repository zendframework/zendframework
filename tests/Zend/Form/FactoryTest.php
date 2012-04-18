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

namespace ZendTest\Form;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Filter;
use Zend\Form;
use Zend\Form\Factory as FormFactory;
use Zend\InputFilter;
use Zend\Validator;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FactoryTest extends TestCase
{
    public function setUp()
    {
        $this->factory = new FormFactory();
    }

    public function testCanCreateElements()
    {
        $element = $this->factory->createElement(array(
            'name'       => 'foo',
            'attributes' => array(
                'type'         => 'text',
                'class'        => 'foo-class',
                'data-js-type' => 'my.form.text',
            ),
        ));
        $this->assertInstanceOf('Zend\Form\ElementInterface', $element);
        $this->assertEquals('foo', $element->getName());
        $this->assertEquals('text', $element->getAttribute('type'));
        $this->assertEquals('foo-class', $element->getAttribute('class'));
        $this->assertEquals('my.form.text', $element->getAttribute('data-js-type'));
    }

    public function testCanCreateFieldsets()
    {
        $this->markTestIncomplete();
    }

    public function testCanCreateFieldsetsWithElements()
    {
        $this->markTestIncomplete();
    }

    public function testCanCreateNestedFieldsets()
    {
        $this->markTestIncomplete();
    }

    public function testCanCreateForms()
    {
        $this->markTestIncomplete();
    }

    public function testCanCreateFormsWithNamedInputFilters()
    {
        $this->markTestIncomplete();
    }

    public function testCanCreateFormsWithInputFilterSpecifications()
    {
        $this->markTestIncomplete();
    }

    public function testCanCreateFormsAndSpecifyHydrator()
    {
        $this->markTestIncomplete();
    }
}
