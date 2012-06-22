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
 * @package    Zend_Tag
 * @subpackage Cloud
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Tag\Cloud;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\Tag\Exception;

/**
 * Plugin manager implementation for decorators.
 *
 * Enforces that decorators retrieved are instances of
 * Decorator\DecoratorInterface. Additionally, it registers a number of default 
 * decorators available.
 *
 * @category   Zend
 * @package    Zend_Tag
 * @subpackage Cloud
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DecoratorPluginManager extends AbstractPluginManager
{
    /**
     * Default set of decorators
     * 
     * @var array
     */
    protected $invokableClasses = array(
        'htmlcloud' => 'Zend\Tag\Cloud\Decorator\HtmlCloud',
        'htmltag'   => 'Zend\Tag\Cloud\Decorator\HtmlTag',
        'tag'       => 'Zend\Tag\Cloud\Decorator\Tag',
   );

    /**
     * Validate the plugin
     *
     * Checks that the decorator loaded is an instance
     * of Decorator\DecoratorInterface.
     * 
     * @param  mixed $plugin 
     * @return void
     * @throws Exception\InvalidArgumentException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Decorator\DecoratorInterface) {
            // we're okay
            return;
        }

        throw new Exception\InvalidArgumentException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Decorator\DecoratorInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}

