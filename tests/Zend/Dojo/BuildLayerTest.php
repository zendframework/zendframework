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
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Dojo;

use Zend\Dojo\BuildLayer,
    Zend\Dojo\View\Helper\Dojo\Container as DojoContainer,
    Zend\Json\Json,
    Zend\Registry,
    Zend\View;

/**
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 */
class BuildLayerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $registry = Registry::getInstance();
        if (isset($registry['Zend\Dojo\View\Helper\Dojo'])) {
            unset($registry['Zend\Dojo\View\Helper\Dojo']);
        }
        $this->view = new View\Renderer\PhpRenderer();
        \Zend\Dojo\Dojo::enableView($this->view);
    }

    public function tearDown()
    {
        unset($this->view);
    }

    public function testViewShouldBeNullByDefault()
    {
        $build = new BuildLayer();
        $this->assertNull($build->getView());
    }

    public function testRetrievingDojoHelperShouldRaiseExceptionWhenNoViewPresent()
    {
        $this->setExpectedException('Zend\Dojo\Exception\RuntimeException', 'View object not registered; cannot retrieve dojo helper');
        $build = new BuildLayer();
        $build->getDojoHelper();
    }

    public function testDojoHelperShouldBeRetrievedFromViewObjectIfNotExplicitySet()
    {
        $build = new BuildLayer(array('view' => $this->view));
        $helper = $build->getDojoHelper();
        $this->assertTrue($helper instanceof DojoContainer);
    }

    public function testLayerScriptPathIsNullByDefault()
    {
        $build = new BuildLayer();
        $this->assertNull($build->getLayerScriptPath());
    }

    public function testLayerScriptPathShouldBeMutable()
    {
        $build = new BuildLayer();
        $path  = __FILE__;
        $build->setLayerScriptPath($path);
        $this->assertEquals($path, $build->getLayerScriptPath());
    }

    public function testShouldNotConsumeJavascriptByDefault()
    {
        $build = new BuildLayer();
        $this->assertFalse($build->consumeJavascript());
    }

    public function testConsumeJavascriptFlagShouldBeMutable()
    {
        $build = new BuildLayer();
        $build->setConsumeJavascript(true);
        $this->assertTrue($build->consumeJavascript());
    }

    public function testShouldNotConsumeOnLoadByDefault()
    {
        $build = new BuildLayer();
        $this->assertFalse($build->consumeOnLoad());
    }

    public function testConsumeOnLoadFlagShouldBeMutable()
    {
        $build = new BuildLayer();
        $build->setConsumeOnLoad(true);
        $this->assertTrue($build->consumeOnLoad());
    }

    public function testLayerNameShouldBeNullByDefault()
    {
        $build = new BuildLayer();
        $this->assertNull($build->getLayerName());
    }

    public function testLayerNameShouldBeMutable()
    {
        $build = new BuildLayer();
        $build->setLayerName('custom.main');
        $this->assertEquals('custom.main', $build->getLayerName());
    }

    public function testSettingLayerNameToInvalidFormatShouldRaiseException()
    {
        $this->setExpectedException('Zend\Dojo\Exception\InvalidArgumentException', 'Invalid layer name provided');
        $build = new BuildLayer();
        $build->setLayerName('customFoo#bar');
    }

    public function testGeneratingLayerScriptShouldReturnValidLayerMarkup()
    {
        $this->view->plugin('dojo')->requireModule('dijit.form.Form')
                           ->requireModule('dijit.form.TextBox')
                           ->requireModule('dijit.form.Button');
        $build = new BuildLayer(array(
            'view'      => $this->view,
            'layerName' => 'foo.bar',
        ));

        $test   = $build->generateLayerScript();
        $script = file_get_contents(__DIR__ . '/TestAsset/BuildLayer.js');

        $test   = $this->stripWhitespace($test);
        $script = $this->stripWhitespace($script);
        $this->assertEquals($script, $test);
    }

    public function testGeneratingLayerScriptWithOnLoadsEnabledShouldReturnValidLayerMarkup()
    {
        $this->view->plugin('dojo')->requireModule('dijit.form.Form')
                           ->requireModule('dijit.form.TextBox')
                           ->requireModule('dijit.form.Button')
                           ->addOnLoad('custom.callback');
        $build = new BuildLayer(array(
            'view'          => $this->view,
            'layerName'     => 'foo.bar',
            'consumeOnLoad' => true,
        ));

        $test   = $build->generateLayerScript();
        $script = file_get_contents(__DIR__ . '/TestAsset/BuildLayerOnLoad.js');

        $test   = $this->stripWhitespace($test);
        $script = $this->stripWhitespace($script);
        $this->assertEquals($script, $test);
    }

    public function testGeneratingLayerScriptWithOnLoadsDisabledShouldNotRenderOnLoadEvents()
    {
        $this->view->plugin('dojo')->requireModule('dijit.form.Form')
                           ->requireModule('dijit.form.TextBox')
                           ->requireModule('dijit.form.Button')
                           ->addOnLoad('custom.callback');
        $build = new BuildLayer(array(
            'view'          => $this->view,
            'layerName'     => 'foo.bar',
        ));

        $test   = $build->generateLayerScript();
        $script = file_get_contents(__DIR__ . '/TestAsset/BuildLayer.js');

        $test   = $this->stripWhitespace($test);
        $script = $this->stripWhitespace($script);
        $this->assertEquals($script, $test);
    }

    public function testGeneratingLayerScriptWithJavascriptsEnabledShouldReturnValidLayerMarkup()
    {
        $this->view->plugin('dojo')->requireModule('dijit.form.Form')
                           ->requireModule('dijit.form.TextBox')
                           ->requireModule('dijit.form.Button')
                           ->addJavascript('custom.callback();');
        $build = new BuildLayer(array(
            'view'              => $this->view,
            'layerName'         => 'foo.bar',
            'consumeJavascript' => true,
        ));

        $test   = $build->generateLayerScript();
        $script = file_get_contents(__DIR__ . '/TestAsset/BuildLayerJavascript.js');

        $test   = $this->stripWhitespace($test);
        $script = $this->stripWhitespace($script);
        $this->assertEquals($script, $test);
    }

    public function testGeneratingLayerScriptWithJavascriptsDisabledShouldNotRenderJavascripts()
    {
        $this->view->plugin('dojo')->requireModule('dijit.form.Form')
                           ->requireModule('dijit.form.TextBox')
                           ->requireModule('dijit.form.Button')
                           ->addJavascript('custom.callback();');
        $build = new BuildLayer(array(
            'view'          => $this->view,
            'layerName'     => 'foo.bar',
        ));

        $test   = $build->generateLayerScript();
        $script = file_get_contents(__DIR__ . '/TestAsset/BuildLayer.js');

        $test   = $this->stripWhitespace($test);
        $script = $this->stripWhitespace($script);
        $this->assertEquals($script, $test);
    }

    public function testProfileOptionsShouldIncludeSaneDefaultsByDefault()
    {
        $build = new BuildLayer();
        $expected = $this->getDefaultProfileOptions();
        $options = $build->getProfileOptions();
        $this->assertEquals($expected, $options);
    }

    public function testAddProfileOptionsShouldAddOptions()
    {
        $options = array('foo' => 'bar');
        $build = new BuildLayer(array(
            'profileOptions' => $options,
        ));
        $build->addProfileOptions(array('bar' => 'baz'));
        $expected = $this->getDefaultProfileOptions() + array('foo' => 'bar', 'bar' => 'baz');
        $this->assertEquals($expected, $build->getProfileOptions());
    }

    public function testAddProfileOptionShouldAddOption()
    {
        $build = new BuildLayer();
        $build->addProfileOption('foo', 'bar');
        $this->assertTrue($build->hasProfileOption('foo'));
    }

    public function testSetProfileOptionsShouldNotOverwriteOptions()
    {
        $options = array('foo' => 'bar');
        $build = new BuildLayer(array(
            'profileOptions' => $options,
        ));
        $build->setProfileOptions(array('bar' => 'baz'));
        $this->assertNotEquals(array('bar' => 'baz'), $build->getProfileOptions());
        $this->assertTrue($build->hasProfileOption('bar'));
    }

    public function testProfilePrefixesAreEmptyByDefault()
    {
        $build = new BuildLayer();
        $prefixes = $build->getProfilePrefixes();
        $this->assertTrue(empty($prefixes));
    }

    public function testProfilePrefixesIncludeLayerNamePrefix()
    {
        $build = new BuildLayer(array('layerName' => 'foo.main'));
        $prefixes = $build->getProfilePrefixes();
        $this->assertTrue(array_key_exists('foo', $prefixes), var_export($prefixes, 1));
        $this->assertEquals(array('foo', '../foo'), $prefixes['foo']);
    }

    public function testProfilePrefixesShouldIncludePrefixesOfAllRequiredModules()
    {
        $this->view->plugin('dojo')->requireModule('dijit.layout.TabContainer')
                           ->requireModule('dojox.layout.ContentPane');
        $build = new BuildLayer(array('view' => $this->view));

        $prefixes = $build->getProfilePrefixes();
        $this->assertTrue(array_key_exists('dijit', $prefixes), var_export($prefixes, 1));
        $this->assertEquals(array('dijit', '../dijit'), $prefixes['dijit']);
        $this->assertTrue(array_key_exists('dojox', $prefixes), var_export($prefixes, 1));
        $this->assertEquals(array('dojox', '../dojox'), $prefixes['dojox']);
    }

    public function testGeneratedDojoBuildProfileWithNoExtraLayerDependencies()
    {
        $build = new BuildLayer(array(
            'view' => $this->view,
            'layerName' => 'zend.main',
            'layerScriptPath' => '../zend/main.js',
        ));
        $profile  = $build->generateBuildProfile();
        $expected = file_get_contents(__DIR__ . '/TestAsset/BuildProfile.js');

        $decodedProfile  = $this->decodeProfileJson($profile);
        $decodedExpected = $this->decodeProfileJson($expected);

        foreach ($decodedExpected as $key => $value) {
            $this->assertArrayHasKey($key, $decodedProfile);
            $this->assertEquals($value, $decodedProfile[$key], $key . ' is not same');
        }
    }

    public function testGeneratedDojoBuildProfileWithLayerDependencies()
    {
        $this->view->plugin('dojo')->requireModule('dijit.layout.BorderContainer')
                           ->requireModule('dojox.layout.ContentPane');
        $build = new BuildLayer(array(
            'view' => $this->view,
            'layerName' => 'zend.main',
            'layerScriptPath' => '../zend/main.js',
        ));
        $profile  = $build->generateBuildProfile();
        $expected = file_get_contents(__DIR__ . '/TestAsset/BuildProfileWithDependencies.js');

        $decodedProfile  = $this->decodeProfileJson($profile);
        $decodedExpected = $this->decodeProfileJson($expected);

        foreach ($decodedExpected as $key => $value) {
            $this->assertArrayHasKey($key, $decodedProfile);
            $this->assertEquals($value, $decodedProfile[$key]);
        }
    }

    protected function stripWhitespace($string)
    {
        $string = preg_replace('/^[ ]+/m', '', $string);
        $string = preg_replace('/([ ]{2,})/s', ' ', $string);
        $string = preg_replace('/(\r|\r\n|\n){2, }/s', "\n", $string);
        $string = preg_replace('/(\r|\r\n|\n)$/', '', $string);
        return $string;
    }

    protected function getDefaultProfileOptions()
    {
        return array(
            'action'        => 'release',
            'optimize'      => 'shrinksafe',
            'layerOptimize' => 'shrinksafe',
            'copyTests'     => false,
            'loader'        => 'default',
            'cssOptimize'   => 'comments',
        );
    }

    protected function decodeProfileJson($profile)
    {
        $profile = preg_replace('/^dependencies = (.*?);$/s', '$1', $profile);
        $profile = preg_replace('/(\b)([^"\':,]+):/', '$1"$2":', $profile);
        $data    = Json::decode($profile, JSON::TYPE_ARRAY);
        ksort($data);
        return $data;
    }
}
