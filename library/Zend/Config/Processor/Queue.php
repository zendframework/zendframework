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
 * @package    Zend_Config
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Config\Processor;

use Zend\Config\Config,
    Zend\Config\Processor,
    Zend\Config\Exception\InvalidArgumentException,
    Zend\Stdlib\PriorityQueue;

/**
 * @category   Zend
 * @package    Zend_Config
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Queue extends PriorityQueue implements Processor
{
    /**
     * Process the whole config structure with each parser in the queue.
     *
     * @param \Zend\Config\Config $config
     * @throws \Zend\Config\Exception\InvalidArgumentException
     */
    public function process(Config $config)
    {
        if ($config->isReadOnly()) {
            throw new InvalidArgumentException('Cannot parse config because it is read-only');
        }

        foreach ($this as $parser) {
            /** @var $parser \Zend\Config\Processor */
            $parser->process($config);
        }
    }

    /**
     * Process a single value
     *
     * @param $value
     * @return mixed
     */
    public function processValue($value)
    {
        foreach ($this as $parser) {
            /** @var $parser \Zend\Config\Processor */
            $value = $parser->processValue($value);
        }

        return $value;
    }
}
