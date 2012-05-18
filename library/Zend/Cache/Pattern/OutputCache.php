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
 * @package    Zend_Cache
 * @subpackage Pattern
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache\Pattern;

use Zend\Cache\Exception,
    Zend\Cache\StorageFactory,
    Zend\Cache\Storage\Adapter\AdapterInterface as StorageAdapter;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Pattern
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class OutputCache extends AbstractPattern
{
    /**
     * The key stack
     *
     * @var array
     */
    protected $keyStack = array();

    /**
     * Set options
     *
     * @param  PatternOptions $options
     * @throws Exception\InvalidArgumentException
     */
    public function setOptions(PatternOptions $options)
    {
        parent::setOptions($options);

        if (!$options->getStorage()) {
            throw new Exception\InvalidArgumentException("Missing option 'storage'");
        }
        return $this;
    }

    /**
     * if there is a cached item with the given key display it's data and return true
     * else start buffering output until end() is called or the script ends.
     *
     * @param  string  $key            Key
     * @param  array   $storageOptions Options passing to Zend\Cache\Storage\Adapter\AdapterInterface::getItem
     * @return boolean
     * @throws Exception
     */
    public function start($key, array $storageOptions = array())
    {
        if (($key = (string) $key) === '') {
            throw new Exception\MissingKeyException('Missing key to read/write output from cache');
        }

        $success = null;
        $data    = $this->getOptions()->getStorage()->getItem($key, $storageOptions, $success);
        if ($success) {
            echo $data;
            return true;
        }

        ob_start();
        ob_implicit_flush(false);
        $this->keyStack[] = $key;
        return false;
    }

    /**
     * Stops bufferung output, write buffered data to cache using the given key on start()
     * and displays the buffer.
     *
     * @param  array   $storageOptions Options passed to Zend\Cache\Storage\Adapter\AdapterInterface::setItem
     * @return boolean TRUE on success, FALSE on failure writing to cache
     * @throws Exception
     */
    public function end(array $storageOptions = array())
    {
        $key = array_pop($this->keyStack);
        if ($key === null) {
            throw new Exception\RuntimeException('Output cache not started');
        }

        $output = ob_end_flush();
        if ($output === false) {
            throw new Exception\RuntimeException('Output buffering not active');
        }

        return $this->getOptions()->getStorage()->setItem($key, $output, $storageOptions);
    }
}
