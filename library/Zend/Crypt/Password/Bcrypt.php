<?php

namespace Zend\Crypt\Password;

use Zend\Math\Math,
    Zend\Config\Config;

/**
 * Bcrypt algorithm using crypt() function of PHP
 * 
 */
class Bcrypt implements PasswordInterface
{
    const MIN_SALT_SIZE = 16;
    protected $cost = '14';
    protected $salt;
    /**
     * Constructor
     * 
     * @param array $options 
     */
    public function __construct($options=array())
    {
        if (!empty($options)) {
            if ($options instanceof Config) {
                $options = $options->toArray();
            } elseif ($options instanceof Traversable) {
                $options = iterator_to_array($options);
            } elseif (!is_array($options)) {
                throw new Exception\InvalidArgumentException(
                    'The options parameter must be an array, a Zend\Config\Config object or a Traversable'
                );
            }
            foreach ($options as $key => $value) {
                switch (strtolower($key)) {
                    case 'salt':
                        $this->setSalt($value);
                        break;
                    case 'cost':
                        $this->setCost($value);
                        break;
                }
            }
        }    
    }
    /**
     * Bcrypt 
     * 
     * @param  string $data
     * @param  string $cost
     * @param  string $salt 
     * @return string
     */
    public function create($password)
    {
        if (empty($this->salt)) {
            $salt = Math::randBytes(self::MIN_SALT_SIZE, true);
        } else {
            $salt = $this->salt;
        }
        $salt64 = substr(str_replace('+', '.', base64_encode($salt)), 0, 22); 
        $hash = crypt($password, '$2a$' . $this->cost . '$' . $salt64);
        if (strlen($hash)<=13) {
            throw new Exception\RuntimeException('Error during the bcrypt generation');
        }
        return $hash;
    }
    /**
     * Verify if a password is correct against an hash value
     * 
     * @param  string $password
     * @param  string $hash
     * @return boolean 
     */
    public function verify($password, $hash)
    {
        return ($hash === crypt($password, $hash));
    }
    /**
     * Set the cost parameter
     * 
     * @param  integer|string $cost
     * @return Bcrypt 
     */
    public function setCost($cost)
    {
        if (!empty($cost)) {
            $cost = (int) $cost;
            if ($cost<4 || $cost>31) {
                throw new Exception\InvalidArgumentException(
                    'The cost parameter of bcrypt must be in range 04-31'
                );
            }
            $this->cost = sprintf('%1$02d', $cost);
        }
        return $this;
    }
    /**
     * Get the cost parameter
     * 
     * @return string 
     */
    public function getCost()
    {
        return $this->cost;
    }
    /**
     * Set the salt value
     * 
     * @param  string $salt
     * @return Bcrypt 
     */
    public function setSalt($salt)
    {
        if (strlen($salt) < self::MIN_SALT_SIZE) {
            throw new Exception\InvalidArgumentException(
                'The length of the salt must be at lest ' . self::MIN_SALT_SIZE . ' bytes'
            );
        }
        $this->salt = $salt;
        return $this;
    }
    /**
     * Get the salt value
     * 
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }
}
