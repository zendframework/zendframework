<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Config;

use Zend\Config\Factory;

/**
 * @group      Zend_Config
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $tmpFiles = array();
    protected $originalIncludePath;

    protected function getTestAssetFileName($ext)
    {
        if (empty($this->tmpfiles[$ext])) {
            $this->tmpfiles[$ext] = tempnam(sys_get_temp_dir(), 'zend-config-writer').'.'.$ext;
        }
        return $this->tmpfiles[$ext];
    }

    public function setUp()
    {
        $this->originalIncludePath = get_include_path();
        set_include_path(__DIR__ . '/TestAssets');
    }

    public function tearDown()
    {
        set_include_path($this->originalIncludePath);

        foreach ($this->tmpFiles as $file) {
            if (file_exists($file)) {
                if (!is_writable($file)) {
                    chmod($file, 0777);
                }
                @unlink($file);
            }
        }
    }

    public function testFromIni()
    {
        $config = Factory::fromFile(__DIR__ . '/TestAssets/Ini/include-base.ini');

        $this->assertEquals('bar', $config['base']['foo']);
    }

    public function testFromXml()
    {
        $config = Factory::fromFile(__DIR__ . '/TestAssets/Xml/include-base.xml');

        $this->assertEquals('bar', $config['base']['foo']);
    }

    public function testFromIniFiles()
    {
        $files = array (
            __DIR__ . '/TestAssets/Ini/include-base.ini',
            __DIR__ . '/TestAssets/Ini/include-base2.ini'
        );
        $config = Factory::fromFiles($files);

        $this->assertEquals('bar', $config['base']['foo']);
        $this->assertEquals('baz', $config['test']['bar']);
    }

    public function testFromXmlFiles()
    {
        $files = array (
            __DIR__ . '/TestAssets/Xml/include-base.xml',
            __DIR__ . '/TestAssets/Xml/include-base2.xml'
        );
        $config = Factory::fromFiles($files);

        $this->assertEquals('bar', $config['base']['foo']);
        $this->assertEquals('baz', $config['test']['bar']);
    }

    public function testFromPhpFiles()
    {
        $files = array (
            __DIR__ . '/TestAssets/Php/include-base.php',
            __DIR__ . '/TestAssets/Php/include-base2.php'
        );
        $config = Factory::fromFiles($files);

        $this->assertEquals('bar', $config['base']['foo']);
        $this->assertEquals('baz', $config['test']['bar']);
    }

    public function testFromIniAndXmlAndPhpFiles()
    {
        $files = array (
            __DIR__ . '/TestAssets/Ini/include-base.ini',
            __DIR__ . '/TestAssets/Xml/include-base2.xml',
            __DIR__ . '/TestAssets/Php/include-base3.php',
        );
        $config = Factory::fromFiles($files);

        $this->assertEquals('bar', $config['base']['foo']);
        $this->assertEquals('baz', $config['test']['bar']);
        $this->assertEquals('baz', $config['last']['bar']);
    }

    public function testFromIniAndXmlAndPhpFilesFromIncludePath()
    {
        $files = array (
            'Ini/include-base.ini',
            'Xml/include-base2.xml',
            'Php/include-base3.php',
        );
        $config = Factory::fromFiles($files, false, true);

        $this->assertEquals('bar', $config['base']['foo']);
        $this->assertEquals('baz', $config['test']['bar']);
        $this->assertEquals('baz', $config['last']['bar']);
    }

    public function testReturnsConfigObjectIfRequestedAndArrayOtherwise()
    {
        $files = array (
            __DIR__ . '/TestAssets/Ini/include-base.ini',
        );

        $configArray = Factory::fromFile($files[0]);
        $this->assertTrue(is_array($configArray));

        $configArray = Factory::fromFiles($files);
        $this->assertTrue(is_array($configArray));

        $configObject = Factory::fromFile($files[0], true);
        $this->assertInstanceOf('Zend\Config\Config', $configObject);

        $configObject = Factory::fromFiles($files, true);
        $this->assertInstanceOf('Zend\Config\Config', $configObject);
    }

    public function testNonExistentFileThrowsRuntimeException()
    {
        $this->setExpectedException('RuntimeException');
        $config = Factory::fromFile('foo.bar');
    }

    public function testUnsupportedFileExtensionThrowsRuntimeException()
    {
        $this->setExpectedException('RuntimeException');
        $config = Factory::fromFile(__DIR__ . '/TestAssets/bad.ext');
    }

    public function testFactoryCanRegisterCustomReaderInstance()
    {
        Factory::registerReader('dum', new Reader\TestAssets\DummyReader());

        $configObject = Factory::fromFile(__DIR__ . '/TestAssets/dummy.dum', true);
        $this->assertInstanceOf('Zend\Config\Config', $configObject);

        $this->assertEquals($configObject['one'], 1);
    }

    public function testFactoryCanRegisterCustomReaderPlugin()
    {
        $dummyReader = new Reader\TestAssets\DummyReader();
        Factory::getReaderPluginManager()->setService('DummyReader', $dummyReader);

        Factory::registerReader('dum', 'DummyReader');

        $configObject = Factory::fromFile(__DIR__ . '/TestAssets/dummy.dum', true);
        $this->assertInstanceOf('Zend\Config\Config', $configObject);

        $this->assertEquals($configObject['one'], 1);
    }

    public function testFactoryToFileInvalidFileExtension()
    {
        $this->setExpectedException('RuntimeException');
        $result = Factory::toFile(__DIR__.'/TestAssets/bad.ext', array());
    }

    public function testFactoryToFileNoDirInHere()
    {
        $this->setExpectedException('RuntimeException');
        $result = Factory::toFile(__DIR__.'/TestAssets/NoDirInHere/nonExisiting/dummy.php', array());
    }

    public function testFactoryWriteToFile()
    {
        $config = array('test' => 'foo', 'bar' => array(0 => 'baz', 1 => 'foo'));

        $file = $this->getTestAssetFileName('php');
        $result = Factory::toFile($file, $config);

        // build string line by line as we are trailing-whitespace sensitive.
        $expected = "<?php\n";
        $expected .= "return array(\n";
        $expected .= "    'test' => 'foo',\n";
        $expected .= "    'bar' => array(\n";
        $expected .= "        0 => 'baz',\n";
        $expected .= "        1 => 'foo',\n";
        $expected .= "    ),\n";
        $expected .= ");\n";

        $this->assertEquals(true, $result);
        $this->assertEquals($expected, file_get_contents($file));
    }

    public function testFactoryToFileWrongConfig()
    {
        $this->setExpectedException('InvalidArgumentException');
        $result = Factory::toFile('test.ini', 'Im wrong');
    }

    public function testFactoryRegisterInvalidWriter()
    {
        $this->setExpectedException('InvalidArgumentException');
        Factory::registerWriter('dum', new Reader\TestAssets\DummyReader());
    }

    public function testFactoryCanRegisterCustomWriterInstance()
    {
        Factory::registerWriter('dum', new Writer\TestAssets\DummyWriter());

        $file = $this->getTestAssetFileName('dum');

        $res = Factory::toFile($file, array('one' => 1));

        $this->assertEquals($res, true);
    }

    public function testFactoryCanRegisterCustomWriterPlugin()
    {
        $dummyWriter = new Writer\TestAssets\DummyWriter();
        Factory::getWriterPluginManager()->setService('DummyWriter', $dummyWriter);

        Factory::registerWriter('dum', 'DummyWriter');

        $file = $this->getTestAssetFileName('dum');

        $res = Factory::toFile($file, array('one' => 1));
        $this->assertEquals($res, true);
    }
}
