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
 * @package    Zend_OpenId
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\OpenId\Extension;

/**
 * Abstract extension class for Zend\OpenId
 *
 * @category   Zend
 * @package    Zend_OpenId
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractExtension
{

    /**
     * Calls given function with given argument for all extensions
     *
     * @param mixed $extensions list of extensions or one extension
     * @param string $func function to be called
     * @param mixed &$params argument to pass to given funcion
     * @return bool
     */
    static public function forAll($extensions, $func, &$params)
    {
        if ($extensions !== null) {
            if (is_array($extensions)) {
                foreach ($extensions as $ext) {
                    if ($ext instanceof AbstractExtension) {
                        if (!$ext->$func($params)) {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }
            } else if (!is_object($extensions) ||
                       !($extensions instanceof AbstractExtension) ||
                       !$extensions->$func($params)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Method to add additional data to OpenID 'checkid_immediate' or
     * 'checkid_setup' request. This method addes nothing but inherited class
     * may add additional data into request.
     *
     * @param array &$params request's var/val pairs
     * @return bool
     */
    public function prepareRequest(&$params)
    {
        return true;
    }

    /**
     * Method to parse OpenID 'checkid_immediate' or 'checkid_setup' request
     * and initialize object with passed data. This method parses nothing but
     * inherited class may override this method to do somthing.
     *
     * @param array $params request's var/val pairs
     * @return bool
     */
    public function parseRequest($params)
    {
        return true;
    }

    /**
     * Method to add additional data to OpenID 'id_res' response. This method
     * addes nothing but inherited class may add additional data into response.
     *
     * @param array &$params response's var/val pairs
     * @return bool
     */
    public function prepareResponse(&$params)
    {
        return true;
    }

    /**
     * Method to parse OpenID 'id_res' response and initialize object with
     * passed data. This method parses nothing but inherited class may override
     * this method to do somthing.
     *
     * @param array $params response's var/val pairs
     * @return bool
     */
    public function parseResponse($params)
    {
        return true;
    }

    /**
     * Method to prepare data to store it in trusted servers database.
     *
     * @param array &$data data to be stored in tusted servers database
     * @return bool
     */
    public function getTrustData(&$data)
    {
        return true;
    }

    /**
     * Method to check if data from trusted servers database is enough to
     * sutisfy request.
     *
     * @param array $data data from tusted servers database
     * @return bool
     */
    public function checkTrustData($data)
    {
        return true;
    }
}
