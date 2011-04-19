<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-webat this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Session
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Session\Storage;

/**
 * Session storage in $_SESSION
 *
 * Replaces the $_SESSION superglobal with an ArrayObject that allows for 
 * property access, metadata storage, locking, and immutability.
 * 
 * @category   Zend
 * @package    Zend_Session
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SessionStorage extends ArrayStorage
{
    /**
     * Constructor
     *
     * Sets the $_SESSION superglobal to an ArrayObject, maintaining previous 
     * values if any discovered.
     * 
     * @param  null|array|ArrayAccess $input 
     * @param  int $flags 
     * @param  string $iteratorClass 
     * @return void
     */
    public function __construct($input = null, $flags = \ArrayObject::ARRAY_AS_PROPS, $iteratorClass = '\\ArrayIterator')
    {
        $resetSession = true;
        if ((null === $input) && isset($_SESSION)) {
            $input = $_SESSION;
            if (is_object($input) && $_SESSION instanceof \ArrayObject) {
                $resetSession = false;
            } elseif (is_object($input) && !$_SESSION instanceof \ArrayObject) {
                $input = (array) $input;
            }
        } elseif (null === $input) {
            $input = array();
        }
        parent::__construct($input, $flags, $iteratorClass);
        if ($resetSession) {
            $_SESSION = $this;
        }
    }

    /**
     * Destructor
     *
     * Resets $_SESSION superglobal to an array, by casting object using 
     * getArrayCopy().
     * 
     * @return void
     */
    public function __destruct()
    {
        $_SESSION = (array) $this->getArrayCopy();
    }

    /**
     * Load session object from an existing array
     *
     * Ensures $_SESSION is set to an instance of the object when complete.
     * 
     * @param  array $array 
     * @return SessionStorage
     */
    public function fromArray(array $array)
    {
        $this->exchangeArray($array);
        if ($_SESSION !== $this) {
            $_SESSION = $this;
        }
        return $this;
    }

    /**
     * Mark object as immutable
     * 
     * @return void
     */
    public function markImmutable()
    {
        $this['_IMMUTABLE'] = true;
    }

    /**
     * Determine if this object is immutable
     * 
     * @return bool
     */
    public function isImmutable()
    {
        return (isset($this['_IMMUTABLE']) && $this['_IMMUTABLE']);
    }
}
