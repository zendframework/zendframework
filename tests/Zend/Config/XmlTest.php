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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace ZendTest\Config;

use \Zend\Config\Xml;

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Config
 */
class XmlTest extends \PHPUnit_Framework_TestCase
{
    protected $_xmlFileConfig;
    protected $_xmlFileAllSectionsConfig;
    protected $_xmlFileCircularConfig;
    protected $_xmlFileInvalid;

    public function setUp()
    {
        $this->_xmlFileConfig = __DIR__ . '/_files/config.xml';
        $this->_xmlFileAllSectionsConfig = __DIR__ . '/_files/allsections.xml';
        $this->_xmlFileCircularConfig = __DIR__ . '/_files/circular.xml';
        $this->_xmlFileTopLevelStringConfig = __DIR__ . '/_files/toplevelstring.xml';
        $this->_xmlFileOneTopLevelStringConfig = __DIR__ . '/_files/onetoplevelstring.xml';
        $this->_nonReadableConfig = __DIR__ . '/_files/nonreadable.xml';
        $this->_xmlFileSameNameKeysConfig = __DIR__ . '/_files/array.xml';
        $this->_xmlFileShortParamsOneConfig = __DIR__ . '/_files/shortparamsone.xml';
        $this->_xmlFileShortParamsTwoConfig = __DIR__ . '/_files/shortparamstwo.xml';
        $this->_xmlFileInvalid = __DIR__ . '/_files/invalid.xml';
    }

    public function testLoadSingleSection()
    {
        $config = new Xml($this->_xmlFileConfig, 'all');
        $this->assertEquals('all', $config->hostname);
        $this->assertEquals('live', $config->db->name);
        $this->assertEquals('multi', $config->one->two->three);
        $this->assertNull(@$config->nonexistent); // property doesn't exist
    }

    public function testSectionInclude()
    {
        $config = new Xml($this->_xmlFileConfig, 'staging');
        $this->assertEquals('false', $config->debug); // only in staging
        $this->assertEquals('thisname', $config->name); // only in all
        $this->assertEquals('username', $config->db->user); // only in all (nested version)
        $this->assertEquals('staging', $config->hostname); // inherited and overridden
        $this->assertEquals('dbstaging', $config->db->name); // inherited and overridden
    }

    public function testMultiDepthExtends()
    {
        $config = new Xml($this->_xmlFileConfig, 'other_staging');

        $this->assertEquals('otherStaging', $config->only_in); // only in other_staging
        $this->assertEquals('false', $config->debug); // 1 level down: only in staging
        $this->assertEquals('thisname', $config->name); // 2 levels down: only in all
        $this->assertEquals('username', $config->db->user); // 2 levels down: only in all (nested version)
        $this->assertEquals('staging', $config->hostname); // inherited from two to one and overridden
        $this->assertEquals('dbstaging', $config->db->name); // inherited from two to one and overridden
        $this->assertEquals('anotherpwd', $config->db->pass); // inherited from two to other_staging and overridden
    }

    public function testErrorNoInitialSection()
    {
        $this->setExpectedException('\\Zend\\Config\\Exception', 'cannot be found in');
        $config = @new Xml($this->_xmlFileConfig, 'notthere');
    }

    public function testErrorNoInitialSectionWhenArrayOfSectionsSpecified()
    {
        $this->setExpectedException('\\Zend\\Config\\Exception', 'cannot be found in');
        $config = @new Xml($this->_xmlFileConfig, array('notthere', 'all'));
    }

    public function testErrorNoExtendsSection()
    {
        $this->setExpectedException('\\Zend\\Config\\Exception', 'cannot be found');
        $config = new Xml($this->_xmlFileConfig, 'extendserror');
    }

    public function testZF413_MultiSections()
    {
        $config = new Xml($this->_xmlFileAllSectionsConfig, array('staging','other_staging'));

        $this->assertEquals('otherStaging', $config->only_in);
        $this->assertEquals('staging', $config->hostname);
    }

    public function testZF413_AllSections()
    {
        $config = new Xml($this->_xmlFileAllSectionsConfig, null);
        $this->assertEquals('otherStaging', $config->other_staging->only_in);
        $this->assertEquals('staging', $config->staging->hostname);
    }

    public function testZF414()
    {
        $config = new Xml($this->_xmlFileAllSectionsConfig, null);
        $this->assertEquals(null, $config->getSectionName());
        $this->assertEquals(true, $config->areAllSectionsLoaded());

        $config = new Xml($this->_xmlFileAllSectionsConfig, 'all');
        $this->assertEquals('all', $config->getSectionName());
        $this->assertEquals(false, $config->areAllSectionsLoaded());

        $config = new Xml($this->_xmlFileAllSectionsConfig, array('staging','other_staging'));
        $this->assertEquals(array('staging','other_staging'), $config->getSectionName());
        $this->assertEquals(false, $config->areAllSectionsLoaded());
    }

    public function testZF415()
    {
        $this->setExpectedException('\\Zend\\Config\\Exception', 'circular inheritance');
        $config = new Xml($this->_xmlFileCircularConfig, null);
    }

    public function testErrorNoFile()
    {
        $this->setExpectedException('\\Zend\\Config\\Exception', 'Filename is not set');
        $config = new Xml('',null);
    }

    public function testZF2162_TopLevelString()
    {
        $config = new Xml($this->_xmlFileTopLevelStringConfig, null);
        $this->assertEquals('one', $config->one);
        $this->assertEquals('three', $config->two->three);
        $this->assertEquals('five', $config->two->four->five);
        $this->assertEquals('three', $config->six->three);

        $config = new Xml($this->_xmlFileOneTopLevelStringConfig);
        $this->assertEquals('one', $config->one);
        $config = new Xml($this->_xmlFileOneTopLevelStringConfig, 'one');
        $this->assertEquals('one', $config->one);

    }

    public function testZF2285_MultipleKeysOfTheSameName()
    {
        $config = new Xml($this->_xmlFileSameNameKeysConfig, null);
        $this->assertEquals('2a', $config->one->two->{0});
        $this->assertEquals('2b', $config->one->two->{1});
        $this->assertEquals('4', $config->three->four->{1});
        $this->assertEquals('5', $config->three->four->{0}->five);
    }

    public function testZF2437_ArraysWithMultipleChildren()
    {
        $config = new Xml($this->_xmlFileSameNameKeysConfig, null);
        $this->assertEquals('1', $config->six->seven->{0}->eight);
        $this->assertEquals('2', $config->six->seven->{1}->eight);
        $this->assertEquals('3', $config->six->seven->{2}->eight);
        $this->assertEquals('1', $config->six->seven->{0}->nine);
        $this->assertEquals('2', $config->six->seven->{1}->nine);
        $this->assertEquals('3', $config->six->seven->{2}->nine);
    }

    /**
     * @group ZF-3578
     */
    public function testInvalidXmlFile()
    {
        $this->setExpectedException('\\Zend\\Config\\Exception', 'parser error');
        $config = new Xml($this->_xmlFileInvalid);
    }

    /**
     * @group ZF-3578
     */
    public function testMissingXmlFile()
    {
        $this->setExpectedException('\\Zend\\Config\\Exception', 'failed to load');
        $config = new Xml('I/dont/exist');
    }

    public function testShortParamsOne()
    {
        $config = new Xml($this->_xmlFileShortParamsOneConfig, 'all');
        $this->assertEquals('all', $config->hostname);
        $this->assertEquals('thisname', $config->name);
        $this->assertEquals('username', $config->db->user);
        $this->assertEquals('live', $config->db->name);
        $this->assertEquals('multi', $config->one->two->three);
    }

    public function testShortParamsTwo()
    {
        $config = new Xml($this->_xmlFileShortParamsTwoConfig, 'all');
        $this->assertEquals('all', $config->hostname);
        $this->assertEquals('thisname', $config->name);
        $this->assertEquals('username', $config->db->user);
        $this->assertEquals('live', $config->db->name);
        $this->assertEquals('multi', $config->one->two->three);
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

        $config = new Xml($string, 'all');

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

        $this->setExpectedException('\\Zend\\Config\\Exception', "Constant with name 'ZEND_CONFIG_XML_TEST_NON_EXISTENT_CONSTANT' was not defined");
        $config = new Xml($string, 'all');
    }

    public function testNamespacedExtends()
    {
        $string = <<<EOT
<?xml version="1.0"?>
<config xmlns:zf="http://framework.zend.com/xml/zend-config-xml/1.0/">
    <all>
        <foo>bar</foo>
    </all>
    <staging zf:extends="all"/>
</config>
EOT;

        $config = new Xml($string);

        $this->assertEquals('bar', $config->staging->foo);
    }

    /*
     * @group 3702
     *
     */
    public function testLoadAnXMLString()
    {
        $string = <<<EOT
<?xml version="1.0"?>
<config>
    <all>
        <hostname>all</hostname>
        <db>
            <host>127.0.0.1</host>
            <name>live</name>
        </db>
    </all>

    <staging extends="all">
        <hostname>staging</hostname>
        <db>
            <name>dbstaging</name>
        </db>
        <debug>false</debug>
    </staging>


</config>
EOT;

        $config = new Xml($string, 'staging');
        $this->assertEquals('staging', $config->hostname);

    }

    /*
     * @group ZF-5800
     *
     */
    public function testArraysOfKeysCreatedUsingAttributesAndKeys()
    {
        $string = <<<EOT
<?xml version="1.0"?>
<config>
<rec>
        <receiver>
                <mail>user1@company.com</mail>
                <name>User Name</name>
                <html>1</html>
        </receiver>
</rec>
<dev extends="rec">
        <receiver mail="nice.guy@company.com" name="Nice Guy" />
        <receiver mail="fred@company.com" html="2" />
</dev>
</config>
EOT;

        $config = new Xml($string, 'dev');
        $this->assertEquals('nice.guy@company.com', $config->receiver->{0}->mail);
        $this->assertEquals('1', $config->receiver->{0}->html);
        $this->assertNull($config->receiver->mail);
    }
}
