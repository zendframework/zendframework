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

namespace Zend\Filter\File;
use Zend\Filter;
use Zend\Filter\Exception;

/**
 * Encrypts a given file and stores the encrypted file content
 *
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Encrypt extends Filter\Encrypt
{
    /**
     * New filename to set
     *
     * @var string
     */
    protected $_filename;

    /**
     * Returns the new filename where the content will be stored
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->_filename;
    }

    /**
     * Sets the new filename where the content will be stored
     *
     * @param  string $filename (Optional) New filename to set
     * @return Encrypt
     */
    public function setFilename($filename = null)
    {
        $this->_filename = $filename;
        return $this;
    }

    /**
     * Defined by Zend\Filter\Filter
     *
     * Encrypts the file $value with the defined settings
     *
     * @param  string $value Full path of file to change
     * @return string The filename which has been set, or false when there were errors
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public function filter($value)
    {
        if (!file_exists($value)) {
            throw new Exception\InvalidArgumentException("File '$value' not found");
        }

        if (!isset($this->_filename)) {
            $this->_filename = $value;
        }

        if (file_exists($this->_filename) and !is_writable($this->_filename)) {
            throw new Exception\RuntimeException("File '{$this->_filename}' is not writable");
        }

        $content = file_get_contents($value);
        if (!$content) {
            throw new Exception\RuntimeException("Problem while reading file '$value'");
        }

        $encrypted = parent::filter($content);
        $result    = file_put_contents($this->_filename, $encrypted);

        if (!$result) {
            throw new Exception\RuntimeException("Problem while writing file '{$this->_filename}'");
        }

        return $this->_filename;
    }
}
