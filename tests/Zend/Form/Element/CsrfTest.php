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
use Zend\Form\Element\Csrf as CsrfElement;
use Zend\Form\Factory;
use Zend\Validator\Csrf as CsrfValidator;

class CsrfTest extends TestCase
{
    public function testLazyLoadsCsrfValidatorByDefault()
    {
        $element   = new CsrfElement();
        $validator = $element->getValidator();
        $this->assertInstanceOf('Zend\Validator\Csrf', $validator);
    }

    public function testCanInjectCsrfValidator()
    {
        $element   = new CsrfElement();
        $validator = new CsrfValidator();
        $element->setValidator($validator);
        $this->assertSame($validator, $element->getValidator());
    }

    public function testValueAttributeIsSetToValidatorHash()
    {
        $element   = new CsrfElement('foo');
        $validator = $element->getValidator();
        $value     = $element->getAttribute('value');
        $this->assertSame($validator->getHash(), $value);

        $validator = new CsrfValidator(array(
            'salt' => 'foobar',
            'name' => $element->getName(),
        ));
        $validator->setSalt('foobarbaz');
        $element->setValidator($validator);
        $value2    = $element->getAttribute('value');
        $this->assertSame($validator->getHash(), $value2);
        $this->assertNotSame($value, $value2, "$value == $value2");
    }
}
