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
 * @package    Zend_Crypt
 * @subpackage Symmetric
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Crypt\Symmetric;

use Zend\ServiceManager\AbstractPluginManager;

/**
 * Plugin manager implementation for the padding adapter instances.
 *
 * Enforces that padding adapters retrieved are instances of
 * Padding\PaddingInterface. Additionally, it registers a number of default 
 * padding adapters available.
 *
 * @category   Zend
 * @package    Zend_Crypt
 * @subpackage Symmetric
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PaddingPluginManager extends AbstractPluginManager
{
    /**
     * Default set of padding adapters
     * 
     * @var array
     */
    protected $invokableClasses = array(
        'pkcs7' => 'Zend\Crypt\Symmetric\Padding\Pkcs7'
    );

    /**
     * Do not share by default
     * 
     * @var bool
     */
    protected $shareByDefault = false;

    /**
     * Validate the plugin
     *
     * Checks that the padding adaper loaded is an instance of Padding\PaddingInterface.
     * 
     * @param  mixed $plugin 
     * @return void
     * @throws Exception\InvalidArgumentException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Padding\PaddingInterface) {
            // we're okay
            return;
        }

        throw new Exception\InvalidArgumentException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Padding\PaddingInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
