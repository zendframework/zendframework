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
 * @package    Zend_Stdlib
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id:$
 */

namespace ZendTest\Stdlib;

use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Stdlib\Hydrator\Reflection;
use ZendTest\Stdlib\TestAsset\ClassMethodsCamelCase;
use ZendTest\Stdlib\TestAsset\ClassMethodsUnderscore;
use ZendTest\Stdlib\TestAsset\ClassMethodsCamelCaseMissing;
use ZendTest\Stdlib\TestAsset\Reflection as ReflectionAsset;

/**
 * @category   Zend
 * @package    Zend_Stdlib
 * @subpackage UnitTests
 * @group      Zend_Stdlib
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class HydratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ClassMethodsCamelCase
     */
    protected $classMethodsCamelCase;

    /**
     * @var ClassMethodsCamelCaseMissing
     */
    protected $classMethodsCamelCaseMissing;

    /**
     * @var ClassMethodsUnderscore
     */
    protected $classMethodsUnderscore;

    /**
     * @var ReflectionAsset
     */
    protected $reflection;

    public function setUp()
    {
        $this->classMethodsCamelCase = new ClassMethodsCamelCase();
        $this->classMethodsCamelCaseMissing = new ClassMethodsCamelCaseMissing();
        $this->classMethodsUnderscore = new ClassMethodsUnderscore();
        $this->reflection = new ReflectionAsset;
    }
    
    public function testInitiateValues()
    {
        $this->assertEquals($this->classMethodsCamelCase->getFooBar(), '1');
        $this->assertEquals($this->classMethodsCamelCase->getFooBarBaz(), '2');
        $this->assertEquals($this->classMethodsUnderscore->getFooBar(), '1');
        $this->assertEquals($this->classMethodsUnderscore->getFooBarBaz(), '2');
    }

    public function testHydratorReflection()
    {
        $hydrator = new Reflection;
        $datas    = $hydrator->extract($this->reflection);
        $this->assertTrue(isset($datas['foo']));
        $this->assertEquals($datas['foo'], '1');
        $this->assertTrue(isset($datas['fooBar']));
        $this->assertEquals($datas['fooBar'], '2');
        $this->assertTrue(isset($datas['fooBarBaz']));
        $this->assertEquals($datas['fooBarBaz'], '3');

        $test = $hydrator->hydrate(array('foo' => 'foo', 'fooBar' => 'bar', 'fooBarBaz' => 'baz'), $this->reflection);
        $this->assertEquals($test->foo, 'foo');
        $this->assertEquals($test->getFooBar(), 'bar');
        $this->assertEquals($test->getFooBarBaz(), 'baz');
    }

    public function testHydratorClassMethodsCamelCase()
    {
        $hydrator = new ClassMethods(false);
        $datas = $hydrator->extract($this->classMethodsCamelCase);
        $this->assertTrue(isset($datas['fooBar']));
        $this->assertEquals($datas['fooBar'], '1');
        $this->assertTrue(isset($datas['fooBarBaz']));
        $this->assertFalse(isset($datas['foo_bar']));
        $test = $hydrator->hydrate(array('fooBar' => 'foo', 'fooBarBaz' => 'bar'), $this->classMethodsCamelCase);
        $this->assertSame($this->classMethodsCamelCase, $test);
        $this->assertEquals($test->getFooBar(), 'foo');
        $this->assertEquals($test->getFooBarBaz(), 'bar');
    }
    
    public function testHydratorClassMethodsCamelCaseWithSetterMissing()
    {
        $hydrator = new ClassMethods(false);
        $datas = $hydrator->extract($this->classMethodsCamelCaseMissing);
        $this->assertTrue(isset($datas['fooBar']));
        $this->assertEquals($datas['fooBar'], '1');
        $this->assertFalse(isset($datas['fooBarBaz']));
        $this->assertFalse(isset($datas['foo_bar']));
        $test = $hydrator->hydrate(array('fooBar' => 'foo'), $this->classMethodsCamelCaseMissing);
        $this->assertSame($this->classMethodsCamelCaseMissing, $test);
        $this->assertEquals($test->getFooBar(), 'foo');
        $this->assertEquals($test->getFooBarBaz(), '2');
    }
    
    public function testHydratorClassMethodsUnderscore()
    {
        $hydrator = new ClassMethods(true);
        $datas = $hydrator->extract($this->classMethodsUnderscore);
        $this->assertTrue(isset($datas['foo_bar']));
        $this->assertEquals($datas['foo_bar'], '1');
        $this->assertTrue(isset($datas['foo_bar_baz']));
        $this->assertFalse(isset($datas['fooBar']));
        $test = $hydrator->hydrate(array('foo_bar' => 'foo', 'foo_bar_baz' => 'bar'), $this->classMethodsUnderscore);
        $this->assertSame($this->classMethodsUnderscore, $test);
        $this->assertEquals($test->getFooBar(), 'foo');
        $this->assertEquals($test->getFooBarBaz(), 'bar');
    }

    public function testHydratorClassMethodsIgnoresInvalidValues()
    {
        $hydrator = new ClassMethods(true);
        $data = array(
            'foo_bar' => '1',
            'foo_bar_baz' => '2',
            'invalid' => 'value'
        );
        $test = $hydrator->hydrate($data, $this->classMethodsUnderscore);
        $this->assertSame($this->classMethodsUnderscore, $test);
    }

    public function testHydratorClassMethodsDefaultBehaviorIsConvertUnderscoreToCamelCase()
    {
        $hydrator = new ClassMethods();
        $datas = $hydrator->extract($this->classMethodsUnderscore);
        $this->assertTrue(isset($datas['foo_bar']));
        $this->assertEquals($datas['foo_bar'], '1');
        $this->assertTrue(isset($datas['foo_bar_baz']));
        $this->assertFalse(isset($datas['fooBar']));
        $test = $hydrator->hydrate(array('foo_bar' => 'foo', 'foo_bar_baz' => 'bar'), $this->classMethodsUnderscore);
        $this->assertSame($this->classMethodsUnderscore, $test);
        $this->assertEquals($test->getFooBar(), 'foo');
        $this->assertEquals($test->getFooBarBaz(), 'bar');
    }
}
