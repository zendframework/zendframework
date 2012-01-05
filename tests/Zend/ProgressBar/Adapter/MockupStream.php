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
 * @package    Zend_Text
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\ProgressBar\Adapter;

/**
 * @category   Zend
 * @package    Zend_ProgressBar
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class MockupStream
{

    private $position;

    private $test;

    public static $tests = array();

    function stream_open($path, $mode, $options, &$opened_path)
    {
        $url = parse_url($path);
        $this->test = $url["host"];
        $this->position = 0;

        self::$tests[$url["host"]] = '';
        return true;
    }

    function stream_read($count)
    {
        $ret = substr(self::$tests[$this->test], $this->position, $count);
        $this->position += strlen($ret);
        return $ret;
    }

    function stream_write($data)
    {
        $left = substr(self::$tests[$this->test], 0, $this->position);
        $right = substr(self::$tests[$this->test], $this->position + strlen($data));
        self::$tests[$this->test] = $left . $data . $right;
        $this->position += strlen($data);
        return strlen($data);
    }

    function stream_tell()
    {
        return $this->position;
    }

    function stream_eof()
    {
        return $this->position >= strlen(self::$tests[$this->test]);
    }

    function stream_seek($offset, $whence)
    {
        switch ($whence) {
            case SEEK_SET:
                if ($offset < strlen(self::$tests[$this->test]) && $offset >= 0) {
                    $this->position = $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            case SEEK_CUR:
                if ($offset >= 0) {
                    $this->position += $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            case SEEK_END:
                if (strlen(self::$tests[$this->test]) + $offset >= 0) {
                    $this->position = strlen(self::$tests[$this->test]) + $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            default:
                return false;
        }
    }

    public function __destruct() {
        unset(self::$tests[$this->test]);
    }
}
