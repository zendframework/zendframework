<?php
/**
 * Created by JetBrains PhpStorm.
 * User: carnage
 * Date: 08/11/13
 * Time: 17:22
 * To change this template use File | Settings | File Templates.
 */

namespace ZendTest\Form;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\InputFilterProviderFieldset;

class InputFilterProviderFieldsetTest extends TestCase
{
    public function setUp()
    {
        $this->fieldset = new InputFilterProviderFieldset();
    }

    public function testCanSetInputFilterSpec()
    {
        $filterSpec = array('filter'=>array('filter_options'));

        $this->fieldset->setInputFilterSpecification($filterSpec);
        $this->assertEquals($filterSpec, $this->fieldset->getInputFilterSpecification());
    }

    public function testCanSetInputFilterSpecViaOptions()
    {
        $filterSpec = array('filter'=>array('filter_options'));

        $this->fieldset->setOptions(array('input_filter_spec'=>$filterSpec));
        $this->assertEquals($filterSpec, $this->fieldset->getInputFilterSpecification());
    }

    public function testFilterSpecIsInitiallyEmpty()
    {
        $this->assertEmpty($this->fieldset->getInputFilterSpecification());
    }

}