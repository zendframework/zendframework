<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ProgressBar
 */

namespace ZendTest\ProgressBar\Adapter;

/**
 * @category   Zend
 * @package    Zend_ProgressBar
 * @subpackage UnitTests
 */
class MockupStream
{

    private $position;

    private $test;

    public static $tests = array();

    public function stream_open($path, $mode, $options, &$opened_path)
    {
        $url = parse_url($path);
        $this->test = $url["host"];
        $this->position = 0;

        self::$tests[$url["host"]] = '';
        return true;
    }

    public function stream_read($count)
    {
        $ret = substr(self::$tests[$this->test], $this->position, $count);
        $this->position += strlen($ret);
        return $ret;
    }

    public function stream_write($data)
    {
        $left = substr(self::$tests[$this->test], 0, $this->position);
        $right = substr(self::$tests[$this->test], $this->position + strlen($data));
        self::$tests[$this->test] = $left . $data . $right;
        $this->position += strlen($data);
        return strlen($data);
    }

    public function stream_tell()
    {
        return $this->position;
    }

    public function stream_eof()
    {
        return $this->position >= strlen(self::$tests[$this->test]);
    }

    public function stream_seek($offset, $whence)
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

    public function __destruct()
    {
        unset(self::$tests[$this->test]);
    }
}
