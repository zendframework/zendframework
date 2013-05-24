<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Hydrator;

use Zend\Stdlib\Hydrator\ClassMethods;
use ZendTest\Stdlib\TestAsset\ClassMethodsOptionalParameters;

/**
 * Unit tests for {@see \Zend\Stdlib\Hydrator\ClassMethods}
 *
 * @covers \Zend\Stdlib\Hydrator\ClassMethods
 * @group Zend_Stdlib
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
}
