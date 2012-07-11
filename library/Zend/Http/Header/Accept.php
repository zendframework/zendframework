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
 * Accept Header
 *
 * @category   Zend
 * @package    Zend\Http\Header
 * @see        http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1
 */
class Accept extends AbstractAccept
{
    protected $regexAddType = '#^([a-zA-Z+-]+|\*)/(\*|[a-zA-Z0-9+-]+)$#';
        
    /**
     * Get field name
     * 
     * @return string
     */
    public function getFieldName()
    {
        return 'Accept';
    }

    /**
     * Cast to string
     * 
     * @return string
     */
    public function toString()
    {
        return 'Accept: ' . $this->getFieldValue();
    }

    /**
     * Add a media type, with the given priority
     * 
     * @param  string $type 
     * @param  int|float $priority 
     * @param  int $level 
     * @return Accept
     */
    public function addMediaType($type, $priority = 1, $level = null)
    {
        return $this->addType($type, $priority, $level);
    }

    /**
     * Does the header have the requested media type?
     * 
     * @param  string $type 
     * @return bool
     */
    public function hasMediaType($type)
    {
        return $this->hasType($type);
    }
}
