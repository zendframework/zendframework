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
 * @package    Writer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Config\Writer;
use Zend\Config;

/**
 * Abstract File Writer
 *
 * @uses       \Zend\Config\Exception
 * @uses       \Zend\Config\Writer\Writer
 * @category   Zend
 * @package    Zend_package
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AbstractFileWriter extends AbstractWriter
{
    /**
     * Filename to write to
     *
     * @var string
     */
    protected $_filename = null;

    /**
     * Whether to exclusively lock the file or not
     *
     * @var boolean
     */
    protected $_exclusiveLock = false;

    /**
     * Set the target filename
     *
     * @param  string $filename
     * @return \Zend\Config\Writer\AbstractFileWriter
     */
    public function setFilename($filename)
    {
        $this->_filename = $filename;

        return $this;
    }

    /**
     * Set wether to exclusively lock the file or not
     *
     * @param  boolean     $exclusiveLock
     * @return \Zend\Config\Writer\AbstractFileWriter
     */
    public function setExclusiveLock($exclusiveLock)
    {
        $this->_exclusiveLock = $exclusiveLock;

        return $this;
    }

    /**
     * Write configuration to file.
     *
     * @param string $filename
     * @param \Zend\Config\Config $config
     * @param bool $exclusiveLock
     * @return void
     */
    public function write($filename = null, Config\Config $config = null, $exclusiveLock = null)
    {
        if ($filename !== null) {
            $this->setFilename($filename);
        }

        if ($config !== null) {
            $this->setConfig($config);
        }

        if ($exclusiveLock !== null) {
            $this->setExclusiveLock($exclusiveLock);
        }

        if ($this->_filename === null) {
            throw new Config\Exception\InvalidArgumentException('No filename was set');
        }

        if ($this->_config === null) {
            throw new Config\Exception\InvalidArgumentException('No config was set');
        }

        $configString = $this->render();

        $flags = 0;

        if ($this->_exclusiveLock) {
            $flags |= LOCK_EX;
        }

        $result = @file_put_contents($this->_filename, $configString, $flags);

        if ($result === false) {
            throw new Config\Exception\RuntimeException('Could not write to file "' . $this->_filename . '"');
        }
    }

    /**
     * Render a Zend_Config into a config file string.
     *
     * @since 1.10
     * @todo For 2.0 this should be redone into an abstract method.
     * @return string
     */
    public function render()
    {
        return "";
    }
}
