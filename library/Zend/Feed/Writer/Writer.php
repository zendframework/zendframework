<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Feed
 */

namespace Zend\Feed\Writer;

/**
* @category Zend
* @package Zend_Feed_Writer
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
     * @var ExtensionManager
     */
    protected static $extensionManager = null;

    /**
     * Array of registered extensions by class postfix (after the base class
     * name) across four categories - data containers and renderers for entry
     * and feed levels.
     *
     * @var array
     */
    protected static $extensions = array(
        'entry'         => array(),
        'feed'          => array(),
        'entryRenderer' => array(),
        'feedRenderer'  => array(),
    );

    /**
     * Set plugin loader for use with Extensions
     *
     * @param ExtensionManager
     */
    public static function setExtensionManager(ExtensionManager $extensionManager)
    {
        self::$extensionManager = $extensionManager;
    }

    /**
     * Get plugin manager for use with Extensions
     *
     * @return ExtensionManager
     */
    public static function getExtensionManager()
    {
        if (!isset(self::$extensionManager)) {
            self::setExtensionManager(new ExtensionManager());
        }
        return self::$extensionManager;
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
        $manager           = self::getExtensionManager();
        if (self::isRegistered($name)) {
            if ($manager->has($feedName)
                || $manager->has($entryName)
                || $manager->has($feedRendererName)
                || $manager->has($entryRendererName)
            ) {
                return;
            }
        }
        if (!$manager->has($feedName)
            && !$manager->has($entryName)
            && !$manager->has($feedRendererName)
            && !$manager->has($entryRendererName)
        ) {
            throw new Exception\RuntimeException('Could not load extension: ' . $name
                . 'using Plugin Loader. Check prefix paths are configured and extension exists.');
        }
        if ($manager->has($feedName)) {
            self::$extensions['feed'][] = $feedName;
        }
        if ($manager->has($entryName)) {
            self::$extensions['entry'][] = $entryName;
        }
        if ($manager->has($feedRendererName)) {
            self::$extensions['feedRenderer'][] = $feedRendererName;
        }
        if ($manager->has($entryRendererName)) {
            self::$extensions['entryRenderer'][] = $entryRendererName;
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
        $feedName          = $extensionName . '\Feed';
        $entryName         = $extensionName . '\Entry';
        $feedRendererName  = $extensionName . '\Renderer\Feed';
        $entryRendererName = $extensionName . '\Renderer\Entry';
        if (in_array($feedName, self::$extensions['feed'])
            || in_array($entryName, self::$extensions['entry'])
            || in_array($feedRendererName, self::$extensions['feedRenderer'])
            || in_array($entryRendererName, self::$extensions['entryRenderer'])
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
        return self::$extensions;
    }

    /**
     * Reset class state to defaults
     *
     * @return void
     */
    public static function reset()
    {
        self::$extensionManager = null;
        self::$extensions   = array(
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
