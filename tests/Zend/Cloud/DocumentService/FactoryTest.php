<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cloud
 */

namespace ZendTest\Cloud\DocumentService;

use Zend\Cloud\DocumentService\Factory as DocumentFactory;
use Zend\Config\Config;
use ZendTest\Cloud\DocumentService\Adapter\SimpleDbTest;
use ZendTest\Cloud\DocumentService\Adapter\WindowsAzureTest;
use PHPUnit_Framework_TestCase as PHPUnitTestCase;

/**
 * Test class for Zend\Cloud\DocumentService\Factory
 *
 * @category   Zend
 * @package    Zend_Cloud_DocumentService
 * @subpackage UnitTests
 * @group      Zend_Cloud
 */
class FactoryTest extends PHPUnitTestCase
{
    public function testGetDocumentAdapterKey()
    {
        $this->assertTrue(is_string(\Zend\Cloud\DocumentService\Factory::DOCUMENT_ADAPTER_KEY));
    }

    public function testGetAdapterWithConfig()
    {
        // SimpleDB adapter
        $simpleDbAdapter = DocumentFactory::getAdapter(
                                    new Config(SimpleDbTest::getConfigArray(), true)
                                );

        $this->assertEquals('Zend\Cloud\DocumentService\Adapter\SimpleDb', get_class($simpleDbAdapter));

        // Azure adapter
        /*
         * Disable WindowsAzure test
        $azureAdapter = DocumentFactory::getAdapter(
                                    new Config(WindowsAzureTest::getConfigArray(), true)
                                );

        $this->assertEquals('Zend\Cloud\DocumentService\Adapter\WindowsAzure', get_class($azureAdapter));
         */
    }
}
