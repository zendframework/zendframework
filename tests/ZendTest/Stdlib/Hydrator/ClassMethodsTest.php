<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Hydrator;

use Zend\Stdlib\Hydrator\ClassMethods;
use ZendTest\Stdlib\TestAsset\ClassMethodsCamelCaseMissing;
use ZendTest\Stdlib\TestAsset\ClassMethodsOptionalParameters;
use ZendTest\Stdlib\TestAsset\ClassMethodsCamelCase;
use ZendTest\Stdlib\TestAsset\ArraySerializable;

/**
 * Unit tests for {@see \Zend\Stdlib\Hydrator\ClassMethods}
 *
 * @covers \Zend\Stdlib\Hydrator\ClassMethods
 */
class ClassMethodsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClassMethods
     */
    protected $hydrator;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->hydrator = new ClassMethods();
    }

    /**
     * Verifies that extraction can happen even when a getter has parameters if those are all optional
     */
    public function testCanExtractFromMethodsWithOptionalParameters()
    {
        $this->assertSame(array('foo' => 'bar'), $this->hydrator->extract(new ClassMethodsOptionalParameters()));
    }

    /**
     * Verifies that the hydrator can act on different instance types
     */
    public function testCanHydratedPromiscuousInstances()
    {
        /* @var $classMethodsCamelCase ClassMethodsCamelCase */
        $classMethodsCamelCase = $this->hydrator->hydrate(
            array('fooBar' => 'baz-tab'),
            new ClassMethodsCamelCase()
        );
        /* @var $classMethodsCamelCaseMissing ClassMethodsCamelCaseMissing */
        $classMethodsCamelCaseMissing = $this->hydrator->hydrate(
            array('fooBar' => 'baz-tab'),
            new ClassMethodsCamelCaseMissing()
        );
        /* @var $arraySerializable ArraySerializable */
        $arraySerializable = $this->hydrator->hydrate(array('fooBar' => 'baz-tab'), new ArraySerializable());

        $this->assertSame('baz-tab', $classMethodsCamelCase->getFooBar());
        $this->assertSame('baz-tab', $classMethodsCamelCaseMissing->getFooBar());
        $this->assertSame(
            array(
                "foo" => "bar",
                "bar" => "foo",
                "blubb" => "baz",
                "quo" => "blubb"
            ),
            $arraySerializable->getArrayCopy()
        );
    }
}
