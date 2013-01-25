<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Di
 */

namespace ZendTest\Di\Definition;

use Zend\Di\Definition\ArrayDefinition;
use PHPUnit_Framework_TestCase as TestCase;

class ArrayDefinitionTest extends TestCase
{
    /**
     * @var ArrayDefinition
     */
    protected $definition = null;

    public function setup()
    {
        $this->definition = new ArrayDefinition(include __DIR__ . '/../_files/definition-array.php');
    }

    public function testArrayDefinitionHasClasses()
    {
        $this->assertTrue($this->definition->hasClass('My\DbAdapter'));
        $this->assertTrue($this->definition->hasClass('My\EntityA'));
        $this->assertTrue($this->definition->hasClass('My\Mapper'));
        $this->assertTrue($this->definition->hasClass('My\RepositoryA'));
        $this->assertTrue($this->definition->hasClass('My\RepositoryB'));
        $this->assertFalse($this->definition->hasClass('My\Foo'));
    }

    public function testArrayDefinitionHasMethods()
    {
        $this->assertTrue($this->definition->hasMethods('My\Mapper'));
        $this->assertFalse($this->definition->hasMethods('My\EntityA'));
        $this->assertTrue($this->definition->hasMethods('My\Mapper'));
        $this->assertFalse($this->definition->hasMethods('My\RepositoryA'));
        $this->assertFalse($this->definition->hasMethods('My\RepositoryB'));
        $this->assertFalse($this->definition->hasMethods('My\Foo'));
    }

    public function testArrayDefinitionCanGetClassses()
    {
        $list = array(
            'My\DbAdapter',
            'My\EntityA',
            'My\Mapper',
            'My\RepositoryA',
            'My\RepositoryB'
        );

        $classes = $this->definition->getClasses();

        foreach ($list as $class) {
            $this->assertContains($class, $classes);
        }

    }

    public function testArrayDefinitionCanGetClassSupertypes()
    {
        $this->assertEquals(array(), $this->definition->getClassSupertypes('My\EntityA'));
        $this->assertContains('My\RepositoryA', $this->definition->getClassSupertypes('My\RepositoryB'));
    }

    public function testArrayDefinitionCanGetInstantiator()
    {
        $this->assertEquals('__construct', $this->definition->getInstantiator('My\RepositoryA'));
        $this->assertNull($this->definition->getInstantiator('My\Foo'));
    }

    public function testArrayDefinitionHasInjectionMethods()
    {
        $this->markTestIncomplete();
    }

    public function testArrayDefinitionHasInjectionMethod()
    {
        $this->markTestIncomplete();
    }

    public function testArrayDefinitionGetInjectionMethods()
    {
        $this->markTestIncomplete();
    }

    public function testArrayDefinitionGetInjectionMethodParameters()
    {
        $this->markTestIncomplete();
    }
}
