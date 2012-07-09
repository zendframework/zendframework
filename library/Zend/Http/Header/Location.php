<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @package   Zend_Http
 */

namespace Zend\Http\Header;

use Zend\Uri\Http as HttpUri;

/**
 * Location Header
 *
 * @category   Zend
 * @package    Zend_Http
 * @subpackage Headers
 * @link       http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.30
 */
class Location extends AbstractLocation
{
    /**
     * Return header name
     *
     * @return string
     */
    public function getFieldName()
    {
        return 'Location';
    }
}
