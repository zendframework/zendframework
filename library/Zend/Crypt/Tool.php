<?php

namespace Zend\Crypt;

class Tool
{
    /**
     * Compare two strings to avoid timing attacks
     *
     * @param  string $stringA
     * @param  string $stringB
     * @return boolean
     */
    public static function compareString($stringA, $stringB)
    {
        $stringA = (string) $stringA;
        $stringB = (string) $stringB;
        if (strlen($stringA) === 0) {
            return false;
        }
        if (strlen($stringA) !== strlen($stringB)) {
            return false;
        }
        $result = 0;
        $len    = strlen($stringA);
        for ($i = 0; $i < $len; $i++) {
            $result |= ord($stringA{$i}) ^ ord($stringB{$i});
        }
        return $result === 0;
    }
}
