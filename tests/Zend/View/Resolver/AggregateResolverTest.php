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
 * @package    Zend_View
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\View\Resolver;

use ArrayObject,
    PHPUnit_Framework_TestCase as TestCase,
    Zend\View\Resolver;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AggregateResolverTest extends TestCase
{
    public function testAggregateIsEmptyByDefault()
    {
        $resolver = new Resolver\AggregateResolver();
        $this->assertEquals(0, count($resolver));
    }

    public function testCanAttachResolvers()
    {
        $resolver = new Resolver\AggregateResolver();
        $resolver->attach(new Resolver\TemplateMapResolver);
        $this->assertEquals(1, count($resolver));
        $resolver->attach(new Resolver\TemplateMapResolver);
        $this->assertEquals(2, count($resolver));
    }

    public function testReturnsNonFalseValueWhenAtLeastOneResolverSucceeds()
    {
        $resolver = new Resolver\AggregateResolver();
        $resolver->attach(new Resolver\TemplateMapResolver(array(
            'foo' => 'bar',
        )));
        $resolver->attach(new Resolver\TemplateMapResolver(array(
            'bar' => 'baz',
        )));
        $test = $resolver->resolve('bar');
        $this->assertEquals('baz', $test);
    }

    public function testLastSuccessfulResolverIsNullInitially()
    {
        $resolver = new Resolver\AggregateResolver();
        $this->assertNull($resolver->getLastSuccessfulResolver());
    }

    public function testCanAccessResolverThatLastSucceeded()
    {
        $resolver = new Resolver\AggregateResolver();
        $fooResolver = new Resolver\TemplateMapResolver(array(
            'foo' => 'bar',
        ));
        $barResolver = new Resolver\TemplateMapResolver(array(
            'bar' => 'baz',
        ));
        $bazResolver = new Resolver\TemplateMapResolver(array(
            'baz' => 'bat',
        ));
        $resolver->attach($fooResolver)
                 ->attach($barResolver)
                 ->attach($bazResolver);

        $test = $resolver->resolve('bar');
        $this->assertEquals('baz', $test);
        $this->assertSame($barResolver, $resolver->getLastSuccessfulResolver());
    }

    public function testReturnsFalseWhenNoResolverSucceeds()
    {
        $resolver = new Resolver\AggregateResolver();
        $resolver->attach(new Resolver\TemplateMapResolver(array(
            'foo' => 'bar',
        )));
        $this->assertFalse($resolver->resolve('bar'));
        $this->assertEquals(Resolver\AggregateResolver::FAILURE_NOT_FOUND, $resolver->getLastLookupFailure());
    }

    public function testLastSuccessfulResolverIsNullWhenNoResolverSucceeds()
    {
        $resolver    = new Resolver\AggregateResolver();
        $fooResolver = new Resolver\TemplateMapResolver(array(
            'foo' => 'bar',
        ));
        $resolver->attach($fooResolver);
        $test = $resolver->resolve('foo');
        $this->assertSame($fooResolver, $resolver->getLastSuccessfulResolver());

        try {
            $test = $resolver->resolve('bar');
            $this->fail('Should not have resolved!');
        } catch (\Exception $e) {
            // exception is expected
        }
        $this->assertNull($resolver->getLastSuccessfulResolver());
    }

    public function testResolvesInOrderOfPriorityProvided()
    {
        $resolver = new Resolver\AggregateResolver();
        $fooResolver = new Resolver\TemplateMapResolver(array(
            'bar' => 'foo',
        ));
        $barResolver = new Resolver\TemplateMapResolver(array(
            'bar' => 'bar',
        ));
        $bazResolver = new Resolver\TemplateMapResolver(array(
            'bar' => 'baz',
        ));
        $resolver->attach($fooResolver, -1)
                 ->attach($barResolver, 100)
                 ->attach($bazResolver);

        $test = $resolver->resolve('bar');
        $this->assertEquals('bar', $test);
    }

    public function testReturnsFalseWhenAttemptingToResolveWhenNoResolversAreAttached()
    {
        $resolver = new Resolver\AggregateResolver();
        $this->assertFalse($resolver->resolve('foo'));
        $this->assertEquals(Resolver\AggregateResolver::FAILURE_NO_RESOLVERS, $resolver->getLastLookupFailure());
    }
}
