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
 * @subpackage Twitter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\Twitter;

use Zend\Feed;
use Zend\Http;
use Zend\Json;
use Zend\Rest\Client;

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Twitter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Search extends Client\RestClient
{
    /**
     * Return Type
     *
     * @var string
     */
    protected $responseType = 'json';

    /**
     * Response Format Types
     *
     * @var array
     */
    protected $responseTypes = array(
        'atom',
        'json',
    );

    /**
     * Uri Component
     *
     * @var \Zend\Uri\Http
     */
    protected $uri;

    /**
     * Twitter api search options
     *
     * @var SearchOptions
     */
    protected $options;

    /**
     * Constructor
     *
     * @param string                           $responseType Return type
     * @param array|\Traversable|SearchOptions $options
     */
    public function __construct($responseType = 'json', $options = null)
    {
        $this->setResponseType($responseType);
        $this->setUri('http://search.twitter.com');

        $this->setHeaders('Accept-Charset', 'ISO-8859-1,utf-8');

        if ($options) {
            $this->setOptions($options);
        }
    }

    /**
     * Set options.
     *
     * @param  array|\Traversable|SearchOptions $options
     * @return SearchOptions
     * @see    getOptions()
     */
    public function setOptions($options)
    {
        if (!$options instanceof SearchOptions) {
            $options = new SearchOptions($options);
        }
        $this->options = $options;
    }

    /**
     * Get options.
     *
     * @return SearchOptions
     * @see setOptions()
     */
    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions(new SearchOptions());
        }
        return clone $this->options;
    }

    /**
     * set responseType
     *
     * @param string $responseType
     * @throws Exception\UnexpectedValueException
     * @return Search
     */
    public function setResponseType($responseType = 'json')
    {
        if (!in_array($responseType, $this->responseTypes, true)) {
            throw new Exception\UnexpectedValueException('Invalid Response Type');
        }
        $this->responseType = $responseType;
        return $this;
    }

    /**
     * Retrieve responseType
     *
     * @return string
     */
    public function getResponseType()
    {
        return $this->responseType;
    }

    /**
     * Performs a Twitter search query.
     *
     * @param  string                           $query   (optional)
     * @param  array|\Traversable|SearchOptions $options (optional)
     * @throws Exception\InvalidArgumentException If query is not defined neither $query nor $options or
     * If query is not a string.
     * @throws Http\Client\Exception\ExceptionInterface
     * @return mixed
     */
    public function execute($query = null, $options = null)
    {
        if (!$options) {
            $options = $this->getOptions();
        } else if (!$options instanceof SearchOptions) {
            $options = new SearchOptions($options);
        }

        if (is_string($query)) {
            $options->setQuery($query);
        } else {
            if ($query) {
                throw new Exception\InvalidArgumentException('query must be a string');
            }

            if (!$options->getQuery()) {
                throw new Exception\InvalidArgumentException('No query defined');
            }
        }

        $response = $this->restGet('/search.' . $this->responseType, $options->toArray());
        switch ($this->responseType) {
            case 'json':
                return Json\Json::decode($response->getBody(), Json\Json::TYPE_ARRAY);
                break;
            case 'atom':
                return Feed\Reader\Reader::importString($response->getBody());
                break;
        }

        return null;
    }
}
