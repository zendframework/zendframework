<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace Zend\Amf\Value\Messaging;

/**
 * Creates the error message to report to flex the issue with the call
 *
 * Corresponds to flex.messaging.messages.ErrorMessage
 *
 * @package    Zend_Amf
 * @subpackage Value
 */
class ErrorMessage extends AcknowledgeMessage
{
    /**
     * Additional data with error
     * @var object
     */
    public $extendedData = null;

    /**
     * Error code number
     * @var string
     */
    public $faultCode;

    /**
     * Description as to the cause of the error
     * @var string
     */
    public $faultDetail;

    /**
     * Short description of error
     * @var string
     */
    public $faultString = '';

    /**
     * root cause of error
     * @var object
     */
    public $rootCause = null;
}
