<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace Zend\Amf\Response;

/**
 * Creates the proper http headers and send the serialized AMF stream to standard out.
 *
 * @package    Zend_Amf
 * @subpackage Response
 */
class HttpResponse extends StreamResponse
{
    /**
     * Create the application response header for AMF and sends the serialized AMF string
     *
     * @return string
     */
    public function getResponse()
    {
        if (!headers_sent()) {
            header('Cache-Control: no-cache, must-revalidate');
            if($this->isIeOverSsl()) {
                header('Cache-Control: cache, must-revalidate');
                header('Pragma: public');
            } else {
                header('Cache-Control: no-cache, must-revalidate');
                header('Pragma: no-cache');
            }
            header('Expires: Thu, 19 Nov 1981 08:52:00 GMT');
            header('Content-Type: application/x-amf');
        }
        return parent::getResponse();
    }

    protected function isIeOverSsl()
    {
        $ssl = isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : false;
        if (!$ssl || ($ssl == 'off')) {
            // IIS reports "off", whereas other browsers simply don't populate
            return false;
        }

        $ua  = $_SERVER['HTTP_USER_AGENT'];
        if (!preg_match('/; MSIE \d+\.\d+;/', $ua)) {
            // Not MicroSoft Internet Explorer
            return false;
        }

        return true;
    }
}
