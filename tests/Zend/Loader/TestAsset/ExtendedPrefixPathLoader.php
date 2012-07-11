<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Loader
 */

namespace ZendTest\Loader\TestAsset;

use Zend\Loader\PrefixPathLoader;

/**
 * @category   Zend
 * @package    Loader
 * @subpackage UnitTests
 * @group      Loader
 */
class ExtendedPrefixPathLoader extends PrefixPathLoader
{
    protected $prefixPaths = array(
        array('prefix' => 'loader', 'path' => __DIR__),
    );

    protected static $staticPaths = array();
}
