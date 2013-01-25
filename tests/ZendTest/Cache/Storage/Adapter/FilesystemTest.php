<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cache
 */

namespace ZendTest\Cache\Storage\Adapter;

use Zend\Cache;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @group      Zend_Cache
 */
class FilesystemTest extends CommonAdapterTest
{

    protected $_tmpCacheDir;
    protected $_umask;

    public function setUp()
    {
        $this->_umask = umask();

        $this->_tmpCacheDir = @tempnam(sys_get_temp_dir(), 'zend_cache_test_');
        if (!$this->_tmpCacheDir) {
            $err = error_get_last();
            $this->fail("Can't create temporary cache directory-file: {$err['message']}");
        } elseif (!@unlink($this->_tmpCacheDir)) {
            $err = error_get_last();
            $this->fail("Can't remove temporary cache directory-file: {$err['message']}");
        } elseif (!@mkdir($this->_tmpCacheDir, 0777)) {
            $err = error_get_last();
            $this->fail("Can't create temporary cache directory: {$err['message']}");
        }

        $this->_options = new Cache\Storage\Adapter\FilesystemOptions(array(
            'cache_dir' => $this->_tmpCacheDir,
        ));
        $this->_storage = new Cache\Storage\Adapter\Filesystem();
        $this->_storage->setOptions($this->_options);

        parent::setUp();
    }

    public function tearDown()
    {
        $this->_removeRecursive($this->_tmpCacheDir);

        if ($this->_umask != umask()) {
            umask($this->_umask);
            $this->fail("Umask wasn't reset");
        }

        parent::tearDown();
    }

    protected function _removeRecursive($dir)
    {
        if (file_exists($dir)) {
            $dirIt = new \DirectoryIterator($dir);
            foreach ($dirIt as $entry) {
                $fname = $entry->getFilename();
                if ($fname == '.' || $fname == '..') {
                    continue;
                }

                if ($entry->isFile()) {
                    unlink($entry->getPathname());
                } else {
                    $this->_removeRecursive($entry->getPathname());
                }
            }

            rmdir($dir);
        }
    }

    public function testNormalizeCacheDir()
    {
        $cacheDir = $cacheDirExpected = realpath(sys_get_temp_dir());

        if (DIRECTORY_SEPARATOR != '/') {
            $cacheDir = str_replace(DIRECTORY_SEPARATOR, '/', $cacheDir);
        }

        $firstSlash = strpos($cacheDir, '/');
        $cacheDir = substr($cacheDir, 0, $firstSlash + 1)
                  . '..//../'
                  . substr($cacheDir, $firstSlash)
                  . '///';

        $this->_options->setCacheDir($cacheDir);
        $cacheDir = $this->_options->getCacheDir();

        $this->assertEquals($cacheDirExpected, $cacheDir);
    }

    public function testSetCacheDirToSystemsTempDirWithNull()
    {
        $this->_options->setCacheDir(null);
        $this->assertEquals(sys_get_temp_dir(), $this->_options->getCacheDir());
    }

    public function testSetCacheDirNoDirectoryException()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_options->setCacheDir(__FILE__);
    }

    public function testSetCacheDirNotWritableException()
    {
        if (substr(PHP_OS, 0, 3) == 'WIN') {
            $this->markTestSkipped("Not testable on windows");
        } else {
            @exec('whoami 2>&1', $out, $ret);
            if ($ret) {
                $err = error_get_last();
                $this->markTestSkipped("Not testable: {$err['message']}");
            } elseif (isset($out[0]) && $out[0] == 'root') {
                $this->markTestSkipped("Not testable as root");
            }
        }

        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');

        // create a not writable temporaty directory
        $testDir = tempnam(sys_get_temp_dir(), 'ZendTest');
        unlink($testDir); mkdir($testDir); chmod($testDir, 0557);

        try {
            $this->_options->setCacheDir($testDir);
        } catch (\Exception $e) {
            rmdir($testDir);
            throw $e;
        }
    }

    public function testSetCacheDirNotReadableException()
    {
        if (substr(PHP_OS, 0, 3) == 'WIN') {
            $this->markTestSkipped("Not testable on windows");
        } else {
            @exec('whoami 2>&1', $out, $ret);
            if ($ret) {
                $this->markTestSkipped("Not testable: " . implode("\n", $out));
            } elseif (isset($out[0]) && $out[0] == 'root') {
                $this->markTestSkipped("Not testable as root");
            }
        }

        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');

        // create a not readable temporaty directory
        $testDir = tempnam(sys_get_temp_dir(), 'ZendTest');
        unlink($testDir); mkdir($testDir); chmod($testDir, 0337);

        try {
            $this->_options->setCacheDir($testDir);
        } catch (\Exception $e) {
            rmdir($testDir);
            throw $e;
        }
    }

    public function testSetFilePermissionThrowsExceptionIfNotWritable()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_options->setFilePermission(0466);
    }

    public function testSetFilePermissionThrowsExceptionIfNotReadable()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_options->setFilePermission(0266);
    }

    public function testSetFilePermissionThrowsExceptionIfExecutable()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_options->setFilePermission(0661);
    }

    public function testSetNoAtimeChangesAtimeOfMetadataCapability()
    {
        $capabilities = $this->_storage->getCapabilities();

        $this->_options->setNoAtime(false);
        $this->assertContains('atime', $capabilities->getSupportedMetadata());

        $this->_options->setNoAtime(true);
        $this->assertNotContains('atime', $capabilities->getSupportedMetadata());
    }

    public function testSetNoCtimeChangesCtimeOfMetadataCapability()
    {
        $capabilities = $this->_storage->getCapabilities();

        $this->_options->setNoCtime(false);
        $this->assertContains('ctime', $capabilities->getSupportedMetadata());

        $this->_options->setNoCtime(true);
        $this->assertNotContains('ctime', $capabilities->getSupportedMetadata());
    }

    public function testSetDirPermissionThrowsExceptionIfNotWritable()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_options->setDirPermission(0577);
    }

    public function testSetDirPermissionThrowsExceptionIfNotReadable()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_options->setDirPermission(0377);
    }

    public function testSetDirPermissionThrowsExceptionIfNotExecutable()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_options->setDirPermission(0677);
    }

    public function testSetDirLevelInvalidException()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_options->setDirLevel(17); // must between 0-16
    }

    public function testSetUmask()
    {
        $this->_options->setUmask(023);
        $this->assertSame(023, $this->_options->getUmask());

        $this->_options->setUmask(false);
        $this->assertFalse($this->_options->getUmask());
    }

    public function testSetUmaskThrowsExceptionIfNotWritable()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_options->setUmask(0300);
    }

    public function testSetUmaskThrowsExceptionIfNotReadable()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_options->setUmask(0200);
    }

    public function testSetUmaskThrowsExceptionIfNotExecutable()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_options->setUmask(0100);
    }

    public function testGetMetadataWithCtime()
    {
        $this->_options->setNoCtime(false);

        $this->assertTrue($this->_storage->setItem('test', 'v'));

        $meta = $this->_storage->getMetadata('test');
        $this->assertInternalType('array', $meta);

        $expectedCtime = filectime($meta['filespec'] . '.dat');
        $this->assertEquals($expectedCtime, $meta['ctime']);
    }

    public function testGetMetadataWithAtime()
    {
        $this->_options->setNoAtime(false);

        $this->assertTrue($this->_storage->setItem('test', 'v'));

        $meta = $this->_storage->getMetadata('test');
        $this->assertInternalType('array', $meta);

        $expectedAtime = fileatime($meta['filespec'] . '.dat');
        $this->assertEquals($expectedAtime, $meta['atime']);
    }
}
