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
 * @package    Zend_Gdata
 * @subpackage Gdata
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
        $client = null,
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
        
        if (!$client instanceof \Zend\Http\Client) {
            throw new App\HttpException(
                    'Client is not an instance of Zend\Http\Client.');
        }

        // Build the HTTP client for authentication
        $client->setUri($loginUri);
        $client->setMethod('POST');
        $useragent = $source . ' Zend_Framework_Gdata/' . \Zend\Version::VERSION;
        $client->setConfig(array(
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
        } catch (\Zend\Http\Client\Exception $e) {
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
            $client->setConfig(array(
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
            }
            else {
                throw new App\AuthException('Authentication with Google failed. Reason: ' .
                    (isset($goog_resp['Error']) ? $goog_resp['Error'] : 'Unspecified.'));
            }
        }
    }

}

