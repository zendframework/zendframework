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

/**
 * @namespace
 */
namespace Zend\Filter\File;
use Zend\Filter,
    Zend\Filter\Exception;

/**
 * @uses       \Zend\Filter\Exception
 * @uses       \Zend\Filter\StringToUpper
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class UpperCase extends Filter\StringToUpper
{
    /**
     * Adds options to the filter at initiation
     *
     * @param string $options
     */
    public function __construct($options = null)
    {
        if (!empty($options)) {
            $this->setEncoding($options);
        }
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Does a lowercase on the content of the given file
     *
     * @param  string $value Full path of file to change
     * @return string The given $value
     * @throws \Zend\Filter\Exception
     */
    public function __invoke($value)
    {
        if (!file_exists($value)) {
            throw new Exception\InvalidArgumentException("File '$value' not found");
        }

        if (!is_writable($value)) {
            throw new Exception\InvalidArgumentException("File '$value' is not writable");
        }

        $content = file_get_contents($value);
        if (!$content) {
            throw new Exception\RuntimeException("Problem while reading file '$value'");
        }

        $content = parent::__invoke($content);
        $result  = file_put_contents($value, $content);

        if (!$result) {
            throw new Exception\RuntimeException("Problem while writing file '$value'");
        }

        return $value;
    }
}
