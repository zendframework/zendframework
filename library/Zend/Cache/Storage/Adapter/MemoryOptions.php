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

use Zend\Cache\Exception;

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
     * - A number less or equal 0 will disable the memory limit
     * - When a number is used, the value is measured in bytes. Shorthand notation may also be used.
     * - If the used memory of PHP exceeds this limit an OutOfSpaceException
     *   will be thrown.
     *
     * @link http://php.net/manual/faq.using.php#faq.using.shorthandbytes
     * @param  string|int $memoryLimit
     * @return MemoryOptions
     */
    public function setMemoryLimit($memoryLimit)
    {
        $memoryLimit = $this->normalizeMemoryLimit($memoryLimit);

        if ($this->memoryLimit != $memoryLimit) {
            $this->triggerOptionEvent('memory_limit', $memoryLimit);
            $this->memoryLimit = $memoryLimit;
        }

        return $this;
    }

    /**
     * Get memory limit
     *
     * If the used memory of PHP exceeds this limit an OutOfSpaceException
     * will be thrown.
     *
     * @return int
     */
    public function getMemoryLimit()
    {
        if ($this->memoryLimit === null) {
            // By default use half of PHP's memory limit if possible
            $memoryLimit = $this->normalizeMemoryLimit(ini_get('memory_limit'));
            if ($memoryLimit >= 0) {
                $this->memoryLimit = (int)($memoryLimit / 2);
            } else {
                // disable memory limit
                $this->memoryLimit = 0;
            }
        }

        return $this->memoryLimit;
    }

    /**
     * Normalized a given value of memory limit into the number of bytes
     *
     * @param string|int $value
     * @return int
     */
    protected function normalizeMemoryLimit($value)
    {
        if (is_numeric($value)) {
            return (int)$value;
        }

        if (!preg_match('/(\-?\d+)\s*(\w*)/', ini_get('memory_limit'), $matches)) {
            throw new Exception\InvalidArgumentException("Invalid  memory limit '{$value}'");
        }

        $value = (int)$matches[1];
        if ($value <= 0) {
            return 0;
        }

        switch (strtoupper($matches[2])) {
            case 'G':
                $value*= 1024;
                // Break intentionally omitted

            case 'M':
                $value*= 1024;
                // Break intentionally omitted

            case 'K':
                $value*= 1024;
                // Break intentionally omitted
        }

        return $value;
    }

}
