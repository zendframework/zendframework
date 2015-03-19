<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\AllTests\StreamWrapper;

/**
 * Class for mocking php://input
 *
 * <code>
 * class ...
 * {
 *     public function setUp()
 *     {
 *         Zend\AllTests\StreamWrapper\PHPInput::mockInput('expected string');
 *     }
 *
 *     public function testReadingFromPhpInput()
 *     {
 *         $this->assertSame('expected string', file_get_contents('php://input'));
 *         $this->assertSame('php://input', Zend\AllTests\StreamWrapper\PHPInput::getCurrentPath());
 *     }
 *
 *     public function tearDown()
 *     {
 *         Zend\AllTests\StreamWrapper\PHPInput::restoreDefault();
 *     }
 * }
 */
class PHPInput
{
    protected static $_data;

    protected static $_returnValues = array();

    protected static $_arguments = array();

    protected $_position = 0;

    public static function mockInput($data)
    {
        stream_wrapper_unregister('php');
        stream_wrapper_register('php', 'ZendTest\\AllTests\\StreamWrapper\\PHPInput');
        static::$_data = $data;
    }

    public static function restoreDefault()
    {
        // Reset static values
        static::$_returnValues = array();
        static::$_arguments = array();

        // Restore original stream wrapper
        stream_wrapper_restore('php');
    }

    public static function methodWillReturn($methodName, $returnValue)
    {
        $methodName = strtolower($methodName);
        static::$_returnValues[$methodName] = $returnValue;
    }

    public static function argumentsPassedTo($methodName)
    {
        $methodName = strtolower($methodName);
        if (isset(static::$_arguments[$methodName])) {
            return static::$_arguments[$methodName];
        }

        return;
    }

    public function stream_open()
    {
        static::$_arguments[__FUNCTION__] = func_get_args();

        if (array_key_exists(__FUNCTION__, static::$_returnValues)) {
            return static::$_returnValues[__FUNCTION__];
        }

        return true;
    }

    public function stream_eof()
    {
        static::$_arguments[__FUNCTION__] = func_get_args();

        if (array_key_exists(__FUNCTION__, static::$_returnValues)) {
            return static::$_returnValues[__FUNCTION__];
        }

        return (0 == strlen(static::$_data));
    }

    public function stream_read($count)
    {
        static::$_arguments[__FUNCTION__] = func_get_args();

        if (array_key_exists(__FUNCTION__, static::$_returnValues)) {
            return static::$_returnValues[__FUNCTION__];
        }

        // To match the behavior of php://input, we need to clear out the data
        // as it is read
        if ($count > strlen(static::$_data)) {
            $data = static::$_data;
            static::$_data = '';
        } else {
            $data = substr(static::$_data, 0, $count);
            static::$_data = substr(static::$_data, $count);
        }
        return $data;
    }

    public function stream_stat()
    {
        static::$_arguments[__FUNCTION__] = func_get_args();

        if (array_key_exists(__FUNCTION__, static::$_returnValues)) {
            return static::$_returnValues[__FUNCTION__];
        }

        return array();
    }
}
