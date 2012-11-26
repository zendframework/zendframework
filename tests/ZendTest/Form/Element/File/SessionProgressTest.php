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
use Zend\Form\Element\File\SessionProgress as SessionProgressElement;

class SessionProgressTest extends TestCase
{
    public function setUp()
    {
        if (false === ini_get('session.upload_progress.name')) {
            $this->markTestSkipped('Session Upload Progress feature is not active');
        }
    }

    public function testAlwaysReturnsSessionName()
    {
        $name = ini_get('session.upload_progress.name');
        $element = new SessionProgressElement('foo');
        $this->assertEquals($name, $element->getName());
        $element->setName('bar');
        $this->assertEquals($name, $element->getName());
    }

    public function testValueIsPopulatedWithUniqueId()
    {
        $element = new SessionProgressElement();
        $value1 = $element->getValue();
        $this->assertNotEmpty($value1);
        $element->setValue(null);
        $value2 = $element->getValue();
        $this->assertNotEmpty($value2);
        $this->assertNotEquals($value2, $value1);
    }
}
