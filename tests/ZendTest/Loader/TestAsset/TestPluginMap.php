<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Loader\TestAsset;

/**
 * @group      Loader
 */
class TestPluginMap implements \IteratorAggregate
{
    /**
     * Plugin map
     *
     * @var array
     */
    public $map = array(
        'map'    => __CLASS__,
        'test'   => 'ZendTest\Loader\PluginClassLoaderTest',
        'loader' => 'Zend\Loader\PluginClassLoader',
    );

    /**
     * Return iterator
     *
     * @return Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->map);
    }
}
