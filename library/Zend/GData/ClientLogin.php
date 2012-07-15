<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData;

/**
 * Class to facilitate Google's "Account Authentication
 * for Installed Applications" also known as "ClientLogin".
 * @see http://code.google.com/apis/accounts/AuthForInstalledApps.html
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Gdata
 */
class ClientLogin
{

    /**
     * The Google client login URI
     *
     */
    const CLIENTLOGIN_URI = 'https://www.google.com/accounts/ClientLogin';

    /**
     * The default 'source' parameter to send to Google
     *
     */
    const DEFAULT_SOURCE = 'Zend-ZendFramework';

    /**
     * Set Google authentication credentials.
     * Must be done before trying to do any Google Data operations that
     * require authentication.
     * For example, viewing private data, or posting or deleting entries.
     *
     * @param string $email
     * @param string $password
     * @param string $service
     * @param \Zend\GData\HttpClient $client
     * @param string $source
     * @param string $loginToken The token identifier as provided by the server.
     * @param string $loginCaptcha The user's response to the CAPTCHA challenge.
     * @param string $accountType An optional string to identify whether the
     * account to be authenticated is a google or a hosted account. Defaults to
     * 'HOSTED_OR_GOOGLE'. See: http://code.google.com/apis/accounts/docs/AuthForInstalledApps.html#Request
     * @throws \Zend\GData\App\AuthException
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\App\CaptchaRequiredException
     * @return \Zend\GData\HttpClient
     */
    public static function getHttpClient($email, $password, $service = 'xapi',
        HttpClient $client = null,
        $source = self::DEFAULT_SOURCE,
        $loginToken = null,
        $loginCaptcha = null,
        $loginUri = self::CLIENTLOGIN_URI,
        $accountType = 'HOSTED_OR_GOOGLE')
    {
        if (! ($email && $password)) {
            throw new App\AuthException(
                   'Please set your Google credentials before trying to ' .
                   'authenticate');
        }

        if ($client == null) {
            $client = new HttpClient();
        }

        // Build the HTTP client for authentication
        $client->setUri($loginUri);
        $client->setMethod('POST');
        $useragent = $source . ' Zend_Framework_Gdata/' . \Zend\Version::VERSION;
        $client->setOptions(array(
                'maxredirects'    => 0,
                'strictredirects' => true,
                'useragent' => $useragent
            )
        );

        $client->setEncType('multipart/form-data');

        $postParams = array('accountType' => $accountType,
                            'Email'       => (string)$email,
                            'Passwd'      => (string) $password,
                            'service'     => (string) $service,
                            'source'      => (string) $source);

        if ($loginToken || $loginCaptcha) {
            if($loginToken && $loginCaptcha) {
                $postParams += array('logintoken' => (string)$loginToken,
                                     'logincaptcha' => (string)$loginCaptcha);
            } else {
                throw new App\AuthException(
                    'Please provide both a token ID and a user\'s response ' .
                    'to the CAPTCHA challenge.');
            }
        }

        $client->setParameterPost($postParams);

        // Send the authentication request
        // For some reason Google's server causes an SSL error. We use the
        // output buffer to supress an error from being shown. Ugly - but works!
        ob_start();
        try {
            $response = $client->send();
        } catch (\Zend\Http\Client\Exception\ExceptionInterface $e) {
            throw new App\HttpException($e->getMessage(), $e);
        }
        ob_end_clean();

        // Parse Google's response
        $goog_resp = array();
        foreach (explode("\n", $response->getBody()) as $l) {
            $l = rtrim($l);
            if ($l) {
                list($key, $val) = explode('=', rtrim($l), 2);
                $goog_resp[$key] = $val;
            }
        }

        if ($response->getStatusCode() == 200) {
            $client->setClientLoginToken($goog_resp['Auth']);
            $useragent = $source . ' Zend_Framework_Gdata/' . \Zend\Version::VERSION;
            $client->setOptions(array(
                    'strictredirects' => true,
                    'useragent' => $useragent
                )
            );
            return $client;

        } elseif ($response->getStatusCode() == 403) {
            // Check if the server asked for a CAPTCHA
            if (array_key_exists('Error', $goog_resp) &&
                $goog_resp['Error'] == 'CaptchaRequired') {
                throw new App\CaptchaRequiredException(
                    $goog_resp['CaptchaToken'], $goog_resp['CaptchaUrl']);
            } else {
                throw new App\AuthException('Authentication with Google failed. Reason: ' .
                    (isset($goog_resp['Error']) ? $goog_resp['Error'] : 'Unspecified.'));
            }
        }
    }

}

