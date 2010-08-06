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
 * @package    Zend_Markup
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Markup;
use Zend\Loader\PluginLoader,
    Zend\Loader\PrefixPathMapper;

/**
 * @category   Zend
 * @package    Zend_Markup
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Markup
{
    const CALLBACK = 'callback';
    const REPLACE  = 'replace';


    /**
     * The parser loader
     *
     * @var \Zend\Loader\PrefixPathMapper
     */
    protected static $_parserLoader;

    /**
     * The renderer loader
     *
     * @var \Zend\Loader\PrefixPathMapper
     */
    protected static $_rendererLoader;


    /**
     * Disable instantiation of \Zend\Markup\Markup
     */
    private function __construct() { }

    /**
     * Get the parser loader
     *
     * @return \Zend\Loader\PrefixPathMapper
     */
    public static function getParserLoader()
    {
        if (!(self::$_parserLoader instanceof PrefixPathMapper)) {
            self::$_parserLoader = new PluginLoader(array(
                'Zend\Markup\Parser' => 'Zend/Markup/Parser/',
            ));
        }

        return self::$_parserLoader;
    }

    /**
     * Get the renderer loader
     *
     * @return \Zend\Loader\PrefixPathMapper
     */
    public static function getRendererLoader()
    {
        if (!(self::$_rendererLoader instanceof PrefixPathMapper)) {
            self::$_rendererLoader = new PluginLoader(array(
                'Zend\Markup\Renderer' => 'Zend/Markup/Renderer/',
            ));
        }

        return self::$_rendererLoader;
    }

    /**
     * Add a parser path
     *
     * @param  string $prefix
     * @param  string $path
     * @return \Zend\Loader\PrefixPathMapper
     */
    public static function addParserPath($prefix, $path)
    {
        return self::getParserLoader()->addPrefixPath($prefix, $path);
    }

    /**
     * Add a renderer path
     *
     * @param  string $prefix
     * @param  string $path
     * @return \Zend\Loader\PrefixPathMapper
     */
    public static function addRendererPath($prefix, $path)
    {
        return self::getRendererLoader()->addPrefixPath($prefix, $path);
    }

    /**
     * Factory pattern
     *
     * @param  string $parser
     * @param  string $renderer
     * @param  array $options
     * @return \Zend\Markup\Renderer\AbstractRenderer
     */
    public static function factory($parser, $renderer = 'Html', array $options = array())
    {
        $parserClass   = self::getParserLoader()->load($parser);
        $rendererClass = self::getRendererLoader()->load($renderer);

        $parser            = new $parserClass();
        $options['parser'] = $parser;
        $renderer          = new $rendererClass($options);

        return $renderer;
    }
}
