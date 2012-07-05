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
 * @package    Zend_Service
 * @subpackage StrikeIron
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\StrikeIron;

/**
 * This class allows StrikeIron authentication credentials to be specified
 * in one place and provides a factory for returning instances of different
 * StrikeIron service classes.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage StrikeIron
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class StrikeIron
{
    /**
     * Options to pass to Zend_Service_StrikeIron_Base constructor
     * @param array
     */
    protected $options;

    /**
     * Class constructor
     *
     * @param array  $options  Options to pass to StrikeIron\Base constructor
     */
    public function __construct($options = array())
    {
        $this->options = $options;
    }

    /**
     * Factory method to return a preconfigured Zend_Service_StrikeIron_*
     * instance.
     *
     * @param  null|string  $options  Service options
     * @return object       Zend\Service\StrikeIron\* instance
     * @throws Exception\RuntimeException if service class not found
     */
    public function getService($options = array())
    {
        $class = isset($options['class']) ? $options['class'] : 'Base';
        unset($options['class']);

        if (strpos($class, '\\') === false) {
            $class = "Zend\\Service\\StrikeIron\\{$class}";
        }

        if (!class_exists($class)) {
            throw new Exception\RuntimeException('Class file not found');
        }

        // instantiate and return the service
        $service = new $class(array_merge($this->options, $options));
        return $service;
    }

}
