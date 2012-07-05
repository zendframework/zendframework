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
 * @package    Zend_Session
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @since      Preview Release 0.2
 */

namespace Zend\Session\Validator;

/**
 * @category   Zend
 * @package    Zend_Session
 * @subpackage Validator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class HttpUserAgent implements ValidatorInterface
{
    /**
     * Constructor - get the current user agent and store it in the session
     * as 'valid data'
     *
     * @return void
     */
    public function __construct($data = null)
    {
        if (empty($data)) {
            $data = isset($_SERVER['HTTP_USER_AGENT'])
                  ? $_SERVER['HTTP_USER_AGENT']
                  : null;
        }
        $this->_data = $data;
    }

    /**
     * isValid() - this method will determine if the current user agent matches the
     * user agent we stored when we initialized this variable.
     *
     * @return bool
     */
    public function isValid()
    {
        $userAgent = isset($_SERVER['HTTP_USER_AGENT'])
                   ? $_SERVER['HTTP_USER_AGENT'] 
                   : null;

        return $userAgent === $this->getData();
    }

    /**
     * Retrieve token for validating call
     * 
     * @return string
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Return validator name
     * 
     * @return string
     */
    public function getName()
    {
        return __CLASS__;
    }
}
