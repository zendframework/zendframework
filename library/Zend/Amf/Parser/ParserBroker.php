<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace Zend\Amf\Parser;

use Zend\Amf\Exception as AMFException;
use Zend\Loader\PluginBroker;

/**
 * Broker for parser resources
 *
 * @category   Zend
 * @package    Zend_Amf
 * @subpackage Parser
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
