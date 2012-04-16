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
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FormTest extends TestCase
{
    public function setUp()
    {
        $this->form = new Form;
    }

    public function testNoInputFilterPresentByDefault()
    {
        $this->assertNull($this->form->getInputFilter());
    }

    public function testCanComposeAnInputFilter()
    {
        $filter = new InputFilter();
        $this->form->setInputFilter($filter);
        $this->assertSame($filter, $this->form->getInputFilter());
    }

    public function testCallingIsValidRaisesExceptionIfNoDataSetAndNoModelBound()
    {
        $this->setExpectedException('Zend\Form\Exception\DomainException');
        $this->form->isValid();
    }

    public function testValidatesEntireDataSetByDefault()
    {
        $this->markTestIncomplete();
    }

    public function testSpecifyingValidationGroupForcesPartialValidation()
    {
        $this->markTestIncomplete();
    }

    public function testSettingValidateAllFlagAfterPartialValidationForcesFullValidation()
    {
        $this->markTestIncomplete();
    }

    /**
     * @todo Should getData() be allowed only after isValid()?
     */
    public function testCallingGetDataReturnsEmptyArrayIfNoDataSetAndNoModelBound()
    {
        $this->assertEquals(array(), $this->form->getData());
    }

    public function testCallingGetDataReturnsNormalizedDataByDefault()
    {
        $this->markTestIncomplete();
    }

    public function testAllowsReturningRawValuesViaGetData()
    {
        $this->markTestIncomplete();
    }

    public function testGetDataReturnsBoundModel()
    {
        $this->markTestIncomplete();
    }

    public function testGetDataCanReturnValuesAsArrayWhenModelIsBound()
    {
        $this->markTestIncomplete();
    }

    public function testValuesBoundToModelAreNormalizedByDefault()
    {
        $this->markTestIncomplete();
    }

    public function testCanBindRawValuesToModel()
    {
        $this->markTestIncomplete();
    }

    public function testGetDataReturnsSubsetOfDataWhenValidationGroupSet()
    {
        $this->markTestIncomplete();
    }

    public function testSettingValidationGroupBindsOnlyThoseValuesToModel()
    {
        $this->markTestIncomplete();
    }
}
