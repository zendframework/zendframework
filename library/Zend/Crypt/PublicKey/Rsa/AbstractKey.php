<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Crypt
 */
namespace Zend\Crypt\PublicKey\Rsa;

use Countable;

/**
 * @category   Zend
 * @package    Zend_Crypt
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractKey implements Countable
{
    /**
     * @var string
     */
    protected $pemString = null;

    /**
     * @var string
     */
    protected $certificateString = null;

    /**
     * Bits, key string and type of key
     *
     * @var array
     */
    protected $details = array();

    /**
     * Key Resource
     *
     * @var resource
     */
    protected $opensslKeyResource = null;

    /**
     * Retrieves key resource
     *
     * @return resource
     */
    public function getOpensslKeyResource()
    {
        return $this->opensslKeyResource;
    }

    /**
     * To string
     *
     * @return string
     * @throws Exception\RuntimeException
     */
    public function toString()
    {
        if (!empty($this->pemString)) {
            return $this->pemString;
        } elseif (!empty($this->certificateString)) {
            return $this->certificateString;
        }
        throw new Exception\RuntimeException('No public key string representation is available');
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Count
     *
     * @return integer
     */
    public function count()
    {
        return $this->details['bits'];
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->details['type'];
    }
}
