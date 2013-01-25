<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Di
 */

namespace ZendTest\Di\ServiceLocator;

use Zend\Di\Di;
use Zend\Di\Config;
use Zend\Di\ServiceLocator\Generator as ContainerGenerator;
use Zend\Di\Definition\BuilderDefinition as Definition;
use Zend\Di\Definition\Builder;
use PHPUnit_Framework_TestCase as TestCase;

class GeneratorTest extends TestCase
{
    protected $tmpFile = false;

    /**
     * @var \Zend\Di\Di
     */
    protected $di = null;

    public function setUp()
    {
        $this->tmpFile = false;
        $this->di = new Di;
    }

    public function tearDown()
    {
        if ($this->tmpFile) {
            unlink($this->tmpFile);
            $this->tmpFile = false;
        }
    }

    public function getTmpFile()
    {
        $this->tmpFile = tempnam(sys_get_temp_dir(), 'zdi');
        return $this->tmpFile;
    }

    public function createDefinitions()
    {
        $inspect = new Builder\PhpClass();
        $inspect->setName('ZendTest\Di\TestAsset\InspectedClass');
        $inspectCtor = new Builder\InjectionMethod();
        $inspectCtor->setName('__construct')
                    ->addParameter('foo', 'composed')
                    ->addParameter('baz', null);
        $inspect->addInjectionMethod($inspectCtor);

        $composed = new Builder\PhpClass();
        $composed->setName('ZendTest\Di\TestAsset\ComposedClass');

        $struct = new Builder\PhpClass();
        $struct->setName('ZendTest\Di\TestAsset\Struct');
        $structCtor = new Builder\InjectionMethod();
        $structCtor->setName('__construct')
                   ->addParameter('param1', null)
                   ->addParameter('param2', 'inspect');

        $definition = new Definition();
        $definition->addClass($inspect)
                   ->addClass($composed)
                   ->addClass($struct);
        $this->di->definitions()->unshift($definition);

        $data = array(
            'instance' => array(
                'alias' => array(
                    'composed' => 'ZendTest\Di\TestAsset\ComposedClass',
                    'inspect'  => 'ZendTest\Di\TestAsset\InspectedClass',
                    'struct'   => 'ZendTest\Di\TestAsset\Struct',
                ),
                'preferences' => array(
                    'composed' => array('composed'),
                    'inspect'  => array('inspect'),
                    'struct'   => array('struct'),
                ),
                'ZendTest\Di\TestAsset\InspectedClass' => array( 'parameters' => array(
                    'baz' => 'BAZ',
                )),
                'ZendTest\Di\TestAsset\Struct' => array( 'parameters' => array(
                    'param1' => 'foo',
                )),
            ),
        );
        $configuration = new Config($data);
        $configuration->configure($this->di);
    }

    public function buildContainerClass($name = 'Application')
    {
        $this->createDefinitions();
        $builder = new ContainerGenerator($this->di);
        $builder->setContainerClass($name);
        $builder->getCodeGenerator($this->getTmpFile())->write();
        $this->assertFileExists($this->tmpFile);
    }

    /**
     * @group one
     */
    public function testCreatesContainerClassFromConfiguredDependencyInjector()
    {
        $this->buildContainerClass();

        $tokens = token_get_all(file_get_contents($this->tmpFile));
        $count  = count($tokens);
        $found  = false;
        $value  = false;
        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[$i];
            if (is_string($token)) {
                continue;
            }
            if (T_CLASS == $token[0]) {
                $found = true;
                $value = false;
                do {
                    $i++;
                    $token = $tokens[$i];
                    if (is_string($token)) {
                        $id = null;
                    } else {
                        list($id, $value) = $token;
                    }
                } while (($i < $count) && ($id != T_STRING));
                break;
            }
        }
        $this->assertTrue($found, "Class token not found");
        $this->assertContains('Application', $value);
    }

    public function testCreatesContainerClassWithCasesForEachService()
    {
        $this->buildContainerClass();

        $tokens   = token_get_all(file_get_contents($this->tmpFile));
        $count    = count($tokens);
        $services = array();
        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[$i];
            if (is_string($token)) {
                continue;
            }
            if ('T_CASE' == token_name($token[0])) {
                do {
                    $i++;
                    if ($i >= $count) {
                        break;
                    }
                    $token = $tokens[$i];
                    if (is_string($token)) {
                        $id = $token;
                    } else {
                        $id = $token[0];
                    }
                } while (($i < $count) && ($id != T_CONSTANT_ENCAPSED_STRING));
                if (is_array($token)) {
                    $services[] = preg_replace('/\\\'/', '', $token[1]);
                }
            }
        }
        $expected = array(
            'composed',
            'ZendTest\Di\TestAsset\ComposedClass',
            'inspect',
            'ZendTest\Di\TestAsset\InspectedClass',
            'struct',
            'ZendTest\Di\TestAsset\Struct',
        );
        $this->assertEquals(count($expected), count($services), var_export($services, 1));
        foreach ($expected as $service) {
            $this->assertContains($service, $services);
        }
    }

    public function testCreatesContainerClassWithMethodsForEachServiceAndAlias()
    {
        $this->buildContainerClass();
        $tokens  = token_get_all(file_get_contents($this->tmpFile));
        $count   = count($tokens);
        $methods = array();
        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[$i];
            if (is_string($token)) {
                continue;
            }
            if ("T_FUNCTION" == token_name($token[0])) {
                $value = false;
                do {
                    $i++;
                    $token = $tokens[$i];
                    if (is_string($token)) {
                        $id = null;
                    } else {
                        list($id, $value) = $token;
                    }
                } while (($i < $count) && (token_name($id) != 'T_STRING'));
                if ($value) {
                    $methods[] = $value;
                }
            }
        }
        $expected = array(
            'get',
            'getZendTestDiTestAssetComposedClass',
            'getComposed',
            'getZendTestDiTestAssetInspectedClass',
            'getInspect',
            'getZendTestDiTestAssetStruct',
            'getStruct',
        );
        $this->assertEquals(count($expected), count($methods), var_export($methods, 1));
        foreach ($expected as $method) {
            $this->assertContains($method, $methods);
        }
    }

    public function testAllowsRetrievingClassFileCodeGenerationObject()
    {
        $this->createDefinitions();
        $builder = new ContainerGenerator($this->di);
        $builder->setContainerClass('Application');
        $codegen = $builder->getCodeGenerator();
        $this->assertInstanceOf('Zend\Code\Generator\FileGenerator', $codegen);
    }

    public function testCanSpecifyNamespaceForGeneratedPhpClassfile()
    {
        $this->createDefinitions();
        $builder = new ContainerGenerator($this->di);
        $builder->setContainerClass('Context')
                ->setNamespace('Application');
        $codegen = $builder->getCodeGenerator();
        $this->assertEquals('Application', $codegen->getNamespace());
    }

    /**
     * @group nullargs
     */
    public function testNullAsOnlyArgumentResultsInEmptyParameterList()
    {
        $this->markTestIncomplete('Null arguments are currently unsupported');
        $opt = new Builder\PhpClass();
        $opt->setName('ZendTest\Di\TestAsset\OptionalArg');
        $optCtor = new Builder\InjectionMethod();
        $optCtor->setName('__construct')
                ->addParameter('param', null);
        $opt->addInjectionMethod($optCtor);
        $def = new Definition();
        $def->addClass($opt);
        $this->di->setDefinition($def);

        $cfg = new Config(array(
            'instance' => array(
                'alias' => array('optional' => 'ZendTest\Di\TestAsset\OptionalArg'),
            ),
            'properties' => array(
                'ZendTest\Di\TestAsset\OptionalArg' => array('param' => null),
            ),
        ));
        $cfg->configure($this->di);

        $builder = new ContainerGenerator($this->di);
        $builder->setContainerClass('Container');
        $codeGen = $builder->getCodeGenerator();
        $classBody = $codeGen->generate();
        $this->assertNotContains('NULL)', $classBody, $classBody);
    }

    /**
     * @group nullargs
     */
    public function testNullAsLastArgumentsResultsInTruncatedParameterList()
    {
        $this->markTestIncomplete('Null arguments are currently unsupported');
        $struct = new Builder\PhpClass();
        $struct->setName('ZendTest\Di\TestAsset\Struct');
        $structCtor = new Builder\InjectionMethod();
        $structCtor->setName('__construct')
                   ->addParameter('param1', null)
                   ->addParameter('param2', null);
        $struct->addInjectionMethod($structCtor);

        $dummy = new Builder\PhpClass();
        $dummy->setName('ZendTest\Di\TestAsset\DummyParams')
              ->setInstantiator(array('ZendTest\Di\TestAsset\StaticFactory', 'factory'));

        $staticFactory = new Builder\PhpClass();
        $staticFactory->setName('ZendTest\Di\TestAsset\StaticFactory');
        $factory = new Builder\InjectionMethod();
        $factory->setName('factory')
                ->addParameter('struct', 'struct')
                ->addParameter('params', null);
        $staticFactory->addInjectionMethod($factory);

        $def = new Definition();
        $def->addClass($struct)
            ->addClass($dummy)
            ->addClass($staticFactory);

        $this->di->setDefinition($def);

        $cfg = new Config(array(
            'instance' => array(
                'alias' => array(
                    'struct'  => 'ZendTest\Di\TestAsset\Struct',
                    'dummy'   => 'ZendTest\Di\TestAsset\DummyParams',
                    'factory' => 'ZendTest\Di\TestAsset\StaticFactory',
                ),
                'properties' => array(
                    'ZendTest\Di\TestAsset\Struct' => array(
                        'param1' => 'foo',
                        'param2' => 'bar',
                    ),
                    'ZendTest\Di\TestAsset\StaticFactory' => array(
                        'params' => null,
                    ),
                ),
            ),
        ));
        $cfg->configure($this->di);

        $builder = new ContainerGenerator($this->di);
        $builder->setContainerClass('Container');
        $codeGen = $builder->getCodeGenerator();
        $classBody = $codeGen->generate();
        $this->assertNotContains('NULL)', $classBody, $classBody);
    }

    /**
     * @group nullargs
     */
    public function testNullArgumentsResultInEmptyMethodParameterList()
    {
        $this->markTestIncomplete('Null arguments are currently unsupported');
        $opt = new Builder\PhpClass();
        $opt->setName('ZendTest\Di\TestAsset\OptionalArg');
        $optCtor = new Builder\InjectionMethod();
        $optCtor->setName('__construct')
                ->addParameter('param', null);
        $optInject = new Builder\InjectionMethod();
        $optInject->setName('inject')
                  ->addParameter('param1', null)
                  ->addParameter('param2', null);
        $opt->addInjectionMethod($optCtor)
            ->addInjectionMethod($optInject);

        $def = new Definition();
        $def->addClass($opt);
        $this->di->setDefinition($def);

        $cfg = new Config(array(
            'instance' => array(
                'alias' => array('optional' => 'ZendTest\Di\TestAsset\OptionalArg'),
            ),
            'properties' => array(
                'ZendTest\Di\TestAsset\OptionalArg' => array(
                    'param'  => null,
                    'param1' => null,
                    'param2' => null,
                ),
            ),
        ));
        $cfg->configure($this->di);

        $builder = new ContainerGenerator($this->di);
        $builder->setContainerClass('Container');
        $codeGen = $builder->getCodeGenerator();
        $classBody = $codeGen->generate();
        $this->assertNotContains('NULL)', $classBody, $classBody);
    }

    public function testClassNamesInstantiatedDirectlyShouldBeFullyQualified()
    {
        $this->createDefinitions();
        $builder = new ContainerGenerator($this->di);
        $builder->setContainerClass('Context')
                ->setNamespace('Application');
        $content = $builder->getCodeGenerator()->generate();
        $count   = substr_count($content, '\ZendTest\Di\TestAsset\\');
        $this->assertEquals(3, $count, $content);
        $this->assertNotContains('\\\\', $content);
    }
}
