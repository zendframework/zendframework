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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Markup;

/**
 * @category   Zend
 * @package    Zend_Markup
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Markup
{
    const CALLBACK = 'callback';
    const REPLACE  = 'replace';


    /**
     * The parser plugin manager
     *
     * @var ParserPluginManager
     */
    protected static $parsers;

    /**
     * The renderer plugin manager
     *
     * @var RendererPluginManager
     */
    protected static $renderers;


    /**
     * Disable instantiation
     */
    private function __construct() { }

    /**
     * Get the parser plugin manager
     *
     * @return ParserPluginManager
     */
    public static function getParserPluginManager()
    {
        if (!self::$parsers instanceof ParserPluginManager) {
            self::$parsers = new ParserPluginManager();
        }

        return self::$parsers;
    }

    /**
     * Get the renderer plugin manager
     *
     * @return RendererPluginManager
     */
    public static function getRendererPluginManager()
    {
        if (!self::$renderers instanceof RendererPluginManager) {
            self::$renderers = new RendererPluginManager();
        }

        return self::$renderers;
    }

    /**
     * Factory pattern
     *
     * @param  string $parser
     * @param  string $renderer
     * @param  array $parserOptions
     * @param  array $rendererOptions
     * @return Renderer\AbstractRenderer
     */
    public static function factory($parser, $renderer = 'Html', array $parserOptions = array(), array $rendererOptions = array())
    {
        $parser    = self::getParserPluginManager()->get($parser, $parserOptions);
        $renderers = self::getRendererPluginManager();
        $renderers->setParser($parser);
        $renderer  = $renderers->get($renderer, $rendererOptions);

        return $renderer;
    }
}
