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
use Zend\InputFilter\Input;
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
        $foo    = new Input('foo');
        $filter->add($foo);
        $this->assertEquals(1, count($filter));
        $bar    = new Input('bar');
        $filter->add($bar);
        $this->assertEquals(2, count($filter));
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

    public function testCanGetValidationMessages()
    {
        $this->markTestIncomplete();
    }
}
