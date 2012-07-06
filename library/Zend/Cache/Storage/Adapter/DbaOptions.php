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
class DbaOptions extends AdapterOptions
{
    /**
     * Namespace separator
     *
     * @var string
     */
    protected $namespaceSeparator = ':';

    /**
     * Pathname to the database file
     *
     * @var string
     */
    protected $pathname = '';

    /**
     * The mode to open the database
     *
     * @var string
     */
    protected $mode = 'c';

    /**
     * The name of the handler which shall be used for accessing the database.
     *
     * @var string
     */
    protected $handler = 'flatfile';

    /**
     * Set namespace separator
     *
     * @param  string $separator
     * @return DbaOptions
     */
    public function setNamespaceSeparator($separator)
    {
        $separator = (string) $separator;
        $this->triggerOptionEvent('namespace_separator', $separator);
        $this->namespaceSeparator = $separator;
        return $this;
    }

    /**
     * Get namespace separator
     *
     * @return string
     */
    public function getNamespaceSeparator()
    {
        return $this->namespaceSeparator;
    }

    /**
     * Set pathname to database file
     *
     * @param string $pathname
     * @return DbaOptions
     */
    public function setPathname($pathname)
    {
        $this->pathname = (string) $pathname;
        $this->triggerOptionEvent('pathname', $pathname);
        return $this;
    }

    /**
     * Get pathname to database file
     *
     * @return string
     */
    public function getPathname()
    {
        return $this->pathname;
    }

    /**
     *
     *
     * @param unknown_type $mode
     * @return \Zend\Cache\Storage\Adapter\DbaOptions
     */
    public function setMode($mode)
    {
        $this->mode = (string) $mode;
        $this->triggerOptionEvent('mode', $mode);
        return $this;
    }

    public function getMode()
    {
        return $this->mode;
    }

    public function setHandler($handler)
    {
        $handler = (string) $handler;

        if (!function_exists('dba_handlers') || !in_array($handler, dba_handlers())) {
            throw new Exception\ExtensionNotLoadedException("DBA-Handler '{$handler}' not supported");
        }

        $this->triggerOptionEvent('handler', $handler);
        $this->handler = $handler;
        return $this;
    }

    public function getHandler()
    {
        return $this->handler;
    }
}
