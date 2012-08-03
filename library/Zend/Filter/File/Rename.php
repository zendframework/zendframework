<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Filter
 */

namespace Zend\Filter\File;

use Traversable;
use Zend\Filter;
use Zend\Filter\Exception;
use Zend\Stdlib\ArrayUtils;

/**
 * @category   Zend
 * @package    Zend_Filter
 */
class Rename extends Filter\AbstractFilter
{
    /**
     * Internal array of array(source, target, overwrite)
     */
    protected $files = array();

    /**
     * Class constructor
     *
     * Options argument may be either a string, a Zend_Config object, or an array.
     * If an array or Zend_Config object, it accepts the following keys:
     * 'source'    => Source filename or directory which will be renamed
     * 'target'    => Target filename or directory, the new name of the source file
     * 'overwrite' => Shall existing files be overwritten ?
     *
     * @param  string|array|Traversable $options Target file or directory to be renamed
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($options)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (is_string($options)) {
            $options = array('target' => $options);
        } elseif (!is_array($options)) {
            throw new Exception\InvalidArgumentException('Invalid options argument provided to filter');
        }

        $this->setFile($options);
    }

    /**
     * Returns the files to rename and their new name and location
     *
     * @return array
     */
    public function getFile()
    {
        return $this->files;
    }

    /**
     * Sets a new file or directory as target, deleting existing ones
     *
     * Array accepts the following keys:
     * 'source'    => Source filename or directory which will be renamed
     * 'target'    => Target filename or directory, the new name of the sourcefile
     * 'overwrite' => Shall existing files be overwritten ?
     *
     * @param  string|array $options Old file or directory to be rewritten
     * @return \Zend\Filter\File\Rename
     */
    public function setFile($options)
    {
        $this->files = array();
        $this->addFile($options);

        return $this;
    }

    /**
     * Adds a new file or directory as target to the existing ones
     *
     * Array accepts the following keys:
     * 'source'    => Source filename or directory which will be renamed
     * 'target'    => Target filename or directory, the new name of the sourcefile
     * 'overwrite' => Shall existing files be overwritten ?
     *
     * @param  string|array $options Old file or directory to be rewritten
     * @return Rename
     * @throws Exception\InvalidArgumentException
     */
    public function addFile($options)
    {
        if (is_string($options)) {
            $options = array('target' => $options);
        } elseif (!is_array($options)) {
            throw new Exception\InvalidArgumentException('Invalid options to rename filter provided');
        }

        $this->_convertOptions($options);

        return $this;
    }

    /**
     * Returns only the new filename without moving it
     * But existing files will be erased when the overwrite option is true
     *
     * @param  string  $value  Full path of file to change
     * @param  boolean $source Return internal informations
     * @return string The new filename which has been set
     * @throws Exception\InvalidArgumentException If the target file already exists.
     */
    public function getNewName($value, $source = false)
    {
        $file = $this->_getFileName($value);
        if (!is_array($file)) {
            return $file;
        }

        if ($file['source'] == $file['target']) {
            return $value;
        }

        if (!file_exists($file['source'])) {
            return $value;
        }

        if (($file['overwrite'] == true) && (file_exists($file['target']))) {
            unlink($file['target']);
        }

        if (file_exists($file['target'])) {
            throw new Exception\InvalidArgumentException(
                sprintf("File '%s' could not be renamed. It already exists.", $value)
            );
        }

        if ($source) {
            return $file;
        }

        return $file['target'];
    }

    /**
     * Defined by Zend\Filter\Filter
     *
     * Renames the file $value to the new name set before
     * Returns the file $value, removing all but digit characters
     *
     * @param  string $value Full path of file to change
     * @throws Exception\RuntimeException
     * @return string The new filename which has been set, or false when there were errors
     */
    public function filter($value)
    {
        $file   = $this->getNewName($value, true);
        if (is_string($file)) {
            return $file;
        }

        $result = rename($file['source'], $file['target']);

        if ($result !== true) {
            throw new Exception\RuntimeException(sprintf("File '%s' could not be renamed. An error occurred while processing the file.", $value));
        }

        return $file['target'];
    }

    /**
     * Internal method for creating the file array
     * Supports single and nested arrays
     *
     * @param  array $options
     * @return array
     */
    protected function _convertOptions($options)
    {
        $files = array();
        foreach ($options as $key => $value) {
            if (is_array($value)) {
                $this->_convertOptions($value);
                continue;
            }

            switch ($key) {
                case "source":
                    $files['source'] = (string) $value;
                    break;

                case 'target' :
                    $files['target'] = (string) $value;
                    break;

                case 'overwrite' :
                    $files['overwrite'] = (boolean) $value;
                    break;

                default:
                    break;
            }
        }

        if (empty($files)) {
            return $this;
        }

        if (empty($files['source'])) {
            $files['source'] = '*';
        }

        if (empty($files['target'])) {
            $files['target'] = '*';
        }

        if (empty($files['overwrite'])) {
            $files['overwrite'] = false;
        }

        $found = false;
        foreach ($this->files as $key => $value) {
            if ($value['source'] == $files['source']) {
                $this->files[$key] = $files;
                $found              = true;
            }
        }

        if (!$found) {
            $count                = count($this->files);
            $this->files[$count] = $files;
        }

        return $this;
    }

    /**
     * Internal method to resolve the requested source
     * and return all other related parameters
     *
     * @param  string $file Filename to get the informations for
     * @return array|string
     */
    protected function _getFileName($file)
    {
        $rename = array();
        foreach ($this->files as $value) {
            if ($value['source'] == '*') {
                if (!isset($rename['source'])) {
                    $rename           = $value;
                    $rename['source'] = $file;
                }
            }

            if ($value['source'] == $file) {
                $rename = $value;
            }
        }

        if (!isset($rename['source'])) {
            return $file;
        }

        if (!isset($rename['target']) or ($rename['target'] == '*')) {
            $rename['target'] = $rename['source'];
        }

        if (is_dir($rename['target'])) {
            $name = basename($rename['source']);
            $last = $rename['target'][strlen($rename['target']) - 1];
            if (($last != '/') and ($last != '\\')) {
                $rename['target'] .= DIRECTORY_SEPARATOR;
            }

            $rename['target'] .= $name;
        }

        return $rename;
    }
}
