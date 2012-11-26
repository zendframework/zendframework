<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Filter
 */

namespace Zend\Filter;

/**
 * Decompresses a given string
 *
 * @category   Zend
 * @package    Zend_Filter
 */
class Decompress extends Compress
{
    /**
     * Defined by Zend_Filter_Filter
     *
     * Decompresses the content $value with the defined settings
     *
     * @param  string $value Content to decompress
     * @return string The decompressed content
     */
    public function __invoke($value)
    {
        return $this->getAdapter()->decompress($value);
    }
}
