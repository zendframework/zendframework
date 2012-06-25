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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
