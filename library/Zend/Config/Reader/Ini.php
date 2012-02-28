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
 * @subpackage Reader
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Config\Reader;

use Zend\Config\Reader,
    Zend\Config\Exception;

/**
 * XML config reader.
 *
 * @category   Zend
 * @package    Zend_Config
 * @subpackage Reader
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Ini implements Reader
{
    /**
     * Separator for nesting levels of configuration data identifiers.
     *
     * @var string
     */
    protected $nestSeparator = '.';

    /**
     * Directory of the file to process.
     *
     * @var string
     */
    protected $directory;

    /**
     * Set nest separator.
     *
     * @param  stirng $separator
     * @return self
     */
    public function setNestSeparator($separator)
    {
        $this->nestSeparator = $separator;
        return $this;
    }

    /**
     * Get nest separator.
     *
     * @return string
     */
    public function getNestSeparator()
    {
        return $this->nestSeparator;
    }

    /**
     * fromFile(): defined by Reader interface.
     *
     * @see    Reader::fromFile()
     * @param  string $filename
     * @return array
     */
    public function fromFile($filename)
    {
        if (!file_exists($filename)) {
            throw new Exception\RuntimeException("The file $filename doesn't exists.");
        }
        $this->directory = dirname($filename);

        set_error_handler(
            function($error, $message = '', $file = '', $line = 0) use ($filename) {
                throw new Exception\RuntimeException(sprintf(
                    'Error reading INI file "%s": %s',
                    $filename, $message
                ), $error);
            }, E_WARNING
        );
        $ini = parse_ini_file($filename, true);
        restore_error_handler();
        
        return $this->process($ini);
    }

    /**
     * fromString(): defined by Reader interface.
     *
     * @see    Reader::fromString()
     * @param  string $string
     * @return array
     */
    public function fromString($string)
    {
        if (empty($string)) {
            return array();
        }
        $this->directory = null;

        set_error_handler(
            function($error, $message = '', $file = '', $line = 0) {
                throw new Exception\RuntimeException(sprintf(
                    'Error reading INI string: %s',
                    $message
                ), $error);
            }, E_WARNING
        );
        $ini = parse_ini_string($string, true);
        restore_error_handler();
        
        return $this->process($ini);
    }

    /**
     * Process data from the parsed ini file.
     *
     * @param  array $data
     * @return array
     */
    protected function process(array $data)
    {
        $config = array();

        foreach ($data as $section => $value) {
            if (is_array($value)) {
                $config[$section] = $this->processSection($value);
            } else {
                $config[$section] = $value;
            }
        }

        return $config;
    }

    /**
     * Process a section.
     *
     * @param  array $section
     * @return array
     */
    protected function processSection(array $section)
    {
        $config = array();

        foreach ($section as $key => $value) {
            $this->processKey($key, $value, $config);
        }

        return $config;
    }

    /**
     * Process a key.
     *
     * @param  string $key
     * @param  string $value
     * @param  array  $config
     * @return array
     */
    protected function processKey($key, $value, array &$config)
    {
        if (strpos($key, $this->nestSeparator) !== false) {
            $pieces = explode($this->nestSeparator, $key, 2);

            if (!strlen($pieces[0]) || !strlen($pieces[1])) {
                throw new Exception\RuntimeException(sprintf('Invalid key "%s"', $key));
            } elseif (!isset($config[$pieces[0]])) {
                if ($pieces[0] === '0' && !empty($config)) {
                    $config = array($pieces[0] => $config);
                } else {
                    $config[$pieces[0]] = array();
                }
            } elseif (!is_array($config[$pieces[0]])) {
                throw new Exception\RuntimeException(sprintf('Cannot create sub-key for "%s", as key already exists', $pieces[0]));
            }

            $this->processKey($pieces[1], $value, $config[$pieces[0]]);
        } else {
            if ($key === '@include') {
                if ($this->directory === null) {
                    throw new Exception\RuntimeException('Cannot process @include statement for a string config');
                }

                $reader  = clone $this;
                $include = $reader->fromFile($this->directory . '/' . $value);
                $config  = array_replace_recursive($config, $include);
            } else {
                $config[$key] = $value;
            }
        }
    }
}
