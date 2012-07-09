<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @package   Zend_Config
 */

namespace Zend\Config\Writer;

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage Writer
 */
interface WriterInterface
{
    /**
     * Write a config object to a file.
     *
     * @param  string  $filename
     * @param  mixed   $config
     * @param  boolean $exclusiveLock
     * @return void
     */
    public function toFile($filename, $config, $exclusiveLock = true);

    /**
     * Write a config object to a string.
     *
     * @param  mixed $config
     * @return string
     */
    public function toString($config);
}
