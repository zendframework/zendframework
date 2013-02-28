<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Stdlib
 */

namespace ZendTest\Stdlib;

use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Stdlib\Hydrator\Reflection;
use Zend\Stdlib\Hydrator\ObjectProperty;
use Zend\Stdlib\Hydrator\ArraySerializable;
use Zend\Stdlib\Hydrator\Filter\FilterComposite;
use ZendTest\Stdlib\TestAsset\ClassMethodsCamelCase;
use ZendTest\Stdlib\TestAsset\ClassMethodsFilterProviderInterface;
use ZendTest\Stdlib\TestAsset\ClassMethodsUnderscore;
use ZendTest\Stdlib\TestAsset\ClassMethodsCamelCaseMissing;
use ZendTest\Stdlib\TestAsset\ClassMethodsInvalidParameter;
use ZendTest\Stdlib\TestAsset\Reflection as ReflectionAsset;
use ZendTest\Stdlib\TestAsset\ReflectionFilter;
use ZendTest\Stdlib\TestAsset\ObjectProperty as ObjectPropertyAsset;
use ZendTest\Stdlib\TestAsset\ArraySerializable as ArraySerializableAsset;
use Zend\Stdlib\Hydrator\Strategy\DefaultStrategy;
use Zend\Stdlib\Hydrator\Strategy\SerializableStrategy;


/**
 * @category   Zend
 * @package    Zend_Stdlib
 * @subpackage UnitTests
 * @group      Zend_Stdlib
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
     * @var ClassMethodsInvalidParameter
     */
    protected $classMethodsInvalidParameter;

    /**
     * @var ReflectionAsset
     */
    protected $reflection;

    public function setUp()
    {
        $this->classMethodsCamelCase = new ClassMethodsCamelCase();
        $this->classMethodsCamelCaseMissing = new ClassMethodsCamelCaseMissing();
        $this->classMethodsUnderscore = new ClassMethodsUnderscore();
        $this->classMethodsInvalidParameter = new ClassMethodsInvalidParameter();
        $this->reflection = new ReflectionAsset;
        $this->classMethodsInvalidParameter = new ClassMethodsInvalidParameter();
    }

    public function testInitiateValues()
    {
        $this->assertEquals($this->classMethodsCamelCase->getFooBar(), '1');
        $this->assertEquals($this->classMethodsCamelCase->getFooBarBaz(), '2');
        $this->assertEquals($this->classMethodsCamelCase->getIsFoo(), true);
        $this->assertEquals($this->classMethodsCamelCase->isBar(), true);
        $this->assertEquals($this->classMethodsCamelCase->getHasFoo(), true);
        $this->assertEquals($this->classMethodsCamelCase->hasBar(), true);
        $this->assertEquals($this->classMethodsUnderscore->getFooBar(), '1');
        $this->assertEquals($this->classMethodsUnderscore->getFooBarBaz(), '2');
        $this->assertEquals($this->classMethodsUnderscore->getIsFoo(), true);
        $this->assertEquals($this->classMethodsUnderscore->isBar(), true);
        $this->assertEquals($this->classMethodsUnderscore->getHasFoo(), true);
        $this->assertEquals($this->classMethodsUnderscore->hasBar(), true);
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
        $this->assertTrue(isset($datas['isFoo']));
        $this->assertEquals($datas['isFoo'], true);
        $this->assertTrue(isset($datas['isBar']));
        $this->assertEquals($datas['isBar'], true);
        $this->assertTrue(isset($datas['hasFoo']));
        $this->assertEquals($datas['hasFoo'], true);
        $this->assertTrue(isset($datas['hasBar']));
        $this->assertEquals($datas['hasBar'], true);
        $test = $hydrator->hydrate(
            array(
                'fooBar' => 'foo',
                'fooBarBaz' => 'bar',
                'isFoo' => false,
                'isBar' => false,
                'hasFoo' => false,
                'hasBar' => false,
            ),
            $this->classMethodsCamelCase
        );
        $this->assertSame($this->classMethodsCamelCase, $test);
        $this->assertEquals($test->getFooBar(), 'foo');
        $this->assertEquals($test->getFooBarBaz(), 'bar');
        $this->assertEquals($test->getIsFoo(), false);
        $this->assertEquals($test->isBar(), false);
        $this->assertEquals($test->getHasFoo(), false);
        $this->assertEquals($test->hasBar(), false);
    }

    public function testHydratorClassMethodsUnderscore()
    {
        $hydrator = new ClassMethods(true);
        $datas = $hydrator->extract($this->classMethodsUnderscore);
        $this->assertTrue(isset($datas['foo_bar']));
        $this->assertEquals($datas['foo_bar'], '1');
        $this->assertTrue(isset($datas['foo_bar_baz']));
        $this->assertFalse(isset($datas['fooBar']));
        $this->assertTrue(isset($datas['is_foo']));
        $this->assertFalse(isset($datas['isFoo']));
        $this->assertEquals($datas['is_foo'], true);
        $this->assertTrue(isset($datas['is_bar']));
        $this->assertFalse(isset($datas['isBar']));
        $this->assertEquals($datas['is_bar'], true);
        $this->assertTrue(isset($datas['has_foo']));
        $this->assertFalse(isset($datas['hasFoo']));
        $this->assertEquals($datas['has_foo'], true);
        $this->assertTrue(isset($datas['has_bar']));
        $this->assertFalse(isset($datas['hasBar']));
        $this->assertEquals($datas['has_bar'], true);
        $test = $hydrator->hydrate(
            array(
                'foo_bar' => 'foo',
                'foo_bar_baz' => 'bar',
                'is_foo' => false,
                'is_bar' => false,
                'has_foo' => false,
                'has_bar' => false,
            ),
            $this->classMethodsUnderscore
        );
        $this->assertSame($this->classMethodsUnderscore, $test);
        $this->assertEquals($test->getFooBar(), 'foo');
        $this->assertEquals($test->getFooBarBaz(), 'bar');
        $this->assertEquals($test->getIsFoo(), false);
        $this->assertEquals($test->isBar(), false);
        $this->assertEquals($test->getHasFoo(), false);
        $this->assertEquals($test->hasBar(), false);
    }

    public function testHydratorClassMethodsOptions()
    {
        $hydrator = new ClassMethods();
        $this->assertTrue($hydrator->getUnderscoreSeparatedKeys());
        $hydrator->setOptions(array('underscoreSeparatedKeys' => false));
        $this->assertFalse($hydrator->getUnderscoreSeparatedKeys());
        $hydrator->setUnderscoreSeparatedKeys(true);
        $this->assertTrue($hydrator->getUnderscoreSeparatedKeys());
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

    public function testRetrieveWildStrategyAndOther()
    {
        $hydrator = new ClassMethods();
        $hydrator->addStrategy('default', new DefaultStrategy());
        $hydrator->addStrategy('*', new SerializableStrategy('phpserialize'));
        $default = $hydrator->getStrategy('default');
        $this->assertEquals(get_class($default), 'Zend\Stdlib\Hydrator\Strategy\DefaultStrategy');
        $serializable = $hydrator->getStrategy('*');
        $this->assertEquals(get_class($serializable), 'Zend\Stdlib\Hydrator\Strategy\SerializableStrategy');
    }

    public function testUseWildStrategyByDefault()
    {
        $hydrator = new ClassMethods();
        $datas = $hydrator->extract($this->classMethodsUnderscore);
        $this->assertEquals($datas['foo_bar'], '1');
        $hydrator->addStrategy('*', new SerializableStrategy('phpserialize'));
        $datas = $hydrator->extract($this->classMethodsUnderscore);
        $this->assertEquals($datas['foo_bar'], 's:1:"1";');
    }

    public function testUseWildStrategyAndOther()
    {
        $hydrator = new ClassMethods();
        $datas = $hydrator->extract($this->classMethodsUnderscore);
        $this->assertEquals($datas['foo_bar'], '1');
        $hydrator->addStrategy('foo_bar', new DefaultStrategy());
        $hydrator->addStrategy('*', new SerializableStrategy('phpserialize'));
        $datas = $hydrator->extract($this->classMethodsUnderscore);
        $this->assertEquals($datas['foo_bar'], '1');
        $this->assertEquals($datas['foo_bar_baz'], 's:1:"2";');
    }

    public function testHydratorClassMethodsCamelCaseWithSetterMissing()
    {
        $hydrator = new ClassMethods(false);

        $datas = $hydrator->extract($this->classMethodsCamelCaseMissing);
        $this->assertTrue(isset($datas['fooBar']));
        $this->assertEquals($datas['fooBar'], '1');
        $this->assertTrue(isset($datas['fooBarBaz']));
        $this->assertFalse(isset($datas['foo_bar']));
        $test = $hydrator->hydrate(array('fooBar' => 'foo', 'fooBarBaz' => 1), $this->classMethodsCamelCaseMissing);
        $this->assertSame($this->classMethodsCamelCaseMissing, $test);
        $this->assertEquals($test->getFooBar(), 'foo');
        $this->assertEquals($test->getFooBarBaz(), '2');
    }

    public function testHydratorClassMethodsManipulateFilter()
    {
        $hydrator = new ClassMethods(false);
        $datas = $hydrator->extract($this->classMethodsCamelCase);

        $this->assertTrue(isset($datas['fooBar']));
        $this->assertEquals($datas['fooBar'], '1');
        $this->assertTrue(isset($datas['fooBarBaz']));
        $this->assertFalse(isset($datas['foo_bar']));
        $this->assertTrue(isset($datas['isFoo']));
        $this->assertEquals($datas['isFoo'], true);
        $this->assertTrue(isset($datas['isBar']));
        $this->assertEquals($datas['isBar'], true);
        $this->assertTrue(isset($datas['hasFoo']));
        $this->assertEquals($datas['hasFoo'], true);
        $this->assertTrue(isset($datas['hasBar']));
        $this->assertEquals($datas['hasBar'], true);

        $hydrator->removeFilter('has');
        $datas = $hydrator->extract($this->classMethodsCamelCase);
        $this->assertTrue(isset($datas['hasFoo'])); //method is getHasFoo
        $this->assertFalse(isset($datas['hasBar'])); //method is hasBar
    }

    public function testHydratorClassMethodsWithCustomFilter()
    {
        $hydrator = new ClassMethods(false);
        $datas = $hydrator->extract($this->classMethodsCamelCase);
        $hydrator->addFilter("exclude",
            function($property) {
                list($class, $method) = explode('::', $property);

                if ($method == 'getHasFoo') {
                    return false;
                }

                return true;
            }, FilterComposite::CONDITION_AND
        );

        $datas = $hydrator->extract($this->classMethodsCamelCase);
        $this->assertFalse(isset($datas['hasFoo']));
    }

    /**
     * @dataProvider filterProvider
     */
    public function testArraySerializableFilter($hydrator, $serializable)
    {
        $this->assertSame(
            array(
                "foo" => "bar",
                "bar" => "foo",
                "blubb" => "baz",
                "quo" => "blubb"
            ),
            $hydrator->extract($serializable)
        );

        $hydrator->addFilter("foo", function($property) {
                if ($property == "foo") {
                    return false;
                }
                return true;
            });

        $this->assertSame(
            array(
                "bar" => "foo",
                "blubb" => "baz",
                "quo" => "blubb"
            ),
            $hydrator->extract($serializable)
        );

        $hydrator->addFilter("len", function($property) {
                if (strlen($property) !== 3) {
                    return false;
                }
                return true;
            }, FilterComposite::CONDITION_AND);

        $this->assertSame(
            array(
                "bar" => "foo",
                "quo" => "blubb"
            ),
            $hydrator->extract($serializable)
        );

        $hydrator->removeFilter("len");
        $hydrator->removeFilter("foo");

        $this->assertSame(
            array(
                "foo" => "bar",
                "bar" => "foo",
                "blubb" => "baz",
                "quo" => "blubb"
            ),
            $hydrator->extract($serializable)
        );
    }

    public function filterProvider()
    {
        return array(
            array(new ObjectProperty(), new ObjectPropertyAsset),
            array(new ArraySerializable(), new ArraySerializableAsset),
            array(new Reflection(), new ReflectionFilter)
        );
    }

    public function testHydratorClassMethodsWithInvalidNumberOfParameters()
    {
        $hydrator = new ClassMethods(false);
        $datas = $hydrator->extract($this->classMethodsInvalidParameter);

        $this->assertTrue($datas['hasBar']);
        $this->assertEquals('Bar', $datas['foo']);
        $this->assertFalse($datas['isBla']);
    }

    public function testObjectBasedFilters()
    {
        $hydrator = new ClassMethods(false);
        $foo = new ClassMethodsFilterProviderInterface();
        $data = $hydrator->extract($foo);
        $this->assertFalse(array_key_exists("filter", $data));
        $this->assertSame("bar", $data["foo"]);
        $this->assertSame("foo", $data["bar"]);
    }
}
