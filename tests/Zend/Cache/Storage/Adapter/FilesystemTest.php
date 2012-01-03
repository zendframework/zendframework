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
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Cache\Storage\Adapter;

use Zend\Cache;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class FilesystemTest extends CommonAdapterTest
{

    protected $_tmpCacheDir;

    public function setUp()
    {
        $this->_tmpCacheDir = @tempnam(sys_get_temp_dir(), 'zend_cache_test_');
        if (!$this->_tmpCacheDir) {
            $err = error_get_last();
            $this->fail("Can't create temporary cache directory-file: {$err['message']}");
        } elseif (!@unlink($this->_tmpCacheDir)) {
            $err = error_get_last();
            $this->fail("Can't remove temporary cache directory-file: {$err['message']}");
        } elseif (!@mkdir($this->_tmpCacheDir, 0777)) {
            $err = error_get_last();
            $this->fail("Can't create temporaty cache directory: {$err['message']}");
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
        $cacheDir = $cacheDirExpected = sys_get_temp_dir();

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

    public function testSetFilePermUpdatesUmask()
    {
        $this->_options->setFilePerm(0606);
        $this->assertEquals(~0606, $this->_options->getFileUmask());
    }

    public function testSetFilePermThrowsExceptionIfNotWritable()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_options->setFilePerm(0466);
    }

    public function testSetFilePermThrowsExceptionIfNotReadable()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_options->setFilePerm(0266);
    }

    public function testSetFilePermThrowsExceptionIfExecutable()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_options->setFilePerm(0661);
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

    public function testSetDirPermUpdatesUmask()
    {
        $this->_options->setDirPerm(0706);
        $this->assertEquals(~0706, $this->_options->getDirUmask());
    }

    public function testSetDirPermThrowsExceptionIfNotWritable()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_options->setDirPerm(0577);
    }

    public function testSetDirPermThrowsExceptionIfNotReadable()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_options->setDirPerm(0377);
    }

    public function testSetDirPermThrowsExceptionIfNotExecutable()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_options->setDirPerm(0677);
    }

    public function testSetDirLevelInvalidException()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_options->setDirLevel(17); // must between 0-16
    }

    public function testSetReadControlAlgoAllowStrlen()
    {
        $this->_options->setReadControlAlgo('strlen');
        $this->assertEquals('strlen', $this->_options->getReadControlAlgo());
    }

    public function testSetReadControlAlgoInvalidException()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_options->setReadControlAlgo('unknown');
    }

    public function testDisabledFileBlocking()
    {
        if (substr(\PHP_OS, 0, 3) == 'WIN') {
            $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        }

        $this->_options->setFileLocking(true);
        $this->_options->setFileBlocking(false);

        // create cache item and get data file
        $this->assertTrue($this->_storage->setItem('key', 'value'));
        $meta = $this->_storage->getMetadata('key');
        $this->assertInternalType('array', $meta);
        $this->assertArrayHasKey('filespec', $meta);
        $file = $meta['filespec'] . '.dat';

        /******************
         * first test with exclusive lock
         */

        // open file and create a lock
        $fp = @fopen($file, 'cb');
        $this->assertInternalType('resource', $fp);
        flock($fp, LOCK_EX);

        // rewriting file should fail in part of open lock
        try {
            $this->_storage->setItem('key', 'lock');

            // close
            flock($fp, LOCK_UN);
            fclose($fp);

            $this->fail('Missing expected exception Zend\Cache\Exception\LockedException');
        } catch (\Zend\Cache\Exception\LockedException $e) {
            // expected exception was thrown

            // close
            flock($fp, LOCK_UN);
            fclose($fp);
        }

        /******************
         * second test with shared lock
         */

        // open file and create a lock
        $fp = @fopen($file, 'rb');
        $this->assertInternalType('resource', $fp);
        flock($fp, LOCK_SH);

        // rewriting file should fail in part of open lock
        try {
            $this->_storage->setItem('key', 'lock');

            // close
            flock($fp, LOCK_UN);
            fclose($fp);

            $this->fail('Missing expected exception Zend\Cache\Exception\LockedException');
        } catch (\Zend\Cache\Exception\LockedException $e) {
            // expected exception was thrown

            // close
            flock($fp, LOCK_UN);
            fclose($fp);
        }
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
