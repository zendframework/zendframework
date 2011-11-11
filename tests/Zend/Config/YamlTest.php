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
 * @version    $Id: IniTest.php 18950 2009-11-12 15:37:56Z alexander $
 */

namespace ZendTest\Config;

use Zend\Config\Yaml as YamlConfig,
    Zend\Config\Exception;

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Config
 */
class YamlTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_iniFileConfig             = __DIR__ . '/_files/config.yaml';
        $this->_iniFileAllSectionsConfig  = __DIR__ . '/_files/allsections.yaml';
        $this->_iniFileCircularConfig     = __DIR__ . '/_files/circular.yaml';
        $this->_nonReadableConfig         = __DIR__ . '/_files/nonreadable.yaml';
        $this->_iniFileInvalid            = __DIR__ . '/_files/invalid.yaml';
        $this->_iniFileSameNameKeysConfig = __DIR__ . '/_files/array.yaml';
        $this->_badIndentationConfig      = __DIR__ . '/_files/badindentation.yaml';
        $this->_booleansConfig            = __DIR__ . '/_files/booleans.yaml';
        $this->_constantsConfig           = __DIR__ . '/_files/constants.yaml';
        $this->_yamlInlineCommentsConfig  = dirname(__FILE__) . '/_files/inlinecomments.yaml';
        $this->_yamlIndentedCommentsConfig  = dirname(__FILE__) . '/_files/indentedcomments.yaml';
        $this->_yamlListConstantsConfig     = dirname(__FILE__) . '/_files/listconstants.yaml';
        $this->_listBooleansConfig          = dirname(__FILE__) . '/_files/listbooleans.yaml';
        
    }

    public function testLoadSingleSection()
    {
        $config = new YamlConfig($this->_iniFileConfig, 'all');

        $this->assertEquals('all', $config->hostname);
        $this->assertEquals('live', $config->db->name);
        $this->assertEquals('multi', $config->one->two->three);
        $this->assertNull(@$config->nonexistent); // property doesn't exist
    }

    public function testSectionInclude()
    {
        $config = new YamlConfig($this->_iniFileConfig, 'staging');

        $this->assertEquals('', $config->debug); // only in staging
        $this->assertEquals('thisname', $config->name); // only in all
        $this->assertEquals('username', $config->db->user); // only in all (nested version)
        $this->assertEquals('staging', $config->hostname); // inherited and overridden
        $this->assertEquals('dbstaging', $config->db->name); // inherited and overridden
    }

    public function testTrueValues()
    {
        $config = new YamlConfig($this->_iniFileConfig, 'debug');

        $this->assertInternalType('string', $config->debug);
        $this->assertEquals('1', $config->debug);
        $this->assertInternalType('string', $config->values->changed);
        $this->assertEquals('1', $config->values->changed);
    }

    public function testEmptyValues()
    {
        $config = new YamlConfig($this->_iniFileConfig, 'debug');

        $this->assertInternalType('string', $config->special->no);
        $this->assertEquals('', $config->special->no);
        $this->assertInternalType('string', $config->special->null);
        $this->assertEquals('', $config->special->null);
        $this->assertInternalType('string', $config->special->false);
        $this->assertEquals('', $config->special->false);
        $this->assertInternalType('string', $config->special->zero);
        $this->assertEquals('0', $config->special->zero);
    }

    public function testMultiDepthExtends()
    {
        $config = new YamlConfig($this->_iniFileConfig, 'other_staging');

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
        $this->setExpectedException('Zend\Config\Exception\RuntimeException', 'cannot be found');
        $config = new YamlConfig($this->_iniFileConfig, 'extendserror');
    }

    /**
     * @group ZF-413
     */
    public function testMultiSections()
    {
        $config = new YamlConfig($this->_iniFileAllSectionsConfig, array('staging','other_staging'));

        $this->assertEquals('otherStaging', $config->only_in);
        $this->assertEquals('staging', $config->hostname);

    }

    /**
     * @group ZF-413
     */
    public function testAllSections()
    {
        $config = new YamlConfig($this->_iniFileAllSectionsConfig, null);
        $this->assertEquals('otherStaging', $config->other_staging->only_in);
        $this->assertEquals('staging', $config->staging->hostname);
    }

    /**
     * @group ZF-414
     */
    public function testGetSectionNameAndAreAllSectionsLoaded()
    {
        $config = new YamlConfig($this->_iniFileAllSectionsConfig, null);
        $this->assertEquals(null, $config->getSectionName());
        $this->assertEquals(true, $config->areAllSectionsLoaded());

        $config = new YamlConfig($this->_iniFileAllSectionsConfig, 'all');
        $this->assertEquals('all', $config->getSectionName());
        $this->assertEquals(false, $config->areAllSectionsLoaded());

        $config = new YamlConfig($this->_iniFileAllSectionsConfig, array('staging','other_staging'));
        $this->assertEquals(array('staging','other_staging'), $config->getSectionName());
        $this->assertEquals(false, $config->areAllSectionsLoaded());
    }

    /**
     * @group ZF-415
     */
    public function testErrorCircularInheritance()
    {
        $this->setExpectedException('Zend\Config\Exception\RuntimeException', 'circular inheritance');
        $config = new YamlConfig($this->_iniFileCircularConfig, null);
    }

    public function testErrorNoFile()
    {
        $this->setExpectedException('Zend\Config\Exception\RuntimeException', 'Filename is not set');
        $config = new YamlConfig('','');
    }

    public function testErrorNoSectionFound()
    {
        try {
            $config = new YamlConfig($this->_iniFileConfig,array('all', 'notthere'));
            $this->fail('An expected exception has not been raised');
        } catch (Exception\RuntimeException $expected) {
            $this->assertContains('cannot be found', $expected->getMessage());
        }

        try {
            $config = new YamlConfig($this->_iniFileConfig,'notthere');
            $this->fail('An expected exception has not been raised');
        } catch (Exception\RuntimeException $expected) {
            $this->assertContains('cannot be found', $expected->getMessage());
        }

    }

    /**
     * @group ZF-3196
     */
    public function testInvalidIniFile()
    {
        try {
            $config = new YamlConfig($this->_iniFileInvalid);
            $this->fail('An expected exception has not been raised');
        } catch (Exception\RuntimeException $expected) {
            $this->assertRegexp('/(Error parsing|syntax error, unexpected)/', $expected->getMessage());
        }

    }

    /**
     * @group ZF-2285
     */
    public function testMultipleKeysOfTheSameName()
    {
        $config = new YamlConfig($this->_iniFileSameNameKeysConfig, null);
        $this->assertEquals('2a', $config->one->two->{0});
        $this->assertEquals('2b', $config->one->two->{1});
        $this->assertEquals('4', $config->three->four->{1});
        $this->assertEquals('5', $config->three->four->{0}->five);
    }

    /**
     * @group ZF-2437
     */
    public function testArraysWithMultipleChildren()
    {
        $config = new YamlConfig($this->_iniFileSameNameKeysConfig, null);
        $this->assertEquals('1', $config->six->seven->{0}->eight);
        $this->assertEquals('2', $config->six->seven->{1}->eight);
        $this->assertEquals('3', $config->six->seven->{2}->eight);
        $this->assertEquals('1', $config->six->seven->{0}->nine);
        $this->assertEquals('2', $config->six->seven->{1}->nine);
        $this->assertEquals('3', $config->six->seven->{2}->nine);
    }

    public function yamlDecoder($string)
    {
        return YamlConfig::decode($string);
    }

    public function testHonorsOptionsProvidedToConstructor()
    {
        $config = new YamlConfig($this->_iniFileAllSectionsConfig, 'debug', array(
            'allow_modifications' => true,
            'skip_extends'        => true,
            'yaml_decoder'        => array($this, 'yamlDecoder'),
            'foo'                 => 'bar', // ignored
        ));
        $this->assertNull($config->name); // verifies extends were skipped
        $config->foo = 'bar';
        $this->assertEquals('bar', $config->foo); // verifies allows modifications
        $this->assertEquals(array($this, 'yamlDecoder'), $config->getYamlDecoder());
    }

    public function testFileNotFound()
    {
        try {
            $file = '__foo__';
            $config = new YamlConfig($file);
            $this->fail('Missing expected exception');
        } catch (Exception\RuntimeException $e) {
            // read exception stack
            do {
                $stack[] = $e;
            } while ( ($e = $e->getPrevious()) );

            // test two thrown exceptions
            $this->assertEquals(2, count($stack));
            $this->assertContains($file, $stack[0]->getMessage());
            $this->assertContains('file_get_contents', $stack[1]->getMessage());
        }
    }

    public function testBadIndentationRaisesException()
    {
        $this->setExpectedException('Zend\Config\Exception\RuntimeException', 'unsupported syntax');
        $config = new YamlConfig($this->_badIndentationConfig, 'all');
    }

    public function testPassingBadYamlDecoderRaisesException()
    {
        $this->setExpectedException('Zend\Config\Exception\InvalidArgumentException', 'must be callable');
        $config = new YamlConfig($this->_iniFileAllSectionsConfig, 'debug', array(
            'yaml_decoder' => '__foo__',
        ));
    }

    public function testParsesBooleansAccordingToOneDotOneSpecification()
    {
        $config = new YamlConfig($this->_booleansConfig, 'production');

        $this->assertTrue($config->usingLowerCasedYes);
        $this->assertTrue($config->usingTitleCasedYes);
        $this->assertTrue($config->usingCapitalYes);
        $this->assertTrue($config->usingLowerY);
        $this->assertTrue($config->usingUpperY);

        $this->assertFalse($config->usingLowerCasedNo);
        $this->assertFalse($config->usingTitleCasedNo);
        $this->assertFalse($config->usingCapitalNo);
        $this->assertFalse($config->usingLowerN);
        $this->assertFalse($config->usingUpperN);

        $this->assertTrue($config->usingLowerCasedTrue);
        $this->assertTrue($config->usingTitleCasedTrue);
        $this->assertTrue($config->usingCapitalTrue);

        $this->assertFalse($config->usingLowerCasedFalse);
        $this->assertFalse($config->usingTitleCasedFalse);
        $this->assertFalse($config->usingCapitalFalse);

        $this->assertTrue($config->usingLowerCasedOn);
        $this->assertTrue($config->usingTitleCasedOn);
        $this->assertTrue($config->usingCapitalOn);

        $this->assertFalse($config->usingLowerCasedOff);
        $this->assertFalse($config->usingTitleCasedOff);
        $this->assertFalse($config->usingCapitalOff);
    }

    public function testHonorsPhpConstants()
    {
        if (!defined('ZEND_CONFIG_YAML_ENV')) {
            define('ZEND_CONFIG_YAML_ENV', 'testing');
        }
        if (!defined('ZEND_CONFIG_YAML_ENV_PATH')) {
            define('ZEND_CONFIG_YAML_ENV_PATH', __DIR__);
        }
        $config = new YamlConfig($this->_constantsConfig, 'production');
        $this->assertEquals(ZEND_CONFIG_YAML_ENV, $config->env);
        $this->assertEquals(ZEND_CONFIG_YAML_ENV_PATH . '/test/this', $config->path);
    }

    public function testAllowsIgnoringConstantStrings()
    {
        if (!defined('ZEND_CONFIG_YAML_ENV')) {
            define('ZEND_CONFIG_YAML_ENV', 'testing');
        }
        if (!defined('ZEND_CONFIG_YAML_ENV_PATH')) {
            define('ZEND_CONFIG_YAML_ENV_PATH', __DIR__);
        }
        $config = new YamlConfig(
            $this->_constantsConfig, 'production', array('ignore_constants' => true)
        );
        $this->assertEquals('ZEND_CONFIG_YAML_ENV', $config->env);
        $this->assertEquals('ZEND_CONFIG_YAML_ENV_PATH/test/this', $config->path);
    }

    /**
     * @group ZF-11329
     */
    public function testAllowsInlineCommentsInValuesUsingHash()
    {
        $config = new YamlConfig($this->_yamlInlineCommentsConfig, null);
        $this->assertSame(
            'APPLICATION_PATH/controllers',
            $config->resources->frontController->controllerDirectory
        );
    }
    
    /**
     * @group ZF-11384
     */
    public function testAllowsIndentedCommentsUsingHash()
    {
        $config = new YamlConfig($this->_yamlIndentedCommentsConfig, null);
        $this->assertSame(
            'APPLICATION_PATH/controllers',
            $config->resources->frontController->controllerDirectory
        );
    }
    
    /**
     * @group ZF-11702
     */
    public function testAllowsConstantsInLists()
    {
        if (!defined('ZEND_CONFIG_YAML_TEST_PATH')) {
            define('ZEND_CONFIG_YAML_TEST_PATH', 'testing');
        }        
        $config = new YamlConfig($this->_yamlListConstantsConfig, 'production');

        $this->assertEquals(ZEND_CONFIG_YAML_TEST_PATH, $config->paths->{0});
        $this->assertEquals(ZEND_CONFIG_YAML_TEST_PATH . '/library/test', $config->paths->{1});
    }
    
    /**
     * @group ZF-11702
     */
    public function testAllowsBooleansInLists()
    {
        $config = new YamlConfig($this->_listBooleansConfig, 'production');

        $this->assertTrue($config->usingLowerCasedYes->{0});
        $this->assertTrue($config->usingTitleCasedYes->{0});
        $this->assertTrue($config->usingCapitalYes->{0});
        $this->assertTrue($config->usingLowerY->{0});
        $this->assertTrue($config->usingUpperY->{0});

        $this->assertFalse($config->usingLowerCasedNo->{0});
        $this->assertFalse($config->usingTitleCasedNo->{0});
        $this->assertFalse($config->usingCapitalNo->{0});
        $this->assertFalse($config->usingLowerN->{0});
        $this->assertFalse($config->usingUpperN->{0});

        $this->assertTrue($config->usingLowerCasedTrue->{0});
        $this->assertTrue($config->usingTitleCasedTrue->{0});
        $this->assertTrue($config->usingCapitalTrue->{0});

        $this->assertFalse($config->usingLowerCasedFalse->{0});
        $this->assertFalse($config->usingTitleCasedFalse->{0});
        $this->assertFalse($config->usingCapitalFalse->{0});

        $this->assertTrue($config->usingLowerCasedOn->{0});
        $this->assertTrue($config->usingTitleCasedOn->{0});
        $this->assertTrue($config->usingCapitalOn->{0});

        $this->assertFalse($config->usingLowerCasedOff->{0});
        $this->assertFalse($config->usingTitleCasedOff->{0});
        $this->assertFalse($config->usingCapitalOff->{0});
    }
        
}
