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

/**
 * @namespace
 */
namespace Zend\Markup;

use Zend\Loader\Broker;

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
     * The parser broker
     *
     * @var \Zend\Loader\Broker
     */
    protected static $parserBroker;

    /**
     * The renderer broker
     *
     * @var \Zend\Loader\Broker
     */
    protected static $rendererBroker;


    /**
     * Disable instantiation of \Zend\Markup\Markup
     */
    private function __construct() { }

    /**
     * Get the parser broker
     *
     * @return \Zend\Loader\Broker
     */
    public static function getParserBroker()
    {
        if (!self::$parserBroker instanceof Broker) {
            self::$parserBroker = new ParserBroker();
        }

        return self::$parserBroker;
    }

    /**
     * Get the renderer broker
     *
     * @return \Zend\Loader\Broker
     */
    public static function getRendererBroker()
    {
        if (!self::$rendererBroker instanceof Broker) {
            self::$rendererBroker = new RendererBroker();
        }

        return self::$rendererBroker;
    }

    /**
     * Factory pattern
     *
     * @param  string $parser
     * @param  string $renderer
     * @param  array $parserOptions
     * @param  array $rendererOptions
     * @return \Zend\Markup\Renderer\AbstractRenderer
     */
    public static function factory($parser, $renderer = 'Html', array $parserOptions = array(), array $rendererOptions = array())
    {
        $parser         = self::getParserBroker()->load($parser, $parserOptions);
        $rendererBroker = self::getRendererBroker();
        $rendererBroker->setParser($parser);
        $renderer       = $rendererBroker->load($renderer, $rendererOptions);

        return $renderer;
    }
}
