<?php
namespace ZendTest\Di;

use Zend\Di\DependencyInjector,
    Zend\Di\Configuration;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Config\Config,
    Zend\Config\Ini as IniConfig,
    Zend\Config\Json as JsonConfig,
    Zend\Config\Xml as XmlConfig,
    Zend\Config\Yaml as YamlConfig;

class ConfigurationTest extends TestCase
{
    public function testCanCreateObjectGraphFromArrayConfiguration()
    {
        $config    = $this->getConfig();
        $di        = new DependencyInjector();
        $dibuilder = new Configuration($di);
        $dibuilder->fromArray($config);
        $this->assertObjectGraph($di);
    }

    public function testCanCreateObjectGraphFromZendConfig()
    {
        $config    = new Config($this->getConfig());
        $di        = new DependencyInjector();
        $dibuilder = new Configuration($di);
        $dibuilder->fromConfig($config);
        $this->assertObjectGraph($di);
    }

    public function testCanCreateObjectGraphFromIniConfig()
    {
        $config    = new IniConfig(__DIR__ . '/_files/config.ini', 'testing');
        $di        = new DependencyInjector();
        $dibuilder = new Configuration($di);
        $dibuilder->fromConfig($config);
        $this->assertObjectGraph($di);
    }

    public function testCanCreateObjectGraphFromXmlConfig()
    {
        $config    = new XmlConfig(__DIR__ . '/_files/config.xml', 'testing');
        $di        = new DependencyInjector();
        $dibuilder = new Configuration($di);
        $dibuilder->fromConfig($config);
        $this->assertObjectGraph($di);
    }

    public function testCanCreateObjectGraphFromYamlConfig()
    {
        $config    = new YamlConfig(__DIR__ . '/_files/config.yml', 'testing');
        $di        = new DependencyInjector();
        $dibuilder = new Configuration($di);
        $dibuilder->fromConfig($config);
        $this->assertObjectGraph($di);
    }

    public function testCanCreateObjectGraphFromJsonConfig()
    {
        $config    = new JsonConfig(__DIR__ . '/_files/config.json', 'testing');
        $di        = new DependencyInjector();
        $dibuilder = new Configuration($di);
        $dibuilder->fromConfig($config);
        $this->assertObjectGraph($di);
    }

    public function assertObjectGraph($di)
    {
        $inspected = $di->get('inspected');
        $injected  = $di->get('injected');
        $struct    = $di->get('struct');
        $params    = $di->get('params');

        $this->assertInstanceOf('ZendTest\Di\TestAsset\InspectedClass', $inspected);
        $this->assertInstanceOf('ZendTest\Di\TestAsset\InjectedMethod', $injected);
        $this->assertInstanceOf('ZendTest\Di\TestAsset\Struct', $struct);
        $this->assertInstanceOf('ZendTest\Di\TestAsset\DummyParams', $params);

        $this->assertEquals('FOO', $inspected->foo);
        $this->assertEquals('BAZ', $inspected->baz);

        $this->assertEquals(array('params' => array('param1' => 'foo', 'param2' => 'bar', 'foo' => 'bar')), (array) $params);

        $this->assertEquals(array('param1' => 'foo', 'param2' => 'bar'), (array) $struct);
        $this->assertSame($params, $injected->object, sprintf('Params: %s; Injected: %s', var_export($params, 1), var_export($injected, 1)));
    }

    public function getConfig()
    {
        return array(
            'definitions' => array(
                array(
                    'class' => 'ZendTest\Di\TestAsset\Struct',
                    'params' => array(
                        'param1' => 'foo',
                        'param2' => 'bar',
                    ),
                    'param_map' => array(
                        'param1' => 0,
                        'param2' => 1,
                    ),
                ),
                array(
                    'class' => 'ZendTest\Di\TestAsset\DummyParams',
                    'constructor_callback' => array(
                        'class'  => 'ZendTest\Di\TestAsset\StaticFactory',
                        'method' => 'factory',
                    ),
                    'params' => array(
                        'struct' => array('__reference' => 'struct'),
                        'params' => array('foo' => 'bar'),
                    ),
                    'param_map' => array(
                        'struct' => 0,
                        'params' => 1,
                    ),
                ),
                array(
                    'class' => 'ZendTest\Di\TestAsset\InjectedMethod',
                    'methods' => array(
                        array(
                            'name' => 'setObject',
                            'args' => array(
                                array('__reference' => 'params'),
                            ),
                        ),
                    ),
                ),
                array(
                    'class' => 'ZendTest\Di\TestAsset\InspectedClass',
                    'params' => array(
                        'baz' => 'BAZ',
                        'foo' => 'FOO',
                    ),
                ),
            ),
            'aliases' => array(
                'struct'    => 'ZendTest\Di\TestAsset\Struct',
                'params'    => 'ZendTest\Di\TestAsset\DummyParams',
                'injected'  => 'ZendTest\Di\TestAsset\InjectedMethod',
                'inspected' => 'ZendTest\Di\TestAsset\InspectedClass',
            ),
        );
        /*
        return array(
            'definitions' => array(
                array(
                    'class' => 'className',
                    'constructor_callback' => false,
                        // or string, or array; if array, 'class' and 'method' 
                        // strings
                    'params' => array(
                        'name' => 'value',
                        // if value is an array, look for '__reference' key, 
                        // and, if found, create a Reference object
                    ),
                    'param_map' => array(
                    ),
                    'tags' => array(),
                    'shared' => true,
                    'methods' => array(
                        array(
                            'name' => 'method_name',
                            'args' => array( /* ... * / ),
                                // if value is an array, look for '__reference' 
                                // key, and, if found, create a Reference object
                        ),
                    ),
                ),
            ),
            'aliases' => array(
                'alias' => 'target',
            ),
        );
         */
    }
}
