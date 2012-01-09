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
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Dojo;

use Zend\Dojo\Data as DojoData,
    Zend\Json\Json;

/**
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    public $dojoData;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->dojoData = new DojoData;
    }

    public function testIdentifierShouldBeNullByDefault()
    {
        $this->assertNull($this->dojoData->getIdentifier());
    }

    public function testIdentifierShouldBeMutable()
    {
        $this->testIdentifierShouldBeNullByDefault();
        $this->dojoData->setIdentifier('id');
        $this->assertEquals('id', $this->dojoData->getIdentifier());
    }

    public function testNullIdentifierShouldBeAllowed()
    {
        $this->dojoData->setIdentifier('foo');
        $this->assertEquals('foo', $this->dojoData->getIdentifier());
        $this->dojoData->setIdentifier(null);
        $this->assertNull($this->dojoData->getIdentifier());
    }

    public function testIntegerIdentifierShouldBeAllowed()
    {
        $this->dojoData->setIdentifier(2);
        $this->assertSame(2, $this->dojoData->getIdentifier());
    }

    public function testSetIdentifierShouldThrowExceptionOnInvalidType()
    {
        $this->setExpectedException('Zend\Dojo\Exception\InvalidArgumentException', 'Invalid identifier; please use a string or integer');
        $this->dojoData->setIdentifier(true);
    }

    public function testLabelShouldBeNullByDefault()
    {
        $this->assertNull($this->dojoData->getLabel());
    }

    public function testLabelShouldBeMutable()
    {
        $this->testLabelShouldBeNullByDefault();
        $this->dojoData->setLabel('title');
        $this->assertEquals('title', $this->dojoData->getLabel());
    }

    public function testLabelShouldBeNullable()
    {
        $this->testLabelShouldBeMutable();
        $this->dojoData->setLabel(null);
        $this->assertNull($this->dojoData->getLabel());
    }

    public function testAddItemShouldThrowExceptionIfNoIdentifierPresentInObject()
    {
        $item = array(
            'id'    => '1',
            'title' => 'foo',
            'url'   => 'http://www.example.com/',
        );
        $this->setExpectedException('Zend\Dojo\Exception\RuntimeException', 'You must set an identifier prior to adding items');
        $this->dojoData->addItem($item);
    }

    public function testAddItemShouldThrowExceptionIfNoIdentifierPresentInItem()
    {
        $item = array(
            'title' => 'foo',
            'url'   => 'http://www.example.com/',
        );
        $this->dojoData->setIdentifier('id');
        $this->setExpectedException('Zend\Dojo\Exception\InvalidArgumentException', 'Item must contain a column matching the currently set identifier');
        $this->dojoData->addItem($item);
    }

    public function testAddItemShouldAcceptArray()
    {
        $item = array(
            'id'    => '1',
            'title' => 'foo',
            'url'   => 'http://www.example.com/',
        );
        $this->dojoData->setIdentifier('id');
        $this->dojoData->addItem($item);
        $this->assertEquals(1, count($this->dojoData));
        $this->assertSame($item, $this->dojoData->getItem(1));
    }

    public function testAddItemShouldAcceptStdObject()
    {
        $item = array(
            'id'    => '1',
            'title' => 'foo',
            'url'   => 'http://www.example.com/',
        );
        $obj = (object) $item;
        $this->dojoData->setIdentifier('id');
        $this->dojoData->addItem($obj);
        $this->assertEquals(1, count($this->dojoData));
        $this->assertSame($item, $this->dojoData->getItem(1));
    }

    public function testAddItemShouldAcceptObjectImplementingToArray()
    {
        $obj = new TestAsset\DataObject;
        $this->dojoData->setIdentifier('id');
        $this->dojoData->addItem($obj);
        $this->assertEquals(1, count($this->dojoData));
        $this->assertSame($obj->item, $this->dojoData->getItem('foo'));
    }

    public function testAddItemShouldThrowErrorOnInvalidItem()
    {
        $this->dojoData->setIdentifier('id');
        $this->setExpectedException('Zend\Dojo\Exception\InvalidArgumentException', 'Only arrays and objects');
        $this->dojoData->addItem('foo');
    }

    public function testAddItemShouldAllowSpecifyingIdentifier()
    {
        $item = array(
            'title' => 'foo',
            'url'   => 'http://www.example.com/',
        );
        $this->dojoData->setIdentifier('id');
        $this->dojoData->addItem($item, 'foo');
        $this->assertEquals(1, count($this->dojoData));
        $stored = $this->dojoData->getItem('foo');
        $this->assertTrue(array_key_exists('id', $stored));
        $this->assertEquals('foo', $stored['id']);
        foreach ($item as $key => $value) {
            $this->assertTrue(array_key_exists($key, $stored));
            $this->assertEquals($value, $stored[$key]);
        }
    }

    public function testOverwritingItemsShouldNotBeAllowedFromAddItem()
    {
        $this->testAddItemShouldAcceptArray();
        $item = array(
            'id'    => '1',
            'title' => 'foo',
            'url'   => 'http://www.example.com/',
        );
        $this->setExpectedException('Zend\Dojo\Exception\InvalidArgumentException', 'Overwriting items using addItem()');
        $this->dojoData->addItem($item);
    }

    public function testSetItemShouldOverwriteExistingItem()
    {
        $this->testAddItemShouldAcceptArray();
        $item = array(
            'id'    => '1',
            'title' => 'bar',
            'url'   => 'http://www.foo.com/',
        );
        $this->assertNotSame($item, $this->dojoData->getItem(1));
        $this->dojoData->setItem($item);
        $this->assertEquals(1, count($this->dojoData));
        $this->assertSame($item, $this->dojoData->getItem(1));
    }

    public function testSetItemShouldAddItemIfNonexistent()
    {
        $item = array(
            'id'    => '1',
            'title' => 'bar',
            'url'   => 'http://www.foo.com/',
        );
        $this->dojoData->setIdentifier('id');
        $this->assertEquals(0, count($this->dojoData));
        $this->dojoData->setItem($item);
        $this->assertEquals(1, count($this->dojoData));
        $this->assertSame($item, $this->dojoData->getItem(1));
    }

    public function testAddItemsShouldAcceptArray()
    {
        $items = array(
            array (
                'id'    => 1,
                'title' => 'Foo',
                'email' => 'foo@bar',
            ),
            array (
                'id'    => 2,
                'title' => 'Bar',
                'email' => 'bar@bar',
            ),
            array (
                'id'    => 3,
                'title' => 'Baz',
                'email' => 'baz@bar',
            ),
        );
        $this->dojoData->setIdentifier('id');
        $this->assertEquals(0, count($this->dojoData));
        $this->dojoData->addItems($items);
        $this->assertEquals(3, count($this->dojoData));
        $this->assertSame($items[0], $this->dojoData->getItem(1));
        $this->assertSame($items[1], $this->dojoData->getItem(2));
        $this->assertSame($items[2], $this->dojoData->getItem(3));
    }

    public function testAddItemsShouldAcceptTraversableObject()
    {
        $obj = new TestAsset\DataCollection;
        $this->dojoData->setIdentifier('id');
        $this->assertEquals(0, count($this->dojoData));
        $this->dojoData->addItems($obj);
        $this->assertEquals(3, count($this->dojoData));
        $this->assertSame($obj->items[0]->toArray(), $this->dojoData->getItem(1));
        $this->assertSame($obj->items[1]->toArray(), $this->dojoData->getItem(2));
        $this->assertSame($obj->items[2]->toArray(), $this->dojoData->getItem(3));
    }

    public function testAddItemsShouldThrowExceptionForInvalidItems()
    {
        $this->setExpectedException('Zend\Dojo\Exception\InvalidArgumentException', 'Only arrays and Traversable objects may be added to ');
        $this->dojoData->addItems('foo');
    }

    public function testSetItemsShouldOverwriteAllCurrentItems()
    {
        $this->testAddItemsShouldAcceptArray();
        $items = $this->dojoData->getItems();
        $obj   = new TestAsset\DataCollection;
        $this->dojoData->setItems($obj);
        $this->assertEquals(3, count($this->dojoData));
        $this->assertNotSame($items, $this->dojoData->getItems());
    }

    public function testRemoveItemShouldRemoveItemSpecifiedByIdentifier()
    {
        $this->testAddItemsShouldAcceptArray();
        $this->assertNotNull($this->dojoData->getItem(1));
        $this->dojoData->removeItem(1);
        $this->assertNull($this->dojoData->getItem(1));
        $this->assertEquals(2, count($this->dojoData));
    }

    public function testClearItemsShouldRemoveAllItems()
    {
        $this->testAddItemsShouldAcceptArray();
        $this->dojoData->clearItems();
        $this->assertEquals(0, count($this->dojoData));
    }

    public function testGetItemShouldReturnNullIfNoMatchingItemExists()
    {
        $this->assertNull($this->dojoData->getItem('bogus'));
    }

    public function testGetItemsShouldReturnArrayOfItems()
    {
        $this->testAddItemsShouldAcceptArray();
        $items = $this->dojoData->getItems();
        $this->assertTrue(is_array($items));
    }

    public function testConstructorShouldSetIdentifierItemsAndLabelWhenPassed()
    {
        $items = array(
            array (
                'id'    => 1,
                'title' => 'Foo',
                'email' => 'foo@bar',
            ),
            array (
                'id'    => 2,
                'title' => 'Bar',
                'email' => 'bar@bar',
            ),
            array (
                'id'    => 3,
                'title' => 'Baz',
                'email' => 'baz@bar',
            ),
        );
        $data = new DojoData('id', $items, 'title');
        $this->assertEquals('id', $data->getIdentifier());
        $this->assertEquals('title', $data->getLabel());
        foreach ($items as $item) {
            $this->assertTrue($data->hasItem($item['id']));
        }
    }

    public function testShouldSerializeToArray()
    {
        $this->testAddItemsShouldAcceptTraversableObject();
        $array = $this->dojoData->toArray();
        $this->assertTrue(is_array($array));
        $this->assertTrue(array_key_exists('identifier', $array));
        $this->assertEquals($this->dojoData->getIdentifier(), $array['identifier']);
        $this->assertEquals(array_values($this->dojoData->getItems()), $array['items']);
    }

    public function testSerializingToArrayShouldIncludeLabelIfPresent()
    {
        $this->testShouldSerializeToArray();
        $this->dojoData->setLabel('title');
        $array = $this->dojoData->toArray();
        $this->assertTrue(is_array($array));
        $this->assertTrue(array_key_exists('label', $array));
        $this->assertEquals($this->dojoData->getLabel(), $array['label']);
    }

    public function testSerializingToArrayShouldThrowErrorIfNoIdentifierInObject()
    {
        $this->testAddItemsShouldAcceptTraversableObject();
        $this->dojoData->setIdentifier(null);
        $this->setExpectedException('Zend\Dojo\Exception\RuntimeException', 'Serialization');
        $array = $this->dojoData->toArray();
    }

    public function testShouldSerializeToJson()
    {
        $this->testAddItemsShouldAcceptTraversableObject();
        $json = $this->dojoData->toJson();
        $this->assertSame($this->dojoData->toArray(), Json::decode($json, Json::TYPE_ARRAY));
    }

    public function testShouldSerializeToStringAsJson()
    {
        $this->testAddItemsShouldAcceptTraversableObject();
        $json = $this->dojoData->toJson();
        $this->assertSame($json, $this->dojoData->__toString());
    }

    public function testShouldImplementArrayAccess()
    {
        $this->assertTrue($this->dojoData instanceof \ArrayAccess);
        $this->testAddItemsShouldAcceptTraversableObject();
        $this->assertEquals($this->dojoData->getItem(1), $this->dojoData[1]);
        $this->dojoData[4] = array(
            'title' => 'ArrayAccess',
            'meta'  => 'fun',
        );
        $this->assertTrue(isset($this->dojoData[4]));
        $this->assertEquals($this->dojoData->getItem(4), $this->dojoData[4]);
        unset($this->dojoData[4]);
        $this->assertFalse(isset($this->dojoData[4]));
    }

    public function testShouldBeTraversable()
    {
        $this->assertTrue($this->dojoData instanceof \Traversable);
        $this->testAddItemsShouldAcceptTraversableObject();
        foreach ($this->dojoData as $key => $item) {
            $this->assertTrue(is_array($item));
            $this->assertEquals($key, $item['id']);
        }
    }

    public function testShouldImplementCountable()
    {
        $this->assertTrue($this->dojoData instanceof \Countable);
    }

    public function testShouldAllowPopulationFromJson()
    {
        $data = array(
            'identifier' => 'id',
            'label'      => 'title',
            'items'      => array(
                array('id' => 1, 'title' => 'One', 'name' => 'First'),
                array('id' => 2, 'title' => 'Two', 'name' => 'Second'),
                array('id' => 3, 'title' => 'Three', 'name' => 'Third'),
                array('id' => 4, 'title' => 'Four', 'name' => 'Fourth'),
            ),
        );
        $json = Json::encode($data);
        $dojoData = new DojoData();
        $dojoData->fromJson($json);
        $test = $dojoData->toArray();
        $this->assertEquals($data, $test);
    }

    public function testFromJsonShouldThrowExceptionOnInvalidData()
    {
        $this->setExpectedException('Zend\Dojo\Exception\InvalidArgumentException', 'fromJson() expects JSON input');
        $this->dojoData->fromJson(new \stdClass);
    }

    /**
     * @group ZF-3841
     */
    public function testDataContainerShouldAcceptAdditionalMetadataPerKey()
    {
        $this->assertNull($this->dojoData->getMetadata('numRows'));
        $this->dojoData->setMetadata('numRows', 100);
        $this->assertEquals(100, $this->dojoData->getMetadata('numRows'));
    }

    /**
     * @group ZF-3841
     */
    public function testDataContainerShouldAcceptAdditionalMetadataEnMasse()
    {
        $metadata = $this->dojoData->getMetadata();
        $this->assertTrue(is_array($metadata));
        $this->assertTrue(empty($metadata));

        $metadata = array('numRows' => 100, 'sort' => 'name');
        $this->dojoData->setMetadata($metadata);
        $test = $this->dojoData->getMetadata();
        $this->assertEquals($metadata, $test);
    }

    /**
     * @group ZF-3841
     */
    public function testDataContainerShouldAllowClearingIndividualMetadataItems()
    {
        $this->testDataContainerShouldAcceptAdditionalMetadataEnMasse();
        $this->dojoData->clearMetadata('numRows');
        $metadata = $this->dojoData->getMetadata();
        $this->assertEquals(1, count($metadata));
        $this->assertFalse(array_key_exists('numRows', $metadata));
        $this->assertTrue(array_key_exists('sort', $metadata));
    }

    /**
     * @group ZF-3841
     */
    public function testDataContainerShouldAllowClearingMetadataEnMasse()
    {
        $this->testDataContainerShouldAcceptAdditionalMetadataEnMasse();
        $this->dojoData->clearMetadata();
        $metadata = $this->dojoData->getMetadata();
        $this->assertEquals(0, count($metadata));
    }

    /**
     * @group ZF-3841
     */
    public function testSerializingToArrayShouldIncludeMetadata()
    {
        $this->testDataContainerShouldAcceptAdditionalMetadataEnMasse();
        $this->dojoData->setIdentifier('id');
        $array = $this->dojoData->toArray();
        $this->assertTrue(array_key_exists('numRows', $array));
        $this->assertTrue(array_key_exists('sort', $array));
    }
}
