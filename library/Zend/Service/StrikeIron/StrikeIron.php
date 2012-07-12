<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
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
