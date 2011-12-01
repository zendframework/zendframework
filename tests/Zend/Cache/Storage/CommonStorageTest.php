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
 * @version    $Id$
 */

namespace ZendTest\Cache\Storage;

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
abstract class CommonStorageTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The storage instance (adapter or plugin)
     *
     * @var Zend\Cache\Storage\Adapter
     */
    protected $_storage;

    protected $_phpDatatypes = array(
        'NULL', 'boolean', 'integer', 'double',
        'string', 'array', 'object', 'resource'
    );

    public function setUp()
    {
        $this->assertInstanceOf(
            'Zend\Cache\Storage\Adapter',
            $this->_storage,
            'Internal storage instance is needed for tests'
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

    public function testOptionNamesValid()
    {
        $options = $this->_storage->getOptions();
        foreach ($options as $name => $value) {
            $this->assertRegExp(
                '/^[a-z]*[a-z0-9_]*[a-z0-9]*$/',
                $name,
                "Invalid option name '{$name}'"
            );
        }
    }

    public function testOptionsGetAndSetDefault()
    {
        $options = $this->_storage->getOptions();
        $this->_storage->setOptions($options);
        $this->assertEquals($options, $this->_storage->getOptions());
    }

    public function testOptionsFluentInterface()
    {
        $options = $this->_storage->getOptions();
        foreach ($options as $option => $value) {
            $method = ucwords(str_replace('_', ' ', $option));
            $method = 'set' . str_replace(' ', '', $method);
            $this->assertSame(
                $this->_storage,
                $this->_storage->{$method}($value),
                "Method '{$method}' doesn't implement the fluent interface"
            );
        }

        $this->assertSame(
            $this->_storage,
            $this->_storage->setOptions(array()),
            "Method 'setOptions' doesn't implement the fluent interface"
        );
    }

}
