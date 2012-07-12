<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Http
 */

namespace Zend\Http\Header;

/**
 * Accept Language Header
 *
 * @category   Zend
 * @package    Zend\Http\Header
 * @see        http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
 */
class AcceptLanguage extends AbstractAccept
{

    protected $regexAddType = '#^([a-zA-Z0-9+-]+|\*)$#';

    /**
     * Get field name
     *
     * @return string
     */
    public function getFieldName()
    {
        return 'Accept-Language';
    }

    /**
     * Cast to string
     *
     * @return string
     */
    public function toString()
    {
        return 'Accept-Language: ' . $this->getFieldValue();
    }

    /**
     * Add a language, with the given priority
     *
     * @param  string $type
     * @param  int|float $priority
     * @return Accept
     */
    public function addLanguage($type, $priority = 1)
    {
        return $this->addType($type, $priority);
    }

    /**
     * Does the header have the requested language?
     *
     * @param  string $type
     * @return bool
     */
    public function hasLanguage($type)
    {
        return $this->hasType($type);
    }

}
