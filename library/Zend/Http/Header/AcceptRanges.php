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
 * @package    Zend\Http\Header
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
namespace Zend\Http\Header;

/**
 * Accept Ranges Header
 *
 * @category   Zend
 * @package    Zend\Http\Header
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.5
 */
class AcceptRanges implements HeaderDescription
{

    protected $rangeUnit = null;

    public static function fromString($headerLine)
    {
        $header = new static();

        list($name, $value) = preg_split('#: #', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'accept-ranges') {
            throw new Exception\InvalidArgumentException('Invalid header line for Accept-Ranges string');
        }

        $header->rangeUnit = trim($value);

        return $header;
    }

    public function getFieldName()
    {
        return 'Accept-Ranges';
    }

    public function getFieldValue()
    {
        return $this->getRangeUnit();
    }

    public function setRangeUnit($rangeUnit)
    {
        $this->rangeUnit = $rangeUnit;
        return $this;
    }

    public function getRangeUnit()
    {
        return $this->rangeUnit;
    }

    public function toString()
    {
        return 'Accept-Ranges: ' . $this->getFieldValue();
    }
    
}
