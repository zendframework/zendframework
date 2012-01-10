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
 * @subpackage Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Log\Filter;

use Zend\Log\Factory,
    Zend\Log\Filter,
    Zend\Log\Exception,
    Zend\Config\Config;

/**
 * @uses       \Zend\Log\Exception\InvalidArgumentException
 * @uses       \Zend\Log\Filter
 * @uses       \Zend\Log\Factory
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractFilter implements Filter, Factory
{
    /**
     * Validate and optionally convert the config to array
     *
     * @param  array|Config $config Config or Array
     * @return array
     * @throws Exception\InvalidArgumentException
     */
    static protected function _parseConfig($config)
    {
        if ($config instanceof Config) {
            $config = $config->toArray();
        }

        if (!is_array($config)) {
            throw new Exception\InvalidArgumentException('Configuration must be an array or instance of Zend\Config\Config');
        }

        return $config;
    }
}
