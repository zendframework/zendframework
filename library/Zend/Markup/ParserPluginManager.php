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

use Zend\ServiceManager\AbstractPluginManager;

/**
 * Plugin manager implementation for parser adapters
 *
 * Enforces that parsers retrieved are instances of
 * Parser\ParserInterface. Additionally, it registers a number of default
 * parsers available.
 *
 * @category   Zend
 * @package    Zend_Markup
 */
class ParserPluginManager extends AbstractPluginManager
{
    /**
     * Default set of parsers
     *
     * @var array
     */
    protected $invokableClasses = array(
        'bbcode'  => 'Zend\Markup\Parser\Bbcode',
        'textile' => 'Zend\Markup\Parser\Textile',
    );

    /**
     * Validate the plugin
     *
     * Checks that the parser loaded is an instance of Parser\ParserInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\InvalidArgumentException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Parser\ParserInterface) {
            // we're okay
            return;
        }

        throw new Exception\InvalidArgumentException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Parser\ParserInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
