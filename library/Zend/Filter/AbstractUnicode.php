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

namespace Zend\Filter;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractUnicode extends AbstractFilter
{
    /**
     * Set the input encoding for the given string
     *
     * @param  string|null $encoding
     * @return StringToLower
     * @throws Exception\InvalidArgumentException
     * @throws Exception\ExtensionNotLoadedException
     */
    public function setEncoding($encoding = null)
    {
        if ($encoding !== null) {
            if (!function_exists('mb_strtolower')) {
                throw new Exception\ExtensionNotLoadedException(sprintf(
                    '%s requires mbstring extension to be loaded',
                    get_class($this)
                ));
            }

            $encoding    = strtolower($encoding);
            $mbEncodings = array_map('strtolower', mb_list_encodings());
            if (!in_array($encoding, $mbEncodings)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    "Encoding '%s' is not supported by mbstring extension",
                    $encoding
                ));
            }
        }

        $this->options['encoding'] = $encoding;
        return $this;
    }

    /**
     * Returns the set encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        if ($this->options['encoding'] === null && function_exists('mb_internal_encoding')) {
            $this->options['encoding'] = mb_internal_encoding();
        }

        return $this->options['encoding'];
    }
}
