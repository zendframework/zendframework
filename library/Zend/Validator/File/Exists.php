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
 * @package   Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Validator\File;
use Zend\Validator,
    Zend\Validator\Exception;

/**
 * Validator which checks if the file already exists in the directory
 *
 * @category  Zend
 * @package   Zend_Validate
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Exists extends Validator\AbstractValidator
{
    /**
     * @const string Error constants
     */
    const DOES_NOT_EXIST = 'fileExistsDoesNotExist';

    /**
     * @var array Error message templates
     */
    protected $_messageTemplates = array(
        self::DOES_NOT_EXIST => "File '%value%' does not exist",
    );

    /**
     * Options for this validator
     *
     * @var array
     */
    protected $options = array(
        'directory' => null,  // internal list of directories
    );

    /**
     * @var array Error message template variables
     */
    protected $_messageVariables = array(
        'directory' => array('options' => 'directory'),
    );

    /**
     * Sets validator options
     *
     * @param  string|array|\Traversable $options
     * @return void
     */
    public function __construct($options = null)
    {
        if (is_string($options)) {
            $options = explode(',', $options);
        }

        if (is_array($options) && !array_key_exists('directory', $options)) {
            $options = array('directory' => $options);
        }

        parent::__construct($options);
    }

    /**
     * Returns the set file directories which are checked
     *
     * @param  boolean $asArray Returns the values as array, when false an concated string is returned
     * @return string
     */
    public function getDirectory($asArray = false)
    {
        $asArray   = (bool) $asArray;
        $directory = (string) $this->options['directory'];
        if ($asArray) {
            $directory = explode(',', $directory);
        }

        return $directory;
    }

    /**
     * Sets the file directory which will be checked
     *
     * @param  string|array $directory The directories to validate
     * @return \Zend\Validator\File\Extension Provides a fluent interface
     */
    public function setDirectory($directory)
    {
        $this->options['directory'] = null;
        $this->addDirectory($directory);
        return $this;
    }

    /**
     * Adds the file directory which will be checked
     *
     * @param  string|array $directory The directory to add for validation
     * @return \Zend\Validator\File\Extension Provides a fluent interface
     */
    public function addDirectory($directory)
    {
        $directories = $this->getDirectory(true);

        if (is_string($directory)) {
            $directory = explode(',', $directory);
        } else if (!is_array($directory)) {
            throw new Exception\InvalidArgumentException('Invalid options to validator provided');
        }

        foreach ($directory as $content) {
            if (empty($content) || !is_string($content)) {
                continue;
            }

            $directories[] = trim($content);
        }
        $directories = array_unique($directories);

        // Sanity check to ensure no empty values
        foreach ($directories as $key => $dir) {
            if (empty($dir)) {
                unset($directories[$key]);
            }
        }

        $this->options['directory'] = implode(',', $directories);

        return $this;
    }

    /**
     * Returns true if and only if the file already exists in the set directories
     *
     * @param  string  $value Real file to check for existance
     * @param  array   $file  File data from \Zend\File\Transfer\Transfer
     * @return boolean
     */
    public function isValid($value, $file = null)
    {
        $directories = $this->getDirectory(true);
        if (($file !== null) and (!empty($file['destination']))) {
            $directories[] = $file['destination'];
        } else if (!isset($file['name'])) {
            $file['name'] = $value;
        }

        $check = false;
        foreach ($directories as $directory) {
            if (empty($directory)) {
                continue;
            }

            $check = true;
            if (!file_exists($directory . DIRECTORY_SEPARATOR . $file['name'])) {
                return $this->_throw($file, self::DOES_NOT_EXIST);
            }
        }

        if (!$check) {
            return $this->_throw($file, self::DOES_NOT_EXIST);
        }

        return true;
    }

    /**
     * Throws an error of the given type
     *
     * @param  string $file
     * @param  string $errorType
     * @return false
     */
    protected function _throw($file, $errorType)
    {
        if ($file !== null) {
            if (is_array($file)) {
                if(array_key_exists('name', $file)) {
                    $this->value = basename($file['name']);
                }
            } else if (is_string($file)) {
                $this->value = basename($file);
            }
        }

        $this->error($errorType);
        return false;
    }
}
