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
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Application;

use Zend\Application,
    Zend\Config\Ini as IniConfig,
    Zend\Loader\StandardAutoloader;

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // Store original include_path
        $this->includePath = get_include_path();

        $this->application = new Application\Application('testing');

        $this->iniOptions = array();
    }

    public function tearDown()
    {
        foreach ($this->iniOptions as $key) {
            ini_restore($key);
        }
    }

    public function testConstructorSetsEnvironment()
    {
        $this->assertEquals('testing', $this->application->getEnvironment());
    }

    public function testConstructorInstantiatesAutoloader()
    {
        $autoloader = $this->application->getAutoloader();
        $this->assertTrue($autoloader instanceof StandardAutoloader);
    }

    public function testConstructorShouldSetOptionsWhenProvided()
    {
        $options = array(
            'foo' => 'bar',
            'bar' => 'baz',
        );
        $application = new Application\Application('testing', $options);
        $this->assertEquals($options, $application->getOptions());
    }

    public function testHasOptionShouldReturnFalseWhenOptionNotPresent()
    {
        $this->assertFalse($this->application->hasOption('foo'));
    }

    public function testHasOptionShouldReturnTrueWhenOptionPresent()
    {
        $options = array(
            'foo' => 'bar',
            'bar' => 'baz',
        );
        $application = new Application\Application('testing', $options);
        $this->assertTrue($application->hasOption('foo'));
    }

    public function testGetOptionShouldReturnNullWhenOptionNotPresent()
    {
        $this->assertNull($this->application->getOption('foo'));
    }

    public function testGetOptionShouldReturnOptionValue()
    {
        $options = array(
            'foo' => 'bar',
            'bar' => 'baz',
        );
        $application = new Application\Application('testing', $options);
        $this->assertEquals($options['foo'], $application->getOption('foo'));
    }

    public function testPassingAutoloaderNamespaceOptionsShouldProxyToAutoloader()
    {
        $autoloader = new TestAsset\Autoloader();
        $this->application->setAutoloader($autoloader);
        $this->application->setOptions(array(
            'autoloaderNamespaces' => array(
                'Foo' => './TestAsset/',
            ),
        ));
        $namespaces = $autoloader->getNamespaces();
        $this->assertArrayHasKey('Foo' . StandardAutoloader::NS_SEPARATOR, $namespaces);
    }

    public function testPassingIncludePathOptionShouldModifyIncludePath()
    {
        $expected = __DIR__ . '/TestAsset';
        $this->application->setOptions(array(
            'includePaths' => array(
                $expected,
            ),
        ));
        $test = get_include_path();
        $this->assertContains($expected, $test);
    }

    public function testPassingPhpSettingsSetsIniValues()
    {
        $this->iniOptions[] = 'y2k_compliance';
        $orig     = ini_get('y2k_compliance');
        $expected = $orig ? 0 : 1;
        $this->application->setOptions(array(
            'phpSettings' => array(
                'y2k_compliance' => $expected,
            ),
        ));
        $this->assertEquals($expected, ini_get('y2k_compliance'));
    }

    public function testPassingPhpSettingsAsArrayShouldConstructDotValuesAndSetRelatedIniValues()
    {
        $this->iniOptions[] = 'date.default_latitude';
        $orig     = ini_get('date.default_latitude');
        $expected = '1.234';
        $this->application->setOptions(array(
            'phpSettings' => array(
                'date' => array(
                    'default_latitude' => $expected,
                ),
            ),
        ));
        $this->assertEquals($expected, ini_get('date.default_latitude'));
    }

    public function testShouldUseBaseBootstrapClassByDefaultIfNoBootstrapRegistered()
    {
        $bootstrap = $this->application->getBootstrap();
        $this->assertTrue($bootstrap instanceof Application\Bootstrap);
    }

    public function testPassingStringBootstrapPathOptionShouldRegisterBootstrap()
    {
        $this->application->setOptions(array(
            'bootstrap' => __DIR__ . '/TestAsset/modules/application/Bootstrap.php',
        ));
        $bootstrap = $this->application->getBootstrap();
        $this->assertTrue($bootstrap instanceof \Bootstrap);
    }

    public function testPassingArrayBootstrapOptionShouldRegisterBootstrapBasedOnPathOption()
    {
        $this->application->setOptions(array(
            'bootstrap' => array(
                'path' => __DIR__ . '/TestAsset/modules/default/Bootstrap.php',
            ),
        ));
        $bootstrap = $this->application->getBootstrap();
        $this->assertTrue($bootstrap instanceof \Bootstrap);
    }

    public function testPassingArrayBootstrapOptionShouldRegisterBootstrapBasedOnPathAndClassOption()
    {
        $this->application->setOptions(array(
            'bootstrap' => array(
                'path'  => __DIR__ . '/TestAsset/ZfAppBootstrap.php',
                'class' => 'ZendTest\\Application\\TestAsset\\ZfAppBootstrap',
            ),
        ));
        $bootstrap = $this->application->getBootstrap();
        $this->assertTrue($bootstrap instanceof TestAsset\ZfAppBootstrap);
    }

    public function testPassingArrayBootstrapWithoutPathOptionShouldRaiseException()
    {
        $this->setExpectedException('Zend\Application\Exception\InvalidArgumentException');
        $this->application->setOptions(array(
            'bootstrap' => array(
                'class' => 'ZfAppBootstrap',
            ),
        ));
        $bootstrap = $this->application->getBootstrap();
    }

    public function testPassingInvalidBootstrapOptionShouldRaiseException()
    {
        $this->setExpectedException('Zend\Application\Exception\InvalidArgumentException');
        $this->application->setOptions(array(
            'bootstrap' => new \stdClass(),
        ));
        $bootstrap = $this->application->getBootstrap();
    }

    public function testPassingInvalidOptionsArgumentToConstructorShouldRaiseException()
    {
        $this->setExpectedException('Zend\Application\Exception\InvalidArgumentException');
        $application = new Application\Application('testing', new \stdClass());
    }

    public function testPassingStringIniConfigPathOptionToConstructorShouldLoadOptions()
    {
        $application = new Application\Application('testing', __DIR__ . '/TestAsset/appconfig.ini');
        $this->assertTrue($application->hasOption('foo'));
    }

    public function testPassingStringXmlConfigPathOptionToConstructorShouldLoadOptions()
    {
        $application = new Application\Application('testing', __DIR__ . '/TestAsset/appconfig.xml');
        $this->assertTrue($application->hasOption('foo'));
    }

    public function testPassingStringPhpConfigPathOptionToConstructorShouldLoadOptions()
    {
        $application = new Application\Application('testing', __DIR__ . '/TestAsset/appconfig.php');
        $this->assertTrue($application->hasOption('foo'));
    }

    public function testPassingStringIncConfigPathOptionToConstructorShouldLoadOptions()
    {
        $application = new Application\Application('testing', __DIR__ . '/TestAsset/appconfig.inc');
        $this->assertTrue($application->hasOption('foo'));
    }

    public function testPassingArrayOptionsWithConfigKeyShouldLoadOptions()
    {
        $application = new Application\Application('testing', array('bar' => 'baz', 'config' => __DIR__ . '/TestAsset/appconfig.inc'));
        $this->assertTrue($application->hasOption('foo'));
        $this->assertTrue($application->hasOption('bar'));
    }

    /**
     * This was changed to have the passed in array always overwrite the config file.
     * @group ZF-6811
     */
    public function testPassingArrayOptionsWithConfigKeyShouldLoadOptionsAndNotOverride()
    {
        $application = new Application\Application('testing', array('foo' => 'baz', 'config' => __DIR__ . '/TestAsset/appconfig.inc'));
        $this->assertNotEquals('bar', $application->getOption('foo'));
    }

    public function testPassingInvalidStringOptionToConstructorShouldRaiseException()
    {
        $this->setExpectedException('Zend\Application\Exception\InvalidArgumentException');
        $application = new Application\Application('testing', __DIR__ . '/TestAsset/appconfig');
    }

    public function testPassingZendConfigToConstructorShouldLoadOptions()
    {
        $config = new IniConfig(__DIR__ . '/TestAsset/appconfig.ini', 'testing');
        $application = new Application\Application('testing', $config);
        $this->assertTrue($application->hasOption('foo'));
    }

    public function testPassingArrayOptionsToConstructorShouldLoadOptions()
    {
        $config = new IniConfig(__DIR__ . '/TestAsset/appconfig.ini', 'testing');
        $application = new Application\Application('testing', $config->toArray());
        $this->assertTrue($application->hasOption('foo'));
    }

    public function testBootstrapImplementsFluentInterface()
    {
        $application = $this->application->bootstrap();
        $this->assertSame($application, $this->application);
    }

    public function testApplicationShouldRaiseExceptionIfBootstrapFileDoesNotContainBootstrapClass()
    {
        $this->setExpectedException('Zend\Application\Exception\InvalidArgumentException');
        $this->application->setOptions(array(
            'bootstrap' => array(
                'path'  => __DIR__ . '/TestAsset/ZfAppNoBootstrap.php',
                'class' => 'ZfAppNoBootstrap',
            ),
        ));
        $bootstrap = $this->application->getBootstrap();
    }

    public function testApplicationShouldRaiseExceptionWhenBootstrapClassNotOfCorrectType()
    {
        $this->setExpectedException('Zend\Application\Exception\InvalidArgumentException');
        $this->application->setOptions(array(
            'bootstrap' => array(
                'path'  => __DIR__ . '/TestAsset/ZfAppBadBootstrap.php',
                'class' => 'ZendTest\\Application\\TestAsset\\ZfAppBadBootstrap',
            ),
        ));
        $bootstrap = $this->application->getBootstrap();
    }

    public function testOptionsShouldRetainOriginalCase()
    {
        $options = array(
            'pluginPaths' => array(
                'Zend_Application_Test_Path' => __DIR__,
            ),
            'Resources' => array(
                'modules' => array(),
                'FrontController' => array(
                    'baseUrl'             => '/foo',
                    'moduleDirectory'     => __DIR__ . '/TestAsset/modules',
                ),
            ),
            'Bootstrap' => array(
                'path'  => __DIR__ . '/TestAsset/ZfAppBootstrap.php',
                'class' => 'ZendTest\\Application\\TestAsset\\ZfAppBootstrap',
            ),
        );
        $this->application->setOptions($options);
        $setOptions = $this->application->getOptions();
        $this->assertSame(array_keys($options), array_keys($setOptions));
    }

    /**
     * @group ZF-6679
     */
    public function testSetOptionsShouldProperlyMergeTwoConfigFileOptions()
    {
        $application = new Application\Application(
            'production', __DIR__ .
            '/TestAsset/zf-6679-1.inc'
        );
        $options = $application->getOptions();
        $this->assertEquals(array('includePaths', 'config'), array_keys($options));
    }

    public function testPassingZfVersionAutoloaderInformationConfiguresAutoloader()
    {
        if (!constant('TESTS_ZEND_LOADER_AUTOLOADER_MULTIVERSION_ENABLED')) {
            $this->markTestSkipped();
        }
        if (!constant('TESTS_ZEND_LOADER_AUTOLOADER_MULTIVERSION_LATEST')) {
            $this->markTestSkipped();
        }
        $path   = constant('TESTS_ZEND_LOADER_AUTOLOADER_MULTIVERSION_PATH');
        $latest = constant('TESTS_ZEND_LOADER_AUTOLOADER_MULTIVERSION_LATEST');

        $application = new Application\Application('production', array(
            'autoloaderZfPath'    => $path,
            'autoloaderZfVersion' => 'latest',
        ));
        $autoloader = $application->getAutoloader();
        $actual     = $autoloader->getZfPath();
        $this->assertContains($latest, $actual);
    }

    /**
     * @group ZF-7742
     */
    public function testHasOptionShouldTreatOptionKeysAsCaseInsensitive()
    {
        $application = new Application\Application('production', array(
            'fooBar' => 'baz',
        ));
        $this->assertTrue($application->hasOption('FooBar'));
    }

    /**
     * @group ZF-7742
     */
    public function testGetOptionShouldTreatOptionKeysAsCaseInsensitive()
    {
        $application = new Application\Application('production', array(
            'fooBar' => 'baz',
        ));
        $this->assertEquals('baz', $application->getOption('FooBar'));
    }

    /**
     * @group ZF-6618
     */
    public function testCanExecuteBoostrapResourceViaApplicationInstanceBootstrapMethod()
    {
        $application = new Application\Application('testing', array(
            'bootstrap' => array(
                'path' => __DIR__ . '/TestAsset/ZfAppBootstrap.php',
                'class' => 'ZendTest\\Application\\TestAsset\\ZfAppBootstrap'
                )
            )
        );
        $application->bootstrap('foo');

        $this->assertEquals(1, $application->getBootstrap()->fooExecuted);
        $this->assertEquals(0, $application->getBootstrap()->barExecuted);
    }

    public function testOptionsCanHandleMuiltipleConigFiles()
    {
        $application = new Application\Application('testing', array(
            'config' => array(
                __DIR__ . '/TestAsset/Zf-6719-1.ini',
                __DIR__ . '/TestAsset/Zf-6719-2.ini'
                )
            )
        );

        $this->assertEquals('baz', $application->getOption('foo'));
    }

    /**
     * @group ZF-10898
     */
    public function testPassingStringIniDistfileConfigPathOptionToConstructorShouldLoadOptions()
    {
        $application = new Application\Application('testing', __DIR__ . '/_files/appconfig.ini.dist');
        $this->assertTrue($application->hasOption('foo'));
    }

    /**
     * @group ZF-10898
     */
    public function testPassingArrayOptionsWithConfigKeyDistfileShouldLoadOptions()
    {
        $application = new Application\Application('testing', array('bar' => 'baz', 'config' => __DIR__ . '/_files/appconfig.ini.dist'));
        $this->assertTrue($application->hasOption('foo'));
        $this->assertTrue($application->hasOption('bar'));
    }

    /**
     * @group ZF-10568
     */
    public function testPassingStringYamlConfigPathOptionToConstructorShouldLoadOptions()
    {
        $application = new Application\Application('testing', __DIR__ . '/TestAsset/appconfig.yaml');
        $this->assertTrue($application->hasOption('foo'));
    }

    /**
     * @group ZF-10568
     */
    public function testPassingStringJsonConfigPathOptionToConstructorShouldLoadOptions()
    {
        $application = new Application\Application('testing', __DIR__ . '/TestAsset/appconfig.json');
        $this->assertTrue($application->hasOption('foo'));
    }

    /**
     * @group ZF-11425
     */
    public function testPassingStringYmlConfigPathOptionToConstructorShouldLoadOptionsAsYaml()
    {
        $application = new Application\Application('testing', __DIR__ . '/TestAsset/appconfig.yml');
        $this->assertTrue($application->hasOption('foo'));
    }
}
