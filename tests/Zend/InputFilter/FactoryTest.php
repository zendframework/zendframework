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
use Zend\Filter;
use Zend\InputFilter\Factory;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator;

class FactoryTest extends TestCase
{
    public function testFactoryComposesFrameworkFilterChainByDefault()
    {
        $this->markTestIncomplete();
    }

    public function testFactoryComposesFrameworkValidatorChainByDefault()
    {
        $this->markTestIncomplete();
    }

    public function testFactoryAllowsInjectingFilterChain()
    {
        $this->markTestIncomplete();
    }

    public function testFactoryAllowsInjectingValidatorChain()
    {
        $this->markTestIncomplete();
    }

    public function testFactoryUsesComposedFilterChainWhenCreatingNewInputObjects()
    {
        $this->markTestIncomplete();
    }

    public function testFactoryUsesComposedValidatorChainWhenCreatingNewInputObjects()
    {
        $this->markTestIncomplete();
    }

    public function testFactoryInjectsComposedFilterChainWhenCreatingNewInputFilterObjects()
    {
        $this->markTestIncomplete();
    }

    public function testFactoryWillCreateInputWithSuggestedFilters()
    {
        $this->markTestIncomplete();
    }

    public function testFactoryWillCreateInputWithSuggestedValidators()
    {
        $this->markTestIncomplete();
    }

    public function testFactoryWillCreateInputWithSuggestedRequiredFlag()
    {
        $this->markTestIncomplete();
    }

    public function testFactoryWillCreateInputWithSuggestedAllowEmptyFlag()
    {
        $this->markTestIncomplete();
    }

    public function testFactoryWillCreateInputWithSuggestedName()
    {
        $this->markTestIncomplete();
    }

    public function testFactoryWillCreateInputFilterAndAllInputObjectsFromGivenConfiguration()
    {
        $this->markTestIncomplete();
    }

    public function testFactoryWillCreateNestedInputFiltersFromGivenConfiguration()
    {
        $this->markTestIncomplete();
    }
}
