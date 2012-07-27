<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Filter
 */

namespace Zend\Filter\Word;

/**
 * @category   Zend
 * @package    Zend_Filter
 */
class SeparatorToCamelCase extends AbstractSeparator
{
    /**
     * Defined by Zend\Filter\Filter
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        // a unicode safe way of converting characters to \x00\x00 notation
        $pregQuotedSeparator = preg_quote($this->separator, '#');

        if (self::hasPcreUnicodeSupport()) {
            parent::setPattern(array('#('.$pregQuotedSeparator.')(\p{L}{1})#eu','#(^\p{Ll}{1})#eu'));
            if (!extension_loaded('mbstring')) {
                parent::setReplacement(array("strtoupper('\\2')","strtoupper('\\1')"));
            } else {
                parent::setReplacement(array("mb_strtoupper('\\2', 'UTF-8')","mb_strtoupper('\\1', 'UTF-8')"));
            }
        } else {
            parent::setPattern(array('#('.$pregQuotedSeparator.')([A-Za-z]{1})#e','#(^[A-Za-z]{1})#e'));
            parent::setReplacement(array("strtoupper('\\2')","strtoupper('\\1')"));
        }

        return parent::filter($value);
    }
}
