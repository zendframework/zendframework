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

/**
 * Test helper
 */

/**
 * Zend_Config_Ini
 */

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Config
 */
class Zend_Config_IniTest extends PHPUnit_Framework_TestCase
{
    protected $_iniFileConfig;
    protected $_iniFileAllSectionsConfig;
    protected $_iniFileCircularConfig;

    public function setUp()
    {
        $this->_iniFileConfig = dirname(__FILE__) . '/_files/config.ini';
        $this->_iniFileAllSectionsConfig = dirname(__FILE__) . '/_files/allsections.ini';
        $this->_iniFileCircularConfig = dirname(__FILE__) . '/_files/circular.ini';
        $this->_iniFileMultipleInheritanceConfig = dirname(__FILE__) . '/_files/multipleinheritance.ini';
        $this->_iniFileSeparatorConfig = dirname(__FILE__) . '/_files/separator.ini';
        $this->_nonReadableConfig = dirname(__FILE__) . '/_files/nonreadable.ini';
        $this->_iniFileNoSectionsConfig = dirname(__FILE__) . '/_files/nosections.ini';
        $this->_iniFileInvalid = dirname(__FILE__) . '/_files/invalid.ini';
    }

    public function testLoadSingleSection()
    {
        $config = new Zend_Config_Ini($this->_iniFileConfig, 'all');

        $this->assertEquals('all', $config->hostname);
        $this->assertEquals('live', $config->db->name);
        $this->assertEquals('multi', $config->one->two->three);
        $this->assertNull(@$config->nonexistent); // property doesn't exist
    }

    public function testSectionInclude()
    {
        $config = new Zend_Config_Ini($this->_iniFileConfig, 'staging');

        $this->assertEquals('', $config->debug); // only in staging
        $this->assertEquals('thisname', $config->name); // only in all
        $this->assertEquals('username', $config->db->user); // only in all (nested version)
        $this->assertEquals('staging', $config->hostname); // inherited and overridden
        $this->assertEquals('dbstaging', $config->db->name); // inherited and overridden
    }

    public function testTrueValues()
    {
        $config = new Zend_Config_Ini($this->_iniFileConfig, 'debug');

        $this->assertType('string', $config->debug);
        $this->assertEquals('1', $config->debug);
        $this->assertType('string', $config->values->changed);
        $this->assertEquals('1', $config->values->changed);
    }

    public function testEmptyValues()
    {
        $config = new Zend_Config_Ini($this->_iniFileConfig, 'debug');

        $this->assertType('string', $config->special->no);
        $this->assertEquals('', $config->special->no);
        $this->assertType('string', $config->special->null);
        $this->assertEquals('', $config->special->null);
        $this->assertType('string', $config->special->false);
        $this->assertEquals('', $config->special->false);
    }

    public function testMultiDepthExtends()
    {
        $config = new Zend_Config_Ini($this->_iniFileConfig, 'other_staging');

        $this->assertEquals('otherStaging', $config->only_in); // only in other_staging
        $this->assertEquals('', $config->debug); // 1 level down: only in staging
        $this->assertEquals('thisname', $config->name); // 2 levels down: only in all
        $this->assertEquals('username', $config->db->user); // 2 levels down: only in all (nested version)
        $this->assertEquals('staging', $config->hostname); // inherited from two to one and overridden
        $this->assertEquals('dbstaging', $config->db->name); // inherited from two to one and overridden
        $this->assertEquals('anotherpwd', $config->db->pass); // inherited from two to other_staging and overridden
    }

    public function testErrorNoExtendsSection()
    {
        try {
            $config = new Zend_Config_Ini($this->_iniFileConfig, 'extendserror');
            $this->fail('An expected Zend_Config_Exception has not been raised');
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('cannot be found', $expected->getMessage());
        }
    }

    public function testInvalidKeys()
    {
        $sections = array('leadingdot', 'onedot', 'twodots', 'threedots', 'trailingdot');
        foreach ($sections as $section) {
            try {
                $config = new Zend_Config_Ini($this->_iniFileConfig, $section);
                $this->fail('An expected Zend_Config_Exception has not been raised');
            } catch (Zend_Config_Exception $expected) {
                $this->assertContains('Invalid key', $expected->getMessage());
            }
        }
    }

    public function testZF426()
    {
        try {
            $config = new Zend_Config_Ini($this->_iniFileConfig, 'zf426');
            $this->fail('An expected Zend_Config_Exception has not been raised');
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('Cannot create sub-key for', $expected->getMessage());
        }
    }

    public function testZF413_MultiSections()
    {
        $config = new Zend_Config_Ini($this->_iniFileAllSectionsConfig, array('staging','other_staging'));

        $this->assertEquals('otherStaging', $config->only_in);
        $this->assertEquals('staging', $config->hostname);

    }

    public function testZF413_AllSections()
    {
        $config = new Zend_Config_Ini($this->_iniFileAllSectionsConfig, null);
        $this->assertEquals('otherStaging', $config->other_staging->only_in);
        $this->assertEquals('staging', $config->staging->hostname);
    }

    public function testZF414()
    {
        $config = new Zend_Config_Ini($this->_iniFileAllSectionsConfig, null);
        $this->assertEquals(null, $config->getSectionName());
        $this->assertEquals(true, $config->areAllSectionsLoaded());

        $config = new Zend_Config_Ini($this->_iniFileAllSectionsConfig, 'all');
        $this->assertEquals('all', $config->getSectionName());
        $this->assertEquals(false, $config->areAllSectionsLoaded());

        $config = new Zend_Config_Ini($this->_iniFileAllSectionsConfig, array('staging','other_staging'));
        $this->assertEquals(array('staging','other_staging'), $config->getSectionName());
        $this->assertEquals(false, $config->areAllSectionsLoaded());
    }

    public function testZF415()
    {
        try {
            $config = new Zend_Config_Ini($this->_iniFileCircularConfig, null);
            $this->fail('An expected Zend_Config_Exception has not been raised');
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('circular inheritance', $expected->getMessage());
        }
    }

    public function testErrorNoFile()
    {
        try {
            $config = new Zend_Config_Ini('','');
            $this->fail('An expected Zend_Config_Exception has not been raised');
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('Filename is not set', $expected->getMessage());
        }
    }

    public function testErrorMultipleExensions()
    {
        try {
            $config = new Zend_Config_Ini($this->_iniFileMultipleInheritanceConfig, 'three');
            zend::dump($config);
            $this->fail('An expected Zend_Config_Exception has not been raised');
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('may not extend multiple sections', $expected->getMessage());
        }
    }

    public function testErrorNoSectionFound()
    {
        try {
            $config = new Zend_Config_Ini($this->_iniFileConfig,array('all', 'notthere'));
            $this->fail('An expected Zend_Config_Exception has not been raised');
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('cannot be found', $expected->getMessage());
        }

        try {
            $config = new Zend_Config_Ini($this->_iniFileConfig,'notthere');
            $this->fail('An expected Zend_Config_Exception has not been raised');
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('cannot be found', $expected->getMessage());
        }

    }

    public function testZF739()
    {
        if (version_compare('5.3.0', PHP_VERSION) < 1) {
            $this->markTestSkipped('PHP >= 5.3.0 does not allow : as a nest separator');
            return;
        }

        $config = new Zend_Config_Ini($this->_iniFileSeparatorConfig, 'all', array('nestSeparator'=>':'));

        $this->assertEquals('all', $config->hostname);
        $this->assertEquals('live', $config->db->name);
        $this->assertEquals('multi', $config->one->two->three);
    }

    public function testZF2508NoSections()
    {
        $config = new Zend_Config_Ini($this->_iniFileNoSectionsConfig);

        $this->assertEquals('all', $config->hostname);
        $this->assertEquals('two', $config->one->two);
        $this->assertEquals('4', $config->one->three->four);
        $this->assertEquals('5', $config->one->three->five);
    }

    public function testZF2843NoSectionNoTree()
    {
        $filename = dirname(__FILE__) . '/_files/zf2843.ini';
        $config = new Zend_Config_Ini($filename, null, array('nestSeparator' => '.'));

        $this->assertEquals('123', $config->abc);
        $this->assertEquals('jkl', $config->ghi);
    }

    public function testZF3196_InvalidIniFile()
    {
        try {
            $config = new Zend_Config_Ini($this->_iniFileInvalid);
            $this->fail('An expected Zend_Config_Exception has not been raised');
        } catch (Zend_Config_Exception $expected) {
            $this->assertRegexp('/(Error parsing|syntax error, unexpected)/', $expected->getMessage());
        }

    }
    
    /**
     * @group ZF-8159
     */
    public function testZF8159()
    {
        $config = new Zend_Config_Ini(
            dirname(__FILE__) . '/_files/zf8159.ini',
            array('first', 'second')
        );
        
        $this->assertTrue(isset(
           $config->user->login->elements->password
        ));
        
        $this->assertEquals(
            'password',
            $config->user->login->elements->password->type
        );
    }

    /*
     * @group ZF-5800
     *
     */
    public function testArraysOfKeysCreatedUsingAttributesAndKeys()
    {
        $filename = dirname(__FILE__) . '/_files/zf5800.ini';
        $config = new Zend_Config_Ini($filename, 'dev');
        $this->assertEquals('nice.guy@company.com', $config->receiver->{0}->mail);
        $this->assertEquals('1', $config->receiver->{0}->html);
        $this->assertNull($config->receiver->mail);
    }
    
    /*
     * @group ZF-6508
     */
    public function testPreservationOfIntegerKeys()
    {
        $filename = dirname(__FILE__) . '/_files/zf6508.ini';
        $config = new Zend_Config_Ini($filename, 'all');
        $this->assertEquals(true, isset($config->{1002}));
        
    }
    

}
