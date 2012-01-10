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
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache\Storage\Adapter;

use Zend\Cache\Utils,
    Zend\Cache\Exception;

/**
 * These are options specific to the APC adapter
 *
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class MemoryOptions extends AdapterOptions
{
    /**
     * memory limit
     *
     * @var null|int
     */
    protected $memoryLimit = null;

    /**
     * Set memory limit
     *
     * If the used memory of PHP exceeds this limit an OutOfCapacityException
     * will be thrown.
     *
     * @param  int $bytes
     * @return MemoryOptions
     */
    public function setMemoryLimit($bytes)
    {
        $this->memoryLimit = (int) $bytes;
        return $this;
    }

    /**
     * Get memory limit
     *
     * If the used memory of PHP exceeds this limit an OutOfCapacityException
     * will be thrown.
     *
     * @return int
     */
    public function getMemoryLimit()
    {
        if ($this->memoryLimit === null) {
            $memoryLimit = Utils::bytesFromString(ini_get('memory_limit'));
            if ($memoryLimit >= 0) {
                $this->memoryLimit = floor($memoryLimit / 2);
            } else {
                // use a hard memory limit of 32M if php memory limit is disabled
                $this->memoryLimit = 33554432;
            }
        }

        return $this->memoryLimit;
    }
}
