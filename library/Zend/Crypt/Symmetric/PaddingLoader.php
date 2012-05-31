<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Crypt
 */

namespace Zend\Crypt\Symmetric;

use Zend\Loader\PluginClassLoader;

/**
 * Plugin Class Loader implementation for padding
 *
 * @category   Zend
 * @package    Zend_Crypt
 * @subpackage Symmetric
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PaddingLoader extends PluginClassLoader
{
    /**
     * @var array Pre-aliased adapters
     */
    protected $plugins = array(
        'pkcs7' => 'Zend\Crypt\Symmetric\Padding\Pkcs7'
    );
}
