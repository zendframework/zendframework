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
class Zend_Service_DeveloperGarden_SendSms
    extends Zend_Service_DeveloperGarden_Client_AbstractClient
{
    /**
     * wsdl file
     *
     * @var string
     */
    protected $_wsdlFile = 'https://gateway.developer.telekom.com/p3gw-mod-odg-sms/services/SmsService?wsdl';

    /**
     * wsdl file local
     *
     * @var string
     */
    protected $_wsdlFileLocal = 'Wsdl/SmsService.wsdl';

    /**
     * Response, Request Classmapping
     *
     * @var array
     *
     */
    protected $_classMap = array(
        'sendSMSResponse'      => 'Zend_Service_DeveloperGarden_Response_SendSms_SendSMSResponse',
        'sendFlashSMSResponse' => 'Zend_Service_DeveloperGarden_Response_SendSms_SendFlashSMSResponse'
    );

    /**
     * this function creates the raw sms object that can be used to send an sms
     * or as flash sms
     *
     * @param string $number
     * @param string $message
     * @param string $originator
     * @param integer $account
     *
     * @return Zend_Service_DeveloperGarden_Request_SendSms_SendSMS
     */
    public function createSms($number = null, $message = null, $originator = null, $account = null)
    {
        $request = new Zend_Service_DeveloperGarden_Request_SendSms_SendSMS($this->getEnvironment());
        $request->setNumber($number)
                ->setMessage($message)
                ->setOriginator($originator)
                ->setAccount($account);
        return $request;
    }

    /**
     * this function creates the raw sms object that can be used to send an sms
     * or as flash sms
     *
     * @param string $number
     * @param string $message
     * @param string $originator
     * @param integer $account
     *
     * @return Zend_Service_DeveloperGarden_Request_SendSms_SendFlashSMS
     */
    public function createFlashSms($number = null, $message = null, $originator = null, $account = null)
    {
        $request = new Zend_Service_DeveloperGarden_Request_SendSms_SendFlashSMS($this->getEnvironment());
        $request->setNumber($number)
                ->setMessage($message)
                ->setOriginator($originator)
                ->setAccount($account);
        return $request;
    }

    /**
     * sends an sms with the given parameters
     *
     * @param Zend_Service_DeveloperGarden_Request_SendSms_AbstractSendSms $sms
     *
     * @return Zend_Service_DeveloperGarden_Response_SendSms_AbstractSendSms
     */
    public function send(Zend_Service_DeveloperGarden_Request_SendSms_AbstractSendSms $sms)
    {
        $client = $this->getSoapClient();
        $request = array(
            'request' => $sms
        );
        switch ($sms->getSmsType()) {
            // Sms
            case 1 :
                $response = $client->sendSms($request);
                break;
            // flashSms
            case 2 :
                $response = $client->sendFlashSms($request);
                break;
            default : {
                throw new Zend_Service_DeveloperGarden_Client_Exception('Unknown SMS Type');
            }
        }

        return $response->parse();
    }
}
