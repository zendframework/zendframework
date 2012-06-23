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

namespace ZendTest\Dojo\View\Helper;

use Zend\Dojo\View\Helper\Dojo as DojoHelper,
    Zend\Dojo\View\Helper\Dojo\Container as DojoContainer,
    Zend\Json\Json,
    Zend\Registry,
    Zend\View\Renderer\PhpRenderer,
    Zend\View\Renderer\RendererInterface as Renderer;

/**
 * Test class for Zend_Dojo_View_Helper_Dojo.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_View
 */
class DojoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        Registry::_unsetInstance();
        $this->view   = $this->getView();
        $this->helper = new DojoContainer();
        $this->helper->setView($this->view);
        Registry::set('Zend\Dojo\View\Helper\Dojo', $this->helper);
        DojoHelper::setUseProgrammatic();
    }

    public function getView()
    {
        $view = new PhpRenderer();
        \Zend\Dojo\Dojo::enableView($view);
        return $view;
    }

    public function testViewPropertyShouldBeNullByDefault()
    {
        $helper = new DojoHelper();
        $this->assertNull($helper->view);
    }

    public function testShouldBeAbleToSetViewProperty()
    {
        $this->assertTrue($this->helper->view instanceof Renderer);
    }

    public function testNoModulesShouldBeRegisteredByDefault()
    {
        $modules = $this->helper->getModules();
        $this->assertTrue(empty($modules));
    }

    public function testShouldBeAbleToRequireModules()
    {
        $this->helper->requireModule('foo.bar');
        $modules = $this->helper->getModules();
        $this->assertContains('foo.bar', $modules);
    }

    /**
     * @group ZF-3914
     */
    public function testShouldAllowRequiringMultipleModulesAtOnce()
    {
        $modules = array('foo.bar', 'bar.baz', 'baz.bat');
        $this->helper->requireModule($modules);
        $test = $this->helper->getModules();
        foreach ($modules as $module) {
            $this->assertTrue(in_array($module, $test));
        }
    }

    public function testInvalidModuleNameShouldThrowExceptionDuringRegistration()
    {
        $this->setExpectedException('Zend\Dojo\View\Exception\InvalidArgumentException', 'invalid character');
        $this->helper->requireModule('foo#$!bar');
    }

    /**
     * @group ZF-3916
     */
    public function testRequireModuleShouldAllowDashAndUnderscoreCharacters()
    {
        $this->helper->requireModule('dojox.highlight.language._www');
        $this->helper->requireModule('dojo.NodeList-fx');
    }

    public function testShouldNotRegisterDuplicateModules()
    {
        $this->helper->requireModule('foo.bar');
        $this->helper->requireModule('foo.bar');
        $modules = $this->helper->getModules();
        $this->assertContains('foo.bar', $modules);
        $this->assertEquals(1, count($modules));
    }

    public function testModulePathsShouldBeEmptyByDefault()
    {
        $paths = $this->helper->getModulePaths();
        $this->assertTrue(empty($paths));
    }

    public function testShouldBeAbleToRegisterModulePaths()
    {
        $this->helper->registerModulePath('custom', '../custom');
        $paths = $this->helper->getModulePaths();
        $this->assertTrue(array_key_exists('custom', $paths), var_export($paths, 1));
        $this->assertContains('../custom', $paths);
    }

    public function testShouldNotBeAbleToRegisterDuplicateModulePaths()
    {
        $this->helper->registerModulePath('custom', '../custom');
        $this->helper->registerModulePath('custom', '../custom');
        $paths = $this->helper->getModulePaths();
        $this->assertEquals(1, count($paths));
        $this->assertTrue(array_key_exists('custom', $paths));
        $this->assertContains('../custom', $paths);
    }

    public function testShouldBeDisabledByDefault()
    {
        $this->assertFalse($this->helper->isEnabled());
    }

    public function testCallingAUseMethodShouldEnableHelper()
    {
        $this->testShouldBeDisabledByDefault();
        $this->helper->setCdnVersion('1.0');
        $this->assertTrue($this->helper->isEnabled());
        $this->helper->disable();
        $this->assertFalse($this->helper->isEnabled());
        $this->helper->setLocalPath('/js/dojo/dojo.js');
        $this->assertTrue($this->helper->isEnabled());
    }

    public function testShouldUtilizeCdnByDefault()
    {
        $this->helper->enable();
        $this->assertTrue($this->helper->useCdn());
    }

    public function testShouldUseGoogleCdnByDefault()
    {
        $this->assertEquals(\Zend\Dojo\Dojo::CDN_BASE_GOOGLE, $this->helper->getCdnBase());
    }

    public function testShouldAllowSpecifyingCdnBasePath()
    {
        $this->testShouldUseGoogleCdnByDefault();
        $this->helper->setCdnBase(\Zend\Dojo\Dojo::CDN_BASE_AOL);
        $this->assertEquals(\Zend\Dojo\Dojo::CDN_BASE_AOL, $this->helper->getCdnBase());
    }

    public function testShouldUseLatestVersionWhenUsingCdnByDefault()
    {
        $this->helper->enable();
        $this->assertEquals('1.4.1', $this->helper->getCdnVersion());
    }

    public function testShouldAllowSpecifyingDojoVersionWhenUtilizingCdn()
    {
        $this->helper->setCdnVersion('1.0');
        $this->assertEquals('1.0', $this->helper->getCdnVersion());
    }

    public function testShouldUseAolCdnDojoPathByDefault()
    {
        $this->assertEquals(\Zend\Dojo\Dojo::CDN_DOJO_PATH_AOL, $this->helper->getCdnDojoPath());
    }

    public function testShouldAllowSpecifyingCdnDojoPath()
    {
        $this->testShouldUseAolCdnDojoPathByDefault();
        $this->helper->setCdnDojoPath(\Zend\Dojo\Dojo::CDN_DOJO_PATH_GOOGLE);
        $this->assertEquals(\Zend\Dojo\Dojo::CDN_DOJO_PATH_GOOGLE, $this->helper->getCdnDojoPath());
    }

    public function testShouldAllowSpecifyingLocalDojoInstall()
    {
        $this->helper->setLocalPath('/js/dojo/dojo.js');
        $this->assertTrue($this->helper->useLocalPath());
    }

    public function testShouldAllowSpecifyingDjConfig()
    {
        $this->helper->setDjConfig(array('parseOnLoad' => 'true'));
        $config = $this->helper->getDjConfig();
        $this->assertTrue(is_array($config));
        $this->assertTrue(array_key_exists('parseOnLoad', $config));
        $this->assertEquals('true', $config['parseOnLoad']);
    }

    public function testShouldAllowRetrievingIndividualDjConfigKeys()
    {
        $this->helper->setDjConfigOption('parseOnLoad', 'true');
        $this->assertEquals('true', $this->helper->getDjConfigOption('parseOnLoad'));
    }

    public function testGetDjConfigShouldReturnEmptyArrayByDefault()
    {
        $this->assertSame(array(), $this->helper->getDjConfig());
    }

    public function testGetDjConfigOptionShouldReturnNullWhenKeyDoesNotExist()
    {
        $this->assertNull($this->helper->getDjConfigOption('bogus'));
    }

    public function testGetDjConfigOptionShouldAllowSpecifyingDefaultValue()
    {
        $this->assertEquals('bar', $this->helper->getDjConfigOption('foo', 'bar'));
    }

    public function testDjConfigShouldSerializeToJson()
    {
        $this->helper->setDjConfigOption('parseOnLoad', true)
                     ->enable();
        $html = $this->helper->__toString();
        $this->assertContains('var djConfig = ', $html, var_export($html, 1));
        $this->assertContains('"parseOnLoad":', $html, $html);
    }

    public function testShouldAllowSpecifyingStylesheetByModuleName()
    {
        $this->helper->addStylesheetModule('dijit.themes.tundra');
        $stylesheets = $this->helper->getStylesheetModules();
        $this->assertContains('dijit.themes.tundra', $stylesheets);
    }

    public function testDuplicateStylesheetModulesShouldNotBeAllowed()
    {
        $this->helper->addStylesheetModule('dijit.themes.tundra');
        $stylesheets = $this->helper->getStylesheetModules();
        $this->assertContains('dijit.themes.tundra', $stylesheets);

        $this->helper->addStylesheetModule('dijit.themes.tundra');
        $stylesheets = $this->helper->getStylesheetModules();
        $this->assertEquals(1, count($stylesheets));
        $this->assertContains('dijit.themes.tundra', $stylesheets);
    }

    /**
     * @group ZF-3916
     */
    public function testAddingStylesheetModuleShouldAllowDashAndUnderscoreCharacters()
    {
        $this->helper->addStylesheetModule('dojox._highlight.pygments');
        $this->helper->addStylesheetModule('dojo.NodeList-fx.styles');
    }


    public function testInvalidStylesheetModuleNameShouldThrowException()
    {
        $this->setExpectedException('Zend\Dojo\View\Exception\InvalidArgumentException', 'Invalid');
        $this->helper->addStylesheetModule('foo/bar/baz');
    }

    public function testRenderingModuleStylesheetShouldProperlyCreatePaths()
    {
        $this->helper->enable()
                     ->addStylesheetModule('dijit.themes.tundra');
        $html = $this->helper->__toString();
        $this->assertContains('dijit/themes/tundra/tundra.css', $html);
    }

    public function testShouldAllowSpecifyingLocalStylesheet()
    {
        $this->helper->addStylesheet('/css/foo.css');
        $css = $this->helper->getStylesheets();
        $this->assertTrue(is_array($css));
        $this->assertContains('/css/foo.css', $css);
    }

    public function testShouldNotAllowSpecifyingDuplicateLocalStylesheets()
    {
        $this->testShouldAllowSpecifyingLocalStylesheet();
        $this->helper->addStylesheet('/css/foo.css');
        $css = $this->helper->getStylesheets();
        $this->assertTrue(is_array($css));
        $this->assertEquals(1, count($css));
        $this->assertContains('/css/foo.css', $css);
    }

    public function testShouldAllowSpecifyingOnLoadFunctionPointer()
    {
        $this->helper->addOnLoad('foo');
        $onLoad = $this->helper->getOnLoadActions();
        $this->assertTrue(is_array($onLoad));
        $this->assertEquals(1, count($onLoad));
        $action = array_shift($onLoad);
        $this->assertTrue(is_string($action));
        $this->assertEquals('foo', $action);
    }

    public function testShouldAllowCapturingOnLoadActions()
    {
        $this->helper->onLoadCaptureStart(); ?>
function() {
    bar();
    baz();
}
<?php   $this->helper->onLoadCaptureEnd();
        $onLoad = $this->helper->getOnLoadActions();
        $this->assertTrue(is_array($onLoad));
        $this->assertEquals(1, count($onLoad));
        $action = array_shift($onLoad);
        $this->assertTrue(is_string($action));
        $this->assertContains('function() {', $action);
        $this->assertContains('bar();', $action);
        $this->assertContains('baz();', $action);
    }

    public function testShouldNotAllowSpecifyingDuplicateOnLoadActions()
    {
        $this->helper->addOnLoad('foo');
        $this->helper->addOnLoad('foo');
        $onLoad = $this->helper->getOnLoadActions();
        $this->assertTrue(is_array($onLoad));
        $this->assertEquals(1, count($onLoad));
        $action = array_shift($onLoad);
        $this->assertEquals('foo', $action);
    }

    public function testDirectMethodShouldReturnContainer()
    {
        $helper = new DojoHelper();
        $this->assertSame($this->helper, $helper->__invoke());
    }

    public function testHelperStorageShouldPersistBetweenViewObjects()
    {
        $view1 = $this->getView();
        $dojo1 = $view1->plugin('dojo');
        $view2 = $this->getView();
        $dojo2 = $view1->plugin('dojo');
        $this->assertSame($dojo1, $dojo2);
    }

    public function testSerializingToStringShouldReturnEmptyStringByDefault()
    {
        $this->assertEquals('', $this->helper->__toString());
    }

    public function testEnablingHelperShouldCauseStringSerializationToWork()
    {
        $this->setupDojo();
        $html = $this->helper->__toString();
        $doc  = new \DOMDocument;
        $doc->loadHTML($html);
        $xPath = new \DOMXPath($doc);
        $results = $xPath->query('//script');
        $this->assertEquals(3, $results->length);
        for ($i = 0; $i < 3; ++$i) {
            $script = $doc->saveXML($results->item($i));
            switch ($i) {
                case 0:
                    $this->assertContains('var djConfig = ', $script);
                    $this->assertContains('parseOnLoad', $script);
                    break;
                case 1:
                    $this->assertRegexp('#src="http://.+/dojo/[0-9.]+/dojo/dojo.xd.js"#', $script);
                    $this->assertContains('/>', $script);
                    break;
                case 2:
                    $this->assertContains('dojo.registerModulePath("custom", "../custom")', $script, $script);
                    $this->assertContains('dojo.require("dijit.layout.ContentPane")', $script, $script);
                    $this->assertContains('dojo.require("custom.foo")', $script, $script);
                    $this->assertContains('dojo.addOnLoad(foo)', $script, $script);
                    break;
            }
        }

        $results = $xPath->query('//style');
        $this->assertEquals(1, $results->length, $html);
        $style = $doc->saveXML($results->item(0));
        $this->assertContains('@import', $style);
        $this->assertEquals(2, substr_count($style, '@import'));
        $this->assertEquals(1, substr_count($style, 'http://ajax.googleapis.com/ajax/libs/dojo/'), $style);
        $this->assertContains('css/custom.css', $style);
        $this->assertContains('dijit/themes/tundra/tundra.css', $style);
    }

    public function testStringSerializationShouldBeDoctypeAware()
    {
        $view = $this->getView();
        $view->plugin('doctype')->__invoke('HTML4_LOOSE');
        $this->helper->setView($view);
        $this->setupDojo();
        $html = $this->helper->__toString();
        $this->assertRegexp('|<style [^>]*>[\r\n]+\s*<!--|', $html);
        $this->assertRegexp('|<script [^>]*>[\r\n]+\s*//<!--|', $html);

        $this->helper = new DojoHelper();
        $view->plugin('doctype')->__invoke('XHTML1_STRICT');
        $this->helper->setView($view);
        $this->setupDojo();
        $html = $this->helper->__toString();

        /**
         * @todo should stylesheets be escaped as CDATA when isXhtml()?
         */
        $this->assertRegexp('|<style [^>]*>[\r\n]+\s*<!--|', $html);
        $this->assertRegexp('|<script [^>]*>[\r\n]+\s*//<!\[CDATA\[|', $html);
    }

    public function testDojoHelperContainerPersistsBetweenViewObjects()
    {
        $this->setupDojo();

        $view = $this->getView();
        $this->assertNotSame($this->view, $view);
        $helper = $view->plugin('dojo')->__invoke();
        $this->assertSame($this->helper, $helper);
    }

    public function testShouldUseProgrammaticDijitCreationByDefault()
    {
        $this->assertTrue(DojoHelper::useProgrammatic());
    }

    public function testShouldAllowSpecifyingDeclarativeDijitCreation()
    {
        $this->testShouldUseProgrammaticDijitCreationByDefault();
        DojoHelper::setUseDeclarative();
        $this->assertTrue(DojoHelper::useDeclarative());
    }

    public function testShouldAllowSpecifyingProgrammaticDijitCreationWithNoScriptGeneration()
    {
        DojoHelper::setUseProgrammatic(-1);
        $this->assertTrue(DojoHelper::useProgrammatic());
        $this->assertTrue(DojoHelper::useProgrammaticNoScript());
    }

    public function testAddingProgrammaticDijitsShouldAcceptIdAndArrayOfDijitParams()
    {
        $this->helper->addDijit('foo', array('dojoType' => 'dijit.form.Form'));
        $dijits = $this->helper->getDijits();
        $this->assertTrue(is_array($dijits));
        $this->assertEquals(1, count($dijits));
        $dijit = array_shift($dijits);
        $this->assertTrue(is_array($dijit));
        $this->assertEquals(2, count($dijit));
        $this->assertTrue(array_key_exists('id', $dijit));
        $this->assertTrue(array_key_exists('params', $dijit));
        $this->assertEquals('foo', $dijit['id']);
        $this->assertTrue(is_array($dijit['params']));
        $this->assertEquals(1, count($dijit['params']));
        $this->assertTrue(array_key_exists('dojoType', $dijit['params']));
        $this->assertEquals('dijit.form.Form', $dijit['params']['dojoType']);
    }

    public function testAddingDuplicateProgrammaticDijitsShouldRaiseExceptions()
    {
        $this->helper->addDijit('foo', array('dojoType' => 'dijit.form.Form'));
        $this->setExpectedException('Zend\Dojo\View\Exception\InvalidArgumentException', 'Duplicate dijit with id ');
        $this->helper->addDijit('foo', array('dojoType' => 'dijit.form.ComboBox'));
    }

    public function testSettingProgrammaticDijitsShouldOverwriteExistingDijits()
    {
        $this->testAddingProgrammaticDijitsShouldAcceptIdAndArrayOfDijitParams();
        $this->helper->setDijit('foo', array('dojoType' => 'dijit.form.ComboBox'));
        $dijits = $this->helper->getDijits();
        $this->assertTrue(is_array($dijits));
        $this->assertEquals(1, count($dijits));
        $dijit = array_shift($dijits);
        $this->assertEquals('dijit.form.ComboBox', $dijit['params']['dojoType']);
    }

    public function testShouldAllowAddingMultipleDijitsAtOnce()
    {
        $dijits = array(
            'foo' => array(
                'dojoType' => 'dijit.form.Form'
            ),
            'bar' => array(
                'dojoType' => 'dijit.form.TextBox',
            ),
        );
        $this->helper->addDijits($dijits);
        $test = $this->helper->getDijits();
        $this->assertTrue(is_array($test));
        $this->assertEquals(2, count($test));
        $keys = array();
        foreach ($test as $dijit) {
            $keys[] = $dijit['id'];
        }
        $this->assertSame(array_keys($dijits), $keys);
    }

    public function testSettingMultipleDijitsAtOnceShouldOverwriteAllDijits()
    {
        $this->testAddingProgrammaticDijitsShouldAcceptIdAndArrayOfDijitParams();
        $dijits = array(
            'bar' => array(
                'dojoType' => 'dijit.form.Form'
            ),
            'baz' => array(
                'dojoType' => 'dijit.form.TextBox',
            ),
        );
        $this->helper->setDijits($dijits);
        $test = $this->helper->getDijits();
        $this->assertTrue(is_array($test));
        $this->assertEquals(2, count($test));
        $keys = array();
        foreach ($test as $dijit) {
            $keys[] = $dijit['id'];
        }
        $this->assertSame(array_keys($dijits), $keys);
    }

    public function testRetrievingDijitsByIdShouldReturnJustParams()
    {
        $this->helper->addDijit('foo', array('dojoType' => 'dijit.form.Form'));
        $params = $this->helper->getDijit('foo');
        $this->assertTrue(is_array($params));
        $this->assertEquals(1, count($params), var_export($params, 1));
        $this->assertTrue(array_key_exists('dojoType', $params));
        $this->assertEquals('dijit.form.Form', $params['dojoType']);
    }

    public function testShouldAllowRemovingIndividualDijits()
    {
        $this->helper->addDijit('foo', array('dojoType' => 'dijit.form.Form'));
        $dijits = $this->helper->getDijits();
        $this->assertTrue(is_array($dijits));
        $this->assertEquals(1, count($dijits));
        $this->helper->removeDijit('foo');
        $dijits = $this->helper->getDijits();
        $this->assertTrue(is_array($dijits));
        $this->assertEquals(0, count($dijits));
    }

    public function testShouldAllowClearingAllDijits()
    {
        $this->testShouldAllowAddingMultipleDijitsAtOnce();
        $this->helper->clearDijits();
        $dijits = $this->helper->getDijits();
        $this->assertTrue(is_array($dijits));
        $this->assertEquals(0, count($dijits));
    }

    public function testShouldAllowRetrievingDijitsAsJsonArray()
    {
        $this->testShouldAllowAddingMultipleDijitsAtOnce();
        $json  = $this->helper->dijitsToJson();
        $array = Json::decode($json);
        $this->assertTrue(is_array($array));

        $keys  = array();
        foreach ($array as $dijit) {
            $keys[] = $dijit->id;
            $this->assertTrue(isset($dijit->params));
            $this->assertTrue(is_object($dijit->params));
        }
        $this->assertSame(array('foo', 'bar'), $keys);
    }

    public function testRenderingShouldCreateZendDijitsObjectAndAddOnloadForDijitsWhenDijitsArePresent()
    {
        $this->helper->enable();
        $this->testShouldAllowAddingMultipleDijitsAtOnce();
        $json = $this->helper->dijitsToJson();
        $html = $this->helper->__toString();
        $this->assertContains($json, $html, $html);

        $found = false;
        foreach ($this->helper->_getZendLoadActions() as $action) {
            if (strstr($action, 'dojo.mixin')) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Dijit onload action not created');
        $this->assertContains($action, $html);
    }

    public function testShouldAllowAddingArbitraryJsToPrimaryDojoScriptTag()
    {
        $this->helper->enable();
        $this->helper->addJavascript('var foo = "bar";');
        $html = $this->helper->__toString();
        $found = false;
        if (preg_match_all('|<script[^>]*>(.*?)(</script>)|s', $html, $m)) {
            foreach ($m[1] as $script)  {
                if (strstr($script, 'var foo = "bar";')) {
                    $found = true;
                    break;
                }
            }
        }
        $this->assertTrue($found, 'Js not found: ' . $html);
    }

    public function testShouldAllowClearingArbitraryJsStack()
    {
        $this->testShouldAllowAddingArbitraryJsToPrimaryDojoScriptTag();
        $this->helper->clearJavascript();
        $js = $this->helper->getJavascript();
        $this->assertTrue(is_array($js));
        $this->assertEquals(0, count($js));
    }

    public function testShouldNotAllowAddingDuplicateArbitraryJsToPrimaryDojoScriptTag()
    {
        $this->helper->addJavascript('var foo = "bar";');
        $this->helper->addJavascript('var foo = "bar";');
        $js = $this->helper->getJavascript();
        $this->assertTrue(is_array($js));
        $this->assertEquals(1, count($js), var_export($js, 1));
        $this->assertEquals('var foo = "bar";', $js[0]);
    }

    public function testShouldAllowCapturingArbitraryJsToPrimaryDojoScriptTag()
    {
        $this->helper->javascriptCaptureStart();
        echo 'var foo = "bar";';
        $this->helper->javascriptCaptureEnd();
        $js = $this->helper->getJavascript();
        $this->assertEquals(1, count($js));
        $this->assertContains('var foo = "bar";', $js[0]);
    }

    public function testNoLayersShouldBeRegisteredByDefault()
    {
        $layers = $this->helper->getLayers();
        $this->assertTrue(is_array($layers));
        $this->assertTrue(empty($layers));
    }

    public function testShouldAllowAddingLayers()
    {
        $this->testNoLayersShouldBeRegisteredByDefault();
        $this->helper->addLayer('/js/foo/foo.xd.js');
        $layers = $this->helper->getLayers();
        $this->assertEquals(1, count($layers));
        $this->assertEquals('/js/foo/foo.xd.js', $layers[0]);

        $this->helper->addLayer('/js/bar/bar.xd.js');
        $layers = $this->helper->getLayers();
        $this->assertEquals(2, count($layers));
        $this->assertEquals('/js/foo/foo.xd.js', $layers[0]);
        $this->assertEquals('/js/bar/bar.xd.js', $layers[1]);
    }

    public function testShouldNotAllowDuplicateLayers()
    {
        $this->testShouldAllowAddingLayers();
        $this->helper->addLayer('/js/foo/foo.xd.js');
        $layers = $this->helper->getLayers();
        $this->assertEquals(2, count($layers));
        $this->assertEquals('/js/foo/foo.xd.js', $layers[0]);
        $this->assertEquals('/js/bar/bar.xd.js', $layers[1]);
    }

    public function testShouldAllowRemovingLayers()
    {
        $this->testShouldAllowAddingLayers();
        $this->helper->removeLayer('/js/foo/foo.xd.js');
        $layers = $this->helper->getLayers();
        $this->assertEquals(1, count($layers));
        $this->assertEquals('/js/bar/bar.xd.js', $layers[0]);
    }

    public function testShouldAllowClearingLayers()
    {
        $this->testShouldAllowAddingLayers();
        $this->helper->clearLayers();
        $layers = $this->helper->getLayers();
        $this->assertTrue(is_array($layers));
        $this->assertTrue(empty($layers));
    }

    public function testShouldRenderScriptTagsWithLayersWhenLayersAreRegistered()
    {
        $this->setupDojo();
        $this->testShouldAllowAddingLayers();
        $html = $this->helper->__toString();
        $doc  = new \DOMDocument;
        $doc->loadHTML($html);
        $xPath = new \DOMXPath($doc);
        $results = $xPath->query('//script');

        $found = array();
        for ($i = 0; $i < $results->length; ++$i) {
            $script = $doc->saveXML($results->item($i));
            foreach (array('foo', 'bar') as $layerType) {
                $layer = sprintf('/js/%s/%s.xd.js', $layerType, $layerType);
                if (strstr($script, $layer)) {
                    $found[] = $layerType;
                    break;
                }
            }
        }
        $this->assertSame(array('foo', 'bar'), $found);
    }

    public function testCallingMethodThatDoesNotExistInContainerShouldRaiseException()
    {
        $this->setExpectedException('Zend\Dojo\View\Exception\BadMethodCallException', 'Invalid method ');
        $dojo = new DojoHelper();
        $dojo->bogus();
    }

    public function testShouldAllowSpecifyingDeclarativeUsage()
    {
        DojoHelper::setUseDeclarative();
        $this->assertTrue(DojoHelper::useDeclarative());
    }

    public function testShouldAllowSpecifyingProgrammaticUsageWithNoScriptGeneration()
    {
        DojoHelper::setUseProgrammatic(-1);
        $this->assertTrue(DojoHelper::useProgrammaticNoScript());
    }

    public function testInvalidFlagPassedToUseProgrammaticShouldUseProgrammaticWithScripts()
    {
        DojoHelper::setUseProgrammatic('foo');
        $this->assertFalse(DojoHelper::useProgrammaticNoScript());
        $this->assertTrue(DojoHelper::useProgrammatic());
    }

    /**
     * @group ZF-3962
     */
    public function testHelperShouldAllowDisablingParseOnLoadWithDeclarativeStyle()
    {
        DojoHelper::setUseDeclarative();
        $this->helper->requireModule('dijit.layout.ContentPane')
                     ->setDjConfigOption('parseOnLoad', 'false')
                     ->enable();
        $html = $this->helper->__toString();
        if (!preg_match('/(var djConfig = .*?(?:};))/s', $html, $matches)) {
            $this->fail('Failed to find djConfig settings: ' . $html);
        }
        $this->assertNotContains('"parseOnLoad":true', $matches[1]);
    }

    /**
     * @group ZF-4522
     */
    public function testOnLoadCaptureStartShouldReturnVoid()
    {
        $test = $this->helper->onLoadCaptureStart();
        $this->helper->onLoadCaptureEnd();
        $this->assertNull($test);
    }

    /**
     * @group ZF-4522
     */
    public function testJavascriptCaptureStartShouldReturnVoid()
    {
        $test = $this->helper->javascriptCaptureStart();
        $this->helper->javascriptCaptureEnd();
        $this->assertNull($test);
    }

    /**
     * @group ZF-4587
     * @group ZF-5808
     */
    public function testZendDijitOnLoadMarkupShouldPrecedeAllOtherOnLoadEvents()
    {
        $this->helper->addOnLoad('zend.custom');
        $this->view->plugin('textbox')->__invoke('foo', 'bar');
        $test = $this->helper->__toString();
        $this->assertRegexp('/zendDijits.*?(zend\.custom)/s', $test, 'Generated markup: ' . $test);
    }

    public function testDojoViewHelperContainerAddOptionsPassesOnAllStringOptions() 
    {
        $helper = $this->helper;
        $options = array(
            'requireModules' => 'ZfTestRequiredModule',
            'laYers' => '_added_layer_',
            'cdnBase' => 'ZF-RLZ',
            'cdnVersion' => '1.9.5',
            'cdnDojoPath' => '_cdn_dojo_path_',
            'localPath' => '/srv/ZF/dojo/',
            'stylesheetmodules' => 'test.stylesheet.module',
            'stylesheets' => 'someStyleSheet',
            'registerdojostylesheet' => true
        );

        $helper->setOptions($options);

        $this->assertEquals(array('ZfTestRequiredModule'), $helper->getModules());
        $this->assertEquals(array('_added_layer_'), $helper->getLayers());
        $this->assertEquals('ZF-RLZ', $helper->getCdnBase());
        $this->assertEquals('1.9.5', $helper->getCdnVersion());
        $this->assertEquals('_cdn_dojo_path_', $helper->getCdnDojoPath());
        $this->assertEquals('/srv/ZF/dojo/', $helper->getLocalPath());
        $this->assertEquals(array('test.stylesheet.module'), $helper->getStyleSheetModules());
        $this->assertEquals(array('someStyleSheet'), $helper->getStylesheets());
        $this->assertTrue($helper->registerDojoStylesheet());
    }

    public function testDojoViewHelperContainerAddOptionsPassesOnAllArrayOptions() 
    {
        $helper = $this->helper;
        $modulePaths = array('module1' => 'path1', 'module2' => 'path2');
        $layers = array('layer_two','layer_three');
        $djConfig = array('foo1' => 'bar1', 'foo2' => 'bar2');
        $stylesheetMods = array('test.one.style', 'test.two.style');
        $stylesheets = array('style1', 'style2');
        $options = array(
            'modulePaths'   => $modulePaths,
            'layers'        => $layers,
            'djConfig'      => $djConfig,
            'styleShEEtModules' => $stylesheetMods,
            'stylesheets'   => $stylesheets,
            'registerdojostylesheet' => false
        );

        $helper->setOptions($options);

        $this->assertEquals($modulePaths, $helper->getModulePaths());
        $this->assertEquals($layers, $helper->getLayers());
        $this->assertEquals($djConfig, $helper->getDjConfig());
        $this->assertEquals($stylesheetMods, $helper->getStyleSheetModules());
        $this->assertEquals($stylesheets, $helper->getStylesheets());
        $this->assertFalse($helper->registerDojoStylesheet());
    }

    public function testJsonExpressionRenders()
    {
        $this->helper->addDijit('foo',
                array('dojoType' => 'dijit.form.TextBox',
                      'onChange' => new \Zend\Json\Expr('function(){alert(\'foo\');}'),
                      ));
        $output = $this->helper->dijitsToJson();
        $this->assertRegexp('#(function\\(\\){alert\\(\'foo\'\\);})#', $output);
    }
    
    public function setupDojo()
    {
        $this->helper->requireModule('dijit.layout.ContentPane')
                     ->registerModulePath('custom', '../custom')
                     ->requireModule('custom.foo')
                     ->setCdnVersion('1.1')
                     ->setDjConfig(array('parseOnLoad' => 'true'))
                     ->addStylesheetModule('dijit.themes.tundra')
                     ->addStylesheet('/css/custom.css')
                     ->addOnLoad('foo');
    }
}
