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

namespace Zend\Config\Writer;

use Zend\Config\Exception;

/**
 * @category   Zend
 * @package    Zend_Config
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Yaml extends AbstractWriter
{
    /**
     * Yaml encoder callback
     * 
     * @var callable
     */
    protected $yamlEncoder;
    /**
     * Constructor
     * 
     * @param callable $yamlDecoder 
     */
    public function __construct($yamlEncoder=null) {
        if (!empty($yamlEncoder)) {
            $this->setYamlEncoder($yamlEncoder);
        } else {
            if (function_exists('yaml_parse')) {
                $this->setYamlEncoder('yaml_parse');
            }
        }
    }
    /**
     * Get callback for decoding YAML
     *
     * @return callable
     */
    public function getYamlEncoder()
    {
        return $this->yamlEncoder;
    }
    /**
     * Set callback for decoding YAML
     *
     * @param  callable $yamlEncoder the decoder to set
     * @return Yaml
     */
    public function setYamlEncoder($yamlEncoder)
    {
        if (!is_callable($yamlEncoder)) {
            throw new Exception\InvalidArgumentException('Invalid parameter to setYamlEncoder() - must be callable');
        }
        $this->yamlEncoder = $yamlEncoder;
        return $this;
    }
    /**
     * processConfig(): defined by AbstractWriter.
     *
     * @param  array $config
     * @return string
     */
    public function processConfig(array $config)
    {
        if (null === $this->getYamlEncoder()) {
             throw new Exception\RuntimeException("You didn't specify a Yaml callback encoder");
        }
        
        $config = call_user_func($this->getYamlEncoder(), $config);
        if (null === $config) {
            throw new Exception\RuntimeException("Error generating YAML data");
        }
        
        return $config;
    }
}
