<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ZendTest\Form\Element\File;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Element\File\UploadProgress as UploadProgressElement;

class UploadProgressTest extends TestCase
{
    public function testAlwaysReturnsUploadIdName()
    {
        $name = 'UPLOAD_IDENTIFIER';
        $element = new UploadProgressElement('foo');
        $this->assertEquals($name, $element->getName());
        $element->setName('bar');
        $this->assertEquals($name, $element->getName());
    }

    public function testValueIsPopulatedWithUniqueId()
    {
        $element = new UploadProgressElement();
        $value1 = $element->getValue();
        $this->assertNotEmpty($value1);
        $element->setValue(null);
        $value2 = $element->getValue();
        $this->assertNotEmpty($value2);
        $this->assertNotEquals($value2, $value1);
    }
}
