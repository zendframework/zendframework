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
use Zend\Service\WindowsAzure\Credentials;
use Zend\Service\WindowsAzure\Exception\RuntimeException;

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractBatchStorage extends Storage
{
    /**
     * Current batch
     *
     * @var Batch
     */
    protected $_currentBatch = null;

    /**
     * Set current batch
     *
     * @param Batch $batch Current batch
     * @throws RuntimeException
     */
    public function setCurrentBatch(Batch $batch = null)
    {
        if ($batch !== null && $this->isInBatch()) {
            throw new RuntimeException('Only one batch can be active at a time.');
        }
        $this->_currentBatch = $batch;
    }

    /**
     * Get current batch
     *
     * @return Batch
     */
    public function getCurrentBatch()
    {
        return $this->_currentBatch;
    }

    /**
     * Is there a current batch?
     *
     * @return boolean
     */
    public function isInBatch()
    {
        return $this->_currentBatch !== null;
    }

    /**
     * Starts a new batch operation set
     *
     * @return Batch
     * @throws RuntimeException
     */
    public function startBatch()
    {
        return new Batch($this, $this->getBaseUrl());
    }

    /**
     * Perform batch using Client channel, combining all batch operations into one request
     *
     * @param array   $operations         Operations in batch
     * @param boolean $forTableStorage    Is the request for table storage?
     * @param boolean $isSingleSelect     Is the request a single select statement?
     * @param string  $resourceType       Resource type
     * @param string  $requiredPermission Required permission
     * @return Response
     */
    public function performBatch($operations = array(), $forTableStorage = false, $isSingleSelect = false,
                                 $resourceType = Storage::RESOURCE_UNKNOWN,
                                 $requiredPermission = Credentials\AbstractCredentials::PERMISSION_READ)
    {
        // Generate boundaries
        $batchBoundary     = 'batch_' . md5(time() . microtime());
        $changesetBoundary = 'changeset_' . md5(time() . microtime());

        // Set headers
        $headers = array();

        // Add version header
        $headers['x-ms-version'] = $this->_apiVersion;

        // Add content-type header
        $headers['Content-Type'] = 'multipart/mixed; boundary=' . $batchBoundary;

        // Set path and query string
        $path        = '/$batch';
        $queryString = '';

        // Set verb
        $httpVerb = Request::METHOD_POST;

        // Generate raw data
        $rawData = '';

        // Single select?
        if ($isSingleSelect) {
            $operation = $operations[0];
            $rawData .= '--' . $batchBoundary . "\n";
            $rawData .= 'Content-Type: application/http' . "\n";
            $rawData .= 'Content-Transfer-Encoding: binary' . "\n\n";
            $rawData .= $operation;
            $rawData .= '--' . $batchBoundary . '--';
        } else {
            $rawData .= '--' . $batchBoundary . "\n";
            $rawData .= 'Content-Type: multipart/mixed; boundary=' . $changesetBoundary . "\n\n";

            // Add operations
            foreach ($operations as $operation) {
                $rawData .= '--' . $changesetBoundary . "\n";
                $rawData .= 'Content-Type: application/http' . "\n";
                $rawData .= 'Content-Transfer-Encoding: binary' . "\n\n";
                $rawData .= $operation;
            }
            $rawData .= '--' . $changesetBoundary . '--' . "\n";

            $rawData .= '--' . $batchBoundary . '--';
        }

        // Generate URL and sign request
        $requestUrl     = $this->_credentials->signRequestUrl(
            $this->getBaseUrl() . $path . $queryString, $resourceType, $requiredPermission);
        $requestHeaders = $this->_credentials->signRequestHeaders($httpVerb, $path, $queryString, $headers,
                                                                  $forTableStorage, $resourceType, $requiredPermission);

        // Prepare request
        $this->_httpClientChannel->resetParameters(true);
        $this->_httpClientChannel->setUri($requestUrl);
        $this->_httpClientChannel->setHeaders($requestHeaders);
        $this->_httpClientChannel->setRawBody($rawData);

        // Execute request
        $response = $this->_retryPolicy->execute(
            array($this->_httpClientChannel, 'request'),
            array($httpVerb)
        );

        return $response;
    }
}
