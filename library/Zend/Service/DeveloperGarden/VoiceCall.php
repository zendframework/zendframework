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
class Zend_Service_DeveloperGarden_VoiceCall
    extends Zend_Service_DeveloperGarden_Client_AbstractClient
{
    /**
     * wsdl file
     *
     * @var string
     */
    protected $_wsdlFile = 'https://gateway.developer.telekom.com/p3gw-mod-odg-voicebutler/services/VoiceButlerService?wsdl';

    /**
     * wsdl file local
     *
     * @var string
     */
    protected $_wsdlFileLocal = 'Wsdl/VoiceButlerService.wsdl';

    /**
     * Response, Request Classmapping
     *
     * @var array
     *
     */
    protected $_classMap = array(
        'newCallResponse'          => 'Zend_Service_DeveloperGarden_Response_VoiceButler_NewCallResponse',
        'newCallSequencedResponse' => 'Zend_Service_DeveloperGarden_Response_VoiceButler_NewCallSequencedResponse',
        'tearDownCallResponse'     => 'Zend_Service_DeveloperGarden_Response_VoiceButler_TearDownCallResponse',
        'callStatusResponse'       => 'Zend_Service_DeveloperGarden_Response_VoiceButler_CallStatusResponse',
        'callStatus2Response'      => 'Zend_Service_DeveloperGarden_Response_VoiceButler_CallStatus2Response'
    );

    /**
     * init a new call with the given params
     *
     * @param string $aNumber
     * @param string $bNumber
     * @param integer $expiration
     * @param integer $maxDuration
     * @param integer $account
     * @param boolean $privacyA
     * @param boolean $privacyB
     * @param string $greeter
     * @return Zend_Service_DeveloperGarden_Response_VoiceButler_NewCallResponse
     */
    public function newCall($aNumber, $bNumber, $expiration, $maxDuration,
        $account = null, $privacyA = null, $privacyB = null, $greeter = null
    ) {
        $request = new Zend_Service_DeveloperGarden_Request_VoiceButler_NewCall(
                $this->getEnvironment()
        );
        $request->setANumber($aNumber)
                ->setBNumber($bNumber)
                ->setPrivacyA($privacyA)
                ->setPrivacyB($privacyB)
                ->setExpiration($expiration)
                ->setMaxDuration($maxDuration)
                ->setGreeter($greeter)
                ->setAccount($account);
        $result = $this->getSoapClient()->newCall(array(
            'request' => $request
        ));

        return $result->parse();
    }


    /**
     * init a new call with the given params but specially here,
     * you can define a set of numbers to be called if the first number
     * isnt reachable (ie: bNumber = +4930-111111,+4930-222222,+4930-333333)
     *
     * @throws Zend_Service_DeveloperGarden_Client_Exception
     * @param string $aNumber
     * @param array $bNumber
     * @param integer $expiration
     * @param integer $maxDuration
     * @param integer $maxWait
     * @param integer $account
     * @param boolean $privacyA
     * @param boolean $privacyB
     * @param string $greeter
     * @return Zend_Service_DeveloperGarden_Response_VoiceButler_NewCallSequencedResponse
     */
    public function newCallSequenced($aNumber, $bNumber, $expiration, $maxDuration,
        $maxWait, $account = null, $privacyA = null, $privacyB = null, $greeter = null
    ) {
        $request = new Zend_Service_DeveloperGarden_Request_VoiceButler_NewCallSequenced(
                $this->getEnvironment()
        );
        $request->setANumber($aNumber)
                ->setBNumber($bNumber)
                ->setPrivacyA($privacyA)
                ->setPrivacyB($privacyB)
                ->setExpiration($expiration)
                ->setMaxDuration($maxDuration)
                ->setMaxWait($maxWait)
                ->setGreeter($greeter)
                ->setAccount($account);
        $result = $this->getSoapClient()->newCallSequenced(array(
            'request' => $request
        ));

        return $result->parse();
    }

    /**
     * This tear down the call with the given sessionId
     *
     * @param string $sessionId
     * @return Zend_Service_DeveloperGarden_Response_VoiceButler_TearDownCallResponse
     */
    public function tearDownCall($sessionId)
    {
        $request = new Zend_Service_DeveloperGarden_Request_VoiceButler_TearDownCall(
            $this->getEnvironment(),
            $sessionId
        );
        $result = $this->getSoapClient()->tearDownCall(array(
            'request' => $request
        ));

        return $result->parse();
    }

    /**
     * checks the callStatus and updates the keepAlive if provided
     *
     * @param string $sessionId
     * @param integer $keepAlive
     * @return Zend_Service_DeveloperGarden_Response_VoiceButler_CallStatusResponse
     */
    public function callStatus($sessionId, $keepAlive = null)
    {
        $request = new Zend_Service_DeveloperGarden_Request_VoiceButler_CallStatus(
            $this->getEnvironment(),
            $sessionId,
            $keepAlive
        );

        $result = $this->getSoapClient()->callStatus2(array(
            'request' => $request
        ));

        return $result->parse();
    }
}
