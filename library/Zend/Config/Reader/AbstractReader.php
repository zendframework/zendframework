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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Config\Reader;

use Zend\Config\Reader,
    Zend\Config\Exception;

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage Reader
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractReader implements Reader
{
    /**
     * Whether to process extends directives.
     *
     * @var boolean
     */
    protected $processExtends = true;

    /**
     * Extend directives to process.
     *
     * @var array
     */
    protected $extends;

    /**
     * Processed extends.
     *
     * @var array
     */
    protected $processedExtends;

    /**
     * fromFile(): defined by Reader interface.
     *
     * @see    Reader::fromFile()
     * @param  string $filename
     * @return array
     */
    public function fromFile($filename)
    {
        $this->extends = array();

        return $this->processAllExtends($this->processFile($filename));
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
        $this->extends = array();

        return $this->processAllExtends($this->processString($string));
    }

    /**
     * Process a file.
     *
     * @param  string $filename
     * @return array
     */
    abstract protected function processFile($filename);

    /**
     * Process a string.
     *
     * @param  string $string
     * @return array
     */
    abstract protected function processString($string);

    /**
     * Process all extends directives.
     *
     * @return array
     */
    protected function processAllExtends(array $data)
    {
        $this->processedExtends = array();

        if (!$this->shouldProcessExtends()) {
            return $data;
        }

        // Check for circular extends
        $extends = $this->extends;

        foreach ($extends as $extendingSection => $extendedSection) {
            if (!isset($data[$extendedSection])) {
                throw new Exception\RuntimeException(sprintf('Missing extended section "%s"', $extendedSection));
            } elseif (!isset($data[$extendingSection])) {
                throw new Exception\RuntimeException(sprintf('Missing extending section "%s"', $extendingSection));
            } elseif (!isset($this->extends[$extendingSection])) {
                continue;
            }

            $this->processExtend($data, $extendingSection, $extendedSection);
        }

        return $data;
    }

    /**
     * Process a single extend directive.
     *
     * The $data array is always passed by references, so modifications on it
     * won't require a copy of it (since it is always modified) on every call.
     *
     * @param  array  $data
     * @param  string $extendingSection
     * @param  string $extendedSection
     * @return void
     */
    protected function processExtend(array &$data, $extendingSection, $extendedSection)
    {
        if (isset($this->processedExtends[$extendingSection])) {
            throw new Exception\RuntimeException('Illegal circular inheritance detected');
        }

        $this->processedExtends[$extendingSection] = $extendedSection;

        if (isset($this->extends[$extendedSection])) {
            $this->processExtends($data, $extendedSection, $this->extends[$extendedSection]);
        }

        $data[$extendingSection] = array_replace_recursive(
            $data[$extendedSection],
            $data[$extendingSection]
        );

        unset($this->extends[$extendingSection]);
    }

    /**
     * Set whether to process extends.
     *
     * @param  boolean $flag
     * @return self
     */
    public function setProcessExtendsFlag($flag)
    {
        $this->processExtends = (boolean) $flag;
        return $this;
    }

    /**
     * Check whether to process extends.
     *
     * @return boolean
     */
    public function shouldProcessExtends()
    {
        return $this->processExtends;
    }
}
