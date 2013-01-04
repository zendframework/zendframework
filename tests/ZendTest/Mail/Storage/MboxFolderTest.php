<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mail
 */

namespace ZendTest\Mail\Storage;

use Zend\Config;
use Zend\Mail\Storage\Folder;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
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
        new Folder\Mbox($this->_params);
    }

    public function testLoadConfig()
    {
        new Folder\Mbox(new Config\Config($this->_params));
    }

    public function testNoParams()
    {
        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        new Folder\Mbox(array());
    }

    public function testFilenameParam()
    {
        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        // filename is not allowed in this subclass
        new Folder\Mbox(array('filename' => 'foobar'));
    }

    public function testLoadFailure()
    {
        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        new Folder\Mbox(array('dirname' => 'This/Folder/Does/Not/Exist'));
    }

    public function testLoadUnkownFolder()
    {
        $this->_params['folder'] = 'UnknownFolder';

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        new Folder\Mbox($this->_params);
    }

    public function testChangeFolder()
    {
        $mail = new Folder\Mbox($this->_params);

        $mail->selectFolder(DIRECTORY_SEPARATOR . 'subfolder' . DIRECTORY_SEPARATOR . 'test');

        $this->assertEquals($mail->getCurrentFolder(), DIRECTORY_SEPARATOR . 'subfolder' . DIRECTORY_SEPARATOR . 'test');
    }

    public function testChangeFolderUnselectable()
    {
        $mail = new Folder\Mbox($this->_params);
        $this->setExpectedException('Zend\Mail\Storage\Exception\RuntimeException');
        $mail->selectFolder(DIRECTORY_SEPARATOR . 'subfolder');
    }

    public function testUnknownFolder()
    {
        $mail = new Folder\Mbox($this->_params);
        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->selectFolder('/Unknown/Folder/');
    }

    public function testGlobalName()
    {
        $mail = new Folder\Mbox($this->_params);

        $this->assertEquals($mail->getFolders()->subfolder->__toString(), DIRECTORY_SEPARATOR . 'subfolder');
    }

    public function testLocalName()
    {
        $mail = new Folder\Mbox($this->_params);

        $this->assertEquals($mail->getFolders()->subfolder->key(), 'test');
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

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->getFolders()->foobar;
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
            } elseif (!function_exists('posix_getuid')) {
                $this->markTestSkipped('Can\t test if you\'re root and we therefore cannot test the error handling');
            }
            $this->fail('no exception while loading invalid dir with subfolder not readable');
        }
    }

    public function testGetInvalidFolder()
    {
        $mail = new Folder\Mbox($this->_params);
        $root = $mail->getFolders();
        $root->foobar = new Folder('x', 'x');
        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->getFolders('foobar');
    }

    public function testGetVanishedFolder()
    {
        $mail = new Folder\Mbox($this->_params);
        $root = $mail->getFolders();
        $root->foobar = new Folder('foobar', DIRECTORY_SEPARATOR . 'foobar');

        $this->setExpectedException('Zend\Mail\Storage\Exception\RuntimeException');
        $mail->selectFolder('foobar');
    }
}
