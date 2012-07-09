<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @package   Zend_Config
 */

namespace Zend\Config\Reader;

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage Reader
 */
interface ReaderInterface
{
    /**
     * Read from a file and create an array
     *
     * @param  string $filename
     * @return array
     */
    public function fromFile($filename);

    /**
     * Read from a string and create an array
     *
     * @param  string $string
     * @return array|boolean
     */
    public function fromString($string);
}
