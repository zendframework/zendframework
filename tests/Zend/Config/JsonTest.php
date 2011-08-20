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

namespace ZendTest\Config;

use Zend\Config\Json as JsonConfig;

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class JsonTest extends \PHPUnit_Framework_TestCase
{
    protected $_iniFileConfig;
    protected $_iniFileAllSectionsConfig;
    protected $_iniFileCircularConfig;

    public function setUp()
    {
        $this->_iniFileConfig = __DIR__ . '/_files/config.json';
        $this->_iniFileAllSectionsConfig = __DIR__ . '/_files/allsections.json';
        $this->_iniFileCircularConfig = __DIR__ . '/_files/circular.json';
        $this->_iniFileMultipleInheritanceConfig = __DIR__ . '/_files/multipleinheritance.json';
        $this->_nonReadableConfig = __DIR__ . '/_files/nonreadable.json';
        $this->_iniFileNoSectionsConfig = __DIR__ . '/_files/nosections.json';
        $this->_iniFileInvalid = __DIR__ . '/_files/invalid.json';
    }

    public function testLoadSingleSection()
    {
        $config = new JsonConfig($this->_iniFileConfig, 'all');

        $this->assertEquals('all', $config->hostname);
        $this->assertEquals('live', $config->db->name);
        $this->assertEquals('multi', $config->one->two->three);
        $this->assertNull(@$config->nonexistent); // property doesn't exist
    }

    public function testSectionInclude()
    {
        $config = new JsonConfig($this->_iniFileConfig, 'staging');

        $this->assertEquals('', $config->debug); // only in staging
        $this->assertEquals('thisname', $config->name); // only in all
        $this->assertEquals('username', $config->db->user); // only in all (nested version)
        $this->assertEquals('dbstaging', $config->db->name); // inherited and overridden
    }

    public function testTrueValues()
    {
        $config = new JsonConfig($this->_iniFileConfig, 'debug');

        $this->assertTrue($config->debug);
        $this->assertTrue($config->values->changed);
    }

    public function testEmptyValues()
    {
        $config = new JsonConfig($this->_iniFileConfig, 'debug');

        $this->assertInternalType('string', $config->special->no);
        $this->assertEquals('no', $config->special->no);
        $this->assertNull($config->special->null);
        $this->assertFalse($config->special->false);
    }

    /**
     * @group review
     */
    public function testMultiDepthExtends()
    {
        $config = new JsonConfig($this->_iniFileConfig, 'other_staging');

        $this->assertEquals('otherStaging', $config->only_in); // only in other_staging
        $this->assertEquals('', $config->debug); // 1 level down: only in staging
        $this->assertEquals('thisname', $config->name); // 2 levels down: only in all
        $this->assertEquals('username', $config->db->user); // 2 levels down: only in all (nested version)
        $this->assertEquals('staging', $config->hostname); // inherited from two to one and overridden
        $this->assertEquals('dbstaging', $config->db->name); // inherited from two to one and overridden
        $this->assertEquals('anotherpwd', $config->db->pass); // inherited from two to other_staging and overridden
    }

    public function testRaisesExceptionWhenSectionNotFound()
    {
        $this->setExpectedException('Zend\Config\Exception\RuntimeException', 'cannot be found');
        $config = new JsonConfig($this->_iniFileConfig, 'extendserror');
    }

    public function testRetrievesAndMergesMultipleSections()
    {
        $config = new JsonConfig($this->_iniFileAllSectionsConfig, array('staging','other_staging'));

        $this->assertEquals('otherStaging', $config->only_in);
        $this->assertEquals('dbstaging', $config->db->name);

    }

    public function testCanRetrieveAllSections()
    {
        $config = new JsonConfig($this->_iniFileAllSectionsConfig, null);
        $this->assertEquals('otherStaging', $config->other_staging->only_in);
        $this->assertEquals('dbstaging', $config->staging->db->name);
    }

    public function testAllowsLoadingAllSectionsOrSomeSectionsSelectively()
    {
        $config = new JsonConfig($this->_iniFileAllSectionsConfig, null);
        $this->assertEquals(null, $config->getSectionName());
        $this->assertEquals(true, $config->areAllSectionsLoaded());

        $config = new JsonConfig($this->_iniFileAllSectionsConfig, 'all');
        $this->assertEquals('all', $config->getSectionName());
        $this->assertEquals(false, $config->areAllSectionsLoaded());

        $config = new JsonConfig($this->_iniFileAllSectionsConfig, array('staging','other_staging'));
        $this->assertEquals(array('staging','other_staging'), $config->getSectionName());
        $this->assertEquals(false, $config->areAllSectionsLoaded());
    }

    public function testDetectsCircularInheritance()
    {
        $this->setExpectedException('Zend\Config\Exception\RuntimeException', 'circular inheritance');
        $config = new JsonConfig($this->_iniFileCircularConfig, null);
    }

    public function testRaisesErrorWhenNoFileProvided()
    {
        $this->setExpectedException('Zend\Config\Exception\InvalidArgumentException', 'not set');
        $config = new JsonConfig('','');
    }

    public function testRaisesErrorOnAttemptsToExtendMultipleSectionsAtOnce()
    {
        $this->setExpectedException('Zend\Config\Exception\RuntimeException', 'Invalid');
        $config = new JsonConfig($this->_iniFileMultipleInheritanceConfig, 'multiinherit');
    }

    public function testRaisesErrorWhenSectionNotFound()
    {
        try {
            $config = new JsonConfig($this->_iniFileConfig,array('all', 'notthere'));
            $this->fail('An expected exception has not been raised');
        } catch (\Zend\Config\Exception $expected) {
            $this->assertContains('cannot be found', $expected->getMessage());
        }

        try {
            $config = new JsonConfig($this->_iniFileConfig,'notthere');
            $this->fail('An expected exception has not been raised');
        } catch (\Zend\Config\Exception $expected) {
            $this->assertContains('cannot be found', $expected->getMessage());
        }
    }

    public function testCanLoadConfigWithNoSections()
    {
        $config = new JsonConfig($this->_iniFileNoSectionsConfig);

        $this->assertEquals('all', $config->hostname);
        $this->assertEquals('two', $config->one->two);
        $this->assertEquals('4', $config->one->three->four);
        $this->assertEquals('5', $config->one->three->five);
    }

    public function testRaisesExceptionOnInvalidJsonMarkup()
    {
        $this->setExpectedException('Zend\Json\Exception', 'Syntax error');
        $config = new JsonConfig($this->_iniFileInvalid);
    }

    public function testOptionsPassedAreHonored()
    {
        $config = new JsonConfig($this->_iniFileConfig, 'staging', array(
            'skipExtends'        => true,
            'allowModifications' => true,
            'bar'                => 'foo', // ignored
        ));
        $this->assertNull($config->name); // demonstrates extends were skipped
        $config->foo = 'bar';
        $this->assertEquals('bar', $config->foo); // demonstrates modifications were made
    }

    public function testZf2StyleOptionsAreHonored()
    {
        $config = new JsonConfig($this->_iniFileConfig, 'staging', array(
            'skip_extends'        => true,
            'allow_modifications' => true,
            'bar'                 => 'foo', // ignored
        ));
        $this->assertNull($config->name); // demonstrates extends were skipped
        $config->foo = 'bar';
        $this->assertEquals('bar', $config->foo); // demonstrates modifications were made
    }

    public function testAllowsPassingJsonStringsToConstructor()
    {
        $json =<<<EOJ
{"all":{"foo":"bar"},"staging":{"_extends":"all","bar":"baz"},"debug":{"_extends":"all","debug":true}}
EOJ;
        $config = new JsonConfig($json, 'debug');
        $this->assertTrue($config->debug);
        $this->assertEquals('bar', $config->foo);
        $this->assertNull($config->bar);
    }

    public function testProcessesSectionsWithSingleValues()
    {
        $json = '{"all":"values"}';
        $config = new JsonConfig($json, 'all');
        $this->assertEquals('values', $config->all);
    }

    public function testReplacesConstantNamesWithValuesByDefault()
    {
        if (!defined('ZEND_CONFIG_JSON_ENV')) {
            define('ZEND_CONFIG_JSON_ENV', 'testing');
        }
        if (!defined('ZEND_CONFIG_JSON_ENV_PATH')) {
            define('ZEND_CONFIG_JSON_ENV_PATH', dirname(__FILE__));
        }
        if (!defined('ZEND_CONFIG_JSON_ENV_INT')) {
            define('ZEND_CONFIG_JSON_ENV_INT', 42);
        }
        $json = '{"env":"ZEND_CONFIG_JSON_ENV","path":"ZEND_CONFIG_JSON_ENV_PATH/tests","int":ZEND_CONFIG_JSON_ENV_INT}';
        $config = new JsonConfig($json);
        $this->assertEquals(ZEND_CONFIG_JSON_ENV, $config->env);
        $this->assertEquals(ZEND_CONFIG_JSON_ENV_PATH . '/tests', $config->path);
        $this->assertEquals(ZEND_CONFIG_JSON_ENV_INT, $config->int);
    }

    public function testCanIgnoreConstantsWhenParsing()
    {
        if (!defined('ZEND_CONFIG_JSON_ENV')) {
            define('ZEND_CONFIG_JSON_ENV', 'testing');
        }
        $json = '{"env":"ZEND_CONFIG_JSON_ENV"}';
        $config = new JsonConfig($json, null, array('ignore_constants' => true));
        $this->assertEquals('ZEND_CONFIG_JSON_ENV', $config->env);
    }

    public function testIgnoringConstantsCanLeadToParseErrors()
    {
        if (!defined('ZEND_CONFIG_JSON_ENV')) {
            define('ZEND_CONFIG_JSON_ENV', 'testing');
        }
        if (!defined('ZEND_CONFIG_JSON_ENV_PATH')) {
            define('ZEND_CONFIG_JSON_ENV_PATH', dirname(__FILE__));
        }
        if (!defined('ZEND_CONFIG_JSON_ENV_INT')) {
            define('ZEND_CONFIG_JSON_ENV_INT', 42);
        }
        $json = '{"env":"ZEND_CONFIG_JSON_ENV","path":"ZEND_CONFIG_JSON_ENV_PATH/tests","int":ZEND_CONFIG_JSON_ENV_INT}';

        $this->setExpectedException('Zend\Json\Exception', 'Syntax');
        $config = new JsonConfig($json, null, array('ignore_constants' => true));
    }

    public function testNestedConfigSetsAreArrays()
    {
        $config = new JsonConfig(__DIR__ . '/_files/nested.json', 'testing');
        $array  = $config->toArray();
        $this->assertInternalType('array', $array['definitions'][0]);
    }
}
