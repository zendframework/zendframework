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
 * @package    Zend_InputFilter
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\InputFilter;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\InputFilter\InputFilter;
use Zend\Filter;
use Zend\Validator;

class InputFilterTest extends TestCase
{
    public function testInputFilterIsEmptyByDefault()
    {
        $filter = new InputFilter();
        $this->assertEquals(0, count($filter));
    }

    public function testAddingInputsIncreasesCountOfFilter()
    {
        $filter = new InputFilter();
        $filter->add('foo');
        $this->assertEquals(1, count($filter));
        $filter->add('bar');
        $this->assertEquals(2, count($filter));
    }

    public function testAddingAnInputCreatesAnEmptyValidatorChain()
    {
        $filter = new InputFilter();
        $filter->add('foo');
        $input      = $filter->get('foo');
        $validators = $input->getValidatorChain();
        $this->assertInstanceOf('Zend\Validator\ValidatorChain', $validators);
        $this->assertEquals(0, count($validators));
    }

    public function testAddingAnInputCreatesAnEmptyFilterChain()
    {
        $filter = new InputFilter();
        $filter->add('foo');
        $input   = $filter->get('foo');
        $filters = $input->getFilterChain();
        $this->assertInstanceOf('Zend\Filter\FilterChain', $filters);
        $this->assertEquals(0, count($filters));
    }

    public function testCanAddInputWithValidatorChain()
    {
        $filter = new InputFilter();
        $chain  = new Validator\ValidatorChain();
        $filter->add('foo', $chain);
        $input = $filter->get('foo');
        $test  = $input->getValidatorChain();
        $this->assertSame($chain, $test);
    }

    public function testCanAddInputWithFilterChain()
    {
        $filter = new InputFilter();
        $chain  = new Filter\FilterChain();
        $filter->add('foo', $chain);
        $input = $filter->get('foo');
        $test  = $input->getFilterChain();
        $this->assertSame($chain, $test);
    }

    public function testCanAddInputWithBothFilterAndValidatorChains()
    {
        $filter     = new InputFilter();
        $validators = new Validator\ValidatorChain();
        $filters    = new Filter\FilterChain();
        $filter->add('foo', $filters, $validators);
        $input = $filter->get('foo');
        $test  = $input->getFilterChain();
        $this->assertSame($filters, $test);
        $test  = $input->getValidatorChain();
        $this->assertSame($validators, $test);
    }

    public function testCanAddInputWithBothFilterAndValidatorChainsAndOrderOfArgumentsDoesNotMatter()
    {
        $filter     = new InputFilter();
        $validators = new Validator\ValidatorChain();
        $filters    = new Filter\FilterChain();
        $filter->add('foo', $validators, $filters);
        $input = $filter->get('foo');
        $test  = $input->getFilterChain();
        $this->assertSame($filters, $test);
        $test  = $input->getValidatorChain();
        $this->assertSame($validators, $test);
    }

    public function testCanAddConcreteInputToInputFilter()
    {
        $this->markTestIncomplete();
    }

    public function testUsesFrameworkFilterBrokerByDefault()
    {
        $this->markTestIncomplete();
    }

    public function testCanInjectAFilterBroker()
    {
        $this->markTestIncomplete();
    }

    public function testInjectedFilterBrokerIsInjectedIntoAllInputsCreatedByInputFilter()
    {
        $this->markTestIncomplete();
    }

    public function testInjectedFilterBrokerIsNotInjectedIntoConcreteInputsAddedToInputFilter()
    {
        $this->markTestIncomplete();
    }

    public function testUsesFrameworkValidatorBrokerByDefault()
    {
        $this->markTestIncomplete();
    }

    public function testCanInjectAValidatorBroker()
    {
        $this->markTestIncomplete();
    }

    public function testInjectedValidatorBrokerIsInjectedIntoAllInputsCreatedByInputFilter()
    {
        $this->markTestIncomplete();
    }

    public function testInjectedValidatorBrokerIsNotInjectedIntoConcreteInputsAddedToInputFilter()
    {
        $this->markTestIncomplete();
    }

    public function testCanAddInputFilterAsInput()
    {
        $this->markTestIncomplete();
    }

    public function testCanValidateEntireDataset()
    {
        $this->markTestIncomplete();
    }

    public function testCanValidatePartialDataset()
    {
        $this->markTestIncomplete();
    }

    public function testCanRetrieveInvalidInputsOnFailedValidation()
    {
        $this->markTestIncomplete();
    }

    public function testCanRetrieveValidInputsOnFailedValidation()
    {
        $this->markTestIncomplete();
    }

    public function testValuesRetrievedAreFiltered()
    {
        $this->markTestIncomplete();
    }

    public function testCanGetRawInputValues()
    {
        $this->markTestIncomplete();
    }
}
