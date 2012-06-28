<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Config
 */

namespace Zend\Config;

use Zend\Stdlib\ArrayUtils;

/**
 * @category  Zend
 * @package   Zend_Config
 */
class Factory
{
    /**
     * Readers used for config files.
     * array key is extension, array value is reader instance or class name
     *
     * @var array
     */
    protected static $readers = array(
        'ini'  => 'Zend\Config\Reader\Ini',
        'json' => 'Zend\Config\Reader\Json',
        'xml'  => 'Zend\Config\Reader\Xml',
        'yaml' => 'Zend\Config\Reader\Yaml',
    );

    /**
     * The reader manager
     *
     * @var null|ReaderPluginManager
     */
    protected static $plugins = null;

    /**
     * Read a config from a file.
     *
     * @param  string  $filename
     * @param  boolean $returnConfigObject 
     * @return array|Config
     * @throws Exception\RuntimeException
     */
    public static function fromFile($filename, $returnConfigObject = false)
    {
        $pathinfo = pathinfo($filename);
        
        if (!isset($pathinfo['extension'])) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Filename "%s" is missing an extension and cannot be auto-detected',
                $filename
            ));
        }
        
        $extension = strtolower($pathinfo['extension']);
       
        if ($extension === 'php') {
            if (!is_file($filename) || !is_readable($filename)) {
                throw new Exception\RuntimeException(sprintf(
                    "File '%s' doesn't exist or not readable",
                    $filename
                ));
            }
            
            $config = include $filename;
        } elseif (isset(self::$readers[$extension])) {
            $reader = self::$readers[$extension];
            if (is_string($reader)) {
                if (!class_exists($reader)) {
                    throw new Exception\RuntimeException(sprintf(
                        'Unable to locate reader class "%s"; class does not exist',
                        $reader
                    ));
                }

                $reader = new $reader();
            }

            if (!$reader instanceof Reader\ReaderInterface) {
                throw new Exception\RuntimeException(sprintf(
                    'Reader should be an instance of %s\Reader\ReaderInterface; received "%s"',
                    __NAMESPACE__,
                    (is_object($reader) ? get_class($reader) : gettype($reader))
                ));
            }

            /** @var Reader\ReaderInterface $reader  */
            $config = $reader->fromFile($filename);
        } else {
            throw new Exception\RuntimeException(sprintf(
                'Unsupported config file extension: .%s',
                $pathinfo['extension']
            ));
        }

        return ($returnConfigObject) ? new Config($config) : $config;
    }

    /**
     * Read configuration from multiple files and merge them.
     *
     * @param  array   $files
     * @param  boolean $returnConfigObject 
     * @return array|Config
     */
    public static function fromFiles(array $files, $returnConfigObject = false)
    {
        $config = array();

        foreach ($files as $file) {
            $config = ArrayUtils::merge($config, self::fromFile($file));
        }

        return ($returnConfigObject) ? new Config($config) : $config;
    }

    /**
     * Set config reader for file extension
     *
     * @param string $extension
     * @param string|Reader\ReaderInterface $reader
     * @throws Exception\InvalidArgumentException
     */
    public static function registerReader($extension, $reader)
    {
        $extension = strtolower($extension);

        if (!is_string($reader) && !$reader instanceof Reader\ReaderInterface) {
            throw new Exception\InvalidArgumentException(
                'Reader should be class name or instance of Zend\Config\Reader\ReaderInterface'
            );
        }

        self::$readers[$extension] = $reader;
    }
}
