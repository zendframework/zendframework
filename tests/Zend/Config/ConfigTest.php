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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Config;

use \Zend\Config\Config;

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Config
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    protected $iniFileConfig;
    protected $iniFileNested;

    public function setUp()
    {
        // Arrays representing common config configurations
        $this->all = array(
            'hostname' => 'all',
            'name' => 'thisname',
            'db' => array(
                'host' => '127.0.0.1',
                'user' => 'username',
                'pass' => 'password',
                'name' => 'live'
                ),
            'one' => array(
                'two' => array(
                    'three' => 'multi'
                    )
                )
            );

        $this->numericData = array(
             0 => 34,
             1 => 'test',
            );

        $this->menuData1 = array(
            'button' => array(
                'b0' => array(
                    'L1' => 'button0-1',
                    'L2' => 'button0-2',
                    'L3' => 'button0-3'
                ),
                'b1' => array(
                    'L1' => 'button1-1',
                    'L2' => 'button1-2'
                ),
                'b2' => array(
                    'L1' => 'button2-1'
                    )
                )
            );

        $this->leadingdot = array('.test' => 'dot-test');
        $this->invalidkey = array(' ' => 'test', ''=>'test2');

    }

    public function testLoadSingleSection()
    {
        $config = new Config($this->all, false);

        $this->assertEquals('all', $config->hostname);
        $this->assertEquals('live', $config->db->name);
        $this->assertEquals('multi', $config->one->two->three);
        $this->assertNull($config->nonexistent); // property doesn't exist
    }

    public function testIsset()
    {
        $config = new Config($this->all, false);

        $this->assertFalse(isset($config->notarealkey));
        $this->assertTrue(isset($config->hostname)); // top level
        $this->assertTrue(isset($config->db->name)); // one level down
    }

    public function testModification()
    {
        $config = new Config($this->all, true);

        // overwrite an existing key
        $this->assertEquals('thisname', $config->name);
        $config->name = 'anothername';
        $this->assertEquals('anothername', $config->name);

        // overwrite an existing multi-level key
        $this->assertEquals('multi', $config->one->two->three);
        $config->one->two->three = 'anothername';
        $this->assertEquals('anothername', $config->one->two->three);

        // create a new multi-level key
        $config->does = array('not'=> array('exist' => 'yet'));
        $this->assertEquals('yet', $config->does->not->exist);

    }

    public function testNoModifications()
    {
        $this->setExpectedException('Zend\Config\Exception\RuntimeException', 'Config is read only');
        $config = new Config($this->all);
        $config->hostname = 'test';
    }

    public function testNoNestedModifications()
    {
        $this->setExpectedException('Zend\Config\Exception\RuntimeException', 'Config is read only');
        $config = new Config($this->all);
        $config->db->host = 'test';
    }

    public function testNumericKeys()
    {
        $data = new Config($this->numericData);
        $this->assertEquals('test', $data->{1});
        $this->assertEquals(34, $data->{0});
    }

    public function testCount()
    {
        $data = new Config($this->menuData1);
        $this->assertEquals(3, count($data->button));
    }

    public function testIterator()
    {
        // top level
        $config = new Config($this->all);
        $var = '';
        foreach ($config as $key=>$value) {
            if (is_string($value)) {
                $var .= "\nkey = $key, value = $value";
            }
        }
        $this->assertContains('key = name, value = thisname', $var);

        // 1 nest
        $var = '';
        foreach ($config->db as $key=>$value) {
            $var .= "\nkey = $key, value = $value";
        }
        $this->assertContains('key = host, value = 127.0.0.1', $var);

        // 2 nests
        $config = new Config($this->menuData1);
        $var = '';
        foreach ($config->button->b1 as $key=>$value) {
            $var .= "\nkey = $key, value = $value";
        }
        $this->assertContains('key = L1, value = button1-1', $var);
    }

    public function testArray()
    {
        $config = new Config($this->all);

        ob_start();
        print_r($config->toArray());
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertContains('Array', $contents);
        $this->assertContains('[hostname] => all', $contents);
        $this->assertContains('[user] => username', $contents);
    }

    public function testErrorWriteToReadOnly()
    {
        $this->setExpectedException('Zend\Config\Exception\RuntimeException', 'Config is read only');
        $config = new Config($this->all);
        $config->test = '32';
    }

    public function testZF343()
    {
        $config_array = array(
            'controls' => array(
                'visible' => array(
                    'name' => 'visible',
                    'type' => 'checkbox',
                    'attribs' => array(), // empty array
                ),
            ),
        );
        $form_config = new Config($config_array, true);
        $this->assertSame(array(), $form_config->controls->visible->attribs->toArray());
    }

    public function testZF402()
    {
        $configArray = array(
            'data1'  => 'someValue',
            'data2'  => 'someValue',
            'false1' => false,
            'data3'  => 'someValue'
            );
        $config = new Config($configArray);
        $this->assertTrue(count($config) === count($configArray));
        $count = 0;
        foreach ($config as $key => $value) {
            if ($key === 'false1') {
                $this->assertTrue($value === false);
            } else {
                $this->assertTrue($value === 'someValue');
            }
            $count++;
        }
        $this->assertTrue($count === 4);
    }

    public function testZf1019_HandlingInvalidKeyNames()
    {
        $config = new Config($this->leadingdot);
        $array = $config->toArray();
        $this->assertContains('dot-test', $array['.test']);
    }

    public function testZF1019_EmptyKeys()
    {
        $config = new Config($this->invalidkey);
        $array = $config->toArray();
        $this->assertContains('test', $array[' ']);
        $this->assertContains('test', $array['']);
    }

    public function testZF1417_DefaultValues()
    {
        $config = new Config($this->all);
        $value = $config->get('notthere', 'default');
        $this->assertTrue($value === 'default');
        $this->assertTrue($config->notThere === null);

    }

    public function testUnsetException()
    {
        // allow modifications is off - expect an exception
        $config = new Config($this->all, false);

        $this->assertTrue(isset($config->hostname)); // top level

        $this->setExpectedException('Zend\Config\Exception\InvalidArgumentException', 'is read only');
        unset($config->hostname);
    }

    public function testUnset()
    {
        // allow modifications is on
        $config = new Config($this->all, true);

        $this->assertTrue(isset($config->hostname));
        $this->assertTrue(isset($config->db->name));

        unset($config->hostname);
        unset($config->db->name);

        $this->assertFalse(isset($config->hostname));
        $this->assertFalse(isset($config->db->name));

    }

    public function testMerge()
    {
        $stdArray = array(
            'test_feature' => false,
            'some_files' => array(
                'foo'=>'dir/foo.xml',
                'bar'=>'dir/bar.xml',
            ),
            2 => 123,
        );
        $stdConfig = new Config($stdArray, true);

        $devArray = array(
            'test_feature'=>true,
            'some_files' => array(
               'bar' => 'myDir/bar.xml',
               'baz' => 'myDir/baz.xml',
            ),
            2 => 456,
        );
        $devConfig = new Config($devArray);

        $stdConfig->merge($devConfig);

        $this->assertTrue($stdConfig->test_feature);
        $this->assertEquals('myDir/bar.xml', $stdConfig->some_files->bar);
        $this->assertEquals('myDir/baz.xml', $stdConfig->some_files->baz);
        $this->assertEquals('dir/foo.xml', $stdConfig->some_files->foo);
        $this->assertEquals(456, $stdConfig->{2});

    }
    
    public function testArrayAccess()
    {
        $config = new Config($this->all, true);

        $this->assertEquals('thisname', $config['name']);
        $config['name'] = 'anothername';
        $this->assertEquals('anothername', $config['name']);
        $this->assertEquals('multi', $config['one']['two']['three']);

        $this->assertTrue(isset($config['hostname']));
        $this->assertTrue(isset($config['db']['name']));

        unset($config['hostname']);
        unset($config['db']['name']);

        $this->assertFalse(isset($config['hostname']));
        $this->assertFalse(isset($config['db']['name']));
    }

    /**
     * Ensures that toArray() supports objects of types other than Zend_Config
     *
     * @return void
     */
    public function testToArraySupportsObjects()
    {
        $configData = array(
            'a' => new \stdClass(),
            'b' => array(
                'c' => new \stdClass(),
                'd' => new \stdClass()
                )
            );
        $config = new Config($configData);
        $this->assertEquals($config->toArray(), $configData);
        $this->assertInstanceOf('stdClass', $config->a);
        $this->assertInstanceOf('stdClass', $config->b->c);
        $this->assertInstanceOf('stdClass', $config->b->d);
    }

    /**
     * ensure that modification is not allowed after calling setReadOnly()
     *
     */
    public function testSetReadOnly()
    {
        $configData = array(
            'a' => 'a'
            );
        $config = new Config($configData, true);
        $config->b = 'b';

        $config->setReadOnly();
        $this->setExpectedException('Zend\Config\Exception\RuntimeException', 'Config is read only');
        $config->c = 'c';
    }

    public function testZF3408_countNotDecreasingOnUnset()
    {
        $configData = array(
            'a' => 'a',
            'b' => 'b',
            'c' => 'c',
            );
        $config = new Config($configData, true);
        $this->assertEquals(count($config), 3);
        unset($config->b);
        $this->assertEquals(count($config), 2);
    }

    public function testZF4107_ensureCloneDoesNotKeepNestedReferences()
    {
        $parent = new Config(array('key' => array('nested' => 'parent')), true);
        $newConfig = clone $parent;
        $newConfig->merge(new Config(array('key' => array('nested' => 'override')), true));

        $this->assertEquals('override', $newConfig->key->nested, '$newConfig is not overridden');
        $this->assertEquals('parent', $parent->key->nested, '$parent has been overridden');

    }

    /**
     * @group ZF-3575
     *
     */
    public function testMergeHonoursAllowModificationsFlagAtAllLevels()
    {
        $config = new Config(array('key' => array('nested' => 'yes'), 'key2'=>'yes'), false);
        $config2 = new Config(array(), true);

        $config2->merge($config);
        try {
            $config2->key2 = 'no';
        }  catch (\Zend\Config\Exception\RuntimeException $e) {
            $this->fail('Unexpected exception at top level has been raised: ' . $e->getMessage());
        }
        $this->assertEquals('no', $config2->key2);

        try {
            $config2->key->nested = 'no';
        }  catch (\Zend\Config\Exception\RuntimeException $e) {
            $this->fail('Unexpected exception on nested object has been raised: ' . $e->getMessage());
        }
        $this->assertEquals('no', $config2->key->nested);

    }

    /**
     * @group ZF-5771a
     *
     */
    public function testUnsettingFirstElementDuringForeachDoesNotSkipAnElement()
    {
        $config = new Config(array(
            'first'  => array(1),
            'second' => array(2),
            'third'  => array(3)
        ), true);

        $keyList = array();
        foreach ($config as $key => $value)
        {
            $keyList[] = $key;
            if ($key == 'first') {
                unset($config->$key); // uses magic Zend\Config\Config::__unset() method
            }
        }

        $this->assertEquals('first', $keyList[0]);
        $this->assertEquals('second', $keyList[1]);
        $this->assertEquals('third', $keyList[2]);
    }

    /**
     * @group ZF-5771
     *
     */
    public function testUnsettingAMiddleElementDuringForeachDoesNotSkipAnElement()
    {
        $config = new Config(array(
            'first'  => array(1),
            'second' => array(2),
            'third'  => array(3)
        ), true);

        $keyList = array();
        foreach ($config as $key => $value)
        {
            $keyList[] = $key;
            if ($key == 'second') {
                unset($config->$key); // uses magic Zend\Config\Config::__unset() method
            }
        }

        $this->assertEquals('first', $keyList[0]);
        $this->assertEquals('second', $keyList[1]);
        $this->assertEquals('third', $keyList[2]);
    }

    /**
     * @group ZF-5771
     *
     */
    public function testUnsettingLastElementDuringForeachDoesNotSkipAnElement()
    {
        $config = new Config(array(
            'first'  => array(1),
            'second' => array(2),
            'third'  => array(3)
        ), true);

        $keyList = array();
        foreach ($config as $key => $value)
        {
            $keyList[] = $key;
            if ($key == 'third') {
                unset($config->$key); // uses magic Zend\Config\Config::__unset() method
            }
        }

        $this->assertEquals('first', $keyList[0]);
        $this->assertEquals('second', $keyList[1]);
        $this->assertEquals('third', $keyList[2]);
    }

    /**
     * @group ZF-4728
     *
     */
    public function testSetReadOnlyAppliesToChildren()
    {
        $config = new Config($this->all, true);

        $config->setReadOnly();
        $this->assertTrue($config->isReadOnly());
        $this->assertTrue($config->one->isReadOnly(), 'First level children are writable');
        $this->assertTrue($config->one->two->isReadOnly(), 'Second level children are writable');
    }

    public function testZF6995_toArrayDoesNotDisturbInternalIterator()
    {
        $config = new Config(range(1,10));
        $config->rewind();
        $this->assertEquals(1, $config->current());

        $config->toArray();
        $this->assertEquals(1, $config->current());
    }
}

