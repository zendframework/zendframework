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
 * @package    Zend_Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mail\Transport;

use Zend\Mail\Exception,
    Zend\Stdlib\Options;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FileOptions extends Options
{
    /**
     * @var string Local client hostname
     */
    protected $path;

    /**
     * @var Callable
     */
    protected $callback;

    /**
     * Set path to stored mail files
     * 
     * @param  string $path 
     * @return FileOptions
     */
    public function setPath($path)
    {
        if (!is_dir($path) || !is_writable($path)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a valid path in which to write mail files; received "%s"',
                __METHOD__,
                (string) $path
            ));
        }
        $this->path = $path;
        return $this;
    }

    /**
     * Get path
     *
     * If none is set, uses value from sys_get_temp_dir()
     * 
     * @return string
     */
    public function getPath()
    {
        if (null === $this->path) {
            $this->setPath(sys_get_temp_dir());
        }
        return $this->path;
    }

    /**
     * Set callback used to generate a file name
     * 
     * @param  Callable $callback 
     * @return FileOptions
     */
    public function setCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a valid callback; received "%s"',
                __METHOD__,
                (is_object($callback) ? get_class($callback) : gettype($callback))
            ));
        }
        $this->callback = $callback;
        return $this;
    }

    /**
     * Get callback used to generate a file name
     * 
     * @return Callable
     */
    public function getCallback()
    {
        if (null === $this->callback) {
            $this->setCallback(function($transport) {
                return 'ZendMail_' . time() . '_' . mt_rand() . '.tmp';
            });
        }
        return $this->callback;
    }
}
