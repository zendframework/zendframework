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
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Filter;

use Traversable;
use Zend\Stdlib\ArrayUtils;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class RealPath extends AbstractFilter
{
    /**
     * @var boolean $_pathExists
     */
    protected $_exists = true;

    /**
     * Class constructor
     *
     * @param boolean|\Traversable $options Options to set
     */
    public function __construct($options = true)
    {
        $this->setExists($options);
    }

    /**
     * Returns true if the filtered path must exist
     *
     * @return boolean
     */
    public function getExists()
    {
        return $this->_exists;
    }

    /**
     * Sets if the path has to exist
     * TRUE when the path must exist
     * FALSE when not existing paths can be given
     *
     * @param boolean|array|Traversable $options Path must exist
     * @return RealPath
     */
    public function setExists($options)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (is_array($options)) {
            if (isset($options['exists'])) {
                $options = (boolean) $options['exists'];
            }
        }

        $this->_exists = (boolean) $options;
        return $this;
    }

    /**
     * Defined by Zend\Filter\FilterInterface
     *
     * Returns realpath($value)
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        $path = (string) $value;
        if ($this->_exists) {
            return realpath($path);
        }

        $realpath = @realpath($path);
        if ($realpath) {
            return $realpath;
        }

        $drive = '';
        if (substr(PHP_OS, 0, 3) == 'WIN') {
            $path = preg_replace('/[\\\\\/]/', DIRECTORY_SEPARATOR, $path);
            if (preg_match('/([a-zA-Z]\:)(.*)/', $path, $matches)) {
                list($fullMatch, $drive, $path) = $matches;
            } else {
                $cwd   = getcwd();
                $drive = substr($cwd, 0, 2);
                if (substr($path, 0, 1) != DIRECTORY_SEPARATOR) {
                    $path = substr($cwd, 3) . DIRECTORY_SEPARATOR . $path;
                }
            }
        } elseif (substr($path, 0, 1) != DIRECTORY_SEPARATOR) {
            $path = getcwd() . DIRECTORY_SEPARATOR . $path;
        }

        $stack = array();
        $parts = explode(DIRECTORY_SEPARATOR, $path);
        foreach ($parts as $dir) {
            if (strlen($dir) && $dir !== '.') {
                if ($dir == '..') {
                    array_pop($stack);
                } else {
                    array_push($stack, $dir);
                }
            }
        }

        return $drive . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $stack);
    }
}
