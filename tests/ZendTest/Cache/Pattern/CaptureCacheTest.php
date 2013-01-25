<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cache
 */

namespace ZendTest\Cache\Pattern;

use Zend\Cache;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @group      Zend_Cache
 */
class CaptureCacheTest extends CommonPatternTest
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

        $this->_options = new Cache\Pattern\PatternOptions(array(
            'public_dir' => $this->_tmpCacheDir
        ));
        $this->_pattern = new Cache\Pattern\CaptureCache();
        $this->_pattern->setOptions($this->_options);

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

    public function testSetThrowsLogicExceptionOnMissingPublicDir()
    {
        $captureCache = new Cache\Pattern\CaptureCache();

        $this->setExpectedException('Zend\Cache\Exception\LogicException');
        $captureCache->set('content', '/pageId');
    }

    public function testSetWithNormalPageId()
    {
        $this->_pattern->set('content', '/dir1/dir2/file');
        $this->assertTrue(file_exists($this->_tmpCacheDir . '/dir1/dir2/file'));
        $this->assertSame(file_get_contents($this->_tmpCacheDir . '/dir1/dir2/file'), 'content');
    }

    public function testSetWithIndexFilename()
    {
        $this->_options->setIndexFilename('test.html');

        $this->_pattern->set('content', '/dir1/dir2/');
        $this->assertTrue(file_exists($this->_tmpCacheDir . '/dir1/dir2/test.html'));
        $this->assertSame(file_get_contents($this->_tmpCacheDir . '/dir1/dir2/test.html'), 'content');
    }

    public function testGetThrowsLogicExceptionOnMissingPublicDir()
    {
        $captureCache = new Cache\Pattern\CaptureCache();

        $this->setExpectedException('Zend\Cache\Exception\LogicException');
        $captureCache->get('/pageId');
    }

    public function testHasThrowsLogicExceptionOnMissingPublicDir()
    {
        $captureCache = new Cache\Pattern\CaptureCache();

        $this->setExpectedException('Zend\Cache\Exception\LogicException');
        $captureCache->has('/pageId');
    }

    public function testRemoveThrowsLogicExceptionOnMissingPublicDir()
    {
        $captureCache = new Cache\Pattern\CaptureCache();

        $this->setExpectedException('Zend\Cache\Exception\LogicException');
        $captureCache->remove('/pageId');
    }
}
