<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Http
 */

namespace Zend\Http\Header\Accept\FieldValuePart;

/**
 * Field Value Part
 *
 *
 * @category   Zend
 * @package    Zend\Http\Header
 * @see        http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1
 */
abstract class AbstractFieldValuePart
{

    /**
     * Internal object used for value retrieval
     * @var object
     */
    private $internalValues;

    /**
     *
     * @param object $internalValues
     */
    public function __construct($internalValues)
    {
        $this->internalValues = $internalValues;
    }

    /**
     *
     * @return object
     */
    protected function getInternalValues()
    {
        return $this->internalValues;
    }

    /**
     * @return string $typeString
     */
    public function getTypeString()
    {
        return $this->getInternalValues()->typeString;
    }

    /**
     * @return float $priority
     */
    public function getPriority()
    {
        return (float) $this->getInternalValues()->priority;
    }

    /**
     * @return StdClass $params
     */
    public function getParams()
    {
        return (object) $this->getInternalValues()->params;
    }

    /**
     * @return raw $raw
     */
    public function getRaw()
    {
        return $this->getInternalValues()->raw;
    }

    /**
     *
     * @param mixed
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getInternalValues()->$key;
    }

}
