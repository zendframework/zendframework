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
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
