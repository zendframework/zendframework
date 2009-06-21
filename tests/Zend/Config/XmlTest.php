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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * Zend_Config_Xml
 */
require_once 'Zend/Config/Xml.php';

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Config_XmlTest extends PHPUnit_Framework_TestCase
{
    protected $_xmlFileConfig;
    protected $_xmlFileAllSectionsConfig;
    protected $_xmlFileCircularConfig;
    protected $_xmlFileInvalid;

    public function setUp()
    {
        $this->_xmlFileConfig = dirname(__FILE__) . '/_files/config.xml';
        $this->_xmlFileAllSectionsConfig = dirname(__FILE__) . '/_files/allsections.xml';
        $this->_xmlFileCircularConfig = dirname(__FILE__) . '/_files/circular.xml';
        $this->_xmlFileTopLevelStringConfig = dirname(__FILE__) . '/_files/toplevelstring.xml';
        $this->_xmlFileOneTopLevelStringConfig = dirname(__FILE__) . '/_files/onetoplevelstring.xml';
        $this->_nonReadableConfig = dirname(__FILE__) . '/_files/nonreadable.xml';
        $this->_xmlFileSameNameKeysConfig = dirname(__FILE__) . '/_files/array.xml';
        $this->_xmlFileShortParamsOneConfig = dirname(__FILE__) . '/_files/shortparamsone.xml';
        $this->_xmlFileShortParamsTwoConfig = dirname(__FILE__) . '/_files/shortparamstwo.xml';
        $this->_xmlFileInvalid = dirname(__FILE__) . '/_files/invalid.xml';
    }

    public function testLoadSingleSection()
    {
        $config = new Zend_Config_Xml($this->_xmlFileConfig, 'all');
        $this->assertEquals('all', $config->hostname);
        $this->assertEquals('live', $config->db->name);
        $this->assertEquals('multi', $config->one->two->three);
        $this->assertNull(@$config->nonexistent); // property doesn't exist
    }

    public function testSectionInclude()
    {
        $config = new Zend_Config_Xml($this->_xmlFileConfig, 'staging');
        $this->assertEquals('false', $config->debug); // only in staging
        $this->assertEquals('thisname', $config->name); // only in all
        $this->assertEquals('username', $config->db->user); // only in all (nested version)
        $this->assertEquals('staging', $config->hostname); // inherited and overridden
        $this->assertEquals('dbstaging', $config->db->name); // inherited and overridden
    }

    public function testMultiDepthExtends()
    {
        $config = new Zend_Config_Xml($this->_xmlFileConfig, 'other_staging');

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
        try {
            $config = @new Zend_Config_Xml($this->_xmlFileConfig, 'notthere');
            $this->fail('An expected Zend_Config_Exception has not been raised');
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('cannot be found in', $expected->getMessage());
        }

        try {
            $config = @new Zend_Config_Xml($this->_xmlFileConfig, array('notthere', 'all'));
            $this->fail('An expected Zend_Config_Exception has not been raised');
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('cannot be found in', $expected->getMessage());
        }
    }

    public function testErrorNoExtendsSection()
    {
        try {
            $config = new Zend_Config_Xml($this->_xmlFileConfig, 'extendserror');
            $this->fail('An expected Zend_Config_Exception has not been raised');
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('cannot be found', $expected->getMessage());
        }
    }

    public function testZF413_MultiSections()
    {
        $config = new Zend_Config_Xml($this->_xmlFileAllSectionsConfig, array('staging','other_staging'));

        $this->assertEquals('otherStaging', $config->only_in);
        $this->assertEquals('staging', $config->hostname);
    }

    public function testZF413_AllSections()
    {
        $config = new Zend_Config_Xml($this->_xmlFileAllSectionsConfig, null);
        $this->assertEquals('otherStaging', $config->other_staging->only_in);
        $this->assertEquals('staging', $config->staging->hostname);
    }

    public function testZF414()
    {
        $config = new Zend_Config_Xml($this->_xmlFileAllSectionsConfig, null);
        $this->assertEquals(null, $config->getSectionName());
        $this->assertEquals(true, $config->areAllSectionsLoaded());

        $config = new Zend_Config_Xml($this->_xmlFileAllSectionsConfig, 'all');
        $this->assertEquals('all', $config->getSectionName());
        $this->assertEquals(false, $config->areAllSectionsLoaded());

        $config = new Zend_Config_Xml($this->_xmlFileAllSectionsConfig, array('staging','other_staging'));
        $this->assertEquals(array('staging','other_staging'), $config->getSectionName());
        $this->assertEquals(false, $config->areAllSectionsLoaded());
    }

    public function testZF415()
    {
        try {
            $config = new Zend_Config_Xml($this->_xmlFileCircularConfig, null);
            $this->fail('An expected Zend_Config_Exception has not been raised');
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('circular inheritance', $expected->getMessage());
        }
    }

    public function testErrorNoFile()
    {
        try {
            $config = new Zend_Config_Xml('',null);
            $this->fail('An expected Zend_Config_Exception has not been raised');
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('Filename is not set', $expected->getMessage());
        }
    }

    public function testZF2162_TopLevelString()
    {
        $config = new Zend_Config_Xml($this->_xmlFileTopLevelStringConfig, null);
        $this->assertEquals('one', $config->one);
        $this->assertEquals('three', $config->two->three);
        $this->assertEquals('five', $config->two->four->five);
        $this->assertEquals('three', $config->six->three);

        $config = new Zend_Config_Xml($this->_xmlFileOneTopLevelStringConfig);
        $this->assertEquals('one', $config->one);
        $config = new Zend_Config_Xml($this->_xmlFileOneTopLevelStringConfig, 'one');
        $this->assertEquals('one', $config->one);

    }

    public function testZF2285_MultipleKeysOfTheSameName()
    {
        $config = new Zend_Config_Xml($this->_xmlFileSameNameKeysConfig, null);
        $this->assertEquals('2a', $config->one->two->{0});
        $this->assertEquals('2b', $config->one->two->{1});
        $this->assertEquals('4', $config->three->four->{1});
        $this->assertEquals('5', $config->three->four->{0}->five);
    }

    public function testZF2437_ArraysWithMultipleChildren()
    {
        $config = new Zend_Config_Xml($this->_xmlFileSameNameKeysConfig, null);
        $this->assertEquals('1', $config->six->seven->{0}->eight);
        $this->assertEquals('2', $config->six->seven->{1}->eight);
        $this->assertEquals('3', $config->six->seven->{2}->eight);
        $this->assertEquals('1', $config->six->seven->{0}->nine);
        $this->assertEquals('2', $config->six->seven->{1}->nine);
        $this->assertEquals('3', $config->six->seven->{2}->nine);
    }

    public function testZF3578_InvalidOrMissingfXmlFile()
    {
        try {
            $config = new Zend_Config_Xml($this->_xmlFileInvalid);
            $this->fail('An expected Zend_Config_Exception has not been raised');
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('parser error', $expected->getMessage());
        }
        try {
            $config = new Zend_Config_Xml('I/dont/exist');
            $this->fail('An expected Zend_Config_Exception has not been raised');
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('failed to load', $expected->getMessage());
        }
    }

    public function testShortParamsOne()
    {
        $config = new Zend_Config_Xml($this->_xmlFileShortParamsOneConfig, 'all');
        $this->assertEquals('all', $config->hostname);
        $this->assertEquals('thisname', $config->name);
        $this->assertEquals('username', $config->db->user);
        $this->assertEquals('live', $config->db->name);
        $this->assertEquals('multi', $config->one->two->three);
    }

    public function testShortParamsTwo()
    {
        $config = new Zend_Config_Xml($this->_xmlFileShortParamsTwoConfig, 'all');
        $this->assertEquals('all', $config->hostname);
        $this->assertEquals('thisname', $config->name);
        $this->assertEquals('username', $config->db->user);
        $this->assertEquals('live', $config->db->name);
        $this->assertEquals('multi', $config->one->two->three);
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

        $config = new Zend_Config_Xml($string, 'staging');
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

        $config = new Zend_Config_Xml($string, 'dev');
        $this->assertEquals('nice.guy@company.com', $config->receiver->{0}->mail);
        $this->assertEquals('1', $config->receiver->{0}->html);
        $this->assertNull($config->receiver->mail);
    }
}
