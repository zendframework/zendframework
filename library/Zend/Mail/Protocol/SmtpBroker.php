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
 * @package    Zend_Mail
 * @subpackage Protocol
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mail\Protocol;

use Zend\Loader\PluginBroker;

/**
 * Plugin Broker for SMTP protocol authentication extensions.
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Protocol
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SmtpBroker extends PluginBroker
{
    /**
     * @var string Default plugin loading strategy
     */
    protected $defaultClassLoader = 'Zend\Mail\Protocol\SmtpLoader';

    /**
     * Determine if we have a valid extension
     * 
     * @param  mixed $plugin 
     * @return true
     * @throws Exception\InvalidHelperException
     */
    protected function validatePlugin($plugin)
    {
        if (!$plugin instanceof Smtp) {
            throw new Exception\InvalidHelperException(
                'SMTP authentication plugins must extend the base SMTP protocol class'
            );
        }
        return true;
    }
}
