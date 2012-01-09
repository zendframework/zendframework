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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Filter;

use Zend\Filter\Inflector as InflectorFilter,
    Zend\Filter\FilterBroker,
    Zend\Loader\Broker,
    Zend\Loader\PluginBroker;

/**
 * Test class for Zend_Filter_Inflector.
 *
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class InflectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->inflector = new InflectorFilter();
        $this->broker    = $this->inflector->getPluginBroker();
    }

    public function testGetPluginBrokerReturnsFilterBrokerByDefault()
    {
        $broker = $this->inflector->getPluginBroker();
        $this->assertTrue($broker instanceof FilterBroker);
    }

    public function testSetPluginBrokerAllowsSettingAlternatePluginBroker()
    {
        $defaultBroker = $this->inflector->getPluginBroker();
        $broker = new PluginBroker();
        $this->inflector->setPluginBroker($broker);
        $receivedBroker = $this->inflector->getPluginBroker();
        $this->assertNotSame($defaultBroker, $receivedBroker);
        $this->assertSame($broker, $receivedBroker);
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
        $inflector = new InflectorFilter('foo/:bar/:baz');
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
        $this->assertTrue($filter instanceof \Zend\Filter\Filter);
    }

    public function testSetFilterRuleWithFilterObjectCreatesRuleEntryWithFilterObject()
    {
        $rules = $this->inflector->getRules();
        $this->assertEquals(0, count($rules));
        $filter = new \Zend\Filter\PregReplace();
        $this->inflector->setFilterRule('controller', $filter);
        $rules = $this->inflector->getRules('controller');
        $this->assertEquals(1, count($rules));
        $received = $rules[0];
        $this->assertTrue($received instanceof \Zend\Filter\Filter);
        $this->assertSame($filter, $received);
    }

    public function testSetFilterRuleWithArrayOfRulesCreatesRuleEntries()
    {
        $rules = $this->inflector->getRules();
        $this->assertEquals(0, count($rules));
        $this->inflector->setFilterRule('controller', array('PregReplace', 'Alpha'));
        $rules = $this->inflector->getRules('controller');
        $this->assertEquals(2, count($rules));
        $this->assertTrue($rules[0] instanceof \Zend\Filter\Filter);
        $this->assertTrue($rules[1] instanceof \Zend\Filter\Filter);
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
        $this->assertTrue($this->inflector->getRule('controller', 1) instanceof \Zend\Filter\StringToLower);
        $this->assertFalse($this->inflector->getRule('controller', 2));
    }

    public function testFilterTransformsStringAccordingToRules()
    {
        $this->inflector->setTarget(':controller/:action.:suffix')
             ->addRules(array(
                 ':controller' => array('Word\\CamelCaseToDash'),
                 ':action'     => array('Word\\CamelCaseToDash'),
                 'suffix'      => 'phtml'
             ));
        $filter = $this->inflector;
        $filtered = $filter(array(
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
        $inflector = new InflectorFilter(
            '?=##controller/?=##action.?=##suffix',
            array(
                 ':controller' => array('Word\\CamelCaseToDash'),
                 ':action'     => array('Word\\CamelCaseToDash'),
                 'suffix'      => 'phtml'
                 ),
            null,
            '?=##'
            );

        $filtered = $inflector(array(
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
        $inflector = new InflectorFilter(
            '?=##controller/?=##action.?=##suffix',
            array(
                 ':controller' => array('Word\\CamelCaseToDash'),
                 ':action'     => array('Word\\CamelCaseToDash'),
                 'suffix'      => 'phtml'
                 ),
            true,
            '?=##'
            );

        $this->setExpectedException('\Zend\Filter\Exception\RuntimeException', 'perhaps a rule was not satisfied');
        $filtered = $inflector(array('controller' => 'FooBar'));
    }

    public function testTargetExceptionNotThrownOnIdentifierNotFollowedByCharacter()
    {
        $inflector = new InflectorFilter(
            'e:\path\to\:controller\:action.:suffix',
            array(
                 ':controller' => array('Word\\CamelCaseToDash', 'StringToLower'),
                 ':action'     => array('Word\\CamelCaseToDash'),
                 'suffix'      => 'phtml'
                ),
            true,
            ':'
            );

        $filtered = $inflector(array('controller' => 'FooBar', 'action' => 'MooToo'));
        $this->assertEquals($filtered, 'e:\path\to\foo-bar\Moo-Too.phtml');
    }

    public function getOptions()
    {
        $options = array(
            'target' => '$controller/$action.$suffix',
            'throwTargetExceptionsOn' => true,
            'targetReplacementIdentifier' => '$',
            'rules' => array(
                ':controller' => array(
                    'rule1' => 'Word\\CamelCaseToUnderscore',
                    'rule2' => 'StringToLower',
                ),
                ':action' => array(
                    'rule1' => 'Word\\CamelCaseToDash',
                    'rule2' => 'StringToUpper',
                ),
                'suffix' => 'php'
            ),
            'filterPrefixPath' => array(
                'Zend\\View\\Filter' => 'Zend/View/Filter/',
                'Foo\\Filter'        => 'foo/filters/'
            ),
        );
        return $options;
    }

    public function getConfig()
    {
        $options = $this->getOptions();
        return new \Zend\Config\Config($options);
    }

    protected function _testOptions($inflector)
    {
        $options = $this->getOptions();
        $broker  = $inflector->getPluginBroker();
        $this->assertEquals($options['target'], $inflector->getTarget());

        $this->assertInstanceOf('Zend\Filter\FilterBroker', $broker);
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
        $inflector = new InflectorFilter($config);
        $this->_testOptions($inflector);
    }

    public function testSetConfigSetsStateAndRules()
    {
        $config = $this->getConfig();
        $inflector = new InflectorFilter();
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
        $inflector = new InflectorFilter(
            ':moduleDir' . DIRECTORY_SEPARATOR . ':controller' . DIRECTORY_SEPARATOR . ':action.:suffix',
            array(
                ':controller' => array('Word\\CamelCaseToDash', 'StringToLower'),
                ':action'     => array('Word\\CamelCaseToDash'),
                'suffix'      => 'phtml'
                ),
            true,
            ':'
            );

        $inflector->setStaticRule('moduleDir', 'C:\htdocs\public\cache\00\01\42\app\modules');

        $filtered = $inflector(array(
            'controller' => 'FooBar',
            'action' => 'MooToo'
            ));
        $this->assertEquals($filtered, 'C:\htdocs\public\cache\00\01\42\app\modules' . DIRECTORY_SEPARATOR . 'foo-bar' . DIRECTORY_SEPARATOR . 'Moo-Too.phtml');
    }

    /**
     * @issue ZF-2522
     */
    public function testTestForFalseInConstructorParams()
    {
        $inflector = new InflectorFilter('something', array(), false, false);
        $this->assertFalse($inflector->isThrowTargetExceptionsOn());
        $this->assertEquals($inflector->getTargetReplacementIdentifier(), ':');

        $inflector = new InflectorFilter('something', array(), false, '#');
    }

    /**
     * @issue ZF-2964
     */
    public function testNoInflectableTarget()
    {
        $inflector = new InflectorFilter('abc');
        $inflector->addRules(array(':foo' => array()));
        $this->assertEquals($inflector(array('fo' => 'bar')), 'abc');
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
        $inflector = new InflectorFilter($options);
    }

    /**
     * @group ZF-8997
     */
    public function testPassingArrayToSetConfigSetsStateAndRules()
    {
        $options = $this->getOptions();
        $inflector = new InflectorFilter();
        $inflector->setOptions($options);
        $this->_testOptions($inflector);
    }

    /**
     * @group ZF-8997
     */
    public function testPassingZendConfigObjectToConstructorSetsStateAndRules()
    {
        $config = $this->getConfig();
        $inflector = new InflectorFilter($config);
        $this->_testOptions($inflector);
    }

    /**
     * @group ZF-8997
     */
    public function testPassingZendConfigObjectToSetConfigSetsStateAndRules()
    {
        $config = $this->getConfig();
        $inflector = new InflectorFilter();
        $inflector->setOptions($config);
        $this->_testOptions($inflector);
    }
}
