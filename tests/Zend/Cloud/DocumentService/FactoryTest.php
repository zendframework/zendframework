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
 * @package    Zend\Cloud\DocumentService
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Cloud\DocumentService;

// Call ZendTest\Cloud\DocumentService\FactoryTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "ZendTest\Cloud\DocumentService\FactoryTest::main");
}

use Zend\Cloud\DocumentService\Factory as DocumentFactory,
    Zend\Config\Config,
    ZendTest\Cloud\DocumentService\Adapter\SimpleDbTest,
    ZendTest\Cloud\DocumentService\Adapter\WindowsAzureTest,
    PHPUnit_Framework_TestCase as PHPUnitTestCase;

/**
 * Test class for Zend\Cloud\DocumentService\Factory
 *
 * @category   Zend
 * @package    Zend\Cloud\DocumentService
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend\Cloud
 */
class FactoryTest extends PHPUnitTestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testGetDocumentAdapterKey()
    {
        $this->assertTrue(is_string(\Zend\Cloud\DocumentService\Factory::DOCUMENT_ADAPTER_KEY));
    }

    public function testGetAdapterWithConfig() {
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

// Call ZendTest\Cloud\DocumentService\FactoryTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "ZendTest\Cloud\DocumentService\FactoryTest::main") {
    FactoryTest::main();
}
