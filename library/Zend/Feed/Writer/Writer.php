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
 * @package    Zend_Feed_Writer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Feed\Writer;

use Zend\Loader\ShortNameLocator,
    Zend\Loader\PrefixPathLoader,
    Zend\Loader\PrefixPathMapper,
    Zend\Loader\Exception\PluginLoaderException;

/**
* @category Zend
* @package Zend_Feed_Writer
* @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
* @license http://framework.zend.com/license/new-bsd New BSD License
*/
class Writer
{
	/**
	 * Namespace constants
	 */
	const NAMESPACE_ATOM_03  = 'http://purl.org/atom/ns#';
    const NAMESPACE_ATOM_10  = 'http://www.w3.org/2005/Atom';
    const NAMESPACE_RDF      = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';
    const NAMESPACE_RSS_090  = 'http://my.netscape.com/rdf/simple/0.9/';
    const NAMESPACE_RSS_10   = 'http://purl.org/rss/1.0/';

    /**
	 * Feed type constants
	 */
	const TYPE_ANY              = 'any';
	const TYPE_ATOM_03          = 'atom-03';
    const TYPE_ATOM_10          = 'atom-10';
    const TYPE_ATOM_ANY         = 'atom';
    const TYPE_RSS_090          = 'rss-090';
    const TYPE_RSS_091          = 'rss-091';
    const TYPE_RSS_091_NETSCAPE = 'rss-091n';
    const TYPE_RSS_091_USERLAND = 'rss-091u';
    const TYPE_RSS_092          = 'rss-092';
    const TYPE_RSS_093          = 'rss-093';
    const TYPE_RSS_094          = 'rss-094';
    const TYPE_RSS_10           = 'rss-10';
    const TYPE_RSS_20           = 'rss-20';
    const TYPE_RSS_ANY          = 'rss';
    
    /**
     * PluginLoader instance used by component
     *
     * @var \Zend\Loader\ShortNameLocator
     */
    protected static $_pluginLoader = null;

    /**
     * Path on which to search for Extension classes
     *
     * @var array
     */
    protected static $_prefixPaths = array();

    /**
     * Array of registered extensions by class postfix (after the base class
     * name) across four categories - data containers and renderers for entry
     * and feed levels.
     *
     * @var array
     */
    protected static $_extensions = array(
        'entry'         => array(),
        'feed'          => array(),
        'entryRenderer' => array(),
        'feedRenderer'  => array(),
    );
    
    /**
     * Set plugin loader for use with Extensions
     *
     * @param  \Zend\Loader\ShortNameLocator
     */
    public static function setPluginLoader(ShortNameLocator $loader)
    {
        self::$_pluginLoader = $loader;
    }

    /**
     * Get plugin loader for use with Extensions
     *
     * @return  \Zend\Loader\ShortNameLocator
     */
    public static function getPluginLoader()
    {
        if (!isset(self::$_pluginLoader)) {
            self::$_pluginLoader = new PrefixPathLoader(array(
                'Zend\\Feed\\Writer\\Extension\\' => 'Zend/Feed/Writer/Extension/',
            ));
        }
        return self::$_pluginLoader;
    }

    /**
     * Add prefix path for loading Extensions
     *
     * @param  string $prefix
     * @param  string $path
     * @return void
     */
    public static function addPrefixPath($prefix, $path)
    {
        $pluginLoader = self::getPluginLoader();
        if (!$pluginLoader instanceof PrefixPathMapper)  {
            return;
        }
        $prefix = rtrim($prefix, '\\');
        $path   = rtrim($path, DIRECTORY_SEPARATOR);
        $pluginLoader->addPrefixPath($prefix, $path);
    }

    /**
     * Add multiple Extension prefix paths at once
     *
     * @param  array $spec
     * @return void
     */
    public static function addPrefixPaths(array $spec)
    {
        $pluginLoader = self::getPluginLoader();
        if (!$pluginLoader instanceof PrefixPathMapper)  {
            return;
        }
        if (isset($spec['prefix']) && isset($spec['path'])) {
            self::addPrefixPath($spec['prefix'], $spec['path']);
        }
        foreach ($spec as $prefixPath) {
            if (isset($prefixPath['prefix']) && isset($prefixPath['path'])) {
                self::addPrefixPath($prefixPath['prefix'], $prefixPath['path']);
            }
        }
    }

    /**
     * Register an Extension by name
     *
     * @param  string $name
     * @return void
     * @throws Exception\RuntimeException if unable to resolve Extension class
     */
    public static function registerExtension($name)
    {
        $feedName          = $name . '\Feed';
        $entryName         = $name . '\Entry';
        $feedRendererName  = $name . '\Renderer\Feed';
        $entryRendererName = $name . '\Renderer\Entry';
        $loader            = self::getPluginLoader();
        if (self::isRegistered($name)) {
            if ($loader->isLoaded($feedName)
                || $loader->isLoaded($entryName)
                || $loader->isLoaded($feedRendererName)
                || $loader->isLoaded($entryRendererName)
            ) {
                return;
            }
        }
        $loader->load($feedName);
        $loader->load($entryName);
        $loader->load($feedRendererName);
        $loader->load($entryRendererName);
        if (!$loader->isLoaded($feedName)
            && !$loader->isLoaded($entryName)
            && !$loader->isLoaded($feedRendererName)
            && !$loader->isLoaded($entryRendererName)
        ) {
            throw new Exception\RuntimeException('Could not load extension: ' . $name
                . 'using Plugin Loader. Check prefix paths are configured and extension exists.');
        }
        if ($loader->isLoaded($feedName)) {
            self::$_extensions['feed'][] = $feedName;
        }
        if ($loader->isLoaded($entryName)) {
            self::$_extensions['entry'][] = $entryName;
        }
        if ($loader->isLoaded($feedRendererName)) {
            self::$_extensions['feedRenderer'][] = $feedRendererName;
        }
        if ($loader->isLoaded($entryRendererName)) {
            self::$_extensions['entryRenderer'][] = $entryRendererName;
        }
    }

    /**
     * Is a given named Extension registered?
     *
     * @param  string $extensionName
     * @return boolean
     */
    public static function isRegistered($extensionName)
    {
        $feedName  = $extensionName . '\\Feed';
        $entryName = $extensionName . '\\Entry';
        $feedRendererName  = $extensionName . '\\Renderer\\Feed';
        $entryRendererName = $extensionName . '\\Renderer\\Entry';
        if (in_array($feedName, self::$_extensions['feed'])
            || in_array($entryName, self::$_extensions['entry'])
            || in_array($feedRendererName, self::$_extensions['feedRenderer'])
            || in_array($entryRendererName, self::$_extensions['entryRenderer'])
        ) {
            return true;
        }
        return false;
    }

    /**
     * Get a list of extensions
     *
     * @return array
     */
    public static function getExtensions()
    {
        return self::$_extensions;
    }

    /**
     * Reset class state to defaults
     *
     * @return void
     */
    public static function reset()
    {
        self::$_pluginLoader = null;
        self::$_prefixPaths  = array();
        self::$_extensions   = array(
            'entry'         => array(),
            'feed'          => array(),
            'entryRenderer' => array(),
            'feedRenderer'  => array(),
        );
    }

    /**
     * Register core (default) extensions
     *
     * @return void
     */
    public static function registerCoreExtensions()
    {
        self::registerExtension('DublinCore');
        self::registerExtension('Content');
        self::registerExtension('Atom');
        self::registerExtension('Slash');
        self::registerExtension('WellFormedWeb');
        self::registerExtension('Threading');
        self::registerExtension('ITunes');
    }
    
    public static function lcfirst($str)
    {
        $str[0] = strtolower($str[0]);
        return $str;
    }

}
