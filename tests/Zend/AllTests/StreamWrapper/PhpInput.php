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
 * @package    Zend
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Class for mocking php://input
 *
 * To use: 
 * <code>
 * Zend_AllTests_StreamWrapper_PhpInput::mockInput($string);
 * $value = file_get_contents('php://input');
 * </code>
 *
 * Once done, call stream_wrapper_restore('php') to restore the original behavior.
 *
 * @category   Zend
 * @package    Zend
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_AllTests_StreamWrapper_PhpInput
{
    protected static $_data;

    protected $_position = 0;

    public static function mockInput($data)
    {
        stream_wrapper_unregister('php');
        stream_wrapper_register('php', 'Zend_AllTests_StreamWrapper_PhpInput');
        self::$_data = $data;
    }

    public function stream_open()
    {
        return true;
    }

    public function stream_eof()
    {
        return (0 == strlen(self::$_data));
    }

    public function stream_read($count)
    {
        // To match the behavior of php://input, we need to clear out the data 
        // as it is read
        if ($count > strlen(self::$_data)) {
            $data = self::$_data;
            self::$_data = '';
        } else {
            $data = substr(self::$_data, 0, $count);
            self::$_data = substr(self::$_data, $count);
        }
        return $data;
    }

    public function stream_stat()
    {
        return array();
    }
}
