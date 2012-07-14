<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace ZendTest\Amf\TestAsset\Server;

/**
 * Example class for sending a session back to ActionScript.
 */
class testSession
{
    /** Check if the session is available or create it. */
    public function __construct()
    {
        if (!isset($_SESSION['count'])) {
            $_SESSION['count'] = 0;
        }
    }

    /** increment the current count session variable and return it's value */
    public function getCount()
    {
        $_SESSION['count']++;
        return $_SESSION['count'];
    }
}

