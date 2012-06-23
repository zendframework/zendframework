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

use Zend\Loader\PluginBroker,
    Zend\Tag\Exception\InvalidArgumentException,
    Zend\Tag\Cloud\Decorator\DecoratorInterface as Decorator;

/**
 * Broker for decorator instances
 *
 * @category   Zend
 * @package    Zend_Tag
 * @subpackage Cloud
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DecoratorBroker extends PluginBroker
{
    /**
     * @var string Default plugin loading strategy
     */
    protected $defaultClassLoader = 'Zend\Tag\Cloud\DecoratorLoader';

    /**
     * Determine if we have a valid decorator
     * 
     * @param  mixed $plugin 
     * @return true
     * @throws InvalidArgumentException
     */
    protected function validatePlugin($plugin)
    {
        if (!$plugin instanceof Decorator) {
            throw new InvalidArgumentException('Tag cloud decorators must implement Zend\Tag\Cloud\Decorator\DecoratorInterface');
        }
        return true;
    }
}
