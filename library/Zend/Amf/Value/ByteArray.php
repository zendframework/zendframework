<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace Zend\Amf\Value;

/**
 * Wrapper class to store an AMF3 flash.utils.ByteArray
 *
 * @package    Zend_Amf
 * @subpackage Value
 */
class ByteArray
{
    /**
     * @var string ByteString Data
     */
    protected $_data = '';

    /**
     * Create a ByteArray
     *
     * @param  string $data
     * @return void
     */
    public function __construct($data)
    {
        $this->_data = $data;
    }

    /**
     * Return the byte stream
     *
     * @return string
     */
    public function getData()
    {
        return $this->_data;
    }
}
