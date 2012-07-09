<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace Zend\Amf\Request;

/**
 * AMF Request object -- Request via HTTP
 *
 * Extends {@link Zend_Amf_Request} to accept a request via HTTP. Request is
 * built at construction time using a raw POST; if no data is available, the
 * request is declared a fault.
 *
 * @package    Zend_Amf
 * @subpackage Request
 */
class HttpRequest extends StreamRequest
{
    /**
     * Raw AMF request
     * @var string
     */
    protected $_rawRequest;

    /**
     * Constructor
     *
     * Attempts to read from php://input to get raw POST request; if an error
     * occurs in doing so, or if the AMF body is invalid, the request is declared a
     * fault.
     *
     */
    public function __construct()
    {
        // php://input allows you to read raw POST data. It is a less memory
        // intensive alternative to $HTTP_RAW_POST_DATA and does not need any
        // special php.ini directives
        $amfRequest = file_get_contents('php://input');

        // Check to make sure that we have data on the input stream.
        if ($amfRequest != '') {
            $this->_rawRequest = $amfRequest;
            $this->initialize($amfRequest);
        } else {
            echo '<p>Zend Amf Endpoint</p>' ;
        }
    }

    /**
     * Retrieve raw AMF Request
     *
     * @return string
     */
    public function getRawRequest()
    {
        return $this->_rawRequest;
    }
}
