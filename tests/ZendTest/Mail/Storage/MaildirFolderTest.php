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
class MaildirFolderTest extends \PHPUnit_Framework_TestCase
{
    protected $_params;
    protected $_originalDir;
    protected $_tmpdir;
    protected $_subdirs = array('.', '.subfolder', '.subfolder.test');

    public function setUp()
    {
        $this->_originalDir = __DIR__ . '/../_files/test.maildir/';

        if (!constant('TESTS_ZEND_MAIL_MAILDIR_ENABLED')) {
            $this->markTestSkipped('You have to unpack maildir.tar in Zend/Mail/_files/test.maildir/ '
                                 . 'directory before enabling the maildir tests');
            return;
        }

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

        foreach ($this->_subdirs as $dir) {
            if ($dir != '.') {
                mkdir($this->_tmpdir . $dir);
            }
            foreach (array('cur', 'new') as $subdir) {
                if (!file_exists($this->_originalDir . $dir . '/' . $subdir)) {
                    continue;
                }
                mkdir($this->_tmpdir . $dir . '/' . $subdir);
                $dh = opendir($this->_originalDir . $dir . '/' . $subdir);
                while (($entry = readdir($dh)) !== false) {
                    $entry = $dir . '/' . $subdir . '/' . $entry;
                    if (!is_file($this->_originalDir . $entry)) {
                        continue;
                    }
                    copy($this->_originalDir . $entry, $this->_tmpdir . $entry);
                }
                closedir($dh);
            }
        }
    }

    public function tearDown()
    {
        foreach (array_reverse($this->_subdirs) as $dir) {
            foreach (array('cur', 'new') as $subdir) {
                if (!file_exists($this->_tmpdir . $dir . '/' . $subdir)) {
                    continue;
                }
                if (!is_dir($this->_tmpdir . $dir . '/' . $subdir)) {
                    continue;
                }
                $dh = opendir($this->_tmpdir . $dir . '/' . $subdir);
                while (($entry = readdir($dh)) !== false) {
                    $entry = $this->_tmpdir . $dir . '/' . $subdir . '/' . $entry;
                    if (!is_file($entry)) {
                        continue;
                    }
                    unlink($entry);
                }
                closedir($dh);
                rmdir($this->_tmpdir . $dir . '/' . $subdir);
            }
            if ($dir != '.' && is_dir($this->_tmpdir . $dir)) {
                rmdir($this->_tmpdir . $dir);
            }
        }
    }

    public function testLoadOk()
    {
        new Folder\Maildir($this->_params);
    }

    public function testLoadConfig()
    {
        new Folder\Maildir(new Config\Config($this->_params));
    }

    public function testNoParams()
    {
        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        new Folder\Maildir(array());
    }

    public function testLoadFailure()
    {
        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        new Folder\Maildir(array('dirname' => 'This/Folder/Does/Not/Exist'));
    }

    public function testLoadUnkownFolder()
    {
        $this->_params['folder'] = 'UnknownFolder';
        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        new Folder\Maildir($this->_params);
    }

    public function testChangeFolder()
    {
        $this->markTestIncomplete("Fail");
        $mail = new Folder\Maildir($this->_params);

        $mail->selectFolder('subfolder.test');

        $this->assertEquals($mail->getCurrentFolder(), 'subfolder.test');
    }

    public function testUnknownFolder()
    {
        $mail = new Folder\Maildir($this->_params);

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->selectFolder('/Unknown/Folder/');
    }

    public function testGlobalName()
    {
        $this->markTestIncomplete("Fail");
        $mail = new Folder\Maildir($this->_params);

        $this->assertEquals($mail->getFolders()->subfolder->__toString(), 'subfolder');
    }

    public function testLocalName()
    {
        $this->markTestIncomplete("Fail");
        $mail = new Folder\Maildir($this->_params);

        $this->assertEquals($mail->getFolders()->subfolder->key(), 'test');
    }

    public function testIterator()
    {
        $this->markTestIncomplete("Fail");
        $mail = new Folder\Maildir($this->_params);
        $iterator = new \RecursiveIteratorIterator($mail->getFolders(), \RecursiveIteratorIterator::SELF_FIRST);
        // we search for this folder because we can't assume a order while iterating
        $search_folders = array('subfolder'      => 'subfolder',
                                'subfolder.test' => 'test',
                                'INBOX'          => 'INBOX');
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
        $this->markTestIncomplete("Fail");
        $mail = new Folder\Maildir($this->_params);
        $iterator = new \RecursiveIteratorIterator($mail->getFolders(), \RecursiveIteratorIterator::SELF_FIRST);
        // we search for this folder because we can't assume a order while iterating
        $search_folders = array('subfolder'      => 'subfolder',
                                'subfolder.test' => 'test',
                                'INBOX'          => 'INBOX');
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

    public function testInboxEquals()
    {
        $this->markTestIncomplete("Fail");
        $mail = new Folder\Maildir($this->_params);
        $iterator = new \RecursiveIteratorIterator($mail->getFolders('INBOX.subfolder'), \RecursiveIteratorIterator::SELF_FIRST);
        // we search for this folder because we can't assume a order while iterating
        $search_folders = array('subfolder.test' => 'test');
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
        $mail = new Folder\Maildir($this->_params);
        $iterator = new \RecursiveIteratorIterator($mail->getFolders(), \RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $localName => $folder) {
            $this->assertEquals($localName, $folder->getLocalName());
        }
    }


    public function testCount()
    {
        $this->markTestIncomplete("Fail");
        $mail = new Folder\Maildir($this->_params);

        $count = $mail->countMessages();
        $this->assertEquals(5, $count);

        $mail->selectFolder('subfolder.test');
        $count = $mail->countMessages();
        $this->assertEquals(1, $count);
    }

    public function testSize()
    {
        $this->markTestIncomplete("Fail");
        $mail = new Folder\Maildir($this->_params);
        $shouldSizes = array(1 => 397, 89, 694, 452, 497);

        $sizes = $mail->getSize();
        $this->assertEquals($shouldSizes, $sizes);

        $mail->selectFolder('subfolder.test');
        $sizes = $mail->getSize();
        $this->assertEquals(array(1 => 467), $sizes);
    }

    public function testFetchHeader()
    {
        $this->markTestIncomplete("Fail");
        $mail = new Folder\Maildir($this->_params);

        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Simple Message', $subject);

        $mail->selectFolder('subfolder.test');
        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Message in subfolder', $subject);
    }

    public function testNotReadableFolder()
    {
        $this->markTestIncomplete("Fail");
        $stat = stat($this->_params['dirname'] . '.subfolder');
        chmod($this->_params['dirname'] . '.subfolder', 0);
        clearstatcache();
        $statcheck = stat($this->_params['dirname'] . '.subfolder');
        if ($statcheck['mode'] % (8 * 8 * 8) !== 0) {
            chmod($this->_params['dirname'] . '.subfolder', $stat['mode']);
            $this->markTestSkipped('cannot remove read rights, which makes this test useless (maybe you are using Windows?)');
            return;
        }

        $check = false;
        try {
            $mail = new Folder\Maildir($this->_params);
        } catch (\Exception $e) {
            $check = true;
            // test ok
        }

        chmod($this->_params['dirname'] . '.subfolder', $stat['mode']);

        if (!$check) {
           $this->fail('no exception while loading invalid dir with subfolder not readable');
        }
    }

    public function testNotReadableMaildir()
    {
        $stat = stat($this->_params['dirname']);
        chmod($this->_params['dirname'], 0);
        clearstatcache();
        $statcheck = stat($this->_params['dirname']);
        if ($statcheck['mode'] % (8 * 8 * 8) !== 0) {
            chmod($this->_params['dirname'], $stat['mode']);
            $this->markTestSkipped('cannot remove read rights, which makes this test useless (maybe you are using Windows?)');
            return;
        }

        $check = false;
        try {
            $mail = new Folder\Maildir($this->_params);
        } catch (\Exception $e) {
            $check = true;
            // test ok
        }

        chmod($this->_params['dirname'], $stat['mode']);

        if (!$check) {
           $this->fail('no exception while loading not readable maildir');
        }
    }

    public function testGetInvalidFolder()
    {
        $mail = new Folder\Maildir($this->_params);
        $root = $mail->getFolders();
        $root->foobar = new Folder('foobar', DIRECTORY_SEPARATOR . 'foobar');

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->selectFolder('foobar');
    }

    public function testGetVanishedFolder()
    {
        $mail = new Folder\Maildir($this->_params);
        $root = $mail->getFolders();
        $root->foobar = new Folder('foobar', 'foobar');

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->selectFolder('foobar');
    }

    public function testGetNotSelectableFolder()
    {
        $mail = new Folder\Maildir($this->_params);
        $root = $mail->getFolders();
        $root->foobar = new Folder('foobar', 'foobar', false);

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->selectFolder('foobar');
    }

    public function testWithAdditionalFolder()
    {
        mkdir($this->_params['dirname'] . '.xyyx');
        mkdir($this->_params['dirname'] . '.xyyx/cur');

        $mail = new Folder\Maildir($this->_params);
        $mail->selectFolder('xyyx');
        $this->assertEquals($mail->countMessages(), 0);

        rmdir($this->_params['dirname'] . '.xyyx/cur');
        rmdir($this->_params['dirname'] . '.xyyx');
    }
}
