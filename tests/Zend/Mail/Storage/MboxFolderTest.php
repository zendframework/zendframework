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
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Mail\Storage;

use Zend\Mail\Storage\Folder;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Mail
 */
class MboxFolderTest extends \PHPUnit_Framework_TestCase
{
    protected $_params;
    protected $_originalDir;
    protected $_tmpdir;
    protected $_subdirs = array('.', 'subfolder');

    public function setUp()
    {
        $this->_originalDir = __DIR__ . '/../_files/test.mbox/';

        if ($this->_tmpdir == null) {
            if (TESTS_ZEND_MAIL_TEMPDIR != null) {
                $this->_tmpdir = TESTS_ZEND_MAIL_TEMPDIR;
            } else {
                $this->_tmpdir = __DIR__ . '/../_files/test.tmp/';
            }
            if (!file_exists($this->_tmpdir)) {
                mkdir($this->_tmpdir);
            }
            $count = 0;
            $dh = opendir($this->_tmpdir);
            while (readdir($dh) !== false) {
                ++$count;
            }
            closedir($dh);
            if ($count != 2) {
                $this->markTestSkipped('Are you sure your tmp dir is a valid empty dir?');
                return;
            }
        }

        $this->_params = array();
        $this->_params['dirname'] = $this->_tmpdir;
        $this->_params['folder']  = 'INBOX';

        foreach ($this->_subdirs as $dir) {
            if ($dir != '.') {
                mkdir($this->_tmpdir . $dir);
            }
            $dh = opendir($this->_originalDir . $dir);
            while (($entry = readdir($dh)) !== false) {
                $entry = $dir . '/' . $entry;
                if (!is_file($this->_originalDir . $entry)) {
                    continue;
                }
                copy($this->_originalDir . $entry, $this->_tmpdir . $entry);
            }
            closedir($dh);
        }
    }

    public function tearDown()
    {
        foreach (array_reverse($this->_subdirs) as $dir) {
            $dh = opendir($this->_tmpdir . $dir);
            while (($entry = readdir($dh)) !== false) {
                $entry = $this->_tmpdir . $dir . '/' . $entry;
                if (!is_file($entry)) {
                    continue;
                }
                unlink($entry);
            }
            closedir($dh);
            if ($dir != '.') {
                rmdir($this->_tmpdir . $dir);
            }
        }
    }

    public function testLoadOk()
    {
        try {
            $mail = new Folder\Mbox($this->_params);
        } catch (\Exception $e) {
            $this->fail('exception raised while loading mbox folder');
        }
    }

    public function testLoadConfig()
    {
        try {
            $mail = new Folder\Mbox(new \Zend\Config\Config($this->_params));
        } catch (\Exception $e) {
            $this->fail('exception raised while loading mbox folder');
        }
    }

    public function testNoParams()
    {
        try {
            $mail = new Folder\Mbox(array());
        } catch (\Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised with empty params');
    }

    public function testFilenameParam()
    {
        try {
            // filename is not allowed in this subclass
            $mail = new Folder\Mbox(array('filename' => 'foobar'));
        } catch (\Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised with filename as param');
    }

    public function testLoadFailure()
    {
        try {
            $mail = new Folder\Mbox(array('dirname' => 'This/Folder/Does/Not/Exist'));
        } catch (\Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while loading unknown dirname');
    }

    public function testLoadUnkownFolder()
    {
        $this->_params['folder'] = 'UnknownFolder';
        try {
            $mail = new Folder\Mbox($this->_params);
        } catch (\Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while loading unknown folder');
    }

    public function testChangeFolder()
    {
        $mail = new Folder\Mbox($this->_params);
        try {
            $mail->selectFolder(DIRECTORY_SEPARATOR . 'subfolder' . DIRECTORY_SEPARATOR . 'test');
        } catch (\Exception $e) {
            $this->fail('exception raised while selecting existing folder');
        }

        $this->assertEquals($mail->getCurrentFolder(), DIRECTORY_SEPARATOR . 'subfolder' . DIRECTORY_SEPARATOR . 'test');
    }

    public function testChangeFolderUnselectable()
    {
        $mail = new Folder\Mbox($this->_params);
        try {
            $mail->selectFolder(DIRECTORY_SEPARATOR . 'subfolder');
        } catch (\Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while selecting unselectable folder');
    }

    public function testUnknownFolder()
    {
        $mail = new Folder\Mbox($this->_params);
        try {
            $mail->selectFolder('/Unknown/Folder/');
        } catch (\Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while selecting unknown folder');
    }

    public function testGlobalName()
    {
        $mail = new Folder\Mbox($this->_params);
        try {
            // explicit call of __toString() needed for PHP < 5.2
            $this->assertEquals($mail->getFolders()->subfolder->__toString(), DIRECTORY_SEPARATOR . 'subfolder');
        } catch (\Zend\Mail\Exception $e) {
            $this->fail('exception raised while selecting existing folder and getting global name');
        }
    }

    public function testLocalName()
    {
        $mail = new Folder\Mbox($this->_params);
        try {
            $this->assertEquals($mail->getFolders()->subfolder->key(), 'test');
        } catch (\Exception $e) {
            $this->fail('exception raised while selecting existing folder and getting local name');
        }
    }

    public function testIterator()
    {
        $mail = new Folder\Mbox($this->_params);
        $iterator = new \RecursiveIteratorIterator($mail->getFolders(), \RecursiveIteratorIterator::SELF_FIRST);
        // we search for this folder because we can't assume a order while iterating
        $search_folders = array(DIRECTORY_SEPARATOR . 'subfolder'                                => 'subfolder',
                                DIRECTORY_SEPARATOR . 'subfolder' . DIRECTORY_SEPARATOR . 'test' => 'test',
                                DIRECTORY_SEPARATOR . 'INBOX'                                    => 'INBOX');
        $found_folders = array();

        foreach ($iterator as $localName => $folder) {
            if (!isset($search_folders[$folder->getGlobalName()])) {
                continue;
            }

            // explicit call of __toString() needed for PHP < 5.2
            $found_folders[$folder->__toString()] = $localName;
        }

        $this->assertEquals($search_folders, $found_folders);
    }

    public function testKeyLocalName()
    {
        $mail = new Folder\Mbox($this->_params);
        $iterator = new \RecursiveIteratorIterator($mail->getFolders(), \RecursiveIteratorIterator::SELF_FIRST);
        // we search for this folder because we can't assume a order while iterating
        $search_folders = array(DIRECTORY_SEPARATOR . 'subfolder'                                => 'subfolder',
                                DIRECTORY_SEPARATOR . 'subfolder' . DIRECTORY_SEPARATOR . 'test' => 'test',
                                DIRECTORY_SEPARATOR . 'INBOX'                                    => 'INBOX');
        $found_folders = array();

        foreach ($iterator as $localName => $folder) {
            if (!isset($search_folders[$folder->getGlobalName()])) {
                continue;
            }

            // explicit call of __toString() needed for PHP < 5.2
            $found_folders[$folder->__toString()] = $localName;
        }

        $this->assertEquals($search_folders, $found_folders);
    }

    public function testSelectable()
    {
        $mail = new Folder\Mbox($this->_params);
        $iterator = new \RecursiveIteratorIterator($mail->getFolders(), \RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $localName => $folder) {
            $this->assertEquals($localName, $folder->getLocalName());
        }
    }


    public function testCount()
    {
        $mail = new Folder\Mbox($this->_params);

        $count = $mail->countMessages();
        $this->assertEquals(7, $count);

        $mail->selectFolder(DIRECTORY_SEPARATOR . 'subfolder' . DIRECTORY_SEPARATOR . 'test');
        $count = $mail->countMessages();
        $this->assertEquals(1, $count);
    }

    public function testSize()
    {
        $mail = new Folder\Mbox($this->_params);
        $shouldSizes = array(1 => 397, 89, 694, 452, 497, 101, 139);

        $sizes = $mail->getSize();
        $this->assertEquals($shouldSizes, $sizes);

        $mail->selectFolder(DIRECTORY_SEPARATOR . 'subfolder' . DIRECTORY_SEPARATOR . 'test');
        $sizes = $mail->getSize();
        $this->assertEquals(array(1 => 410), $sizes);
    }

    public function testFetchHeader()
    {
        $mail = new Folder\Mbox($this->_params);

        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Simple Message', $subject);

        $mail->selectFolder(DIRECTORY_SEPARATOR . 'subfolder' . DIRECTORY_SEPARATOR . 'test');
        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Message in subfolder', $subject);
    }

    public function testSleepWake()
    {
        $mail = new Folder\Mbox($this->_params);

        $mail->selectFolder(DIRECTORY_SEPARATOR . 'subfolder' . DIRECTORY_SEPARATOR . 'test');
        $count = $mail->countMessages();
        $content = $mail->getMessage(1)->getContent();

        $serialzed = serialize($mail);
        $mail = null;
        $mail = unserialize($serialzed);

        $this->assertEquals($mail->countMessages(), $count);
        $this->assertEquals($mail->getMessage(1)->getContent(), $content);

        $mail->selectFolder(DIRECTORY_SEPARATOR . 'subfolder' . DIRECTORY_SEPARATOR . 'test');
        $this->assertEquals($mail->countMessages(), $count);
        $this->assertEquals($mail->getMessage(1)->getContent(), $content);
    }

    public function testNotMboxFile()
    {
        touch($this->_params['dirname'] . 'foobar');
        $mail = new Folder\Mbox($this->_params);

        try {
            $mail->getFolders()->foobar;
        } catch (\Exception $e) {
            return; // ok
        }

        $this->fail('file, which is not mbox, got parsed');
    }

    public function testNotReadableFolder()
    {
        $stat = stat($this->_params['dirname'] . 'subfolder');
        chmod($this->_params['dirname'] . 'subfolder', 0);
        clearstatcache();
        $statcheck = stat($this->_params['dirname'] . 'subfolder');
        if ($statcheck['mode'] % (8 * 8 * 8) !== 0) {
            chmod($this->_params['dirname'] . 'subfolder', $stat['mode']);
            $this->markTestSkipped('cannot remove read rights, which makes this test useless (maybe you are using Windows?)');
            return;
        }

        $check = false;
        try {
            $mail = new Folder\Mbox($this->_params);
        } catch (\Exception $e) {
            $check = true;
            // test ok
        }

        chmod($this->_params['dirname'] . 'subfolder', $stat['mode']);

        if (!$check) {
            if (function_exists('posix_getuid') && posix_getuid() === 0) {
                $this->markTestSkipped('seems like you are root and we therefore cannot test the error handling');
            }
            $this->fail('no exception while loading invalid dir with subfolder not readable');
        }
    }

    public function testGetInvalidFolder()
    {
        $mail = new Folder\Mbox($this->_params);
        $root = $mail->getFolders();
        $root->foobar = new Folder('x', 'x');
        try {
            $mail->getFolders('foobar');
        } catch (\Exception $e) {
            return; // ok
        }

        $this->fail('no error while getting invalid folder');
    }

    public function testGetVanishedFolder()
    {
        $mail = new Folder\Mbox($this->_params);
        $root = $mail->getFolders();
        $root->foobar = new Folder('foobar', DIRECTORY_SEPARATOR . 'foobar');

        try {
            $mail->selectFolder('foobar');
        } catch (\Exception $e) {
            return; // ok
        }

        $this->fail('no error while getting vanished folder');
    }
}
