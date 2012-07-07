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
 * @package    Zend_I18n
 * @subpackage Translator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\I18n\Translator;

use Zend\I18n\Exception;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Plugin manager implementation for translation loaders.
 *
 * Enforces that filters retrieved are either callbacks or instances of
 * Loader\LoaderInterface. Additionally, it registers a number of default 
 * loaders.
 *
 * @category   Zend
 * @package    Zend_I18n
 * @subpackage Translator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class LoaderPluginManager extends AbstractPluginManager
{
    /**
     * Default set of loaders
     * 
     * @var array
     */
    protected $invokableClasses = array(
        'phparray' => 'Zend\I18n\Translator\Loader\PhpArray',
        'gettext'  => 'Zend\I18n\Translator\Loader\Gettext',
    );

    /**
     * Validate the plugin
     *
     * Checks that the filter loaded is an instance of Loader\LoaderInterface.
     * 
     * @param  mixed $plugin 
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Loader\LoaderInterface) {
            // we're okay
            return;
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Loader\LoaderInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
