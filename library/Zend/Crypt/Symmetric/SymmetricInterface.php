<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Crypt\Symmetric;

interface SymmetricInterface
{
    /**
     * @param string $data
     * @return string
     */
    public function encrypt($data);
    
    /**
     * @param string $data
     * @return string
     */
    public function decrypt($data);

    /**
     * @param string $key
     * @return Mcrypt
     */
    public function setKey($key);
    
    /**
     * @return null|string
     */
    public function getKey();

    /**
     * @return int
     */
    public function getKeySize();

    /**
     * @return string
     */
    public function getAlgorithm();

    /**
     * @param  string $algo 
     * @return Mcrypt
     */
    public function setAlgorithm($algo);

    /**
     * @return string
     */
    public function getSupportedAlgorithms();

    /**
     * @param string|false $salt
     * @return Mcrypt
     */
    public function setSalt($salt);
    
    /**
     * @return null|string
     */
    public function getSalt();

    /**
     * @return int
     */
    public function getSaltSize();

    /**
     * @return int
     */
    public function getBlockSize();

    /**
     * @param string $mode
     * @return Mcrypt
     */
    public function setMode($mode);

    /**
     * @return string
     */
    public function getMode();

    /**
     * @return string
     */
    public function getSupportedModes();
}
