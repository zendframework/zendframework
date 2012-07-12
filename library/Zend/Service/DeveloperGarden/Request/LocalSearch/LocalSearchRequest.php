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
class Zend_Service_DeveloperGarden_Request_LocalSearch_LocalSearchRequest
    extends Zend_Service_DeveloperGarden_Request_AbstractRequest
{
    /**
     * array of search parameters
     *
     * @var array
     */
    public $searchParameters = null;

    /**
     * original object
     *
     * @var Zend_Service_DeveloperGarden_LocalSearch_SearchParameters
     */
    private $_searchParameters = null;

    /**
     * account id
     *
     * @var integer
     */
    public $account = null;

    /**
     * constructor give them the environment and the sessionId
     *
     * @param integer $environment
     * @param Zend_Service_DeveloperGarden_LocalSearch_SearchParameters $searchParameters
     * @param integer $account
     * @return Zend_Service_DeveloperGarden_Request_AbstractRequest
     */
    public function __construct($environment,
        Zend_Service_DeveloperGarden_LocalSearch_SearchParameters $searchParameters,
        $account = null
    ) {
        parent::__construct($environment);
        $this->setSearchParameters($searchParameters)
             ->setAccount($account);
    }

    /**
     * @param integer $account
     */
    public function setAccount($account = null)
    {
        $this->account = $account;
        return $this;
    }

    /**
     * @return integer
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param Zend_Service_DeveloperGarden_LocalSearch_SearchParameters $searchParameters
     */
    public function setSearchParameters(
        Zend_Service_DeveloperGarden_LocalSearch_SearchParameters $searchParameters
    ) {
        $this->searchParameters  = $searchParameters->getSearchParameters();
        $this->_searchParameters = $searchParameters;
        return $this;
    }

    /**
     * @return Zend_Service_DeveloperGarden_LocalSearch_SearchParameters
     */
    public function getSearchParameters()
    {
        return $this->_searchParameters;
    }

}
