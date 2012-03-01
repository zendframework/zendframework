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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Session\SaveHandler;

use Zend\Session\SaveHandler as Savable,
    Zend\Cache\Storage\Adapter as StorageAdapter,
    Zend\Session\Exception;

/**
 * Cache session save handler
 *
 * @uses       Zend\Config
 * @uses       Zend\Cache\Storage\Adapter
 * @uses       Zend\Session\SaveHandler\Exception
 * @category   Zend
 * @package    Zend_Session
 * @subpackage SaveHandler
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Cache implements Savable
{
    /**
     * Session Save Path
     *
     * @var string
     */
    protected $sessionSavePath;

    /**
     * Session Name
     *
     * @var string
     */
    protected $sessionName;

    /**
     * Constructor
     *
     * @param  Zend\Cache\Storage\Adapter $storageAdapter
     * @return void
     * @throws Zend\Session\Exception
     */
    public function __construct(StorageAdapter $storageAdapter)
    {
        $this->setStorageAdapter($storageAdapter);
    }

    /**
     * Open Session
     *
     * @param string $save_path
     * @param string $name
     * @return boolean
     */
    public function open($save_path, $name)
    {
        // @todo figure out if we want to use these
        $this->sessionSavePath = $save_path;
        $this->sessionName     = $name;

        return true;
    }

    /**
     * Close session
     *
     * @return boolean
     */
    public function close()
    {
        return true;
    }

    /**
     * Read session data
     *
     * @param string $id
     * @return string
     */
    public function read($id)
    {
        return $this->getStorageAdapter()->getItem($id);
    }

    /**
     * Write session data
     *
     * @param string $id
     * @param string $data
     * @return boolean
     */
    public function write($id, $data)
    {
        return $this->getStorageAdapter()->setItem($id, $data);
    }

    /**
     * Destroy session
     *
     * @param string $id
     * @return boolean
     */
    public function destroy($id)
    {
        return $this->getStorageAdapter()->removeItem($id);
    }

    /**
     * Garbage Collection
     *
     * @param int $maxlifetime
     * @return true
     */
    public function gc($maxlifetime)
    {
        return true;
    }

    /**
     * Set cache storage adapter
     *
     * Allows passing a string class name or StorageAdapter object.
     *
     * @param Zend\Cache\Storage\Adapter
     * @return void
     */
    public function setStorageAdapter(StorageAdapter $storageAdapter)
    {
        $this->storageAdapter = $storageAdapter;
    }

    /**
     * Get Cache Storage Adapter Object
     *
     * @return Zend\Cache\Storage\Adapter
     */
    public function getStorageAdapter()
    {
        return $this->storageAdapter;
    }
}
