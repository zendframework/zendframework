<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\App;

/**
 * Gdata exceptions
 *
 * Class to represent an exception that occurs during the use of ClientLogin.
 * This particular exception happens when a CAPTCHA challenge is issued. This
 * challenge is a visual puzzle presented to the user to prove that they are
 * not an automated system.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage App
 */
class CaptchaRequiredException extends AuthException
{
    /**
     * The Google Accounts URL prefix.
     */
    const ACCOUNTS_URL = 'https://www.google.com/accounts/';

    /**
     * The token identifier from the server.
     *
     * @var string
     */
    private $captchaToken;

    /**
     * The URL of the CAPTCHA image.
     *
     * @var string
     */
    private $captchaUrl;

    /**
     * Constructs the exception to handle a CAPTCHA required response.
     *
     * @param string $captchaToken The CAPTCHA token ID provided by the server.
     * @param string $captchaUrl The URL to the CAPTCHA challenge image.
     */
    public function __construct($captchaToken, $captchaUrl)
    {
        $this->captchaToken = $captchaToken;
        $this->captchaUrl   = self::ACCOUNTS_URL . $captchaUrl;
        parent::__construct('CAPTCHA challenge issued by server');
    }

    /**
     * Retrieves the token identifier as provided by the server.
     *
     * @return string
     */
    public function getCaptchaToken()
    {
        return $this->captchaToken;
    }

    /**
     * Retrieves the URL CAPTCHA image as provided by the server.
     *
     * @return string
     */
    public function getCaptchaUrl()
    {
        return $this->captchaUrl;
    }

}
