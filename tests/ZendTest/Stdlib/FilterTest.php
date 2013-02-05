<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link           http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright      Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license        http://framework.zend.com/license/new-bsd New BSD License
 * @package        Zend_Service
 */

namespace ZendTest\Stdlib;

use Zend\Stdlib\Hydrator\Filter\HasFilter,
    Zend\Stdlib\Hydrator\Filter\IsFilter,
    Zend\Stdlib\Hydrator\Filter\GetFilter;

class ValidationTest extends \PHPUnit_Framework_TestCase
{
    public function testHasValidation()
    {
        $hasValidation = new HasFilter();
        $this->assertTrue($hasValidation->filter('hasFoo'));
        $this->assertTrue($hasValidation->filter('Foo::hasFoo'));
        $this->assertFalse($hasValidation->filter('FoohasFoo'));
        $this->assertFalse($hasValidation->filter('Bar::FoohasFoo'));
        $this->assertFalse($hasValidation->filter('hAsFoo'));
        $this->assertFalse($hasValidation->filter('Blubb::hAsFoo'));
        $this->assertFalse($hasValidation->filter(get_class($this). '::hAsFoo'));
    }

    public function testGetValidation()
    {
        $hasValidation = new GetFilter();
        $this->assertTrue($hasValidation->filter('getFoo'));
        $this->assertTrue($hasValidation->filter('Bar::getFoo'));
        $this->assertFalse($hasValidation->filter('GetFooBar'));
        $this->assertFalse($hasValidation->filter('Foo::GetFooBar'));
        $this->assertFalse($hasValidation->filter('GETFoo'));
        $this->assertFalse($hasValidation->filter('Blubb::GETFoo'));
        $this->assertFalse($hasValidation->filter(get_class($this).'::GETFoo'));
    }

    public function testIsValidation()
    {
        $hasValidation = new IsFilter();
        $this->assertTrue($hasValidation->filter('isFoo'));
        $this->assertTrue($hasValidation->filter('Blubb::isFoo'));
        $this->assertFalse($hasValidation->filter('IsFooBar'));
        $this->assertFalse($hasValidation->filter('Foo::IsFooBar'));
        $this->assertFalse($hasValidation->filter('ISFoo'));
        $this->assertFalse($hasValidation->filter('Bar::ISFoo'));
        $this->assertFalse($hasValidation->filter(get_class($this).'::ISFoo'));
    }

}
