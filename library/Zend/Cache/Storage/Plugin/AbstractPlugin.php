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
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache\Storage\Plugin;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractPlugin implements PluginInterface
{
    /**
     * @var PluginOptions
     */
    protected $options;

    /**
     * Set pattern options
     *
     * @param  PluginOptions $options
     * @return AbstractPlugin
     */
    public function setOptions(PluginOptions $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Get all pattern options
     *
     * @return PluginOptions
     */
    public function getOptions()
    {
        if (null === $this->options) {
            $this->setOptions(new PluginOptions());
        }
        return $this->options;
    }
}
