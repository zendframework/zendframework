<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ZendTest\Form\Element;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Element\File as FileElement;
use Zend\InputFilter\Factory as InputFilterFactory;

class FileTest extends TestCase
{
    public function testProvidesDefaultInputSpecification()
    {
        $element = new FileElement('foo');
        $this->assertEquals('file', $element->getAttribute('type'));

        $inputSpec = $element->getInputSpecification();
        $factory = new InputFilterFactory();
        $input = $factory->createInput($inputSpec);
        $this->assertInstanceOf('Zend\InputFilter\FileInput', $input);
    }

    public function testWillAddFileEnctypeAttributeToForm()
    {
        $file = new FileElement('foo');
        $formMock = $this->getMock('Zend\Form\Form');
        $formMock->expects($this->exactly(1))
            ->method('setAttribute')
            ->with($this->stringContains('enctype'),
                   $this->stringContains('multipart/form-data'));
        $file->prepareElement($formMock);
    }
}
