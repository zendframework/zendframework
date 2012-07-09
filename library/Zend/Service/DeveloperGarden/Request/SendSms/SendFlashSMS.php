<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage DeveloperGarden
 * @author     Marco Kaiser
 */
class Zend_Service_DeveloperGarden_Request_SendSms_SendFlashSMS
    extends Zend_Service_DeveloperGarden_Request_SendSms_AbstractSendSms
{
    /**
     * this is the sms type
     * 2 = FlashSMS
     *
     * @var integer
     */
    protected $_smsType = 2;
}
