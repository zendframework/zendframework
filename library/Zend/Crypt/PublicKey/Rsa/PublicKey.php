<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Crypt
 */
namespace Zend\Crypt\PublicKey\Rsa;

/**
 * @category   Zend
 * @package    Zend_Crypt
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PublicKey extends AbstractKey
{
    /**
     * @param string $string
     * @throws Exception\RuntimeException
     */
    public function __construct($string)
    {
        $result = openssl_pkey_get_public($string);
        if (!$result) {
            throw new Exception\RuntimeException(
                'Unable to load public key; openssl ' . openssl_error_string() . 'STR:' .$string
            );
        }

        if (strpos($string, '-----BEGIN CERTIFICATE-----') !== false) {
            $this->certificateString = $string;
        } else {
            $this->pemString = $string;
        }

        $this->opensslKeyResource = $result;
        $this->details            = openssl_pkey_get_details($this->opensslKeyResource);
    }

    /**
     * @return null
     */
    public function getCertificate()
    {
        return $this->certificateString;
    }

}
