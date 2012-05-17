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
use ZendTest\Stdlib\TestAsset\ClassMethodsCamelCase,
    ZendTest\Stdlib\TestAsset\ClassMethodsUnderscore,
    ZendTest\Stdlib\TestAsset\ClassMethodsCamelCaseMissing;

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
    public function setUp()
    {
        $this->classMethodsCamelCase = new ClassMethodsCamelCase();
        $this->classMethodsCamelCaseMissing = new ClassMethodsCamelCaseMissing();
        $this->classMethodsUnderscore = new ClassMethodsUnderscore();
    }
    
    public function testInitiateValues()
    {
        $this->assertEquals($this->classMethodsCamelCase->getFooBar(), '1');
        $this->assertEquals($this->classMethodsCamelCase->getFooBarBaz(), '2');
        $this->assertEquals($this->classMethodsUnderscore->getFooBar(), '1');
        $this->assertEquals($this->classMethodsUnderscore->getFooBarBaz(), '2');
    }

    public function testHydratorClassMethodsCamelCase()
    {
        $hydrator = new ClassMethods(true);
        $datas = $hydrator->extract($this->classMethodsCamelCase);
        $this->assertTrue(isset($datas['fooBar']));
        $this->assertEquals($datas['fooBar'], '1');
        $this->assertTrue(isset($datas['fooBarBaz']));
        $this->assertFalse(isset($datas['foo_bar']));
        $hydrator->hydrate(array('fooBar' => 'foo', 'fooBarBaz' => 'bar'), $this->classMethodsCamelCase);
        $this->assertEquals($this->classMethodsCamelCase->getFooBar(), 'foo');
        $this->assertEquals($this->classMethodsCamelCase->getFooBarBaz(), 'bar');
    }
    
    public function testHydratorClassMethodsCamelCaseWithSetterMissing()
    {
        $hydrator = new ClassMethods(true);
        $datas = $hydrator->extract($this->classMethodsCamelCaseMissing);
        $this->assertTrue(isset($datas['fooBar']));
        $this->assertEquals($datas['fooBar'], '1');
        $this->assertFalse(isset($datas['fooBarBaz']));
        $this->assertFalse(isset($datas['foo_bar']));
        $hydrator->hydrate(array('fooBar' => 'foo'), $this->classMethodsCamelCaseMissing);
        $this->assertEquals($this->classMethodsCamelCaseMissing->getFooBar(), 'foo');
        $this->assertEquals($this->classMethodsCamelCaseMissing->getFooBarBaz(), '2');
    }
    
    public function testHydratorClassMethodsUnderscore()
    {
        $hydrator = new ClassMethods(false);
        $datas = $hydrator->extract($this->classMethodsUnderscore);
        $this->assertTrue(isset($datas['foo_bar']));
        $this->assertEquals($datas['foo_bar'], '1');
        $this->assertTrue(isset($datas['foo_bar_baz']));
        $this->assertFalse(isset($datas['fooBar']));
        $hydrator->hydrate(array('foo_bar' => 'foo', 'foo_bar_baz' => 'bar'), $this->classMethodsUnderscore);
        $this->assertEquals($this->classMethodsUnderscore->getFooBar(), 'foo');
        $this->assertEquals($this->classMethodsUnderscore->getFooBarBaz(), 'bar');
    }
}
