<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cache
 */

namespace ZendTest\Cache\Storage\Adapter;

use Zend\Cache\Storage\AvailableSpaceCapableInterface;
use Zend\Cache\Storage\IterableInterface;
use Zend\Cache\Storage\IteratorInterface;
use Zend\Cache\Storage\StorageInterface;
use Zend\Cache\Storage\ClearExpiredInterface;
use Zend\Cache\Storage\ClearByNamespaceInterface;
use Zend\Cache\Storage\ClearByPrefixInterface;
use Zend\Cache\Storage\FlushableInterface;
use Zend\Cache\Storage\OptimizableInterface;
use Zend\Cache\Storage\TaggableInterface;
use Zend\Cache\Storage\TotalSpaceCapableInterface;
use Zend\Http\Header\Expires;
use Zend\Stdlib\ErrorHandler;

/**
 * PHPUnit test case
 */

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @group      Zend_Cache
 */
abstract class CommonAdapterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The storage adapter
     *
     * @var StorageInterface
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
            'Zend\Cache\Storage\StorageInterface',
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

    public function testHasItemReturnsTrueOnValidItem()
    {
        $this->assertTrue($this->_storage->setItem('key', 'value'));
        $this->assertTrue($this->_storage->hasItem('key'));
    }

    public function testHasItemReturnsFalseOnMissingItem()
    {
        $this->assertFalse($this->_storage->hasItem('key'));
    }

    public function testHasItemReturnsFalseOnExpiredItem()
    {
        $capabilities = $this->_storage->getCapabilities();

        if ($capabilities->getMinTtl() === 0) {
            $this->markTestSkipped("Adapter doesn't support item expiration");
        }

        $ttl = $capabilities->getTtlPrecision();
        $this->_options->setTtl($ttl);

        $this->assertTrue($this->_storage->setItem('key', 'value'));

        // wait until the item expired
        $wait = $ttl + $capabilities->getTtlPrecision();
        usleep($wait * 2000000);

        if (!$capabilities->getUseRequestTime()) {
            $this->assertFalse($this->_storage->hasItem('key'));
        } else {
            $this->assertTrue($this->_storage->hasItem('key'));
        }
    }

    public function testHasItemNonReadable()
    {
        $this->assertTrue($this->_storage->setItem('key', 'value'));

        $this->_options->setReadable(false);
        $this->assertFalse($this->_storage->hasItem('key'));
    }

    public function testHasItemsReturnsKeysOfFoundItems()
    {
        $this->assertTrue($this->_storage->setItem('key1', 'value1'));
        $this->assertTrue($this->_storage->setItem('key2', 'value2'));

        $result = $this->_storage->hasItems(array('missing', 'key1', 'key2'));
        sort($result);

        $exprectedResult = array('key1', 'key2');
        $this->assertEquals($exprectedResult, $result);
    }

    public function testHasItemsReturnsEmptyArrayIfNonReadable()
    {
        $this->assertTrue($this->_storage->setItem('key', 'value'));

        $this->_options->setReadable(false);
        $this->assertEquals(array(), $this->_storage->hasItems(array('key')));
    }

    public function testGetItemReturnsNullOnMissingItem()
    {
        $this->assertNull($this->_storage->getItem('unknwon'));
    }

    public function testGetItemSetsSuccessFlag()
    {
        $success = null;

        // $success = false on get missing item
        $this->_storage->getItem('unknown', $success);
        $this->assertFalse($success);

        // $success = true on get valid item
        $this->_storage->setItem('test', 'test');
        $this->_storage->getItem('test', $success);
        $this->assertTrue($success);
    }

    public function testGetItemReturnsNullOnExpiredItem()
    {
        $capabilities = $this->_storage->getCapabilities();

        if ($capabilities->getMinTtl() === 0) {
            $this->markTestSkipped("Adapter doesn't support item expiration");
        }

        if ($capabilities->getUseRequestTime()) {
            $this->markTestSkipped("Can't test get expired item if request time will be used");
        }

        $ttl = $capabilities->getTtlPrecision();
        $this->_options->setTtl($ttl);

        $this->_storage->setItem('key', 'value');

        // wait until expired
        $wait = $ttl + $capabilities->getTtlPrecision();
        usleep($wait * 2000000);

        $this->assertNull($this->_storage->getItem('key'));
    }

    public function testGetItemReturnsNullIfNonReadable()
    {
        $this->_options->setReadable(false);

        $this->assertTrue($this->_storage->setItem('key', 'value'));
        $this->assertNull($this->_storage->getItem('key'));
    }

    public function testGetItemsReturnsKeyValuePairsOfFoundItems()
    {
        $this->assertTrue($this->_storage->setItem('key1', 'value1'));
        $this->assertTrue($this->_storage->setItem('key2', 'value2'));

        $result = $this->_storage->getItems(array('missing', 'key1', 'key2'));
        ksort($result);

        $exprectedResult = array(
            'key1' => 'value1',
            'key2' => 'value2',
        );
        $this->assertEquals($exprectedResult, $result);
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

    public function testGetMetadataReturnsFalseOnMissingItem()
    {
        $this->assertFalse($this->_storage->getMetadata('unknown'));
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
        $this->assertSame(array(), $this->_storage->setItems($items));

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

    public function testSetGetHasAndRemoveItemWithoutNamespace()
    {
        $this->_storage->getOptions()->setNamespace('');

        $this->assertTrue($this->_storage->setItem('key', 'value'));
        $this->assertEquals('value', $this->_storage->getItem('key'));
        $this->assertTrue($this->_storage->hasItem('key'));

        $this->assertTrue($this->_storage->removeItem('key'));
        $this->assertFalse($this->_storage->hasItem('key'));
        $this->assertNull($this->_storage->getItem('key'));
    }

    public function testSetGetHasAndRemoveItemsWithoutNamespace()
    {
        $this->_storage->getOptions()->setNamespace('');

        $items = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        );

        $this->assertSame(array(), $this->_storage->setItems($items));

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

        $this->assertSame(array('missing'), $this->_storage->removeItems(array('missing', 'key1', 'key3')));
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
        $this->assertSame(array(), $this->_storage->setItems($items));

        $this->_options->setNamespace('defaultns2');
        $this->assertSame(array(),  $this->_storage->hasItems(array_keys($items)));

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
        $this->assertSame(array('missing'), $this->_storage->removeItems(array('missing', 'key1', 'key3')));
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

    public function testSetAndGetExpiredItem()
    {
        $capabilities = $this->_storage->getCapabilities();

        if ($capabilities->getMinTtl() === 0) {
            $this->markTestSkipped("Adapter doesn't support item expiration");
        }

        $ttl = $capabilities->getTtlPrecision();
        $this->_options->setTtl($ttl);

        $this->_storage->setItem('key', 'value');

        // wait until expired
        $wait = $ttl + $capabilities->getTtlPrecision();
        usleep($wait * 2000000);

        if ($capabilities->getUseRequestTime()) {
            // Can't test much more if the request time will be used
            $this->assertEquals('value', $this->_storage->getItem('key'));
            return;
        }

        $this->assertNull($this->_storage->getItem('key'));

        $this->_options->setTtl(0);
        if ($capabilities->getExpiredRead()) {
            $this->assertEquals('value', $this->_storage->getItem('key'));
        } else {
            $this->assertNull($this->_storage->getItem('key'));
        }
    }

    public function testSetAndGetExpiredItems()
    {
        $capabilities = $this->_storage->getCapabilities();

        if ($capabilities->getMinTtl() === 0) {
            $this->markTestSkipped("Adapter doesn't support item expiration");
        }

        $ttl = $capabilities->getTtlPrecision();
        $this->_options->setTtl($ttl);

        $items = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3'
        );
        $this->assertSame(array(), $this->_storage->setItems($items));

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

        $this->_options->setTtl(0);
        if ($capabilities->getExpiredRead()) {
            $rs = $this->_storage->getItems(array_keys($items));
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

    public function testAddItemReturnsFalseIfItemAlreadyExists()
    {
        $this->assertTrue($this->_storage->setItem('key', 'value'));
        $this->assertFalse($this->_storage->addItem('key', 'newValue'));
    }

    public function testAddItemReturnsFalseIfNonWritable()
    {
        $this->_options->setWritable(false);

        $this->assertFalse($this->_storage->addItem('key', 'value'));
        $this->assertFalse($this->_storage->hasItem('key'));
    }

    public function testAddItemsReturnsFailedKeys()
    {
        $this->assertTrue($this->_storage->setItem('key1', 'value1'));

        $failedKeys = $this->_storage->addItems(array(
            'key1' => 'XYZ',
            'key2' => 'value2',
        ));

        $this->assertSame(array('key1'), $failedKeys);
        $this->assertSame('value1', $this->_storage->getItem('key1'));
        $this->assertTrue($this->_storage->hasItem('key2'));
    }

    public function testReplaceExistingItem()
    {
        $this->assertTrue($this->_storage->setItem('key', 'value'));
        $this->assertTrue($this->_storage->replaceItem('key', 'anOtherValue'));
        $this->assertEquals('anOtherValue', $this->_storage->getItem('key'));
    }

    public function testReplaceItemReturnsFalseOnMissingItem()
    {
        $this->assertFalse($this->_storage->replaceItem('missingKey', 'value'));
    }

    public function testReplaceItemReturnsFalseIfNonWritable()
    {
        $this->_storage->setItem('key', 'value');
        $this->_options->setWritable(false);

        $this->assertFalse($this->_storage->replaceItem('key', 'newvalue'));
        $this->assertEquals('value', $this->_storage->getItem('key'));
    }

    public function testReplaceItemsReturnsFailedKeys()
    {
        $this->assertTrue($this->_storage->setItem('key1', 'value1'));

        $failedKeys = $this->_storage->replaceItems(array(
            'key1' => 'XYZ',
            'key2' => 'value2',
        ));

        $this->assertSame(array('key2'), $failedKeys);
        $this->assertSame('XYZ', $this->_storage->getItem('key1'));
        $this->assertFalse($this->_storage->hasItem('key2'));
    }

    public function testRemoveItemReturnsFalseOnMissingItem()
    {
        $this->assertFalse($this->_storage->removeItem('missing'));
    }

    public function testRemoveItemsReturnsMissingKeys()
    {
        $this->_storage->setItem('key', 'value');
        $this->assertSame(array('missing'), $this->_storage->removeItems(array('key', 'missing')));
    }

    public function testCheckAndSetItem()
    {
        $this->assertTrue($this->_storage->setItem('key', 'value'));

        $success  = null;
        $casToken = null;
        $this->assertEquals('value', $this->_storage->getItem('key', $success, $casToken));
        $this->assertNotNull($casToken);

        $this->assertTrue($this->_storage->checkAndSetItem($casToken, 'key', 'newValue'));
        $this->assertFalse($this->_storage->checkAndSetItem($casToken, 'key', 'failedValue'));
        $this->assertEquals('newValue', $this->_storage->getItem('key'));
    }

    public function testIncrementItem()
    {
       $this->assertTrue($this->_storage->setItem('counter', 10));
       $this->assertEquals(15, $this->_storage->incrementItem('counter', 5));
       $this->assertEquals(15, $this->_storage->getItem('counter'));
    }

    public function testIncrementItemInitialValue()
    {
        $this->assertEquals(5, $this->_storage->incrementItem('counter', 5));
        $this->assertEquals(5, $this->_storage->getItem('counter'));
    }

    public function testIncrementItemReturnsFalseIfNonWritable()
    {
        $this->_storage->setItem('key', 10);
        $this->_options->setWritable(false);

        $this->assertFalse($this->_storage->incrementItem('key', 5));
        $this->assertEquals(10, $this->_storage->getItem('key'));
    }

    public function testIncrementItemsResturnsKeyValuePairsOfWrittenItems()
    {
        $this->assertTrue($this->_storage->setItem('key1', 10));

        $result = $this->_storage->incrementItems(array(
            'key1' => 10,
            'key2' => 10,
        ));
        ksort($result);

        $this->assertSame(array(
            'key1' => 20,
            'key2' => 10,
        ), $result);
    }

    public function testIncrementItemsReturnsEmptyArrayIfNonWritable()
    {
        $this->_storage->setItem('key', 10);
        $this->_options->setWritable(false);

        $this->assertSame(array(), $this->_storage->incrementItems(array('key' => 5)));
        $this->assertEquals(10, $this->_storage->getItem('key'));
    }

    public function testDecrementItem()
    {
       $this->assertTrue($this->_storage->setItem('counter', 30));
       $this->assertEquals(25, $this->_storage->decrementItem('counter', 5));
       $this->assertEquals(25, $this->_storage->getItem('counter'));
    }

    public function testDecrementItemInitialValue()
    {
        $this->assertEquals(-5, $this->_storage->decrementItem('counter', 5));
        $this->assertEquals(-5, $this->_storage->getItem('counter'));
    }

    public function testDecrementItemReturnsFalseIfNonWritable()
    {
        $this->_storage->setItem('key', 10);
        $this->_options->setWritable(false);

        $this->assertFalse($this->_storage->decrementItem('key', 5));
        $this->assertEquals(10, $this->_storage->getItem('key'));
    }

    public function testDecrementItemsReturnsEmptyArrayIfNonWritable()
    {
        $this->_storage->setItem('key', 10);
        $this->_options->setWritable(false);

        $this->assertSame(array(), $this->_storage->decrementItems(array('key' => 5)));
        $this->assertEquals(10, $this->_storage->getItem('key'));
    }

    public function testTouchItem()
    {
        $capabilities = $this->_storage->getCapabilities();

        if ($capabilities->getMinTtl() === 0) {
            $this->markTestSkipped("Adapter doesn't support item expiration");
        }

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

    public function testTouchItemReturnsFalseOnMissingItem()
    {
        $this->assertFalse($this->_storage->touchItem('missing'));
    }

    public function testTouchItemReturnsFalseIfNonWritable()
    {
        $this->_options->setWritable(false);

        $this->assertFalse($this->_storage->touchItem('key'));
    }

    public function testTouchItemsReturnsGivenKeysIfNonWritable()
    {
        $this->_options->setWritable(false);
        $this->assertSame(array('key'), $this->_storage->touchItems(array('key')));
    }

    public function testOptimize()
    {
        if (!($this->_storage instanceof OptimizableInterface)) {
            $this->markTestSkipped("Storage doesn't implement OptimizableInterface");
        }

        $this->assertTrue($this->_storage->optimize());
    }

    public function testIterator()
    {
        if (!$this->_storage instanceof IterableInterface) {
            $this->markTestSkipped("Storage doesn't implement IterableInterface");
        }

        $items = array(
            'key1' => 'value1',
            'key2' => 'value2',
        );
        $this->assertSame(array(), $this->_storage->setItems($items));

        // check iterator aggregate
        $iterator = $this->_storage->getIterator();
        $this->assertInstanceOf('Zend\Cache\Storage\IteratorInterface', $iterator);
        $this->assertSame(IteratorInterface::CURRENT_AS_KEY, $iterator->getMode());

        // check mode CURRENT_AS_KEY
        $iterator = $this->_storage->getIterator();
        $iterator->setMode(IteratorInterface::CURRENT_AS_KEY);
        $keys = iterator_to_array($iterator, false);
        sort($keys);
        $this->assertSame(array_keys($items), $keys);

        // check mode CURRENT_AS_VALUE
        $iterator = $this->_storage->getIterator();
        $iterator->setMode(IteratorInterface::CURRENT_AS_VALUE);
        $result = iterator_to_array($iterator, true);
        ksort($result);
        $this->assertSame($items, $result);
    }

    public function testFlush()
    {
        if (!($this->_storage instanceof FlushableInterface)) {
            $this->markTestSkipped("Storage doesn't implement OptimizableInterface");
        }

        $this->assertSame(array(), $this->_storage->setItems(array(
           'key1' => 'value1',
           'key2' => 'value2',
        )));

        $this->assertTrue($this->_storage->flush());
        $this->assertFalse($this->_storage->hasItem('key1'));
        $this->assertFalse($this->_storage->hasItem('key2'));
    }

    public function testClearByPrefix()
    {
        if (!($this->_storage instanceof ClearByPrefixInterface)) {
            $this->markTestSkipped("Storage doesn't implement ClearByPrefixInterface");
        }

        $this->assertSame(array(), $this->_storage->setItems(array(
            'key1' => 'value1',
            'key2' => 'value2',
            'test' => 'value',
        )));

        $this->assertTrue($this->_storage->clearByPrefix('key'));
        $this->assertFalse($this->_storage->hasItem('key1'));
        $this->assertFalse($this->_storage->hasItem('key2'));
        $this->assertTrue($this->_storage->hasItem('test'));
    }

    public function testClearByPrefixThrowsInvalidArgumentExceptionOnEmptyPrefix()
    {
        if (!($this->_storage instanceof ClearByPrefixInterface)) {
            $this->markTestSkipped("Storage doesn't implement ClearByPrefixInterface");
        }

        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_storage->clearByPrefix('');
    }

    public function testClearByNamespace()
    {
        if (!($this->_storage instanceof ClearByNamespaceInterface)) {
            $this->markTestSkipped("Storage doesn't implement ClearByNamespaceInterface");
        }

        // write 2 items of 2 different namespaces
        $this->_options->setNamespace('ns1');
        $this->assertTrue($this->_storage->setItem('key1', 'value1'));
        $this->_options->setNamespace('ns2');
        $this->assertTrue($this->_storage->setItem('key2', 'value2'));

        // clear unknown namespace should return true but clear nothing
        $this->assertTrue($this->_storage->clearByNamespace('unknown'));
        $this->_options->setNamespace('ns1');
        $this->assertTrue($this->_storage->hasItem('key1'));
        $this->_options->setNamespace('ns2');
        $this->assertTrue($this->_storage->hasItem('key2'));

        // clear "ns1"
        $this->assertTrue($this->_storage->clearByNamespace('ns1'));
        $this->_options->setNamespace('ns1');
        $this->assertFalse($this->_storage->hasItem('key1'));
        $this->_options->setNamespace('ns2');
        $this->assertTrue($this->_storage->hasItem('key2'));

        // clear "ns2"
        $this->assertTrue($this->_storage->clearByNamespace('ns2'));
        $this->_options->setNamespace('ns1');
        $this->assertFalse($this->_storage->hasItem('key1'));
        $this->_options->setNamespace('ns2');
        $this->assertFalse($this->_storage->hasItem('key2'));
    }

    public function testClearByNamespaceThrowsInvalidArgumentExceptionOnEmptyNamespace()
    {
        if (!($this->_storage instanceof ClearByNamespaceInterface)) {
            $this->markTestSkipped("Storage doesn't implement ClearByNamespaceInterface");
        }

        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_storage->clearByNamespace('');
    }

    public function testClearExpired()
    {
        if (!($this->_storage instanceof ClearExpiredInterface)) {
            $this->markTestSkipped("Storage doesn't implement ClearExpiredInterface");
        }

        $capabilities = $this->_storage->getCapabilities();
        $ttl = $capabilities->getTtlPrecision();
        $this->_options->setTtl($ttl);

        $this->assertTrue($this->_storage->setItem('key1', 'value1'));

        // wait until the first item expired
        $wait = $ttl + $capabilities->getTtlPrecision();
        usleep($wait * 2000000);

        $this->assertTrue($this->_storage->setItem('key2', 'value2'));

        $this->assertTrue($this->_storage->clearExpired());

        if ($capabilities->getUseRequestTime()) {
            $this->assertTrue($this->_storage->hasItem('key1'));
        } else {
            $this->assertFalse($this->_storage->hasItem('key1', array('ttl' => 0)));
        }

        $this->assertTrue($this->_storage->hasItem('key2'));
    }

    public function testTaggable()
    {
        if (!($this->_storage instanceof TaggableInterface)) {
            $this->markTestSkipped("Storage doesn't implement TaggableInterface");
        }

        $this->assertSame(array(), $this->_storage->setItems(array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        )));

        $this->assertTrue($this->_storage->setTags('key1', array('tag1a', 'tag1b')));
        $this->assertTrue($this->_storage->setTags('key2', array('tag2a', 'tag2b')));
        $this->assertTrue($this->_storage->setTags('key3', array('tag3a', 'tag3b')));
        $this->assertFalse($this->_storage->setTags('missing', array('tag')));

        // return tags
        $tags = $this->_storage->getTags('key1');
        $this->assertInternalType('array', $tags);
        sort($tags);
        $this->assertSame(array('tag1a', 'tag1b'), $tags);

        // this should remove nothing
        $this->assertTrue($this->_storage->clearByTags(array('tag1a', 'tag2a')));
        $this->assertTrue($this->_storage->hasItem('key1'));
        $this->assertTrue($this->_storage->hasItem('key2'));
        $this->assertTrue($this->_storage->hasItem('key3'));

        // this should remove key1 and key2
        $this->assertTrue($this->_storage->clearByTags(array('tag1a', 'tag2b'), true));
        $this->assertFalse($this->_storage->hasItem('key1'));
        $this->assertFalse($this->_storage->hasItem('key2'));
        $this->assertTrue($this->_storage->hasItem('key3'));

        // this should remove key3
        $this->assertTrue($this->_storage->clearByTags(array('tag3a', 'tag3b'), true));
        $this->assertFalse($this->_storage->hasItem('key1'));
        $this->assertFalse($this->_storage->hasItem('key2'));
        $this->assertFalse($this->_storage->hasItem('key3'));
    }

    public function testGetTotalSpace()
    {
        if (!($this->_storage instanceof TotalSpaceCapableInterface)) {
            $this->markTestSkipped("Storage doesn't implement TotalSpaceCapableInterface");
        }

        $totalSpace = $this->_storage->getTotalSpace();
        $this->assertGreaterThanOrEqual(0, $totalSpace);

        if ($this->_storage instanceof AvailableSpaceCapableInterface) {
            $availableSpace = $this->_storage->getAvailableSpace();
            $this->assertGreaterThanOrEqual($availableSpace, $totalSpace);
        }
    }

    public function testGetAvailableSpace()
    {
        if (!($this->_storage instanceof AvailableSpaceCapableInterface)) {
            $this->markTestSkipped("Storage doesn't implement AvailableSpaceCapableInterface");
        }

        $availableSpace = $this->_storage->getAvailableSpace();
        $this->assertGreaterThanOrEqual(0, $availableSpace);

        if ($this->_storage instanceof TotalSpaceCapableInterface) {
            $totalSpace = $this->_storage->getTotalSpace();
            $this->assertLessThanOrEqual($totalSpace, $availableSpace);
        }
    }
}
