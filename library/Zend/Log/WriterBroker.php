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
 * @package    Zend_Log
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Log;

use Zend\Loader\PluginBroker;

/**
 * @category   Zend
 * @package    Zend_Log
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class WriterBroker extends PluginBroker
{
    /**
     * @var string Default plugin loading strategy
     */
    protected $defaultClassLoader = 'Zend\Log\WriterLoader';

    /**
     * Determine whether we have a valid plugin
     *
     * @param mixed $plugin
     * @return boolean
     * @throws Exception\InvalidArgumentException
     */
    protected function validatePlugin($plugin)
    {
        if (!$plugin instanceof Writer\WriterInterface) {
            throw new Exception\InvalidArgumentException('Writer must implement Zend\Log\Writer');
        }

        return true;
    }
}
