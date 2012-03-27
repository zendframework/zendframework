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
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Cache\Storage\Adapter;
use Zend\Cache\Storage\Adapter,
    Zend\Cache,
    Zend\Stdlib\ErrorHandler;

/**
 * PHPUnit test case
 */

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
abstract class CommonAdapterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The storage adapter
     *
     * @var Zend\Cache\Storage\Adapter
     */
    protected $_storage;

    /**
     * All datatypes of PHP
     *
     * @var string[]
     */
    protected $_phpDatatypes = array(
        'NULL', 'boolean', 'integer', 'double',
        'string', 'array', 'object', 'resource'
    );

    public function setUp()
    {
        $this->assertInstanceOf(
            'Zend\Cache\Storage\Adapter',
            $this->_storage,
            'Storage adapter instance is needed for tests'
        );
        $this->assertInstanceOf(
            'Zend\Cache\Storage\Adapter\AdapterOptions',
            $this->_options,
            'Options instance is needed for tests'
        );
    }

    public function tearDown()
    {
        // be sure the error handler has been stopped
        if (ErrorHandler::started()) {
            ErrorHandler::stop();
            $this->fail('ErrorHandler not stopped');
        }
    }

    public function testOptionNamesValid()
    {
        $options = $this->_storage->getOptions()->toArray();
        foreach ($options as $name => $value) {
            $this->assertRegExp(
                '/^[a-z]+[a-z0-9_]*[a-z0-9]+$/',
                $name,
                "Invalid option name '{$name}'"
            );
        }
    }

    public function testGettersAndSettersOfOptionsExists()
    {
        $options = $this->_storage->getOptions();
        foreach ($options->toArray() as $option => $value) {
            if ($option == 'adapter') {
                // Skip this, as it's a "special" value
                continue;
            }
            $method = ucwords(str_replace('_', ' ', $option));
            $method = str_replace(' ', '', $method);

            $this->assertTrue(
                method_exists($options, 'set' . $method),
                "Missing method 'set'{$method}"
            );

            $this->assertTrue(
                method_exists($options, 'get' . $method),
                "Missing method 'get'{$method}"
            );
        }
    }

    public function testOptionsGetAndSetDefault()
    {
        $options = $this->_storage->getOptions();
        $this->_storage->setOptions($options);
        $this->assertSame($options, $this->_storage->getOptions());
    }

    public function testOptionsFluentInterface()
    {
        $options = $this->_storage->getOptions();
        foreach ($options->toArray() as $option => $value) {
            $method = ucwords(str_replace('_', ' ', $option));
            $method = 'set' . str_replace(' ', '', $method);
            $this->assertSame(
                $options,
                $options->{$method}($value),
                "Method '{$method}' doesn't implement the fluent interface"
            );
        }

        $this->assertSame(
            $this->_storage,
            $this->_storage->setOptions($options),
            "Method 'setOptions' doesn't implement the fluent interface"
        );
    }

    public function testGetCapabilities()
    {
        $capabilities = $this->_storage->getCapabilities();
        $this->assertInstanceOf('Zend\Cache\Storage\Capabilities', $capabilities);
    }

    public function testDatatypesCapability()
    {
        $capabilities = $this->_storage->getCapabilities();
        $datatypes = $capabilities->getSupportedDatatypes();
        $this->assertInternalType('array', $datatypes);

        foreach ($datatypes as $sourceType => $targetType) {
            $this->assertContains(
                $sourceType, $this->_phpDatatypes,
                "Unknown source type '{$sourceType}'"
            );
            if (is_string($targetType)) {
                $this->assertContains(
                    $targetType, $this->_phpDatatypes,
                    "Unknown target type '{$targetType}'"
                );
            } else {
                $this->assertInternalType(
                    'bool', $targetType,
                    "Target type must be a string or boolean"
                );
            }
        }
    }

    public function testSupportedMetadataCapability()
    {
        $capabilities = $this->_storage->getCapabilities();
        $metadata = $capabilities->getSupportedMetadata();
        $this->assertInternalType('array', $metadata);

        foreach ($metadata as $property) {
            $this->assertInternalType('string', $property);
        }
    }

    public function testTtlCapabilities()
    {
        $capabilities = $this->_storage->getCapabilities();

        $this->assertInternalType('integer', $capabilities->getMaxTtl());
        $this->assertGreaterThanOrEqual(0, $capabilities->getMaxTtl());

        $this->assertInternalType('bool', $capabilities->getStaticTtl());

        $this->assertInternalType('numeric', $capabilities->getTtlPrecision());
        $this->assertGreaterThan(0, $capabilities->getTtlPrecision());

        $this->assertInternalType('bool', $capabilities->getExpiredRead());
    }

    public function testKeyCapabilities()
    {
        $capabilities = $this->_storage->getCapabilities();

        $this->assertInternalType('integer', $capabilities->getMaxKeyLength());
        $this->assertGreaterThanOrEqual(-1, $capabilities->getMaxKeyLength());

        $this->assertInternalType('bool', $capabilities->getNamespaceIsPrefix());

        $this->assertInternalType('string', $capabilities->getNamespaceSeparator());
    }

    public function testGetItemReturnsFalseIfIgnoreMissingItemsEnabled()
    {
        $this->_options->setIgnoreMissingItems(true);
        $this->assertFalse($this->_storage->getItem('unknown'));
    }

    public function testGetItemThrowsItemNotFoundExceptionIfIgnoreMissingItemsDisabled()
    {
        $this->_options->setIgnoreMissingItems(false);

        $this->setExpectedException('Zend\Cache\Exception\ItemNotFoundException');
        $this->_storage->getItem('unknown');
    }

    public function testGetItemThrowsItemNotFoundExceptionIfIgnoreMissingItemsDisabledAndItemExpired()
    {
        $this->_options->setIgnoreMissingItems(false);

        $capabilities = $this->_storage->getCapabilities();
        if ($capabilities->getUseRequestTime()) {
            $this->markTestSkipped("Can't test get expired item if request time will be used");
        }

        $ttl = $capabilities->getTtlPrecision();
        $this->_options->setTtl($ttl);

        $this->_storage->setItem('key', 'value');

        // wait until expired
        $wait = $ttl + $capabilities->getTtlPrecision();
        usleep($wait * 2000000);

        $this->setExpectedException('Zend\Cache\Exception\ItemNotFoundException');
        $this->_storage->getItem('key');
    }

    public function testGetItemReturnsFalseIfNonReadable()
    {
        $this->_options->setReadable(false);

        $this->assertTrue($this->_storage->setItem('key', 'value'));
        $this->assertFalse($this->_storage->getItem('key'));
    }

    public function testGetItemsReturnsEmptyArrayIfNonReadable()
    {
        $this->_options->setReadable(false);

        $this->assertTrue($this->_storage->setItem('key', 'value'));
        $this->assertEquals(array(), $this->_storage->getItems(array('key')));
    }

    public function testGetMetadata()
    {
        $capabilities = $this->_storage->getCapabilities();
        $supportedMetadatas = $capabilities->getSupportedMetadata();

        $this->assertTrue($this->_storage->setItem('key', 'value'));
        $metadata = $this->_storage->getMetadata('key');

        $this->assertInternalType('array', $metadata);
        foreach ($supportedMetadatas as $supportedMetadata) {
            $this->assertArrayHasKey($supportedMetadata, $metadata);
        }
    }

    public function testGetMetadataReturnsFalseIfIgnoreMissingItemsEnabled()
    {
        $this->_options->setIgnoreMissingItems(true);
        $this->assertFalse($this->_storage->getMetadata('unknown'));
    }

    public function testGetMetadataThrowsItemNotFoundExceptionIfIgnoreMissingItemsDisabled()
    {
        $this->_options->setIgnoreMissingItems(false);

        $this->setExpectedException('Zend\Cache\Exception\ItemNotFoundException');
        $this->_storage->getMetadata('unknown');
    }

    public function testGetMetadataReturnsFalseIfNonReadable()
    {
        $this->_options->setReadable(false);

        $this->assertTrue($this->_storage->setItem('key', 'value'));
        $this->assertFalse($this->_storage->getMetadata('key'));
    }

    public function testGetMetadatas()
    {
        $capabilities = $this->_storage->getCapabilities();
        $supportedMetadatas = $capabilities->getSupportedMetadata();

        $items = array(
            'key1' => 'value1',
            'key2' => 'value2'
        );
        $this->assertTrue($this->_storage->setItems($items));

        $metadatas = $this->_storage->getMetadatas(array_keys($items));
        $this->assertInternalType('array', $metadatas);
        $this->assertSame(count($items), count($metadatas));
        foreach ($metadatas as $k => $metadata) {
            $this->assertInternalType('array', $metadata);
            foreach ($supportedMetadatas as $supportedMetadata) {
                $this->assertArrayHasKey($supportedMetadata, $metadata);
            }
        }
    }

    public function testGetMetadatasReturnsEmptyArrayIfNonReadable()
    {
        $this->_options->setReadable(false);

        $this->assertTrue($this->_storage->setItem('key', 'value'));
        $this->assertEquals(array(), $this->_storage->getMetadatas(array('key')));
    }

    public function testSetGetHasAndRemoveItem()
    {
        $this->assertTrue($this->_storage->setItem('key', 'value'));
        $this->assertEquals('value', $this->_storage->getItem('key'));
        $this->assertTrue($this->_storage->hasItem('key'));
        $this->assertTrue($this->_storage->removeItem('key'));
        $this->assertFalse($this->_storage->hasItem('key'));
        $this->assertFalse($this->_storage->getItem('key'));
    }

    public function testSetGetHasAndRemoveItems()
    {
        $items = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        );

        $this->assertTrue( $this->_storage->setItems($items) );

        $rs = $this->_storage->getItems(array_keys($items));
        $this->assertInternalType('array', $rs);
        foreach ($items as $key => $value) {
            $this->assertArrayHasKey($key, $rs);
            $this->assertEquals($value, $rs[$key]);
        }

        $rs = $this->_storage->hasItems(array_keys($items));
        $this->assertInternalType('array', $rs);
        $this->assertEquals(count($items), count($rs));
        foreach ($items as $key => $value) {
            $this->assertContains($key, $rs);
        }

        // remove the first and the last item
        $this->assertTrue($this->_storage->removeItems(array('key1', 'key3')));
        unset($items['key1'], $items['key3']);

        $rs = $this->_storage->getItems(array_keys($items));
        $this->assertInternalType('array', $rs);
        foreach ($items as $key => $value) {
            $this->assertArrayHasKey($key, $rs);
            $this->assertEquals($value, $rs[$key]);
        }

        $rs = $this->_storage->hasItems(array_keys($items));
        $this->assertInternalType('array', $rs);
        $this->assertEquals(count($items), count($rs));
        foreach ($items as $key => $value) {
            $this->assertContains($key, $rs);
        }
    }

    public function testSetGetHasAndRemoveItemWithNamespace()
    {
        // write "key" to default namespace
        $this->_options->setNamespace('defaultns1');
        $this->assertTrue( $this->_storage->setItem('key', 'defaultns1') );

        // write "key" to an other default namespace
        $this->_options->setNamespace('defaultns2');
        $this->assertTrue( $this->_storage->setItem('key', 'defaultns2') );

        // test value of defaultns2
        $this->assertTrue($this->_storage->hasItem('key'));
        $this->assertEquals('defaultns2', $this->_storage->getItem('key') );

        // test value of defaultns1
        $this->_options->setNamespace('defaultns1');
        $this->assertTrue($this->_storage->hasItem('key'));
        $this->assertEquals('defaultns1', $this->_storage->getItem('key') );

        // remove item of defaultns1
        $this->_options->setNamespace('defaultns1');
        $this->assertTrue($this->_storage->removeItem('key'));
        $this->assertFalse($this->_storage->hasItem('key'));

        // remove item of defaultns2
        $this->_options->setNamespace('defaultns2');
        $this->assertTrue($this->_storage->removeItem('key'));
        $this->assertFalse($this->_storage->hasItem('key'));
    }

    public function testSetGetHasAndRemoveItemsWithNamespace()
    {
        $items = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        );

        $this->_options->setNamespace('defaultns1');
        $this->assertTrue( $this->_storage->setItems($items) );

        $this->_options->setNamespace('defaultns2');
        $this->assertEquals(array(),  $this->_storage->hasItems(array_keys($items)));

        $this->_options->setNamespace('defaultns1');
        $rs = $this->_storage->getItems(array_keys($items));
        $this->assertInternalType('array', $rs);
        foreach ($items as $key => $value) {
            $this->assertArrayHasKey($key, $rs);
            $this->assertEquals($value, $rs[$key]);
        }

        $rs = $this->_storage->hasItems(array_keys($items));
        $this->assertInternalType('array', $rs);
        $this->assertEquals(count($items), count($rs));
        foreach ($items as $key => $value) {
            $this->assertContains($key, $rs);
        }

        // remove the first and the last item
        $this->assertTrue($this->_storage->removeItems(array('key1', 'key3')));
        unset($items['key1'], $items['key3']);

        $rs = $this->_storage->getItems(array_keys($items));
        $this->assertInternalType('array', $rs);
        foreach ($items as $key => $value) {
            $this->assertArrayHasKey($key, $rs);
            $this->assertEquals($value, $rs[$key]);
        }

        $rs = $this->_storage->hasItems(array_keys($items));
        $this->assertInternalType('array', $rs);
        $this->assertEquals(count($items), count($rs));
        foreach ($items as $key => $value) {
            $this->assertContains($key, $rs);
        }
    }

    public function testSetGetHasAndRemoveItemWithSpecificNamespace()
    {
        $this->_options->setNamespace('defaultns');

        // write "key" without a namespace
        $this->assertTrue( $this->_storage->setItem('key', 'nons'));

        // write "key" with a default namespace
        $this->assertTrue( $this->_storage->setItem('key', 'ns1', array('namespace' => 'ns1')));

        // write "key" with an other default namespace
        $this->assertTrue( $this->_storage->setItem('key', 'ns2', array('namespace' => 'ns2')));

        // test value of ns2
        $this->assertEquals('ns2', $this->_storage->getItem('key', array('namespace' => 'ns2')));

        // test value of ns1
        $this->assertEquals('ns1', $this->_storage->getItem('key', array('namespace' => 'ns1')));

        // test value without namespace
        $this->assertEquals('nons', $this->_storage->getItem('key'));

        // remove item without namespace
        $this->assertTrue($this->_storage->removeItem('key'));
        $this->assertFalse($this->_storage->hasItem('key'));

        // remove item of ns1
        $this->assertTrue($this->_storage->removeItem('key', array('namespace' => 'ns1')));
        $this->assertFalse($this->_storage->hasItem('key', array('namespace' => 'ns1')));

        // remove item of ns2
        $this->assertTrue($this->_storage->removeItem('key', array('namespace' => 'ns2')));
        $this->assertFalse($this->_storage->hasItem('key', array('namespace' => 'ns2')));
    }

    public function testSetGetHasAndRemoveItemsWithSpecificNamespace()
    {
        $this->_options->setNamespace('defaultns');

        $items = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        );

        $this->assertTrue( $this->_storage->setItems($items, array('namespace' => 'specificns')) );
        $this->assertEquals(array(),  $this->_storage->hasItems(array_keys($items)));

        $rs = $this->_storage->getItems(array_keys($items), array('namespace' => 'specificns'));
        $this->assertInternalType('array', $rs);
        foreach ($items as $key => $value) {
            $this->assertArrayHasKey($key, $rs);
            $this->assertEquals($value, $rs[$key]);
        }


        $rs = $this->_storage->hasItems(array_keys($items), array('namespace' => 'specificns'));
        $this->assertInternalType('array', $rs);
        $this->assertEquals(count($items), count($rs));
        foreach ($items as $key => $value) {
            $this->assertContains($key, $rs);
        }

        // remove the first and the last item
        $this->assertTrue($this->_storage->removeItems(array('key1', 'key3'), array('namespace' => 'specificns')));
        unset($items['key1'], $items['key3']);

        $rs = $this->_storage->getItems(array_keys($items), array('namespace' => 'specificns'));
        $this->assertInternalType('array', $rs);
        foreach ($items as $key => $value) {
            $this->assertArrayHasKey($key, $rs);
            $this->assertEquals($value, $rs[$key]);
        }

        $rs = $this->_storage->hasItems(array_keys($items), array('namespace' => 'specificns'));
        $this->assertInternalType('array', $rs);
        $this->assertEquals(count($items), count($rs));
        foreach ($items as $key => $value) {
            $this->assertContains($key, $rs);
        }
    }

    public function testSetAndGetExpiredItem()
    {
        $capabilities = $this->_storage->getCapabilities();

        $ttl = $capabilities->getTtlPrecision();
        $this->_options->setTtl($ttl);

        $this->_storage->setItem('key', 'value');

        // wait until expired
        $wait = $ttl + $capabilities->getTtlPrecision();
        usleep($wait * 2000000);

        if (!$capabilities->getUseRequestTime()) {
            $this->assertFalse($this->_storage->getItem('key'));
        } else {
            $this->assertEquals('value', $this->_storage->getItem('key'));
        }

        if ($capabilities->getExpiredRead()) {
            $this->assertEquals('value', $this->_storage->getItem('key', array('ttl' => 0)));
        }
    }

    public function testSetAndGetExpiredItems()
    {
        $capabilities = $this->_storage->getCapabilities();

        $ttl = $capabilities->getTtlPrecision();
        $this->_options->setTtl($ttl);

        $items = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3'
        );
        $this->assertTrue($this->_storage->setItems($items));

        // wait until expired
        $wait = $ttl + $capabilities->getTtlPrecision();
        usleep($wait * 2000000);

        $rs = $this->_storage->getItems(array_keys($items));
        if (!$capabilities->getUseRequestTime()) {
            $this->assertEquals(array(), $rs);
        } else {
            ksort($rs);
            $this->assertEquals($items, $rs);
        }

        if ($capabilities->getExpiredRead()) {
            $rs = $this->_storage->getItems(array_keys($items), array('ttl' => 0));
            ksort($rs);
            $this->assertEquals($items, $rs);
        }
    }

    public function testSetAndGetItemOfDifferentTypes()
    {
        $capabilities = $this->_storage->getCapabilities();

        $types = array(
            'NULL'     => null,
            'boolean'  => true,
            'integer'  => 12345,
            'double'   => 123.45,
            'string'   => 'string', // already tested
            'array'    => array('one', 'tow' => 'two', 'three' => array('four' => 'four')),
            'object'   => new \stdClass(),
            'resource' => fopen(__FILE__, 'r'),
        );
        $types['object']->one = 'one';
        $types['object']->two = new \stdClass();
        $types['object']->two->three = 'three';

        foreach ($capabilities->getSupportedDatatypes() as $sourceType => $targetType) {
            if ($targetType === false) {
                continue;
            }

            $value = $types[$sourceType];
            $this->assertTrue($this->_storage->setItem('key', $value), "Failed to set type '{$sourceType}'");

            if ($targetType === true) {
                $this->assertSame($value, $this->_storage->getItem('key'));
            } elseif (is_string($targetType)) {
                settype($value, $targetType);
                $this->assertEquals($value, $this->_storage->getItem('key'));
            }
        }
    }

    public function testSetItemReturnsFalseIfNonWritable()
    {
        $this->_options->setWritable(false);

        $this->assertFalse($this->_storage->setItem('key', 'value'));
        $this->assertFalse($this->_storage->hasItem('key'));
    }

    public function testAddNewItem()
    {
        $this->assertTrue($this->_storage->addItem('key', 'value'));
        $this->assertTrue($this->_storage->hasItem('key'));
    }

    public function testAddItemThrowsExceptionIfItemAlreadyExists()
    {
        $this->assertTrue($this->_storage->setItem('key', 'value'));

        $this->setExpectedException('Zend\Cache\Exception');
        $this->_storage->addItem('key', 'newValue');
    }

    public function testAddItemReturnsFalseIfNonWritable()
    {
        $this->_options->setWritable(false);

        $this->assertFalse($this->_storage->addItem('key', 'value'));
        $this->assertFalse($this->_storage->hasItem('key'));
    }

    public function testReplaceExistingItem()
    {
        $this->assertTrue($this->_storage->setItem('key', 'value'));
        $this->assertTrue($this->_storage->replaceItem('key', 'anOtherValue'));
        $this->assertEquals('anOtherValue', $this->_storage->getItem('key'));
    }

    public function testReplaceItemThrowsItemNotFoundException()
    {
        $this->setExpectedException('Zend\Cache\Exception\ItemNotFoundException');
        $this->_storage->replaceItem('missingKey', 'value');
    }

    public function testReplaceItemReturnsFalseIfNonWritable()
    {
        $this->_storage->setItem('key', 'value');
        $this->_options->setWritable(false);

        $this->assertFalse($this->_storage->replaceItem('key', 'newvalue'));
        $this->assertEquals('value', $this->_storage->getItem('key'));
    }

    public function testRemoveMissingItemReturnsTrueIfIgnoreMissingItemsEnabled()
    {
        $this->_options->setIgnoreMissingItems(true);

        $this->assertTrue($this->_storage->removeItem('missing'));
    }

    public function testRemoveMissingItemThrowsExceptionIfIgnoreMissingItemsDisabled()
    {
        $this->_options->setIgnoreMissingItems(false);

        $this->setExpectedException('Zend\Cache\Exception\ItemNotFoundException');
        $this->_storage->removeItem('missing');
    }

    public function testRemoveMissingItemsReturnsTrueIfIgnoreMissingItemsEnabled()
    {
        $this->_options->setIgnoreMissingItems(true);
        $this->_storage->setItem('key', 'value');

        $this->assertTrue($this->_storage->removeItems(array('key', 'missing')));
    }

    public function testRemoveMissingItemsThrowsExceptionIfIgnoreMissingItemsDisabled()
    {
        $this->_options->setIgnoreMissingItems(false);
        $this->_storage->setItem('key', 'value');

        $this->setExpectedException('Zend\Cache\Exception\ItemNotFoundException');
        $this->_storage->removeItems(array('key', 'missing'));
    }

    public function testCheckAndSetItem()
    {
        $this->assertTrue($this->_storage->setItem('key', 'value'));

        $token = null;
        $this->assertEquals('value', $this->_storage->getItem('key', array('token' => &$token)));
        $this->assertNotNull($token);

        $this->assertTrue($this->_storage->checkAndSetItem($token, 'key', 'newValue'));
        $this->assertFalse($this->_storage->checkAndSetItem($token, 'key', 'failedValue'));
        $this->assertEquals('newValue', $this->_storage->getItem('key'));
    }

    public function testGetDelayedAndFetch()
    {
        $items = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3'
        );

        $this->assertTrue($this->_storage->setItems($items));

        $this->assertTrue($this->_storage->getDelayed(array_keys($items)));
        $fetchedKeys = array();
        while ( $item = $this->_storage->fetch() ) {
            $this->assertArrayHasKey('key', $item);
            $this->assertArrayHasKey('value', $item);

            $this->assertArrayHasKey($item['key'], $items);
            $this->assertEquals($items[$item['key']], $item['value']);
            $fetchedKeys[] = $item['key'];
        }
        sort($fetchedKeys);
        $this->assertEquals(array_keys($items), $fetchedKeys);
    }

    public function testGetDelayedAndFetchAll()
    {
        $items = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3'
        );

        $this->assertTrue($this->_storage->setItems($items));

        $this->assertTrue($this->_storage->getDelayed(array_keys($items)));

        $fetchedItems = $this->_storage->fetchAll();
        $this->assertEquals(count($items), count($fetchedItems));
        foreach ($fetchedItems as $item) {
            $this->assertArrayHasKey('key', $item);
            $this->assertArrayHasKey('value', $item);
            $this->assertEquals($items[$item['key']], $item['value']);
        }
    }

    public function testGetDelayedAndFetchAllWithSelectValue()
    {
        $items = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3'
        );

        $this->assertTrue($this->_storage->setItems($items));

        $this->assertTrue($this->_storage->getDelayed(array_keys($items), array(
            'select' => 'value'
        )));

        $fetchedItems = $this->_storage->fetchAll();
        $this->assertEquals(count($items), count($fetchedItems));
        foreach ($fetchedItems as $item) {
            $this->assertArrayNotHasKey('key', $item);
            $this->assertArrayHasKey('value', $item);
            $this->assertContains($item['value'], $items);
        }
    }

    public function testGetDelayedAndFetchAllWithSelectInfo()
    {
        $items = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3'
        );

        $this->assertTrue($this->_storage->setItems($items));

        $capabilities = $this->_storage->getCapabilities();
        $this->assertTrue($this->_storage->getDelayed(array_keys($items), array(
            'select' => $capabilities->getSupportedMetadata()
        )));

        $fetchedItems = $this->_storage->fetchAll();

        $this->assertEquals(count($items), count($fetchedItems));
        foreach ($fetchedItems as $item) {
            if (is_array($capabilities->getSupportedMetadata())) {
                foreach ($capabilities->getSupportedMetadata() as $selectProperty) {
                    $this->assertArrayHasKey($selectProperty, $item);
                }
            }
        }
    }

    public function testGetDelayedWithCallback()
    {
        $items = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3'
        );

        $this->assertTrue($this->_storage->setItems($items));

        $fetchedItems = array();
        $this->assertTrue($this->_storage->getDelayed(array_keys($items), array(
            'callback' => function($item) use (&$fetchedItems) {
                $fetchedItems[] = $item;
            },
        )));

        // wait for callback
        sleep(1);

        $this->assertEquals(count($items), count($fetchedItems));
        foreach ($fetchedItems as $item) {
            $this->assertArrayHasKey('key', $item);
            $this->assertArrayHasKey('value', $item);
            $this->assertEquals($items[$item['key']], $item['value']);
        }
    }

    public function testGetDelayedWithCallbackAndSelectInfo()
    {
        $items = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3'
        );

        $this->assertTrue($this->_storage->setItems($items));

        $fetchedItems = array();
        $capabilities = $this->_storage->getCapabilities();
        $this->assertTrue($this->_storage->getDelayed(array_keys($items), array(
            'callback' => function($item) use (&$fetchedItems) {
                $fetchedItems[] = $item;
            },
            'select' => $capabilities->getSupportedMetadata()
        )));

        // wait for callback
        sleep(1);

        $this->assertEquals(count($items), count($fetchedItems));
        foreach ($fetchedItems as $item) {
            foreach ($capabilities->getSupportedMetadata() as $selectProperty) {
                $this->assertArrayHasKey($selectProperty, $item);
            }
        }
    }

    public function testGetDelayedThrowExceptionOnActiveStatement()
    {
        $this->assertTrue($this->_storage->setItem('key', 'value'));
        $this->assertTrue($this->_storage->getDelayed(array('key')));

        $this->setExpectedException('Zend\Cache\Exception');
        $this->_storage->getDelayed(array('key'));
    }

    public function testIncrementItem()
    {
       $this->assertTrue($this->_storage->setItem('counter', 10));
       $this->assertEquals(15, $this->_storage->incrementItem('counter', 5));
       $this->assertEquals(15, $this->_storage->getItem('counter'));
    }

    public function testIncrementInitialValue()
    {
        $this->_options->setIgnoreMissingItems(true);

        $this->assertEquals(5, $this->_storage->incrementItem('counter', 5));
        $this->assertEquals(5, $this->_storage->getItem('counter'));
    }

    public function testIncrementItemThrowsItemNotFoundException()
    {
        $this->_options->setIgnoreMissingItems(false);

        $this->setExpectedException('Zend\Cache\Exception\ItemNotFoundException');
        $this->_storage->incrementItem(5, 'counter');
    }

    public function testIncrementItemReturnsFalseIfNonWritable()
    {
        $this->_storage->setItem('key', 10);
        $this->_options->setWritable(false);

        $this->assertFalse($this->_storage->incrementItem('key', 5));
        $this->assertEquals(10, $this->_storage->getItem('key'));
    }

    public function testIncrementItemsReturnsFalseIfNonWritable()
    {
        $this->_storage->setItem('key', 10);
        $this->_options->setWritable(false);

        $this->assertFalse($this->_storage->incrementItems(array('key' => 5)));
        $this->assertEquals(10, $this->_storage->getItem('key'));
    }

    public function testDecrementItem()
    {
       $this->assertTrue($this->_storage->setItem('counter', 30));
       $this->assertEquals(25, $this->_storage->decrementItem('counter', 5));
       $this->assertEquals(25, $this->_storage->getItem('counter'));
    }

    public function testDecrmentInitialValue()
    {
        $this->_options->setIgnoreMissingItems(true);
        $this->assertEquals(-5, $this->_storage->decrementItem('counter', 5));
        $this->assertEquals(-5, $this->_storage->getItem('counter'));
    }

    public function testDecrementItemThrowsItemNotFoundException()
    {
        $this->_options->setIgnoreMissingItems(false);
        $this->setExpectedException('Zend\Cache\Exception\ItemNotFoundException');
        $this->_storage->decrementItem(5, 'counter');
    }

    public function testDecrementItemReturnsFalseIfNonWritable()
    {
        $this->_storage->setItem('key', 10);
        $this->_options->setWritable(false);

        $this->assertFalse($this->_storage->decrementItem('key', 5));
        $this->assertEquals(10, $this->_storage->getItem('key'));
    }

    public function testDecrementItemsReturnsFalseIfNonWritable()
    {
        $this->_storage->setItem('key', 10);
        $this->_options->setWritable(false);

        $this->assertFalse($this->_storage->decrementItems(array('key' => 5)));
        $this->assertEquals(10, $this->_storage->getItem('key'));
    }

    public function testTouchItem()
    {
        $capabilities = $this->_storage->getCapabilities();
        $this->_options->setTtl(2 * $capabilities->getTtlPrecision());

        $this->assertTrue($this->_storage->setItem('key', 'value'));

        // sleep 1 times before expire to touch the item
        usleep($capabilities->getTtlPrecision() * 1000000);
        $this->assertTrue($this->_storage->touchItem('key'));

        usleep($capabilities->getTtlPrecision() * 1000000);
        $this->assertTrue($this->_storage->hasItem('key'));

        if (!$capabilities->getUseRequestTime()) {
            usleep($capabilities->getTtlPrecision() * 2000000);
            $this->assertFalse($this->_storage->hasItem('key'));
        }
    }

    public function testTouchInitialValueIfIgnoreMissingItemsEnabled()
    {
        $this->_options->setIgnoreMissingItems(true);

        $this->_storage->touchItem('newkey');
        $this->assertEquals('', $this->_storage->getItem('newkey'));
    }

    public function testTouchItemThrowsItemNotFoundExceptionIfIgnoreMissingItemsDisabled()
    {
        $this->_options->setIgnoreMissingItems(false);

        $this->setExpectedException('Zend\Cache\Exception\ItemNotFoundException');
        $this->_storage->touchItem('newkey');
    }

    public function testTouchItemReturnsFalseIfNonWritable()
    {
        $this->_options->setWritable(false);

        $this->assertFalse($this->_storage->touchItem('key'));
    }

    public function testTouchItemsReturnsFalseIfNonWritable()
    {
        $this->_options->setWritable(false);

        $this->assertFalse($this->_storage->touchItems(array('key')));
    }

    public function testClearExpiredByNamespace()
    {
        $capabilities = $this->_storage->getCapabilities();
        if (!$capabilities->getClearByNamespace()) {
            $this->setExpectedException('Zend\Cache\Exception\RuntimeException');
            $this->_storage->clearByNamespace(Adapter::MATCH_EXPIRED);
            return;
        }

        $ttl = $capabilities->getTtlPrecision();
        $this->_options->setTtl($ttl);

        $this->assertTrue($this->_storage->setItem('key1', 'value1'));

        // wait until the first item expired
        $wait = $ttl + $capabilities->getTtlPrecision();
        usleep($wait * 2000000);

        $this->assertTrue($this->_storage->setItem('key2', 'value2'));

        $this->assertTrue($this->_storage->clearByNamespace(Adapter::MATCH_EXPIRED));

        if ($capabilities->getUseRequestTime()) {
            $this->assertTrue($this->_storage->hasItem('key1'));
        } else {
            $this->assertFalse($this->_storage->hasItem('key1', array('ttl' => 0)));
        }

        $this->assertTrue($this->_storage->hasItem('key2'));
    }

    public function testClearActiveByNamespace()
    {
        $capabilities = $this->_storage->getCapabilities();
        if (!$capabilities->getClearByNamespace()) {
            $this->setExpectedException('Zend\Cache\Exception\RuntimeException');
            $this->_storage->clearByNamespace(Adapter::MATCH_ACTIVE);
            return;
        }

        $ttl = $capabilities->getTtlPrecision();
        $this->_options->setTtl($ttl);

        $this->assertTrue($this->_storage->setItem('key1', 'value1'));

        // wait until the first item expired
        $wait = $ttl + $capabilities->getTtlPrecision();
        usleep($wait * 2000000);

        $this->assertTrue($this->_storage->setItem('key2', 'value2'));

        $this->assertTrue($this->_storage->clearByNamespace(Adapter::MATCH_ACTIVE));

        if ($capabilities->getExpiredRead() && !$capabilities->getUseRequestTime()) {
            $this->assertTrue($this->_storage->hasItem('key1', array('ttl' => 0)));
        }
        $this->assertFalse($this->_storage->hasItem('key2', array('ttl' => 0)));
    }

    public function testClearAllByNamespace()
    {
        $capabilities = $this->_storage->getCapabilities();
        if (!$capabilities->getClearByNamespace()) {
            $this->setExpectedException('Zend\Cache\Exception\RuntimeException');
            $this->_storage->clearByNamespace(Adapter::MATCH_ALL);
            return;
        }

        $items = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3'
        );
        $namespaces = array('ns1', 'ns2');

        foreach ($namespaces as $ns) {
            $this->_options->setNamespace($ns);
            foreach ($items as $k => $v) {
                $this->assertTrue($this->_storage->setItem($ns.$k, $ns.$v));
            }
        }

        $clearNs = array_shift($namespaces);
        $this->_options->setNamespace($clearNs);
        $this->assertTrue($this->_storage->clearByNamespace(Adapter::MATCH_ALL));

        // wait
        usleep($capabilities->getTtlPrecision() * 2000000);

        foreach ($items as $k => $v) {
            $this->assertFalse($this->_storage->hasItem($clearNs.$k));
        }

        foreach ($namespaces as $ns) {
            $this->_options->setNamespace($ns);
            foreach ($items as $k => $v) {
                $this->assertTrue($this->_storage->hasItem($ns.$k));
            }
        }
    }

    public function testClearAll()
    {
        $capabilities = $this->_storage->getCapabilities();
        if (!$capabilities->getClearAllNamespaces()) {
            $this->setExpectedException('Zend\Cache\Exception');
            $this->_storage->clear(Adapter::MATCH_ALL);
            return;
        }

        $items = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3'
        );
        $namespaces = array('ns1', 'ns2');

        foreach ($namespaces as $ns) {
            $this->_options->setNamespace($ns);
            foreach ($items as $k => $v) {
                $this->assertTrue($this->_storage->setItem($ns.$k, $ns.$v));
            }
        }

        $this->assertTrue($this->_storage->clear(Adapter::MATCH_ALL));

        // wait
        usleep($capabilities->getTtlPrecision() * 2000000);

        foreach ($namespaces as $ns) {
            $this->_options->setNamespace($ns);
            foreach ($items as $k => $v) {
                $this->assertFalse($this->_storage->hasItem($ns.$k));
            }
        }
    }

    public function testFindActive()
    {
        $capabilities = $this->_storage->getCapabilities();
        if (!$capabilities->getIterable()) {
            $this->markTestSkipped("Find isn't supported by this adapter");
        }

        $this->_options->setTtl($capabilities->getTtlPrecision());

        $this->assertTrue($this->_storage->setItem('key1', 'value1'));
        $this->assertTrue($this->_storage->setItem('key2', 'value2'));

        // wait until first 2 items expired
        usleep(($capabilities->getTtlPrecision() * 1000000) + 1000000);

        $this->assertTrue($this->_storage->setItem('key3', 'value3'));
        $this->assertTrue($this->_storage->setItem('key4', 'value4'));

        $this->assertTrue($this->_storage->find(Adapter::MATCH_ACTIVE));

        if ($capabilities->getUseRequestTime()) {
            $expectedItems = array(
                'key1' => 'value1',
                'key2' => 'value2',
                'key3' => 'value3',
                'key4' => 'value4'
            );
        } else {
            $expectedItems = array(
                'key3' => 'value3',
                'key4' => 'value4'
            );
        }

        $actualItems = array();
        while (($item = $this->_storage->fetch()) !== false) {
            // check $item
            $this->assertArrayHasKey('key', $item);
            $this->assertArrayHasKey('value', $item);

            $actualItems[ $item['key'] ] = $item['value'];
        }

        ksort($actualItems);
        $this->assertEquals($expectedItems, $actualItems);
    }

    public function testFindExpired()
    {
        $capabilities = $this->_storage->getCapabilities();
        if (!$capabilities->getIterable()) {
            $this->markTestSkipped("Find isn't supported by this adapter");
        }

        $this->_options->setTtl($capabilities->getTtlPrecision());

        $this->assertTrue($this->_storage->setItem('key1', 'value1'));
        $this->assertTrue($this->_storage->setItem('key2', 'value2'));

        // wait until first 2 items expired
        usleep($capabilities->getTtlPrecision() * 2000000);

        $this->assertTrue($this->_storage->setItem('key3', 'value3'));
        $this->assertTrue($this->_storage->setItem('key4', 'value4'));

        $this->assertTrue($this->_storage->find(Adapter::MATCH_EXPIRED));

        if ($capabilities->getExpiredRead() && !$capabilities->getUseRequestTime()) {
            $expectedItems = array(
                'key1' => 'value1',
                'key2' => 'value2'
            );
        } else {
            $expectedItems = array();
        }

        $actualItems = array();
        while (($item = $this->_storage->fetch()) !== false) {
            // check $item
            $this->assertArrayHasKey('key', $item);
            $this->assertArrayHasKey('value', $item);
            $this->assertEquals(2, count($item));

            $actualItems[ $item['key'] ] = $item['value'];
        }

        ksort($actualItems);
        $this->assertEquals($expectedItems, $actualItems);
    }

    public function testHasItemWithNonReadable()
    {
        $this->assertTrue($this->_storage->setItem('key', 'value'));

        $this->_options->setReadable(false);
        $this->assertFalse($this->_storage->hasItem('key'));
    }

    /*
    public function testHasItemsWithNonReadable()
    {
        $this->assertTrue($this->_storage->setItem('key', 'value'));

        $this->_options->setReadable(false);
        $this->assertEquals(array(), $this->_storage->hasItems(array('key')));
    }

    public function testGetCapacity()
    {
        $capacity = $this->_storage->getCapacity();

        $this->assertArrayHasKey('total', $capacity);
        $this->assertInternalType('numeric', $capacity['total']);

        $this->assertArrayHasKey('free', $capacity);
        $this->assertInternalType('numeric', $capacity['free']);

        $this->assertGreaterThanOrEqual(
            $capacity['free'], $capacity['total'],
            "The total storage space must be greater or equal than the free space"
        );
    }

    public function testOptimizeSimpleCall()
    {
        $rs = $this->_storage->optimize();
        $this->assertTrue($rs);
    }

    public function testTagsAreUsedWhenCaching()
    {
        $capabilities = $this->_storage->getCapabilities();
        if (!$capabilities->getTagging()) {
            $this->markTestSkipped("Tags are not supported by this adapter");
        }

        // Ensure we don't have expired items in the cache for this test
        $this->_options->setTtl(60);
        $this->_storage->setItem('someitem', 'somevalue', array('tags' => array('foo')));
        $this->assertTrue($this->_storage->find(Cache\Storage\Adapter::MATCH_TAGS_OR, array('tags' => array('foo'))));
        $actualItems = array();
        while (($item = $this->_storage->fetch()) !== false) {
            // check $item
            $this->assertArrayHasKey('key', $item);
            $this->assertArrayHasKey('value', $item);

            $actualItems[ $item['key'] ] = $item['value'];
        }
        $this->assertEquals(1, count($actualItems));
        $this->assertArrayHasKey('someitem', $actualItems);
        $this->assertEquals('somevalue', $actualItems['someitem']);
    }
     */
}
