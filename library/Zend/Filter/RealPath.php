<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Filter
 */

namespace Zend\Filter;

use Traversable;
use Zend\Stdlib\ErrorHandler;

/**
 * @category   Zend
 * @package    Zend_Filter
 */
class RealPath extends AbstractFilter
{
    /**
     * @var array $options
     */
    protected $options = array(
        'exists' => true
    );

    /**
     * Class constructor
     *
     * @param  bool|Traversable $existsOrOptions Options to set
     */
    public function __construct($existsOrOptions = true)
    {
        if ($existsOrOptions !== null) {
            if (!static::isOptions($existsOrOptions)) {
                $this->setExists($existsOrOptions);
            } else {
                $this->setOptions($existsOrOptions);
            }
        }
    }

    /**
     * Sets if the path has to exist
     * TRUE when the path must exist
     * FALSE when not existing paths can be given
     *
     * @param  bool $flag Path must exist
     * @return RealPath
     */
    public function setExists($flag = true)
    {
        $this->options['exists'] = (bool) $flag;
        return $this;
    }

    /**
     * Returns true if the filtered path must exist
     *
     * @return bool
     */
    public function getExists()
    {
        return $this->options['exists'];
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
        if ($this->options['exists']) {
            return realpath($path);
        }

        ErrorHandler::start();
        $realpath = realpath($path);
        ErrorHandler::stop();
        if ($realpath) {
            return $realpath;
        }

        $drive = '';
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $path = preg_replace('/[\\\\\/]/', DIRECTORY_SEPARATOR, $path);
            if (preg_match('/([a-zA-Z]\:)(.*)/', $path, $matches)) {
                list(, $drive, $path) = $matches;
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
