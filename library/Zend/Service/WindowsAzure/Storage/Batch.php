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
 * @package    Zend_Service_WindowsAzure
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\WindowsAzure\Storage;

use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Service\WindowsAzure\Exception\RuntimeException;

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Batch
{
    /**
     * Storage client the batch is defined on
     *
     * @var AbstractBatchStorage
     */
    protected $_storageClient = null;

    /**
     * For table storage?
     *
     * @var boolean
     */
    protected $_forTableStorage = false;

    /**
     * Base URL
     *
     * @var string
     */
    protected $_baseUrl;

    /**
     * Pending operations
     *
     * @var array
     */
    protected $_operations = array();

    /**
     * Does the batch contain a single select?
     *
     * @var boolean
     */
    protected $_isSingleSelect = false;

    /**
     * Creates a new Batch
     *
     * @param AbstractBatchStorage $storageClient Storage client the batch is defined on
     * @param string               $baseUrl
     */
    public function __construct(AbstractBatchStorage $storageClient = null, $baseUrl = '')
    {
        $this->_storageClient = $storageClient;
        $this->_baseUrl       = $baseUrl;
        $this->_beginBatch();
    }

    /**
     * Get base URL for creating requests
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }

    /**
     * Starts a new batch operation set
     *
     * @throws RuntimeException
     */
    protected function _beginBatch()
    {
        $this->_storageClient->setCurrentBatch($this);
    }

    /**
     * Cleanup current batch
     */
    protected function _clean()
    {
        unset($this->_operations);
        $this->_storageClient->setCurrentBatch(null);
        $this->_storageClient = null;
        unset($this);
    }

    /**
     * Enlist operation in current batch
     *
     * @param string  $path            Path
     * @param string  $queryString     Query string
     * @param string  $httpVerb        HTTP verb the request will use
     * @param array   $headers         x-ms headers to add
     * @param boolean $forTableStorage Is the request for table storage?
     * @param mixed   $rawData         Optional RAW HTTP data to be sent over the wire
     * @throws RuntimeException
     */
    public function enlistOperation($path = '/', $queryString = '', $httpVerb = Request::METHOD_GET, $headers = array(),
                                    $forTableStorage = false, $rawData = null)
    {
        // Set _forTableStorage
        if ($forTableStorage) {
            $this->_forTableStorage = true;
        }

        // Set _isSingleSelect
        if ($httpVerb == Request::METHOD_GET) {
            if (count($this->_operations) > 0) {
                throw new RuntimeException('Select operations can only be performed in an empty batch transaction.');
            }
            $this->_isSingleSelect = true;
        }

        // Clean path
        if (strpos($path, '/') !== 0) {
            $path = '/' . $path;
        }

        // Clean headers
        if ($headers === null) {
            $headers = array();
        }

        // URL encoding
        $path        = Storage::urlencode($path);
        $queryString = Storage::urlencode($queryString);

        // Generate URL
        $requestUrl = $this->getBaseUrl() . $path . $queryString;

        // Generate $rawData
        if ($rawData === null) {
            $rawData = '';
        }

        // Add headers
        if ($httpVerb != Request::METHOD_GET) {
            $headers['Content-ID'] = count($this->_operations) + 1;
            if ($httpVerb != Request::METHOD_DELETE) {
                $headers['Content-Type'] = 'application/atom+xml;type=entry';
            }
            $headers['Content-Length'] = strlen($rawData);
        }

        // Generate $operation
        $operation = '';
        $operation .= $httpVerb . ' ' . $requestUrl . ' HTTP/1.1' . "\n";
        foreach ($headers as $key => $value) {
            $operation .= $key . ': ' . $value . "\n";
        }
        $operation .= "\n";

        // Add data
        $operation .= $rawData;

        // Store operation
        $this->_operations[] = $operation;
    }

    /**
     * Commit current batch
     *
     * @throws RuntimeException
     * @return Response
     */
    public function commit()
    {
        // Perform batch
        $response = $this->_storageClient->performBatch($this->_operations, $this->_forTableStorage,
                                                        $this->_isSingleSelect);

        // Dispose
        $this->_clean();

        // Parse response
        $errors = null;
        preg_match_all('/<message (.*)>(.*)<\/message>/', $response->getBody(), $errors);

        // Error?
        if (count($errors[2]) > 0) {
            throw new RuntimeException('An error has occured while committing a batch: ' . $errors[2][0]);
        }

        // Return
        return $response;
    }

    /**
     * Rollback current batch
     */
    public function rollback()
    {
        // Dispose
        $this->_clean();
    }

    /**
     * Get operation count
     *
     * @return integer
     */
    public function getOperationCount()
    {
        return count($this->_operations);
    }

    /**
     * Is single select?
     *
     * @return boolean
     */
    public function isSingleSelect()
    {
        return $this->_isSingleSelect;
    }
}
