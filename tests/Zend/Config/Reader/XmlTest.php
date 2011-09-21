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
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Config\Reader;

use \Zend\Config\Reader\Xml;

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Config
 */
class XmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Xml
     */
    protected $reader;
    
    protected $_xmlFileConfig;
    protected $_xmlFileAllSectionsConfig;
    protected $_xmlFileCircularConfig;
    protected $_xmlFileInvalid;

    public function setUp()
    {
        $this->reader = new Xml();
    }

    public function testConstants()
    {
        if (!defined('ZEND_CONFIG_XML_TEST_CONSTANT')) {
            define('ZEND_CONFIG_XML_TEST_CONSTANT', 'test');
        }

        $string = <<<EOT
<?xml version="1.0"?>
<config xmlns:zf="http://framework.zend.com/xml/zend-config-xml/1.0/">
    <all>
        <foo>foo-<zf:const zf:name="ZEND_CONFIG_XML_TEST_CONSTANT"/>-bar-<zf:const zf:name="ZEND_CONFIG_XML_TEST_CONSTANT"/></foo>
        <bar><const name="ZEND_CONFIG_XML_TEST_CONSTANT"/></bar>
    </all>
</config>
EOT;

        $config = $this->reader->readString($string)->all;

        $this->assertEquals('foo-test-bar-test', $config->foo);
        $this->assertEquals('ZEND_CONFIG_XML_TEST_CONSTANT', $config->bar->const->name);
    }

    public function testNonExistentConstant()
    {
        $string = <<<EOT
<?xml version="1.0"?>
<config xmlns:zf="http://framework.zend.com/xml/zend-config-xml/1.0/">
    <all>
        <foo>foo-<zf:const zf:name="ZEND_CONFIG_XML_TEST_NON_EXISTENT_CONSTANT"/></foo>
    </all>
</config>
EOT;

        $this->setExpectedException('Zend\Config\Exception\RuntimeException', 'Constant with name "ZEND_CONFIG_XML_TEST_NON_EXISTENT_CONSTANT" was not defined');
        $config = $this->reader->readString($string);
    }
}
