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
class Zend_Service_DeveloperGarden_LocalSearch
    extends Zend_Service_DeveloperGarden_Client_AbstractClient
{
    /**
     * wsdl file
     *
     * @var string
     */
    protected $_wsdlFile = 'https://gateway.developer.telekom.com/p3gw-mod-odg-localsearch/services/localsearch?wsdl';

    /**
     * wsdl file local
     *
     * @var string
     */
    protected $_wsdlFileLocal = 'Wsdl/localsearch.wsdl';

    /**
     * Response, Request Classmapping
     *
     * @var array
     *
     */
    protected $_classMap = array(
        'LocalSearchResponseType' => 'Zend_Service_DeveloperGarden_Response_LocalSearch_LocalSearchResponseType'
    );

    /**
     * localSearch with the given parameters
     *
     * @param Zend_Service_DeveloperGarden_LocalSearch_SearchParameters $searchParameters
     * @param integer $account
     * @return Zend_Service_DeveloperGarden_Response_LocalSearch_LocalSearchResponseType
     */
    public function localSearch(
        Zend_Service_DeveloperGarden_LocalSearch_SearchParameters $searchParameters,
        $account = null
    ) {
        $request = new Zend_Service_DeveloperGarden_Request_LocalSearch_LocalSearchRequest(
            $this->getEnvironment(),
            $searchParameters,
            $account
        );

        $result = $this->getSoapClient()->localSearch($request);

        $response = new Zend_Service_DeveloperGarden_Response_LocalSearch_LocalSearchResponse($result);
        return $response->parse();
    }
}
