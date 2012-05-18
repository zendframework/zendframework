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
 * @category  Zend
 * @package   Zend_File_Transfer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\File\Transfer;

/**
 * Base class for all protocols supporting file transfers
 *
 * @category  Zend
 * @package   Zend_File_Transfer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Transfer
{
    /**
     * Array holding all directions
     *
     * @var array
     */
    protected $adapter = array();

    /**
     * Creates a file processing handler
     *
     * @param  string  $adapter   Adapter to use
     * @param  boolean $direction OPTIONAL False means Download, true means upload
     * @param  array   $options   OPTIONAL Options to set for this adapter
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($adapter = 'Http', $direction = false, $options = array())
    {
        $this->setAdapter($adapter, $direction, $options);
    }

    /**
     * Sets a new adapter
     *
     * @param  string  $adapter   Adapter to use
     * @param  boolean $direction OPTIONAL False means Download, true means upload
     * @param  array   $options   OPTIONAL Options to set for this adapter
     * @return Transfer
     * @throws Exception\InvalidArgumentException
     */
    public function setAdapter($adapter, $direction = false, $options = array())
    {
        if (!is_string($adapter)) {
            throw new Exception\InvalidArgumentException('Adapter must be a string');
        }
        
        if ($adapter[0] != '\\') {
            $adapter = '\Zend\File\Transfer\Adapter\\' . ucfirst($adapter);
        }

        $direction = (integer) $direction;
        $this->adapter[$direction] = new $adapter($options);
        if (!$this->adapter[$direction] instanceof Adapter\AbstractAdapter) {
            throw new Exception\InvalidArgumentException(
                'Adapter ' . $adapter . ' does not extend Zend\File\Transfer\Adapter\AbstractAdapter'
            );
        }

        return $this;
    }

    /**
     * Returns all set adapters
     *
     * @param boolean $direction On null, all directions are returned
     *                           On false, download direction is returned
     *                           On true, upload direction is returned
     * @return array|Adapter\AbstractAdapter
     */
    public function getAdapter($direction = null)
    {
        if ($direction === null) {
            return $this->adapter;
        }

        $direction = (integer) $direction;
        return $this->adapter[$direction];
    }

    /**
     * Calls all methods from the adapter
     *
     * @param  string $method  Method to call
     * @param  array  $options Options for this method
     * @return mixed
     */
    public function __call($method, array $options)
    {
        if (array_key_exists('direction', $options)) {
            $direction = (integer) $options['direction'];
        } else {
            $direction = 0;
        }

        if (method_exists($this->adapter[$direction], $method)) {
            return call_user_func_array(array($this->adapter[$direction], $method), $options);
        }

        throw new Exception\BadMethodCallException("Unknown method '" . $method . "' called!");
    }
}
