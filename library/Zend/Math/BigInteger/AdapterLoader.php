<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Math
 */

namespace Zend\Math\BigInteger;

use Zend\Loader\PluginClassLoader;

/**
 * Plugin Class Loader implementation for BigInteger adapters.
 *
 * @category   Zend
 * @package    Zend_Math
 * @subpackage BigInteger
 */
class AdapterLoader extends PluginClassLoader
{
    /**
     * @var array Pre-aliased adapters 
     */
    protected $plugins = array(
        'bcmath'  => 'Zend\Math\BigInteger\Adapter\Bcmath',
        'bc_math' => 'Zend\Math\BigInteger\Adapter\Bcmath',
        'gmp'     => 'Zend\Math\BigInteger\Adapter\Gmp',
    );
}
