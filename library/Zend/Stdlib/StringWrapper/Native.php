<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Stdlib
 */

namespace Zend\Stdlib\StringWrapper;

use Zend\Stdlib\StringUtils;

/**
 * @category   Zend
 * @package    Zend_Stdlib
 * @subpackage StringWrapper
 */
class Native extends AbstractStringWrapper
{
    public function __construct()
    {
        $this->charsets = StringUtils::getSingleByteCharsets();
    }

    public function strlen($str, $charset = 'UTF-8')
    {
        return strlen($str);
    }

    public function substr($str, $offset = 0, $length = null, $charset = 'UTF-8')
    {
        return substr($str, $offset, $length);
    }

    public function strpos($haystack, $needle, $offset = 0, $charset = 'UTF-8')
    {
        return strpos($haystack, $needle, $offset);
    }

    public function convert($str, $toCharset, $fromCharset = 'UTF-8')
    {
        return false;
    }
}
