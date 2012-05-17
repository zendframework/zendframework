<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Crypt
 */

namespace Zend\Crypt;

use Zend\Loader\PluginClassLoader;

/**
 * Plugin Class Loader implementation for symmetric cipher adapter
 *
 * @category   Zend
 * @package    Zend_Crypt
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SymmetricLoader extends PluginClassLoader
{
    /**
     * @var array Pre-aliased adapters
     */
    protected $plugins = array(
        'mcrypt' => 'Zend\Crypt\Symmetric\Mcrypt'
    );
}
