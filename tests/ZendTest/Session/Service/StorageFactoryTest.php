<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Session\Service;

use Zend\ServiceManager\ServiceManager;
use Zend\Session\Service\StorageFactory;

/**
 * @group      Zend_Session
 */
class StorageFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->services = new ServiceManager();
        $this->services->setFactory('Zend\Session\Storage\StorageInterface', 'Zend\Session\Service\StorageFactory');
    }

    public function sessionStorageConfig()
    {
        return array(
            'array-storage-short' => array(array(
                'session_storage' => array(
                    'type' => 'ArrayStorage',
                    'options' => array(
                        'input' => array(
                            'foo' => 'bar',
                        ),
                    ),
                ),
            ), 'Zend\Session\Storage\ArrayStorage'),
            'array-storage-fqcn' => array(array(
                'session_storage' => array(
                    'type' => 'Zend\Session\Storage\ArrayStorage',
                    'options' => array(
                        'input' => array(
                            'foo' => 'bar',
                        ),
                    ),
                ),
            ), 'Zend\Session\Storage\ArrayStorage'),
            'session-array-storage-short' => array(array(
                'session_storage' => array(
                    'type' => 'SessionArrayStorage',
                    'options' => array(
                        'input' => array(
                            'foo' => 'bar',
                        ),
                    ),
                ),
            ), 'Zend\Session\Storage\SessionArrayStorage'),
            'session-array-storage-fqcn' => array(array(
                'session_storage' => array(
                    'type' => 'Zend\Session\Storage\SessionArrayStorage',
                    'options' => array(
                        'input' => array(
                            'foo' => 'bar',
                        ),
                    ),
                ),
            ), 'Zend\Session\Storage\SessionArrayStorage'),
        );
    }

    /**
     * @dataProvider sessionStorageConfig
     */
    public function testUsesConfigurationToCreateStorage($config, $class)
    {
        $this->services->setService('Config', $config);
        $storage = $this->services->get('Zend\Session\Storage\StorageInterface');
        $this->assertInstanceOf($class, $storage);
        $test = $storage->toArray();
        $this->assertEquals($config['session_storage']['options']['input'], $test);
    }

    public function invalidSessionStorageConfig()
    {
        return array(
            'unknown-class-short' => array(array(
                'session_storage' => array(
                    'type' => 'FooStorage',
                    'options' => array(),
                ),
            )),
            'unknown-class-fqcn' => array(array(
                'session_storage' => array(
                    'type' => 'Foo\Bar\Baz\Bat',
                    'options' => array(),
                ),
            )),
            'bad-class' => array(array(
                'session_storage' => array(
                    'type' => 'Zend\Session\Config\StandardConfig',
                    'options' => array(),
                ),
            )),
            'good-class-invalid-options' => array(array(
                'session_storage' => array(
                    'type' => 'ArrayStorage',
                    'options' => array(
                        'input' => 'this is invalid',
                    ),
                ),
            )),
        );
    }

    /**
     * @dataProvider invalidSessionStorageConfig
     */
    public function testInvalidConfigurationRaisesServiceNotCreatedException($config)
    {
        $this->services->setService('Config', $config);
        $this->setExpectedException('Zend\ServiceManager\Exception\ServiceNotCreatedException');
        $storage = $this->services->get('Zend\Session\Storage\StorageInterface');
    }
}
