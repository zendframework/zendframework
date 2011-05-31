<?php
namespace ZendTest\Di;

use Zend\Di\ContainerBuilder,
    Zend\Di\DependencyInjector,
    Zend\Di\Definition,
    Zend\Di\Reference;

use PHPUnit_Framework_TestCase as TestCase;

class ContainerBuilderTest extends TestCase
{
    public $tmpFile = false;

    public function setUp()
    {
        $this->tmpFile = false;
        $this->di = new DependencyInjector;
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
        $inspect  = new Definition('ZendTest\Di\TestAsset\InspectedClass');
        $inspect->setParam('foo', new Reference('composed'))
                ->setParam('baz', 'BAZ');
        $composed = new Definition('ZendTest\Di\TestAsset\ComposedClass');
        $struct   = new Definition('ZendTest\Di\TestAsset\Struct');
        $struct->setParam('param1', 'foo')
               ->setParam('param2', new Reference('inspect'));
        $this->di->setDefinition($composed, 'composed')
                 ->setDefinition($inspect, 'inspect')
                 ->setDefinition($struct, 'struct');
    }

    public function buildContainerClass($name = 'Application')
    {
        $this->createDefinitions();
        $builder = new ContainerBuilder($this->di);
        $builder->setContainerClass($name);
        $builder->getCodeGenerator($this->getTmpFile())->write();
        $this->assertFileExists($this->tmpFile);
    }

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
            if ("T_CLASS" == token_name($token[0])) {
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
                } while (($i < $count) && (token_name($id) != 'T_STRING'));
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
        $builder = new ContainerBuilder($this->di);
        $builder->setContainerClass('Application');
        $codegen = $builder->getCodeGenerator();
        $this->assertInstanceOf('Zend\CodeGenerator\Php\PhpFile', $codegen);
    }

    public function testCanSpecifyNamespaceForGeneratedPhpClassfile()
    {
        $this->createDefinitions();
        $builder = new ContainerBuilder($this->di);
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
        $def = new Definition('ZendTest\Di\TestAsset\OptionalArg');
        $def->setParamMap(array('param' => 0))
            ->setParam('param', null);
        $this->di->setDefinition($def, 'optional');

        $builder = new ContainerBuilder($this->di);
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
        $struct = new Definition('ZendTest\Di\TestAsset\Struct');
        $struct->setParam('param1', 'foo')
               ->setParam('param2', 'bar');
        $dummy  = new Definition('ZendTest\Di\TestAsset\DummyParams');
        $dummy->setConstructorCallback(array('ZendTest\Di\TestAsset\StaticFactory', 'factory'))
              ->setParam('struct', new Reference('struct'))
              ->setParam('params', null)
              ->setParamMap(array('struct' => 0, 'params' => 1));
        $this->di->setDefinition($struct, 'struct')
                 ->setDefinition($dummy, 'dummy');

        $builder = new ContainerBuilder($this->di);
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
        $def = new Definition('ZendTest\Di\TestAsset\OptionalArg');
        $def->setParamMap(array('param' => 0))
            ->addMethodCall('inject', array(null, null));
        $this->di->setDefinition($def, 'optional');

        $builder = new ContainerBuilder($this->di);
        $builder->setContainerClass('Container');
        $codeGen = $builder->getCodeGenerator();
        $classBody = $codeGen->generate();
        $this->assertNotContains('NULL)', $classBody, $classBody);
    }

    public function testClassNamesInstantiatedDirectlyShouldBeFullyQualified()
    {
        $this->createDefinitions();
        $builder = new ContainerBuilder($this->di);
        $builder->setContainerClass('Context')
                ->setNamespace('Application');
        $content = $builder->getCodeGenerator()->generate();
        $count   = substr_count($content, '\ZendTest\Di\TestAsset\\');
        $this->assertEquals(3, $count, $content);
        $this->assertNotContains('\\\\', $content);
    }
}
