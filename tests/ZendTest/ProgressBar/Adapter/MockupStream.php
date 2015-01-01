<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\ProgressBar\Adapter;

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

        static::$tests[$url["host"]] = '';
        return true;
    }

    public function stream_read($count)
    {
        $ret = substr(static::$tests[$this->test], $this->position, $count);
        $this->position += strlen($ret);
        return $ret;
    }

    public function stream_write($data)
    {
        $left = substr(static::$tests[$this->test], 0, $this->position);
        $right = substr(static::$tests[$this->test], $this->position + strlen($data));
        static::$tests[$this->test] = $left . $data . $right;
        $this->position += strlen($data);
        return strlen($data);
    }

    public function stream_tell()
    {
        return $this->position;
    }

    public function stream_eof()
    {
        return $this->position >= strlen(static::$tests[$this->test]);
    }

    public function stream_seek($offset, $whence)
    {
        switch ($whence) {
            case SEEK_SET:
                if ($offset < strlen(static::$tests[$this->test]) && $offset >= 0) {
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
                if (strlen(static::$tests[$this->test]) + $offset >= 0) {
                    $this->position = strlen(static::$tests[$this->test]) + $offset;
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
        unset(static::$tests[$this->test]);
    }
}
