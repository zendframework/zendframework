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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 */
class Zend_Dojo_BuildLayerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $registry = Zend_Registry::getInstance();
        if (isset($registry['Zend_Dojo_View_Helper_Dojo'])) {
            unset($registry['Zend_Dojo_View_Helper_Dojo']);
        }
        $this->view = new Zend_View();
        Zend_Dojo::enableView($this->view);
    }

    public function testViewShouldBeNullByDefault()
    {
        $build = new Zend_Dojo_BuildLayer();
        $this->assertNull($build->getView());
    }

    /**
     * @expectedException Zend_Dojo_Exception
     */
    public function testRetrievingDojoHelperShouldRaiseExceptionWhenNoViewPresent()
    {
        $build = new Zend_Dojo_BuildLayer();
        $build->getDojoHelper();
    }

    public function testDojoHelperShouldBeRetrievedFromViewObjectIfNotExplicitySet()
    {
        $build = new Zend_Dojo_BuildLayer(array('view' => $this->view));
        $helper = $build->getDojoHelper();
        $this->assertTrue($helper instanceof Zend_Dojo_View_Helper_Dojo_Container);
    }

    public function testLayerScriptPathIsNullByDefault()
    {
        $build = new Zend_Dojo_BuildLayer();
        $this->assertNull($build->getLayerScriptPath());
    }

    public function testLayerScriptPathShouldBeMutable()
    {
        $build = new Zend_Dojo_BuildLayer();
        $path  = __FILE__;
        $build->setLayerScriptPath($path);
        $this->assertEquals($path, $build->getLayerScriptPath());
    }

    public function testShouldNotConsumeJavascriptByDefault()
    {
        $build = new Zend_Dojo_BuildLayer();
        $this->assertFalse($build->consumeJavascript());
    }

    public function testConsumeJavascriptFlagShouldBeMutable()
    {
        $build = new Zend_Dojo_BuildLayer();
        $build->setConsumeJavascript(true);
        $this->assertTrue($build->consumeJavascript());
    }

    public function testShouldNotConsumeOnLoadByDefault()
    {
        $build = new Zend_Dojo_BuildLayer();
        $this->assertFalse($build->consumeOnLoad());
    }

    public function testConsumeOnLoadFlagShouldBeMutable()
    {
        $build = new Zend_Dojo_BuildLayer();
        $build->setConsumeOnLoad(true);
        $this->assertTrue($build->consumeOnLoad());
    }

    public function testLayerNameShouldBeNullByDefault()
    {
        $build = new Zend_Dojo_BuildLayer();
        $this->assertNull($build->getLayerName());
    }

    public function testLayerNameShouldBeMutable()
    {
        $build = new Zend_Dojo_BuildLayer();
        $build->setLayerName('custom.main');
        $this->assertEquals('custom.main', $build->getLayerName());
    }

    /**
     * @expectedException Zend_Dojo_Exception
     */
    public function testSettingLayerNameToInvalidFormatShouldRaiseException()
    {
        $build = new Zend_Dojo_BuildLayer();
        $build->setLayerName('customFoo#bar');
    }

    public function testGeneratingLayerScriptShouldReturnValidLayerMarkup()
    {
        $this->view->dojo()->requireModule('dijit.form.Form')
                           ->requireModule('dijit.form.TextBox')
                           ->requireModule('dijit.form.Button');
        $build = new Zend_Dojo_BuildLayer(array(
            'view'      => $this->view,
            'layerName' => 'foo.bar',
        ));

        $test   = $build->generateLayerScript();
        $script = file_get_contents(dirname(__FILE__) . '/_files/BuildLayer.js');

        $test   = $this->stripWhitespace($test);
        $script = $this->stripWhitespace($script);
        $this->assertEquals($script, $test);
    }

    public function testGeneratingLayerScriptWithOnLoadsEnabledShouldReturnValidLayerMarkup()
    {
        $this->view->dojo()->requireModule('dijit.form.Form')
                           ->requireModule('dijit.form.TextBox')
                           ->requireModule('dijit.form.Button')
                           ->addOnLoad('custom.callback');
        $build = new Zend_Dojo_BuildLayer(array(
            'view'          => $this->view,
            'layerName'     => 'foo.bar',
            'consumeOnLoad' => true,
        ));

        $test   = $build->generateLayerScript();
        $script = file_get_contents(dirname(__FILE__) . '/_files/BuildLayerOnLoad.js');

        $test   = $this->stripWhitespace($test);
        $script = $this->stripWhitespace($script);
        $this->assertEquals($script, $test);
    }

    public function testGeneratingLayerScriptWithOnLoadsDisabledShouldNotRenderOnLoadEvents()
    {
        $this->view->dojo()->requireModule('dijit.form.Form')
                           ->requireModule('dijit.form.TextBox')
                           ->requireModule('dijit.form.Button')
                           ->addOnLoad('custom.callback');
        $build = new Zend_Dojo_BuildLayer(array(
            'view'          => $this->view,
            'layerName'     => 'foo.bar',
        ));

        $test   = $build->generateLayerScript();
        $script = file_get_contents(dirname(__FILE__) . '/_files/BuildLayer.js');

        $test   = $this->stripWhitespace($test);
        $script = $this->stripWhitespace($script);
        $this->assertEquals($script, $test);
    }

    public function testGeneratingLayerScriptWithJavascriptsEnabledShouldReturnValidLayerMarkup()
    {
        $this->view->dojo()->requireModule('dijit.form.Form')
                           ->requireModule('dijit.form.TextBox')
                           ->requireModule('dijit.form.Button')
                           ->addJavascript('custom.callback();');
        $build = new Zend_Dojo_BuildLayer(array(
            'view'              => $this->view,
            'layerName'         => 'foo.bar',
            'consumeJavascript' => true,
        ));

        $test   = $build->generateLayerScript();
        $script = file_get_contents(dirname(__FILE__) . '/_files/BuildLayerJavascript.js');

        $test   = $this->stripWhitespace($test);
        $script = $this->stripWhitespace($script);
        $this->assertEquals($script, $test);
    }

    public function testGeneratingLayerScriptWithJavascriptsDisabledShouldNotRenderJavascripts()
    {
        $this->view->dojo()->requireModule('dijit.form.Form')
                           ->requireModule('dijit.form.TextBox')
                           ->requireModule('dijit.form.Button')
                           ->addJavascript('custom.callback();');
        $build = new Zend_Dojo_BuildLayer(array(
            'view'          => $this->view,
            'layerName'     => 'foo.bar',
        ));

        $test   = $build->generateLayerScript();
        $script = file_get_contents(dirname(__FILE__) . '/_files/BuildLayer.js');

        $test   = $this->stripWhitespace($test);
        $script = $this->stripWhitespace($script);
        $this->assertEquals($script, $test);
    }

    public function testProfileOptionsShouldIncludeSaneDefaultsByDefault()
    {
        $build = new Zend_Dojo_BuildLayer();
        $expected = $this->getDefaultProfileOptions();
        $options = $build->getProfileOptions();
        $this->assertEquals($expected, $options);
    }

    public function testAddProfileOptionsShouldAddOptions()
    {
        $options = array('foo' => 'bar');
        $build = new Zend_Dojo_BuildLayer(array(
            'profileOptions' => $options,
        ));
        $build->addProfileOptions(array('bar' => 'baz'));
        $expected = $this->getDefaultProfileOptions() + array('foo' => 'bar', 'bar' => 'baz');
        $this->assertEquals($expected, $build->getProfileOptions());
    }

    public function testAddProfileOptionShouldAddOption()
    {
        $build = new Zend_Dojo_BuildLayer();
        $build->addProfileOption('foo', 'bar');
        $this->assertTrue($build->hasProfileOption('foo'));
    }

    public function testSetProfileOptionsShouldNotOverwriteOptions()
    {
        $options = array('foo' => 'bar');
        $build = new Zend_Dojo_BuildLayer(array(
            'profileOptions' => $options,
        ));
        $build->setProfileOptions(array('bar' => 'baz'));
        $this->assertNotEquals(array('bar' => 'baz'), $build->getProfileOptions());
        $this->assertTrue($build->hasProfileOption('bar'));
    }

    public function testProfilePrefixesAreEmptyByDefault()
    {
        $build = new Zend_Dojo_BuildLayer();
        $prefixes = $build->getProfilePrefixes();
        $this->assertTrue(empty($prefixes));
    }

    public function testProfilePrefixesIncludeLayerNamePrefix()
    {
        $build = new Zend_Dojo_BuildLayer(array('layerName' => 'foo.main'));
        $prefixes = $build->getProfilePrefixes();
        $this->assertTrue(array_key_exists('foo', $prefixes), var_export($prefixes, 1));
        $this->assertEquals(array('foo', '../foo'), $prefixes['foo']);
    }

    public function testProfilePrefixesShouldIncludePrefixesOfAllRequiredModules()
    {
        $this->view->dojo()->requireModule('dijit.layout.TabContainer')
                           ->requireModule('dojox.layout.ContentPane');
        $build = new Zend_Dojo_BuildLayer(array('view' => $this->view));

        $prefixes = $build->getProfilePrefixes();
        $this->assertTrue(array_key_exists('dijit', $prefixes), var_export($prefixes, 1));
        $this->assertEquals(array('dijit', '../dijit'), $prefixes['dijit']);
        $this->assertTrue(array_key_exists('dojox', $prefixes), var_export($prefixes, 1));
        $this->assertEquals(array('dojox', '../dojox'), $prefixes['dojox']);
    }

    public function testGeneratedDojoBuildProfileWithNoExtraLayerDependencies()
    {
        $build = new Zend_Dojo_BuildLayer(array(
            'view' => $this->view,
            'layerName' => 'zend.main',
            'layerScriptPath' => '../zend/main.js',
        ));
        $profile  = $build->generateBuildProfile();
        $expected = file_get_contents(dirname(__FILE__) . '/_files/BuildProfile.js');

        $decodedProfile  = $this->decodeProfileJson($profile);
        $decodedExpected = $this->decodeProfileJson($expected);

        $this->assertEquals($decodedExpected, $decodedProfile, "Expected:\n" . var_export($decodedExpected, 1) . "\nActual:\n" . var_export($decodedProfile, 1));
    }

    public function testGeneratedDojoBuildProfileWithLayerDependencies()
    {
        $this->view->dojo()->requireModule('dijit.layout.BorderContainer')
                           ->requireModule('dojox.layout.ContentPane');
        $build = new Zend_Dojo_BuildLayer(array(
            'view' => $this->view,
            'layerName' => 'zend.main',
            'layerScriptPath' => '../zend/main.js',
        ));
        $profile  = $build->generateBuildProfile();
        $expected = file_get_contents(dirname(__FILE__) . '/_files/BuildProfileWithDependencies.js');

        $decodedProfile  = $this->decodeProfileJson($profile);
        $decodedExpected = $this->decodeProfileJson($expected);

        $this->assertEquals($decodedExpected, $decodedProfile, "Expected:\n" . var_export($decodedExpected, 1) . "\nActual:\n" . var_export($decodedProfile, 1));
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
        $data    = Zend_Json::decode($profile);
        ksort($data);
        return $data;
    }
}
