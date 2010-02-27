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
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


// Call Zend_Filter_InflectorTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Filter_InflectorTest::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Filter_Inflector
 */
require_once 'Zend/Filter/Inflector.php';

/**
 * @see Zend_Config
 */
require_once 'Zend/Config.php';


/**
 * Test class for Zend_Filter_Inflector.
 *
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class Zend_Filter_InflectorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite('Zend_Filter_InflectorTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->inflector = new Zend_Filter_Inflector();
        $this->loader    = $this->inflector->getPluginLoader();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->loader->clearPaths();
    }

    public function testGetPluginLoaderReturnsLoaderByDefault()
    {
        $loader = $this->inflector->getPluginLoader();
        $this->assertTrue($loader instanceof Zend_Loader_PluginLoader_Interface);
        $paths = $loader->getPaths();
        $this->assertEquals(1, count($paths));
        $this->assertTrue(array_key_exists('Zend_Filter_', $paths));
    }

    public function testSetPluginLoaderAllowsSettingAlternatePluginLoader()
    {
        $defaultLoader = $this->inflector->getPluginLoader();
        $loader = new Zend_Loader_PluginLoader();
        $this->inflector->setPluginLoader($loader);
        $receivedLoader = $this->inflector->getPluginLoader();
        $this->assertNotSame($defaultLoader, $receivedLoader);
        $this->assertSame($loader, $receivedLoader);
    }

    public function testAddFilterPrefixPathAddsPathsToPluginLoader()
    {
        $this->inflector->addFilterPrefixPath('Foo_Bar', 'Zend/View/');
        $loader = $this->inflector->getPluginLoader();
        $paths  = $loader->getPaths();
        $this->assertTrue(array_key_exists('Foo_Bar_', $paths));
    }

    public function testTargetAccessorsWork()
    {
        $this->inflector->setTarget('foo/:bar/:baz');
        $this->assertEquals('foo/:bar/:baz', $this->inflector->getTarget());
    }

    public function testTargetInitiallyNull()
    {
        $this->assertNull($this->inflector->getTarget());
    }

    public function testPassingTargetToConstructorSetsTarget()
    {
        $inflector = new Zend_Filter_Inflector('foo/:bar/:baz');
        $this->assertEquals('foo/:bar/:baz', $inflector->getTarget());
    }

    public function testSetTargetByReferenceWorks()
    {
        $target = 'foo/:bar/:baz';
        $this->inflector->setTargetReference($target);
        $this->assertEquals('foo/:bar/:baz', $this->inflector->getTarget());
        $target .= '/:bat';
        $this->assertEquals('foo/:bar/:baz/:bat', $this->inflector->getTarget());
    }

    public function testSetFilterRuleWithStringRuleCreatesRuleEntryAndFilterObject()
    {
        $rules = $this->inflector->getRules();
        $this->assertEquals(0, count($rules));
        $this->inflector->setFilterRule('controller', 'PregReplace');
        $rules = $this->inflector->getRules('controller');
        $this->assertEquals(1, count($rules));
        $filter = $rules[0];
        $this->assertTrue($filter instanceof Zend_Filter_Interface);
    }

    public function testSetFilterRuleWithFilterObjectCreatesRuleEntryWithFilterObject()
    {
        $rules = $this->inflector->getRules();
        $this->assertEquals(0, count($rules));
        $filter = new Zend_Filter_PregReplace();
        $this->inflector->setFilterRule('controller', $filter);
        $rules = $this->inflector->getRules('controller');
        $this->assertEquals(1, count($rules));
        $received = $rules[0];
        $this->assertTrue($received instanceof Zend_Filter_Interface);
        $this->assertSame($filter, $received);
    }

    public function testSetFilterRuleWithArrayOfRulesCreatesRuleEntries()
    {
        $rules = $this->inflector->getRules();
        $this->assertEquals(0, count($rules));
        $this->inflector->setFilterRule('controller', array('PregReplace', 'Alpha'));
        $rules = $this->inflector->getRules('controller');
        $this->assertEquals(2, count($rules));
        $this->assertTrue($rules[0] instanceof Zend_Filter_Interface);
        $this->assertTrue($rules[1] instanceof Zend_Filter_Interface);
    }

    public function testAddFilterRuleAppendsRuleEntries()
    {
        $rules = $this->inflector->getRules();
        $this->assertEquals(0, count($rules));
        $this->inflector->setFilterRule('controller', 'PregReplace');
        $rules = $this->inflector->getRules('controller');
        $this->assertEquals(1, count($rules));
        $this->inflector->addFilterRule('controller', 'Alpha');
        $rules = $this->inflector->getRules('controller');
        $this->assertEquals(2, count($rules));
    }

    public function testSetStaticRuleCreatesScalarRuleEntry()
    {
        $rules = $this->inflector->getRules();
        $this->assertEquals(0, count($rules));
        $this->inflector->setStaticRule('controller', 'foobar');
        $rules = $this->inflector->getRules('controller');
        $this->assertEquals('foobar', $rules);
    }

    public function testSetStaticRuleMultipleTimesOverwritesEntry()
    {
        $rules = $this->inflector->getRules();
        $this->assertEquals(0, count($rules));
        $this->inflector->setStaticRule('controller', 'foobar');
        $rules = $this->inflector->getRules('controller');
        $this->assertEquals('foobar', $rules);
        $this->inflector->setStaticRule('controller', 'bazbat');
        $rules = $this->inflector->getRules('controller');
        $this->assertEquals('bazbat', $rules);
    }

    public function testSetStaticRuleReferenceAllowsUpdatingRuleByReference()
    {
        $rule  = 'foobar';
        $rules = $this->inflector->getRules();
        $this->assertEquals(0, count($rules));
        $this->inflector->setStaticRuleReference('controller', $rule);
        $rules = $this->inflector->getRules('controller');
        $this->assertEquals('foobar', $rules);
        $rule .= '/baz';
        $rules = $this->inflector->getRules('controller');
        $this->assertEquals('foobar/baz', $rules);
    }

    public function testAddRulesCreatesAppropriateRuleEntries()
    {
        $rules = $this->inflector->getRules();
        $this->assertEquals(0, count($rules));
        $this->inflector->addRules(array(
            ':controller' => array('PregReplace', 'Alpha'),
            'suffix'      => 'phtml',
        ));
        $rules = $this->inflector->getRules();
        $this->assertEquals(2, count($rules));
        $this->assertEquals(2, count($rules['controller']));
        $this->assertEquals('phtml', $rules['suffix']);
    }

    public function testSetRulesCreatesAppropriateRuleEntries()
    {
        $this->inflector->setStaticRule('some-rules', 'some-value');
        $rules = $this->inflector->getRules();
        $this->assertEquals(1, count($rules));
        $this->inflector->setRules(array(
            ':controller' => array('PregReplace', 'Alpha'),
            'suffix'      => 'phtml',
        ));
        $rules = $this->inflector->getRules();
        $this->assertEquals(2, count($rules));
        $this->assertEquals(2, count($rules['controller']));
        $this->assertEquals('phtml', $rules['suffix']);
    }

    public function testGetRule()
    {
        $this->inflector->setFilterRule(':controller', array('Alpha', 'StringToLower'));
        $this->assertTrue($this->inflector->getRule('controller', 1) instanceof Zend_Filter_StringToLower);
        $this->assertFalse($this->inflector->getRule('controller', 2));
    }

    public function testFilterTransformsStringAccordingToRules()
    {
        $this->inflector->setTarget(':controller/:action.:suffix')
             ->addRules(array(
                 ':controller' => array('Word_CamelCaseToDash'),
                 ':action'     => array('Word_CamelCaseToDash'),
                 'suffix'      => 'phtml'
             ));
        $filtered = $this->inflector->filter(array(
            'controller' => 'FooBar',
            'action'     => 'bazBat'
        ));
        $this->assertEquals('Foo-Bar/baz-Bat.phtml', $filtered);
    }

    public function testTargetReplacementIdentiferAccessorsWork()
    {
        $this->assertEquals(':', $this->inflector->getTargetReplacementIdentifier());
        $this->inflector->setTargetReplacementIdentifier('?=');
        $this->assertEquals('?=', $this->inflector->getTargetReplacementIdentifier());
    }

    public function testTargetReplacementIdentiferWorksWhenInflected()
    {
        $this->inflector = new Zend_Filter_Inflector(
            '?=##controller/?=##action.?=##suffix',
            array(
                 ':controller' => array('Word_CamelCaseToDash'),
                 ':action'     => array('Word_CamelCaseToDash'),
                 'suffix'      => 'phtml'
                 ),
            null,
            '?=##'
            );

        $filtered = $this->inflector->filter(array(
            'controller' => 'FooBar',
            'action'     => 'bazBat'
        ));

        $this->assertEquals('Foo-Bar/baz-Bat.phtml', $filtered);
    }

    public function testThrowTargetExceptionsAccessorsWork()
    {
        $this->assertEquals(':', $this->inflector->getTargetReplacementIdentifier());
        $this->inflector->setTargetReplacementIdentifier('?=');
        $this->assertEquals('?=', $this->inflector->getTargetReplacementIdentifier());
    }

    public function testThrowTargetExceptionsOnAccessorsWork()
    {
        $this->assertTrue($this->inflector->isThrowTargetExceptionsOn());
        $this->inflector->setThrowTargetExceptionsOn(false);
        $this->assertFalse($this->inflector->isThrowTargetExceptionsOn());
    }

    public function testTargetExceptionThrownWhenTargetSourceNotSatisfied()
    {
        $this->inflector = new Zend_Filter_Inflector(
            '?=##controller/?=##action.?=##suffix',
            array(
                 ':controller' => array('Word_CamelCaseToDash'),
                 ':action'     => array('Word_CamelCaseToDash'),
                 'suffix'      => 'phtml'
                 ),
            true,
            '?=##'
            );

        try {
            $filtered = $this->inflector->filter(array('controller' => 'FooBar'));
            $this->fail('Exception was not thrown when it was suppose to be.');
        } catch (Exception $e) {
            $this->assertTrue($e instanceof Zend_Filter_Exception);
        }
    }

    public function testTargetExceptionNotThrownOnIdentifierNotFollowedByCharacter()
    {
        $this->inflector = new Zend_Filter_Inflector(
            'e:\path\to\:controller\:action.:suffix',
            array(
                 ':controller' => array('Word_CamelCaseToDash', 'StringToLower'),
                 ':action'     => array('Word_CamelCaseToDash'),
                 'suffix'      => 'phtml'
                ),
            true,
            ':'
            );

        try {
            $filtered = $this->inflector->filter(array('controller' => 'FooBar', 'action' => 'MooToo'));
            $this->assertEquals($filtered, 'e:\path\to\foo-bar\Moo-Too.phtml');
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function getOptions()
    {
        $options = array(
            'target' => '$controller/$action.$suffix',
            'filterPrefixPath' => array(
                'Zend_View_Filter' => 'Zend/View/Filter/',
                'Foo_Filter'       => 'foo/filters/'
            ),
            'throwTargetExceptionsOn' => true,
            'targetReplacementIdentifier' => '$',
            'rules' => array(
                ':controller' => array(
                    'rule1' => 'Word_CamelCaseToUnderscore',
                    'rule2' => 'StringToLower',
                ),
                ':action' => array(
                    'rule1' => 'Word_CamelCaseToDash',
                    'rule2' => 'StringToUpper',
                ),
                'suffix' => 'php'
            ),
        );
        return $options;
    }

    public function getConfig()
    {
        $options = $this->getOptions();
        return new Zend_Config($options);
    }

    protected function _testOptions($inflector)
    {
        $options = $this->getOptions();
        $loader  = $inflector->getPluginLoader();
        $this->assertEquals($options['target'], $inflector->getTarget());

        $viewFilterPath = $loader->getPaths('Zend_View_Filter');
        $this->assertEquals($options['filterPrefixPath']['Zend_View_Filter'], $viewFilterPath[0]);
        $fooFilterPath = $loader->getPaths('Foo_Filter');
        $this->assertEquals($options['filterPrefixPath']['Foo_Filter'], $fooFilterPath[0]);

        $this->assertTrue($inflector->isThrowTargetExceptionsOn());

        $this->assertEquals($options['targetReplacementIdentifier'], $inflector->getTargetReplacementIdentifier());

        $rules = $inflector->getRules();
        foreach (array_values($options['rules'][':controller']) as $key => $rule) {
            $class = get_class($rules['controller'][$key]);
            $this->assertContains($rule, $class);
        }
        foreach (array_values($options['rules'][':action']) as $key => $rule) {
            $class = get_class($rules['action'][$key]);
            $this->assertContains($rule, $class);
        }
        $this->assertEquals($options['rules']['suffix'], $rules['suffix']);
    }

    public function testPassingConfigObjectToConstructorSetsStateAndRules()
    {
        $config = $this->getConfig();
        $inflector = new Zend_Filter_Inflector($config);
        $this->_testOptions($inflector);
    }

    public function testSetConfigSetsStateAndRules()
    {
        $config = $this->getConfig();
        $inflector = new Zend_Filter_Inflector();
        $inflector->setConfig($config);
        $this->_testOptions($inflector);
    }

    /**
     * Added str_replace('\\', '\\\\', ..) to all processedParts values to disable backreferences
     *
     * @issue ZF-2538 Zend_Filter_Inflector::filter() fails with all numeric folder on Windows
     */
    public function testCheckInflectorWithPregBackreferenceLikeParts()
    {

        $this->inflector = new Zend_Filter_Inflector(
            ':moduleDir' . DIRECTORY_SEPARATOR . ':controller' . DIRECTORY_SEPARATOR . ':action.:suffix',
            array(
                ':controller' => array('Word_CamelCaseToDash', 'StringToLower'),
                ':action'     => array('Word_CamelCaseToDash'),
                'suffix'      => 'phtml'
                ),
            true,
            ':'
            );

        $this->inflector->setStaticRule('moduleDir', 'C:\htdocs\public\cache\00\01\42\app\modules');

        try {
            $filtered = $this->inflector->filter(array(
                'controller' => 'FooBar',
                'action' => 'MooToo'
                ));
            $this->assertEquals($filtered, 'C:\htdocs\public\cache\00\01\42\app\modules' . DIRECTORY_SEPARATOR . 'foo-bar' . DIRECTORY_SEPARATOR . 'Moo-Too.phtml');
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * @issue ZF-2522
     */
    public function testTestForFalseInConstructorParams()
    {
        $inflector = new Zend_Filter_Inflector('something', array(), false, false);
        $this->assertFalse($inflector->isThrowTargetExceptionsOn());
        $this->assertEquals($inflector->getTargetReplacementIdentifier(), ':');

        $inflector = new Zend_Filter_Inflector('something', array(), false, '#');
        $this->assertEquals($inflector->getTargetReplacementIdentifier(), '#');
    }

    /**
     * @issue ZF-2964
     */
    public function testNoInflectableTarget()
    {
        $inflector = new Zend_Filter_Inflector('abc');
        $inflector->addRules(array(':foo' => array()));
        $this->assertEquals($inflector->filter(array('fo' => 'bar')), 'abc');
    }

    /**
     * @issue ZF-7544
     */
    public function testAddFilterRuleMultipleTimes()
    {
        $rules = $this->inflector->getRules();
        $this->assertEquals(0, count($rules));
        $this->inflector->setFilterRule('controller', 'PregReplace');
        $rules = $this->inflector->getRules('controller');
        $this->assertEquals(1, count($rules));
        $this->inflector->addFilterRule('controller', array('Alpha', 'StringToLower'));
        $rules = $this->inflector->getRules('controller');
        $this->assertEquals(3, count($rules));
        $this->_context = 'StringToLower';
        $this->inflector->setStaticRuleReference('context' , $this->_context);
        $this->inflector->addFilterRule('controller', array('Alpha', 'StringToLower'));
        $rules = $this->inflector->getRules('controller');
        $this->assertEquals(5, count($rules));
    }

    /**
     * @group ZF-8997
     */
    public function testPassingArrayToConstructorSetsStateAndRules()
    {
        $options = $this->getOptions();
        $inflector = new Zend_Filter_Inflector($options);
        $this->_testOptions($inflector);
    }

    /**
     * @group ZF-8997
     */
    public function testPassingArrayToSetConfigSetsStateAndRules()
    {
        $options = $this->getOptions();
        $inflector = new Zend_Filter_Inflector();
        $inflector->setOptions($options);
        $this->_testOptions($inflector);
    }

    /**
     * @group ZF-8997
     */
    public function testPassingZendConfigObjectToConstructorSetsStateAndRules()
    {
        $config = $this->getConfig();
        $inflector = new Zend_Filter_Inflector($config);
        $this->_testOptions($inflector);
    }

    /**
     * @group ZF-8997
     */
    public function testPassingZendConfigObjectToSetConfigSetsStateAndRules()
    {
        $config = $this->getConfig();
        $inflector = new Zend_Filter_Inflector();
        $inflector->setOptions($config);
        $this->_testOptions($inflector);
    }
}

// Call Zend_Filter_InflectorTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Zend_Filter_InflectorTest::main') {
    Zend_Filter_InflectorTest::main();
}
