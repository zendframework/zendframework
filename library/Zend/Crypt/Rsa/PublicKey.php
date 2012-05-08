<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Crypt
 */

namespace Zend\Crypt\Rsa;

use Zend\Crypt\Rsa\Exception\RuntimeException;

/**
 * @category   Zend
 * @package    Zend_Crypt
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PublicKey extends Key
{

    protected $_certificateString = null;

    public function __construct($string)
    {
        $this->_parse($string);
    }

    /**
     * @param string $string
     * @throws RuntimeException
     */
    protected function _parse($string)
    {
        if (preg_match("/^-----BEGIN CERTIFICATE-----/", $string)) {
            $this->_certificateString = $string;
        } else {
            $this->_pemString = $string;
        }

        $result = openssl_get_publickey($string);
        if (!$result) {
            throw new RuntimeException('Unable to load public key');
        }

        $this->_opensslKeyResource = $result;
        $this->_details = openssl_pkey_get_details($this->_opensslKeyResource);
    }

    public function getCertificate()
    {
        return $this->_certificateString;
    }

}
