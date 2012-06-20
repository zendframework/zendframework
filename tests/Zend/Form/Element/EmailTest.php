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
use Zend\Form\Element\Email as EmailElement;
use Zend\Form\Factory;
use Zend\Validator\EmailAddress as EmailValidator;

class EmailTest extends TestCase
{
    public function testLazyLoadsEmailValidatorByDefault()
    {
        $element   = new EmailElement();
        $validator = $element->getValidator();
        $this->assertInstanceOf('Zend\Validator\EmailAddress', $validator);
    }

    public function testLazyLoadsExplodeValidatorWhenMultipleAttributeSet()
    {
        $element   = new EmailElement();
        $element->setAttribute('multiple', true);
        $validator = $element->getValidator();
        $this->assertInstanceOf('Zend\Validator\Explode', $validator);
    }

    public function testCanInjectCustomValidator()
    {
        $element   = new EmailElement();
        $validator = $this->getMock('Zend\Validator\ValidatorInterface');
        $element->setValidator($validator);
        $this->assertSame($validator, $element->getValidator());
    }

    public function testProvidesInputSpecificationThatIncludesCustomValidator()
    {
        $element   = new EmailElement();
        $validator = $this->getMock('Zend\Validator\ValidatorInterface');
        $element->setValidator($validator);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);
        $test = array_shift($inputSpec['validators']);
        $this->assertSame($validator, $test);
    }
}
