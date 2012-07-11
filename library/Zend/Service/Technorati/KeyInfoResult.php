<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace Zend\Service\Technorati;

use DomDocument;
use DOMXPath;

/**
 * Represents a single Technorati KeyInfo query result object.
 * It provides information about your Technorati API Key daily usage.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 */
class KeyInfoResult
{
    /**
     * Technorati API key
     *
     * @var     string
     * @access  protected
     */
    protected $apiKey;

    /**
     * Number of queries used today
     *
     * @var     int
     * @access  protected
     */
    protected $apiQueries;

    /**
     * Total number of available queries per day
     *
     * @var     int
     * @access  protected
     */
    protected $maxQueries;


    /**
     * Constructs a new object from DOM Element.
     * Parses given Key element from $dom and sets API key string.
     *
     * @param   DomElement $dom the ReST fragment for this object
     * @param   string $apiKey  the API Key string
     */
    public function __construct(DomDocument $dom, $apiKey = null)
    {
        $xpath = new DOMXPath($dom);

        $this->apiQueries   = (int) $xpath->query('/tapi/document/result/apiqueries/text()')->item(0)->data;
        $this->maxQueries   = (int) $xpath->query('/tapi/document/result/maxqueries/text()')->item(0)->data;
        $this->setApiKey($apiKey);
    }


    /**
     * Returns API Key string.
     *
     * @return  string  API Key string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Returns the number of queries sent today.
     *
     * @return  int     number of queries sent today
     */
    public function getApiQueries()
    {
        return $this->apiQueries;
    }

    /**
     * Returns Key's daily query limit.
     *
     * @return  int     maximum number of available queries per day
     */
    public function getMaxQueries()
    {
        return $this->maxQueries;
    }


    /**
     * Sets API Key string.
     *
     * @param   string $apiKey  the API Key
     * @return  KeyInfoResult $this instance
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }
}
