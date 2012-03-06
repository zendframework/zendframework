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
 * @package    Zend_Config
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Config\Writer;

use Zend\Config\Writer,
    Zend\Config\Exception,
    Zend\Config\Config,
    Zend\Stdlib\IteratorToArray,
    Traversable;

/**
 * @category   Zend
 * @package    Zend_Config
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractWriter implements Writer
{
    /**
     * toFile(): defined by Writer interface.
     *
     * @see    Writer::toFile()
     * @param  string  $filename
     * @param  mixed   $config
     * @param  boolean $exclusiveLock
     * @return void
     */
    public function toFile($filename, $config, $exclusiveLock = true)
    {
        if (empty($filename)) {
            throw new Exception\InvalidArgumentException('No file name specified');
        }
        
        $flags = 0;

        if ($exclusiveLock) {
            $flags |= LOCK_EX;
        }
        
        set_error_handler(
            function($error, $message = '', $file = '', $line = 0) use ($filename) {
                throw new Exception\RuntimeException(sprintf(
                    'Error writing to "%s": %s',
                    $filename, $message
                ), $error);
            }, E_WARNING
        );
        file_put_contents($filename, $this->toString($config), $exclusiveLock);
        restore_error_handler();
    }

    /**
     * toString(): defined by Writer interface.
     *
     * @see    Writer::toString()
     * @param  mixed   $config
     * @return void
     */
    public function toString($config)
    {
        if ($config instanceof Traversable) {
            $config = IteratorToArray::convert($config);
        } elseif (!is_array($config)) {
            throw new Exception\InvalidArgumentException(__METHOD__ . ' expects an array or Traversable config');
        }

        return $this->processConfig($config);
    }

    /**
     * Process an array configuration.
     *
     * @return string
     */
    abstract protected function processConfig(array $config);
}
