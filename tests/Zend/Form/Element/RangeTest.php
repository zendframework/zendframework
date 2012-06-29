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

namespace ZendTest\Form\Element;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Element\Range as RangeElement;
use Zend\Form\Factory;

class RangeTest extends TestCase
{
    public function testCanInjectMultipleValidators()
    {
        $element   = new RangeElement();
        $validators = array();
        $firstValidator = $this->getMock('Zend\Validator\ValidatorInterface');
        $secondValidator = $this->getMock('Zend\Validator\ValidatorInterface');
        $validators[] = $firstValidator;
        $validators[] = $secondValidator;
        $element->setValidators($validators);
        $this->assertSame(array($firstValidator, $secondValidator), $element->getValidators());
    }

    public function testProvidesInputSpecificationThatIncludesValidator()
    {
        $element = new RangeElement();
        $firstValidator = $this->getMock('Zend\Validator\ValidatorInterface');
        $validators[] = $firstValidator;
        $element->setValidators($validators);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);
        $test = array_shift($inputSpec['validators']);
        $this->assertSame($firstValidator, $test);
    }
}
