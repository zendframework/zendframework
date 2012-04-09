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
 * @package    Zend_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\File\Transfer\Adapter;

use Zend\File\Transfer\Adapter;

/**
 * Test class for Zend\File\Transfer\Adapter\AbstractAdapter
 *
 * @category   Zend
 * @package    Zend_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_File
 */
class AbstractAdapterTestMockAdapter extends Adapter\AbstractAdapter
{
    public $received = false;

    public $tmpDir;

    public function __construct()
    {
        $testfile = __DIR__ . '/_files/test.txt';
        $this->files = array(
            'foo' => array(
                'name'      => 'foo.jpg',
                'type'      => 'image/jpeg',
                'size'      => 126976,
                'tmp_name'  => '/tmp/489127ba5c89c',
                'options'   => array('ignoreNoFile' => false, 'useByteString' => true, 'detectInfos' => true),
                'validated' => false,
                'received'  => false,
                'filtered'  => false,
            ),
            'bar' => array(
                'name'     => 'bar.png',
                'type'     => 'image/png',
                'size'     => 91136,
                'tmp_name' => '/tmp/489128284b51f',
                'options'  => array('ignoreNoFile' => false, 'useByteString' => true),
                'validated' => false,
                'received'  => false,
                'filtered'  => false,
            ),
            'baz' => array(
                'name'     => 'baz.text',
                'type'     => 'text/plain',
                'size'     => 1172,
                'tmp_name' => $testfile,
                'options'  => array('ignoreNoFile' => false, 'useByteString' => true),
                'validated' => false,
                'received'  => false,
                'filtered'  => false,
            ),
            'file_0_' => array(
                'name'      => 'foo.jpg',
                'type'      => 'image/jpeg',
                'size'      => 126976,
                'tmp_name'  => '/tmp/489127ba5c89c',
                'options'   => array('ignoreNoFile' => false, 'useByteString' => true),
                'validated' => false,
                'received'  => false,
                'filtered'  => false,
            ),
            'file_1_' => array(
                'name'     => 'baz.text',
                'type'     => 'text/plain',
                'size'     => 1172,
                'tmp_name' => $testfile,
                'options'  => array('ignoreNoFile' => false, 'useByteString' => true),
                'validated' => false,
                'received'  => false,
                'filtered'  => false,
            ),
            'file' => array(
                'name'      => 'foo.jpg',
                'multifiles' => array(0 => 'file_0_', 1 => 'file_1_')
            ),
        );
    }

    public function send($options = null)
    {
        return;
    }

    public function receive($options = null)
    {
        $this->received = true;
        return;
    }

    public function isSent($file = null)
    {
        return false;
    }

    public function isReceived($file = null)
    {
        return $this->received;
    }

    public function isUploaded($files = null)
    {
        return true;
    }

    public function isFiltered($files = null)
    {
        return true;
    }

    public static function getProgress()
    {
        return;
    }

    public function getTmpDir()
    {
        $this->tmpDir = parent::getTmpDir();
    }

    public function isPathWriteable($path)
    {
        return parent::isPathWriteable($path);
    }

    public function addInvalidFile()
    {
        $this->files += array(
            'test' => array(
                'name'      => 'test.txt',
                'type'      => 'image/jpeg',
                'size'      => 0,
                'tmp_name'  => '',
                'options'   => array('ignoreNoFile' => true, 'useByteString' => true),
                'validated' => false,
                'received'  => false,
                'filtered'  => false,
            )
        );
    }

}
