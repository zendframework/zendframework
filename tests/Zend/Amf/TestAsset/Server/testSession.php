<?php

namespace ZendTest\Amf\TestAsset\Server;

/**
 * Example class for sending a session back to ActionScript.
 */
class testSession
{
    /** Check if the session is available or create it. */
    public function __construct() {
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

