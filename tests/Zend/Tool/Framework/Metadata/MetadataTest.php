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
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see TestHelper.php
 */

/**
 * @see Zend_Tool_Framework_Action_Base
 */

/**
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group Zend_Tool
 * @group Zend_Tool_Framework
 * @group Zend_Tool_Framework_Manifest
 */
class Zend_Tool_Framework_Manifest_MetadataTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Tool_Framework_Manifest_Metadata
     */
    protected $_metadata = null;

    public function setup()
    {
        $this->_metadata = new Zend_Tool_Framework_Metadata_Basic();
    }

    public function teardown()
    {
        $this->_metadata = null;
    }

    public function testConstructorWillAcceptAndPersistValues()
    {
        $obj1 = new ArrayObject();

        $metadata = new Zend_Tool_Framework_Metadata_Basic(array(
            'name' => 'Foo',
            'value' => 'Bar',
            'reference' => $obj1
            ));

        $this->assertEquals('Foo', $metadata->getName());
        $this->assertEquals('Bar', $metadata->getValue());
        $this->assertTrue($obj1 === $metadata->getReference());
    }

    public function testSetOptionsPersistValues()
    {
        $obj1 = new ArrayObject();

        $this->_metadata->setOptions(array(
            'name' => 'Foo',
            'value' => 'Bar',
            'reference' => $obj1
            ));

        $this->assertEquals('Foo', $this->_metadata->getName());
        $this->assertEquals('Bar', $this->_metadata->getValue());
        $this->assertTrue($obj1 === $this->_metadata->getReference());
    }

    public function testGetTypeHasDefaultValue()
    {
        $this->assertEquals('Basic', $this->_metadata->getType());
    }

    public function testTypeIsModifiable()
    {
        $this->_metadata->setType('foo');
        $this->assertEquals('foo', $this->_metadata->getType());
    }

    public function testSettersPersistValuesAndAreRetievableThroughGetters()
    {
        $this->_metadata->setName('Foo');
        $this->assertEquals('Foo', $this->_metadata->getName());
        $this->_metadata->setValue('Bar');
        $this->assertEquals('Bar', $this->_metadata->getValue());
    }

    public function testGetAttributesReturnsProperValues()
    {
        $obj1 = new ArrayObject();

        $this->_metadata->setOptions(array(
            'name' => 'Foo',
            'value' => null,
            'reference' => $obj1
            ));

        $attributes = $this->_metadata->getAttributes();

        $this->assertEquals(4, count($attributes));

        $this->assertEquals('Basic', $attributes['type']);
        $this->assertEquals('Foo', $attributes['name']);
        $this->assertEquals(null, $attributes['value']);
        $this->assertTrue($obj1 === $attributes['reference']);

        $attributes = $this->_metadata->getAttributes(Zend_Tool_Framework_Metadata_Basic::ATTRIBUTES_NO_PARENT);

        $this->assertEquals(0, count($attributes));


        $attributes = $this->_metadata->getAttributes(Zend_Tool_Framework_Metadata_Basic::ATTRIBUTES_ALL, true);

        $this->assertEquals(4, count($attributes));

        $this->assertEquals('Basic', $attributes['type']);
        $this->assertEquals('Foo', $attributes['name']);
        $this->assertEquals('(null)', $attributes['value']);
        $this->assertEquals('(object)', $attributes['reference']);

    }

    public function testMetadataObjectCanCastToStringRepresentation()
    {
        $obj1 = new ArrayObject();

        $this->_metadata->setOptions(array(
            'name' => 'Foo',
            'value' => 'Bar',
            'reference' => $obj1
            ));

        $this->assertEquals('Type: Basic, Name: Foo, Value: Bar', (string) $this->_metadata);
    }

}
