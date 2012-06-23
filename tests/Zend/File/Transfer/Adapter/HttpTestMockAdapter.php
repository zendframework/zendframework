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
class HttpTestMockAdapter extends Adapter\Http
{
    public function __construct()
    {
        self::$callbackApc = array('ZendTest\File\Transfer\Adapter\HttpTestMockAdapter', 'apcTest');
        parent::__construct();
    }

    public function isValid($files = null)
    {
        return true;
    }

    public function isValidParent($files = null)
    {
        return parent::isValid($files);
    }

    public static function isApcAvailable()
    {
        return true;
    }

    public static function apcTest($id)
    {
        return array('total' => 100, 'current' => 100, 'rate' => 10);
    }

    public static function uPTest($id)
    {
        return array('bytes_total' => 100, 'bytes_uploaded' => 100, 'speed_average' => 10, 'cancel_upload' => true);
    }

    public function switchApcToUP()
    {
        self::$callbackApc = null;
        self::$callbackUploadProgress = array('ZendTest\File\Transfer\Adapter\HttpTestMockAdapter', 'uPTest');
    }
}
