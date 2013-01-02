<?php
/**
* Zend Framework (http://framework.zend.com/)
*
* @link http://github.com/zendframework/zf2 for the canonical source repository
* @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
* @license http://framework.zend.com/license/new-bsd New BSD License
* @package Zend_Di
*/

namespace ZendTest\Di;

use Zend\Di\DefinitionList;
use Zend\Di\Definition\ClassDefinition;

use PHPUnit_Framework_TestCase as TestCase;

class DefinitionListTest extends TestCase
{
    public function testGetClassSupertypes()
    {
        $definitionClassA = new ClassDefinition("A");
        $superTypesA = array("superA");
        $definitionClassA->setSupertypes($superTypesA);

        $definitionClassB = new ClassDefinition("B");
        $definitionClassB->setSupertypes(array("superB"));

        $definitionList = new DefinitionList(array($definitionClassA, $definitionClassB));

        $this->assertEquals($superTypesA, $definitionList->getClassSupertypes("A"));
    }

    public function testHasMethods()
    {
        $definition = $this->getMock('Zend\Di\Definition\ClassDefinition', array(), array(), '', false);

        $definition->expects($this->any())
            ->method('hasClass')
            ->will($this->returnValue(true));

        $definition->expects($this->any())
            ->method('hasMethods')
            ->will($this->returnValue(true));

        $definitionList = new DefinitionList(array($definition));

        $this->assertTrue($definitionList->hasMethods('foo'));
    }

    public function testHasMethodsWhenDefinitionHasNotClass()
    {
        $definition = $this->getMock('Zend\Di\Definition\ClassDefinition', array(), array(), '', false);

        $definition->expects($this->any())
            ->method('hasClass')
            ->will($this->returnValue(false));

        $definitionList = new DefinitionList(array($definition));

        $this->assertFalse($definitionList->hasMethods('foo'));
    }

    public function testHasMethodsWhenDefinitionHasNotMethods()
    {
        $definition = $this->getMock('Zend\Di\Definition\ClassDefinition', array(), array(), '', false);

        $definition->expects($this->any())
            ->method('hasClass')
            ->will($this->returnValue(true));

        $definition->expects($this->any())
            ->method('hasMethods')
            ->will($this->returnValue(false));

        $definitionList = new DefinitionList(array($definition));

        $this->assertFalse($definitionList->hasMethods('foo'));
    }

    public function testHasMethod()
    {
        $definition = $this->getMock('Zend\Di\Definition\ClassDefinition', array(), array(), '', false);

        $definition->expects($this->any())
            ->method('hasClass')
            ->will($this->returnValue(true));

        $definition->expects($this->any())
            ->method('hasMethods')
            ->will($this->returnValue(true));

        $definition->expects($this->any())
            ->method('hasMethod')
            ->will($this->returnValue(true));

        $definitionList = new DefinitionList(array($definition));

        $this->assertTrue($definitionList->hasMethod('foo', 'doFoo'));
    }

    public function testHasMethodWithoutDefinitions()
    {
        $definitionList = new DefinitionList(array());

        $this->assertFalse($definitionList->hasMethod('foo', 'doFoo'));
    }
}
