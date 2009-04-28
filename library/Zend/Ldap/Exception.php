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
 * @package    Zend_Ldap
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Exception
 */
require_once 'Zend/Exception.php';

class Zend_Ldap_Exception extends Zend_Exception
{
    const LDAP_SUCCESS = 0x00;
    const LDAP_OPERATIONS_ERROR = 0x01;
    const LDAP_PROTOCOL_ERROR = 0x02;
    const LDAP_TIMELIMIT_EXCEEDED = 0x03;
    const LDAP_SIZELIMIT_EXCEEDED = 0x04;
    const LDAP_COMPARE_FALSE = 0x05;
    const LDAP_COMPARE_TRUE = 0x06;
    const LDAP_AUTH_METHOD_NOT_SUPPORTED = 0x07;
    const LDAP_STRONG_AUTH_REQUIRED = 0x08;
    const LDAP_PARTIAL_RESULTS = 0x09;
    const LDAP_REFERRAL = 0x0a;
    const LDAP_ADMINLIMIT_EXCEEDED = 0x0b;
    const LDAP_UNAVAILABLE_CRITICAL_EXTENSION = 0x0c;
    const LDAP_CONFIDENTIALITY_REQUIRED = 0x0d;
    const LDAP_SASL_BIND_IN_PROGRESS = 0x0e;
    const LDAP_NO_SUCH_ATTRIBUTE = 0x10;
    const LDAP_UNDEFINED_TYPE = 0x11;
    const LDAP_INAPPROPRIATE_MATCHING = 0x12;
    const LDAP_CONSTRAINT_VIOLATION = 0x13;
    const LDAP_TYPE_OR_VALUE_EXISTS = 0x14;
    const LDAP_INVALID_SYNTAX = 0x15;
    const LDAP_NO_SUCH_OBJECT = 0x20;
    const LDAP_ALIAS_PROBLEM = 0x21;
    const LDAP_INVALID_DN_SYNTAX = 0x22;
    const LDAP_IS_LEAF = 0x23;
    const LDAP_ALIAS_DEREF_PROBLEM = 0x24;
    const LDAP_PROXY_AUTHZ_FAILURE = 0x2F;
    const LDAP_INAPPROPRIATE_AUTH = 0x30;
    const LDAP_INVALID_CREDENTIALS = 0x31;
    const LDAP_INSUFFICIENT_ACCESS = 0x32;
    const LDAP_BUSY = 0x33;
    const LDAP_UNAVAILABLE = 0x34;
    const LDAP_UNWILLING_TO_PERFORM = 0x35;
    const LDAP_LOOP_DETECT = 0x36;
    const LDAP_NAMING_VIOLATION = 0x40;
    const LDAP_OBJECT_CLASS_VIOLATION = 0x41;
    const LDAP_NOT_ALLOWED_ON_NONLEAF = 0x42;
    const LDAP_NOT_ALLOWED_ON_RDN = 0x43;
    const LDAP_ALREADY_EXISTS = 0x44;
    const LDAP_NO_OBJECT_CLASS_MODS = 0x45;
    const LDAP_RESULTS_TOO_LARGE = 0x46;
    const LDAP_AFFECTS_MULTIPLE_DSAS = 0x47;
    const LDAP_OTHER = 0x50;
    const LDAP_SERVER_DOWN = 0x51;
    const LDAP_LOCAL_ERROR = 0x52;
    const LDAP_ENCODING_ERROR = 0x53;
    const LDAP_DECODING_ERROR = 0x54;
    const LDAP_TIMEOUT = 0x55;
    const LDAP_AUTH_UNKNOWN = 0x56;
    const LDAP_FILTER_ERROR = 0x57;
    const LDAP_USER_CANCELLED = 0x58;
    const LDAP_PARAM_ERROR = 0x59;
    const LDAP_NO_MEMORY = 0x5a;
    const LDAP_CONNECT_ERROR = 0x5b;
    const LDAP_NOT_SUPPORTED = 0x5c;
    const LDAP_CONTROL_NOT_FOUND = 0x5d;
    const LDAP_NO_RESULTS_RETURNED = 0x5e;
    const LDAP_MORE_RESULTS_TO_RETURN = 0x5f;
    const LDAP_CLIENT_LOOP = 0x60;
    const LDAP_REFERRAL_LIMIT_EXCEEDED = 0x61;
    const LDAP_CUP_RESOURCES_EXHAUSTED = 0x71;
    const LDAP_CUP_SECURITY_VIOLATION = 0x72;
    const LDAP_CUP_INVALID_DATA = 0x73;
    const LDAP_CUP_UNSUPPORTED_SCHEME = 0x74;
    const LDAP_CUP_RELOAD_REQUIRED = 0x75;
    const LDAP_CANCELLED = 0x76;
    const LDAP_NO_SUCH_OPERATION = 0x77;
    const LDAP_TOO_LATE = 0x78;
    const LDAP_CANNOT_CANCEL = 0x79;
    const LDAP_ASSERTION_FAILED = 0x7A;
    const LDAP_SYNC_REFRESH_REQUIRED = 0x1000;
    const LDAP_X_SYNC_REFRESH_REQUIRED = 0x4100;
    const LDAP_X_NO_OPERATION = 0x410e;
    const LDAP_X_ASSERTION_FAILED = 0x410f;
    const LDAP_X_NO_REFERRALS_FOUND = 0x4110;
    const LDAP_X_CANNOT_CHAIN = 0x4111;

    /* internal error code constants */

    const LDAP_X_DOMAIN_MISMATCH = 0x7001;

    /**
     * @param mixed $ldap A Zend_Ldap object or raw LDAP context resource
     * @param string $str An informtive exception message
     * @param int $code An LDAP error code
     */
    public function __construct($ldap = null, $str = null, $code = 0)
    {
        $resource = null;
        if (is_resource($ldap)) {
            $resource = $ldap;
        } else if (is_object($ldap)) {
            $resource = $ldap->getResource();
        }

        $message = '';
        if ($code === 0)
            $code = Zend_Ldap_Exception::getLdapCode($resource);
        if ($code)
            $message .= '0x' . dechex($code);
        if (is_resource($resource)) {

            /* The various error retrieval functions can return
             * different things so we just try to collect what we
             * can and eliminate dupes.
             */

            $estr1 = @ldap_error($resource);
            if ($code !== 0 && $estr1 === 'Success')
                $estr1 = @ldap_err2str($code);
            if ($estr1 !== $str)
                $this->_append($message, $estr1);

            @ldap_get_option($resource, LDAP_OPT_ERROR_STRING, $estr2);
            if ($estr2 !== $str && $estr2 !== $estr1)
                $this->_append($message, $estr2);
        }

        $this->_append($message, $str);

        parent::__construct($message, $code);
    }

    private function _append(&$message, $estr)
    {
        if ($estr) {
            if ($message)
                $message .= ': ';
            $message .= $estr;
        }
    }

    /**
     * @param mixed $ldap A Zend_Ldap object or raw LDAP context resource
     * @return int The current error code for the resource
     */
    public static function getLdapCode($ldap)
    {
        $resource = null;
        if (is_resource($ldap)) {
            $resource = $ldap;
        } else if (is_object($ldap)) {
            $resource = $ldap->getResource();
        }
        if (is_resource($resource)) {
            $ret = @ldap_get_option($resource, LDAP_OPT_ERROR_NUMBER, $err);
            if ($ret === true) {
                if ($err <= -1 && $err >= -17) {
                    /* For some reason draft-ietf-ldapext-ldap-c-api-xx.txt error
                     * codes in OpenLDAP are negative values from -1 to -17. 
                     */
                    $err = Zend_Ldap_Exception::LDAP_SERVER_DOWN + (-$err - 1);
                }
                return $err;
            }
        }
        return 0;
    }

    /**
     * @param string $message An informtive exception message
     *
     */
    public function setMessage($message)
    {
        $this->_message = $message;
    }

    /**
     * @return int The current error code for this exception
     */
    public function getErrorCode()
    {
        return $this->getCode();
    }
}
