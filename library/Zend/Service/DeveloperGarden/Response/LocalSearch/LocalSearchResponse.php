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
class Zend_Service_DeveloperGarden_Response_LocalSearch_LocalSearchResponse
    extends Zend_Service_DeveloperGarden_Response_BaseType
{
    /**
     *
     * @var Zend_Service_DeveloperGarden_Response_LocalSearch_LocalSearchResponseType
     */
    public $searchResult = null;

    /**
     * constructor
     *
     * @param Zend_Service_DeveloperGarden_Response_LocalSearch_LocalSearchResponseType $response
     * @todo implement special result methods
     */
    public function __construct(
        Zend_Service_DeveloperGarden_Response_LocalSearch_LocalSearchResponseType $response
    ) {
        $this->errorCode     = $response->getErrorCode();
        $this->errorMessage  = $response->getErrorMessage();
        $this->statusCode    = $response->getStatusCode();
        $this->statusMessage = $response->getStatusMessage();
        $this->searchResult  = $response;
    }

    /**
     * returns the raw search result
     *
     * @return Zend_Service_DeveloperGarden_Response_LocalSearch_LocalSearchResponseType
     */
    public function getSearchResult()
    {
        return $this->searchResult;
    }

    /**
     * overwrite hasError to not handle 0103 error (empty result)
     *
     * @return boolean
     */
    public function hasError()
    {
        $result = parent::hasError();
        if (!$result && $this->statusCode == '0103') {
            $result = false;
        }
        return $result;
    }
}
