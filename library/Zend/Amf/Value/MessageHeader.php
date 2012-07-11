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
 * Message Headers provide context for the processing of the
 * the AMF Packet and all subsequent Messages.
 *
 * Multiple Message Headers may be included within an AMF Packet.
 *
 * @package    Zend_Amf
 * @subpackage Value
 */
class MessageHeader
{
    /**
     * Name of the header
     *
     * @var string
     */
    public $name;

    /**
     * Flag if the data has to be parsed on return
     *
     * @var boolean
     */
    public $mustRead;

    /**
     * Length of the data field
     *
     * @var int
     */
    public $length;

    /**
     * Data sent with the header name
     *
     * @var mixed
     */
    public $data;

    /**
     * Used to create and store AMF Header data.
     *
     * @param String $name
     * @param Boolean $mustRead
     * @param misc $content
     * @param integer $length
     */
    public function __construct($name, $mustRead, $data, $length=null)
    {
        $this->name     = $name;
        $this->mustRead = (bool) $mustRead;
        $this->data     = $data;
        if (null !== $length) {
            $this->length = (int) $length;
        }
    }
}
