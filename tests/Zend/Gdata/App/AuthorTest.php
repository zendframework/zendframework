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
 * @category     Zend
 * @package      Zend_Gdata_App
 * @subpackage UnitTests
 * @copyright    Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com);
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Gdata/App/Extension/Author.php';
require_once 'Zend/Gdata/App.php';

/**
 * @package Zend_Gdata_App
 * @subpackage UnitTests
 */
class Zend_Gdata_App_AuthorTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->authorText = file_get_contents(
                'Zend/Gdata/App/_files/AuthorElementSample1.xml',
                true);
        $this->author = new Zend_Gdata_App_Extension_Author();
    }
      
    public function testEmptyAuthorShouldHaveEmptyExtensionsList() {
        $this->assertTrue(is_array($this->author->extensionElements));
        $this->assertTrue(count($this->author->extensionElements) == 0);
    }
      
    public function testNormalAuthorShouldHaveNoExtensionElements() {
        $this->author->name = new Zend_Gdata_App_Extension_Name('Jeff Scudder');
        $this->assertEquals($this->author->name->text, 'Jeff Scudder');
        $this->assertEquals(count($this->author->extensionElements), 0);
        $newAuthor = new Zend_Gdata_App_Extension_Author(); 
        $newAuthor->transferFromXML($this->author->saveXML());
        $this->assertEquals(count($newAuthor->extensionElements), 0);
        $newAuthor->extensionElements = array(
                new Zend_Gdata_App_Extension_Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(count($newAuthor->extensionElements), 1);
        $this->assertEquals($newAuthor->name->text, 'Jeff Scudder');

        /* try constructing using magic factory */
        $app = new Zend_Gdata_App();
        $newAuthor2 = $app->newAuthor();
        $newAuthor2->transferFromXML($newAuthor->saveXML());
        $this->assertEquals(count($newAuthor2->extensionElements), 1);
        $this->assertEquals($newAuthor2->name->text, 'Jeff Scudder');
    }

    public function testEmptyAuthorToAndFromStringShouldMatch() {
        $authorXml = $this->author->saveXML();
        $newAuthor = new Zend_Gdata_App_Extension_Author();
        $newAuthor->transferFromXML($authorXml);
        $newAuthorXml = $newAuthor->saveXML();
        $this->assertTrue($authorXml == $newAuthorXml);
    }

    public function testAuthorWithNameEmailToAndFromStringShouldMatch() {
        $this->author->name = new Zend_Gdata_App_Extension_Name('Jeff Scudder');
        $this->author->email = new Zend_Gdata_App_Extension_Email(
        		'api.jscudder@gmail.com');
        $this->author->uri = new Zend_Gdata_App_Extension_Uri(
        		'http://code.google.com/apis/gdata/');
        $authorXml = $this->author->saveXML();
        $newAuthor = new Zend_Gdata_App_Extension_Author();
        $newAuthor->transferFromXML($authorXml);
        $newAuthorXml = $newAuthor->saveXML();
        $this->assertTrue($authorXml == $newAuthorXml);
        $this->assertEquals('Jeff Scudder', $newAuthor->name->text);
        $this->assertEquals('api.jscudder@gmail.com', $newAuthor->email->text);
        $this->assertEquals('http://code.google.com/apis/gdata/', $newAuthor->uri->text);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->author->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->author->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->author->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->author->extensionAttributes['foo2']['value']);
        $authorXml = $this->author->saveXML();
        $newAuthor = new Zend_Gdata_App_Extension_Author();
        $newAuthor->transferFromXML($authorXml);
        //var_dump($this->author);
        //print $authorXml;
        $this->assertEquals('bar', $newAuthor->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newAuthor->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullAuthorToAndFromString() {
        $this->author->transferFromXML($this->authorText);
        $this->assertEquals($this->author->name->text, 'John Doe');
        $this->assertEquals($this->author->email->text, 
                'johndoes@someemailadress.com');
        $this->assertEquals($this->author->uri->text, 'http://www.google.com');
    }

}
