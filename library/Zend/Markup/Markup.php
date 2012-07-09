<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Markup
 */

namespace Zend\Markup;

/**
 * @category   Zend
 * @package    Zend_Markup
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
