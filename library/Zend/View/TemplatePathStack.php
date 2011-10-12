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
 * @package    Zend_View
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\View;

use SplFileInfo,
    Zend\Stdlib\SplStack;

/**
 * Resolves view scripts based on a stack of paths
 *
 * @category   Zend
 * @package    Zend_View
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class TemplatePathStack implements TemplateResolver
{
    /**
     * @var SplStack
     */
    protected $paths;

    /**
     * Flag indicating whether or not LFI protection for rendering view scripts is enabled
     * @var bool
     */
    protected $lfiProtectionOn = true;

    /**@+
     * Flags used to determine if a stream wrapper should be used for enabling short tags
     * @var bool
     */
    protected $useViewStream    = false;
    protected $useStreamWrapper = false;
    /**@-*/

    /**
     * Constructor
     *
     * @param  null|array|Traversable $options
     * @return void
     */
    public function __construct($options = null)
    {
        $this->useViewStream = (bool) ini_get('short_open_tag');
        if ($this->useViewStream) {
            if (!in_array('zend.view', stream_get_wrappers())) {
                stream_wrapper_register('zend.view', 'Zend\View\Stream');
            }
        }

        $this->paths = new SplStack;
        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * Configure object
     *
     * @param  array|Traversable $options
     * @return void
     */
    public function setOptions($options)
    {
        if (!is_array($options) && !$options instanceof \Traversable) {
            throw new Exception(sprintf(
                'Expected array or Traversable object; received "%s"',
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }

        foreach ($options as $key => $value) {
            switch (strtolower($key)) {
                case 'lfi_protection':
                    $this->setLfiProtection($value);
                    break;
                case 'script_paths':
                    $this->addPaths($value);
                    break;
                case 'use_stream_wrapper':
                    $this->setUseStreamWrapper($value);
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Add many paths to the stack at once
     *
     * @param  array $paths
     * @return TemplatePathStack
     */
    public function addPaths(array $paths)
    {
        foreach ($paths as $path) {
            $this->addPath($path);
        }
        return $this;
    }

    /**
     * Rest the path stack to the paths provided
     *
     * @param  SplStack|array $paths
     * @return TemplatePathStack
     */
    public function setPaths($paths)
    {
        if ($paths instanceof SplStack) {
            $this->paths = $paths;
        } elseif (is_array($paths)) {
            $this->clearPaths();
            $this->addPaths($paths);
        } else {
            throw new InvalidArgumentException(
                "Invalid argument provided for \$paths, expecting either an array or SplStack object"
            );
        }

        return $this;
    }

    /**
     * Normalize a path for insertion in the stack
     *
     * @param  string $path
     * @return string
     */
    public static function normalizePath($path)
    {
        $path = rtrim($path, '/');
        $path = rtrim($path, '\\');
        $path .= DIRECTORY_SEPARATOR;
        return $path;
    }

    /**
     * Add a single path to the stack
     *
     * @param  string $path
     * @return TemplatePathStack
     */
    public function addPath($path)
    {
        if (!is_string($path)) {
            throw new Exception(sprintf(
                'Invalid path provided; must be a string, received %s',
                gettype($path)
            ));
        }
        $this->paths[] = static::normalizePath($path);
        return $this;
    }

    /**
     * Clear all paths
     *
     * @return void
     */
    public function clearPaths()
    {
        $this->paths = new SplStack;
    }

    /**
     * Returns stack of paths
     *
     * @return SplStack
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * Set LFI protection flag
     *
     * @param  bool $flag
     * @return \Zend\View\TemplatePathStack
     */
    public function setLfiProtection($flag)
    {
        $this->lfiProtectionOn = (bool) $flag;
        return $this;
    }

    /**
     * Return status of LFI protection flag
     *
     * @return bool
     */
    public function isLfiProtectionOn()
    {
        return $this->lfiProtectionOn;
    }

    /**
     * Set flag indicating if stream wrapper should be used if short_open_tag is off
     *
     * @param  bool $flag
     * @return \Zend\View\View
     */
    public function setUseStreamWrapper($flag)
    {
        $this->useStreamWrapper = (bool) $flag;
        return $this;
    }

    /**
     * Should the stream wrapper be used if short_open_tag is off?
     *
     * Returns true if the use_stream_wrapper flag is set, and if short_open_tag
     * is disabled.
     *
     * @return bool
     */
    public function useStreamWrapper()
    {
        return ($this->useViewStream && $this->useStreamWrapper);
    }

    /**
     * Retrieve the filesystem path to a view script
     *
     * @param  string $name
     * @return string
     */
    public function getScriptPath($name)
    {
        if ($this->isLfiProtectionOn() && preg_match('#\.\.[\\\/]#', $name)) {
            $e = new Exception('Requested scripts may not include parent directory traversal ("../", "..\\" notation)');
            throw $e;
        }

        if (!count($this->paths)) {
            $e = new Exception('No view script directory set; unable to determine location for view script');
            throw $e;
        }

        $paths   = PATH_SEPARATOR;
        foreach ($this->paths as $path) {
            $file = new SplFileInfo($path . $name);
            if ($file->isReadable()) {
                // Found! Return it.
                if (($filePath = $file->getRealPath()) === false && substr($path, 0, 7) === 'phar://') {
                    // Do not try to expand phar paths (realpath + phars == fail)
                    $filePath = $path . $name;
                    if (!file_exists($filePath)) {
                        break;
                    }
                } 
                if ($this->useStreamWrapper()) {
                    // If using a stream wrapper, prepend the spec to the path
                    $filePath = 'zend.view://' . $filePath;
                }
                return $filePath;
            }
            $paths .= $path . PATH_SEPARATOR;
        }

        $e = new Exception(sprintf(
            'Script "%s" not found in path (%s)',
            $name, trim($paths, PATH_SEPARATOR)
        ));
        throw $e;
    }
}
