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
 * @category  Zend
 * @package   Zend_Config
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Config;

use Zend\Stdlib\ArrayUtils;

/**
 * Declared abstract to prevent instantiation
 * 
 * @category  Zend
 * @package   Zend_Config
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Factory
{
    /**
     * Readers used for config files.
     *
     * @var array
     */
    protected static $readers = array(
        'ini' => 'Ini',
        'xml' => 'Xml'
    );

    /**
     * Read a config from a file.
     *
     * @param  string  $filename
     * @param  boolean $returnConfigObject 
     * @return array|Config
     */
    public static function fromFile($filename, $returnConfigObject = false)
    {
        $pathinfo = pathinfo($filename);
        
        if (!isset($pathinfo['extension'])) {
            throw new Exception\RuntimeException(sprintf(
                'Filename "%s" is missing an extension and cannot be auto-detected',
                $filename
            ));
        }
        
        $extension = strtolower($pathinfo['extension']);
       
        if ($extension === 'php') {
            if (!is_file($filename) || !is_readable($filename)) {
                throw new Exception\RuntimeException(sprintf('Filename "%s" is either not a file or not readable', $filename));
            }
            
            $config = include $filename;
        } elseif (isset(self::$readers[$extension])) {
            if (is_string(self::$readers[$extension])) {
                $classname = __NAMESPACE__ . '\\Reader\\' . self::$readers[$extension];
                self::$readers[$extension] = new $classname();
            }
            
            $config = self::$readers[$extension]->fromFile($filename);
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
}
