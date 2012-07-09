<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Loader
 */

namespace Zend\Loader;

/**
 * Plugin class loader interface
 *
 * @category   Zend
 * @package    Zend_Loader
 */
interface PrefixPathMapper extends ShortNameLocator
{
    /**
     * Add prefixed paths to the registry of paths
     *
     * @param string $prefix
     * @param string $path
     * @return \Zend\Loader\PrefixPathMapper
     */
    public function addPrefixPath($prefix, $path);

    /**
     * Remove a prefix (or prefixed-path) from the registry
     *
     * @param string $prefix
     * @param string $path
     * @return \Zend\Loader\PrefixPathMapper
     */
    public function removePrefixPath($prefix, $path);
}
