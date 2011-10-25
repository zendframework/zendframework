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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Validator\File;

use Zend\Loader;

/**
 * Validator for the file extension of a file
 *
 * @uses      \Zend\Loader
 * @uses      \Zend\Validator\AbstractValidator
 * @category  Zend
 * @package   Zend_Validate
 * @copyright Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Extension extends \Zend\Validator\AbstractValidator
{
    /**
     * @const string Error constants
     */
    const FALSE_EXTENSION = 'fileExtensionFalse';
    const NOT_FOUND       = 'fileExtensionNotFound';

    /**
     * @var array Error message templates
     */
    protected $_messageTemplates = array(
        self::FALSE_EXTENSION => "File '%value%' has a false extension",
        self::NOT_FOUND       => "File '%value%' is not readable or does not exist",
    );

    /**
     * Options for this valdiator
     *
     * @var array
     */
    protected $options = array(
        'case' => false,   // Validate case sensitive
        'extension' => '', // List of extensions
    );

    /**
     * @var array Error message template variables
     */
    protected $_messageVariables = array(
        'extension' => array('options' => 'extension'),
    );

    /**
     * Sets validator options
     *
     * @param  string|array|\Zend\Config\Config $options
     * @return void
     */
    public function __construct($options = null)
    {
        if ($options instanceof \Zend\Config\Config) {
            $options = $options->toArray();
        }

        $case = null;
        if (1 < func_num_args()) {
            $case = func_get_arg(1);
        }

        if (is_array($options)) {
            if (isset($options['case'])) {
                $case = $options['case'];
                unset($options['case']);
            }

            if (!array_key_exists('extension', $options)) {
                $options = array('extension' => $options);
            }
        } else {
            $options = array('extension' => $options);
        }

        if ($case !== null) {
            $options['case'] = $case;
        }

        parent::__construct($options);
    }

    /**
     * Returns the case option
     *
     * @return boolean
     */
    public function getCase()
    {
        return $this->options['case'];
    }

    /**
     * Sets the case to use
     *
     * @param  boolean $case
     * @return \Zend\Validator\File\Extension Provides a fluent interface
     */
    public function setCase($case)
    {
        $this->options['case'] = (boolean) $case;
        return $this;
    }

    /**
     * Returns the set file extension
     *
     * @return array
     */
    public function getExtension()
    {
        $extension = explode(',', $this->options['extension']);

        return $extension;
    }

    /**
     * Sets the file extensions
     *
     * @param  string|array $extension The extensions to validate
     * @return \Zend\Validator\File\Extension Provides a fluent interface
     */
    public function setExtension($extension)
    {
        $this->options['extension'] = null;
        $this->addExtension($extension);
        return $this;
    }

    /**
     * Adds the file extensions
     *
     * @param  string|array $extension The extensions to add for validation
     * @return \Zend\Validator\File\Extension Provides a fluent interface
     */
    public function addExtension($extension)
    {
        $extensions = $this->getExtension();
        if (is_string($extension)) {
            $extension = explode(',', $extension);
        }

        foreach ($extension as $content) {
            if (empty($content) || !is_string($content)) {
                continue;
            }

            $extensions[] = trim($content);
        }

        $extensions = array_unique($extensions);

        // Sanity check to ensure no empty values
        foreach ($extensions as $key => $ext) {
            if (empty($ext)) {
                unset($extensions[$key]);
            }
        }

        $this->options['extension'] = implode(',', $extensions);
        return $this;
    }

    /**
     * Returns true if and only if the fileextension of $value is included in the
     * set extension list
     *
     * @param  string  $value Real file to check for extension
     * @param  array   $file  File data from \Zend\File\Transfer\Transfer
     * @return boolean
     */
    public function isValid($value, $file = null)
    {
        if ($file === null) {
            $file = array('name' => basename($value));
        }

        // Is file readable ?
        if (!Loader::isReadable($value)) {
            return $this->_throw($file, self::NOT_FOUND);
        }

        if ($file !== null) {
            $info['extension'] = substr($file['name'], strrpos($file['name'], '.') + 1);
        } else {
            $info = pathinfo($value);
        }

        $extensions = $this->getExtension();

        if ($this->getCase() && (in_array($info['extension'], $extensions))) {
            return true;
        } else if (!$this->getCase()) {
            foreach ($extensions as $extension) {
                if (strtolower($extension) == strtolower($info['extension'])) {
                    return true;
                }
            }
        }

        return $this->_throw($file, self::FALSE_EXTENSION);
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
                    $this->value = $file['name'];
                }
            } else if (is_string($file)) {
                $this->value = $file;
            }
        }

        $this->error($errorType);
        return false;
    }
}
