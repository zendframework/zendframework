<?php

namespace Zend\Crypt\Password;

interface PasswordInterface 
{
    /**
     * Create a password hash for a given plain text password
     *
     * @param  string $password The password to hash
     * @return string The formatted password hash
     */
    public function create($password);
    /**
     * Verify a password hash against a given plain text password
     *
     * @param  string $password The password to hash
     * @param  string $hash     The supplied hash to validate
     * @return boolean Does the password validate against the hash
     */
    public function verify($password, $hash);
}
