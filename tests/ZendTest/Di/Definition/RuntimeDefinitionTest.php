<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Di
 */

namespace ZendTest\Di\Definition;

use PHPUnit_Framework_TestCase as TestCase;

use Zend\Di\Definition\RuntimeDefinition;

class RuntimeDefinitionTest extends TestCase
{
    /**
     * @group ZF2-308
     */
    public function testStaticMethodsNotIncludedInDefinitions()
    {
        $definition = new RuntimeDefinition;
        $this->assertTrue($definition->hasMethod('ZendTest\Di\TestAsset\SetterInjection\StaticSetter', 'setFoo'));
        $this->assertFalse($definition->hasMethod('ZendTest\Di\TestAsset\SetterInjection\StaticSetter', 'setName'));
    }

    public function testIncludesDefaultMethodParameters()
    {
        $definition = new RuntimeDefinition();

        $definition->forceLoadClass('ZendTest\Di\TestAsset\ConstructorInjection\OptionalParameters');

        $this->assertSame(
            array(
                'ZendTest\Di\TestAsset\ConstructorInjection\OptionalParameters::__construct:0' => array(
                    'a',
                    null,
                    false,
                    null,
                ),
                'ZendTest\Di\TestAsset\ConstructorInjection\OptionalParameters::__construct:1' => array(
                    'b',
                    null,
                    false,
                    'defaultConstruct',
                ),
                'ZendTest\Di\TestAsset\ConstructorInjection\OptionalParameters::__construct:2' => array(
                    'c',
                    null,
                    false,
                    array(),
                ),
            ),
            $definition->getMethodParameters(
                'ZendTest\Di\TestAsset\ConstructorInjection\OptionalParameters',
                '__construct'
            )
        );
    }
}
