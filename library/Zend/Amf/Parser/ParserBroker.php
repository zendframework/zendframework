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
 * @package    Zend_Amf
 * @subpackage Parser
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Amf\Parser;

use Zend\Loader\PluginBroker,
    Zend\Amf\Exception as AMFException;

/**
 * Broker for parser resources
 *
 * @category   Zend
 * @package    Zend_Amf
 * @subpackage Parser
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ParserBroker extends PluginBroker
{
    /**
     * @var string Default plugin loading strategy
     */
    protected $defaultClassLoader = 'Zend\Amf\Parser\ParserLoader';

    /**
     * Determine if we have a valid parser
     * 
     * @param  mixed $plugin 
     * @return true
     * @throws AMFException
     */
    protected function validatePlugin($plugin)
    {
        if (!method_exists($plugin, 'parse')) {
            throw new AMFException(sprintf(
                'Parsers must implement a parse() method; none defined in class "%s"',
                get_class($plugin)
            ));
        }
        return true;
    }
}
