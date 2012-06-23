<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service_WindowsAzure
 */

namespace Zend\Service\WindowsAzure\Storage;

use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Service\WindowsAzure\Credentials;
use Zend\Service\WindowsAzure\Exception\InvalidArgumentException;
use Zend\Service\WindowsAzure\Exception\RuntimeException;
use Zend\Service\WindowsAzure\RetryPolicy;

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage Storage
 */
class Table extends AbstractBatchStorage
{
    /**
     * Creates a new Table instance
     *
     * @param string                          $host            Storage host name
     * @param string                          $accountName     Account name for Windows Azure
     * @param string                          $accountKey      Account key for Windows Azure
     * @param boolean                         $usePathStyleUri Use path-style URI's
     * @param RetryPolicy\AbstractRetryPolicy $retryPolicy     Retry policy to use when making requests
     */
    public function __construct($host = Storage::URL_DEV_TABLE,
                                $accountName = Credentials\AbstractCredentials::DEVSTORE_ACCOUNT,
                                $accountKey = Credentials\AbstractCredentials::DEVSTORE_KEY, $usePathStyleUri = false,
                                RetryPolicy\AbstractRetryPolicy $retryPolicy = null)
    {
        parent::__construct($host, $accountName, $accountKey, $usePathStyleUri, $retryPolicy);

        // Always use SharedKeyLite authentication
        $this->_credentials = new Credentials\SharedKeyLite($accountName, $accountKey, $this->_usePathStyleUri);

        // API version
        $this->_apiVersion = '2009-04-14';
    }

    /**
     * Check if a table exists
     *
     * @param string $tableName Table name
     * @return boolean
     * @throws InvalidArgumentException
     */
    public function tableExists($tableName)
    {
        if ($tableName === '') {
            throw new InvalidArgumentException('Table name is not specified.');
        }

        // List tables
        $tables = $this->listTables($tableName);
        foreach ($tables as $table) {
            if ($table->Name == $tableName) {
                return true;
            }
        }

        return false;
    }

    /**
     * List tables
     *
     * @param  string $nextTableName Next table name, used for listing tables when total amount of tables is > 1000.
     * @throws RuntimeException
     * @return array
     */
    public function listTables($nextTableName = '')
    {
        // Build query string
        $queryString = '';
        if ($nextTableName != '') {
            $queryString = '?NextTableName=' . $nextTableName;
        }

        // Perform request
        $response = $this->_performRequest('Tables', $queryString, Request::METHOD_GET, null, true);
        if ($response->isSuccess()) {
            // Parse result
            $result = $this->_parseResponse($response);

            if (!$result || !$result->entry) {
                return array();
            }

            $entries = null;
            if (count($result->entry) > 1) {
                $entries = $result->entry;
            } else {
                $entries = array($result->entry);
            }

            // Create return value
            $returnValue = array();
            foreach ($entries as $entry) {
                $tableName = $entry->xpath('.//m:properties/d:TableName');
                $tableName = (string)$tableName[0];

                $returnValue[] = new TableInstance(
                    (string)$entry->id,
                    $tableName,
                    (string)$entry->link['href'],
                    (string)$entry->updated
                );
            }

            // More tables?
            if ($response->headers()->get('x-ms-continuation-NextTableName') !== null) {
                $returnValue = array_merge($returnValue,
                                           $this->listTables($response->headers()
                                                                 ->get('x-ms-continuation-NextTableName')));
            }

            return $returnValue;
        } else {
            throw new RuntimeException($this->_getErrorMessage($response, 'Resource could not be accessed.'));
        }
    }

    /**
     * Create table
     *
     * @param string $tableName Table name
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @return TableInstance
     */
    public function createTable($tableName)
    {
        if ($tableName === '') {
            throw new InvalidArgumentException('Table name is not specified.');
        }

        // Generate request body
        $requestBody = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>
                        <entry
                        	xmlns:d="http://schemas.microsoft.com/ado/2007/08/dataservices"
                        	xmlns:m="http://schemas.microsoft.com/ado/2007/08/dataservices/metadata"
                        	xmlns="http://www.w3.org/2005/Atom">
                          <title />
                          <updated>{tpl:Updated}</updated>
                          <author>
                            <name />
                          </author>
                          <id />
                          <content type="application/xml">
                            <m:properties>
                              <d:TableName>{tpl:TableName}</d:TableName>
                            </m:properties>
                          </content>
                        </entry>';

        $requestBody = $this->_fillTemplate($requestBody, array(
                                                               'BaseUrl'     => $this->getBaseUrl(),
                                                               'TableName'   => htmlspecialchars($tableName),
                                                               'Updated'     => $this->isoDate(),
                                                               'AccountName' => $this->_accountName
                                                          ));

        // Add header information
        $headers                          = array();
        $headers['Content-Type']          = 'application/atom+xml';
        $headers['DataServiceVersion']    = '1.0;NetFx';
        $headers['MaxDataServiceVersion'] = '1.0;NetFx';

        // Perform request
        $response = $this->_performRequest('Tables', '', Request::METHOD_POST, $headers, true, $requestBody);
        if ($response->isSuccess()) {
            // Parse response
            $entry = $this->_parseResponse($response);

            $tableName = $entry->xpath('.//m:properties/d:TableName');
            $tableName = (string)$tableName[0];

            return new TableInstance(
                (string)$entry->id,
                $tableName,
                (string)$entry->link['href'],
                (string)$entry->updated
            );
        } else {
            throw new RuntimeException($this->_getErrorMessage($response, 'Resource could not be accessed.'));
        }
    }

    /**
     * Delete table
     *
     * @param string $tableName Table name
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function deleteTable($tableName)
    {
        if ($tableName === '') {
            throw new InvalidArgumentException('Table name is not specified.');
        }

        // Add header information
        $headers                 = array();
        $headers['Content-Type'] = 'application/atom+xml';

        // Perform request
        $response = $this->_performRequest(
            'Tables(\'' . $tableName . '\')', '', Request::METHOD_DELETE, $headers, true, null);
        if (!$response->isSuccess()) {
            throw new RuntimeException($this->_getErrorMessage($response, 'Resource could not be accessed.'));
        }
    }

    /**
     * Insert entity into table
     *
     * @param string                     $tableName   Table name
     * @param TableEntity                $entity      Entity to insert
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @return TableEntity
     */
    public function insertEntity($tableName, TableEntity $entity)
    {
        if ($tableName === '') {
            throw new InvalidArgumentException('Table name is not specified.');
        }
        if ($entity === null) {
            throw new InvalidArgumentException('Entity is not specified.');
        }

        // Generate request body
        $requestBody = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>
                        <entry xmlns:d="http://schemas.microsoft.com/ado/2007/08/dataservices" xmlns:m="http://schemas.microsoft.com/ado/2007/08/dataservices/metadata" xmlns="http://www.w3.org/2005/Atom">
                          <title />
                          <updated>{tpl:Updated}</updated>
                          <author>
                            <name />
                          </author>
                          <id />
                          <content type="application/xml">
                            <m:properties>
                              {tpl:Properties}
                            </m:properties>
                          </content>
                        </entry>';

        $requestBody = $this->_fillTemplate($requestBody, array(
                                                               'Updated'    => $this->isoDate(),
                                                               'Properties' => $this->_generateAzureRepresentation($entity)
                                                          ));

        // Add header information
        $headers                 = array();
        $headers['Content-Type'] = 'application/atom+xml';

        // Perform request
        $response = null;
        if ($this->isInBatch()) {
            $this->getCurrentBatch()
                ->enlistOperation($tableName, '', Request::METHOD_POST, $headers, true, $requestBody);
            return null;
        } else {
            $response = $this->_performRequest($tableName, '', Request::METHOD_POST, $headers, true, $requestBody);
        }
        if ($response->isSuccess()) {
            // Parse result
            $result = $this->_parseResponse($response);

            $timestamp = $result->xpath('//m:properties/d:Timestamp');
            $timestamp = (string)$timestamp[0];

            $etag = $result->attributes('http://schemas.microsoft.com/ado/2007/08/dataservices/metadata');
            $etag = (string)$etag['etag'];

            // Update properties
            $entity->setTimestamp($timestamp);
            $entity->setEtag($etag);

            return $entity;
        } else {
            throw new RuntimeException($this->_getErrorMessage($response, 'Resource could not be accessed.'));
        }
    }

    /**
     * Delete entity from table
     *
     * @param string                     $tableName   Table name
     * @param TableEntity                $entity      Entity to delete
     * @param boolean                    $verifyEtag  Verify etag of the entity (used for concurrency)
     * @return null
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @return null
     */
    public function deleteEntity($tableName, TableEntity $entity, $verifyEtag = false)
    {
        if ($tableName === '') {
            throw new InvalidArgumentException('Table name is not specified.');
        }
        if ($entity === null) {
            throw new InvalidArgumentException('Entity is not specified.');
        }

        // Add header information
        $headers = array();
        if (!$this->isInBatch()) {
            // http://social.msdn.microsoft.com/Forums/en-US/windowsazure/thread/9e255447-4dc7-458a-99d3-bdc04bdc5474/
            $headers['Content-Type'] = 'application/atom+xml';
        }
        $headers['Content-Length'] = 0;
        if (!$verifyEtag) {
            $headers['If-Match'] = '*';
        } else {
            $headers['If-Match'] = $entity->getEtag();
        }

        // Perform request
        $response = null;
        if ($this->isInBatch()) {
            $this->getCurrentBatch()->enlistOperation(
                $tableName . '(PartitionKey=\'' . $entity->getPartitionKey() . '\', RowKey=\'' . $entity->getRowKey() .
                '\')', '', Request::METHOD_DELETE, $headers, true, null);
            return null;
        } else {
            $response = $this->_performRequest(
                $tableName . '(PartitionKey=\'' . $entity->getPartitionKey() . '\', RowKey=\'' . $entity->getRowKey() .
                '\')', '', Request::METHOD_DELETE, $headers, true, null);
        }
        if (!$response->isSuccess()) {
            throw new RuntimeException($this->_getErrorMessage($response, 'Resource could not be accessed.'));
        }
    }

    /**
     * Retrieve entity from table, by id
     *
     * @param string $tableName    Table name
     * @param string $partitionKey Partition key
     * @param string $rowKey       Row key
     * @param string $entityClass  Entity class name*
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @return TableEntity
     */
    public function retrieveEntityById($tableName, $partitionKey, $rowKey, $entityClass = 'DynamicTableEntity')
    {
        if ($tableName === '') {
            throw new InvalidArgumentException('Table name is not specified.');
        }
        if ($partitionKey === '') {
            throw new InvalidArgumentException('Partition key is not specified.');
        }
        if ($rowKey === '') {
            throw new InvalidArgumentException('Row key is not specified.');
        }
        if ($entityClass === '') {
            throw new InvalidArgumentException('Entity class is not specified.');
        }


        // Check for combined size of partition key and row key
        // http://msdn.microsoft.com/en-us/library/dd179421.aspx
        if (strlen($partitionKey . $rowKey) >= 256) {
            // Start a batch if possible
            if ($this->isInBatch()) {
                throw new RuntimeException(
                    'Entity cannot be retrieved. A transaction is required to retrieve the entity, '
                    . 'but another transaction is already active.'
                );
            }

            $this->startBatch();
        }

        // Fetch entities from Azure
        $result = $this->retrieveEntities(
            $this->select()
                ->from($tableName)
                ->wherePartitionKey($partitionKey)
                ->whereRowKey($rowKey),
            '',
            $entityClass
        );

        // Return
        if (count($result) == 1) {
            return $result[0];
        }

        return null;
    }

    /**
     * Create a new TableEntityQuery
     *
     * @return TableEntityQuery
     */
    public function select()
    {
        return new TableEntityQuery();
    }

    /**
     * Retrieve entities from table
     *
     * @param string|TableEntityQuery $tableName        Table name -or- TableEntityQuery instance
     * @param string                  $filter           Filter condition (not applied when $tableName is a TableEntityQuery instance)
     * @param string                  $entityClass      Entity class name
     * @param string                  $nextPartitionKey Next partition key, used for listing entities when total amount of entities is > 1000.
     * @param string                  $nextRowKey       Next row key, used for listing entities when total amount of entities is > 1000.
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @return array Array of TableEntity
     */
    public function retrieveEntities($tableName, $filter = '', $entityClass = 'DynamicTableEntity',
                                     $nextPartitionKey = null, $nextRowKey = null)
    {
        if ($tableName === '') {
            throw new InvalidArgumentException('Table name is not specified.');
        }
        if ($entityClass === '') {
            throw new InvalidArgumentException('Entity class is not specified.');
        }

        // Convenience...
        if (class_exists($filter)) {
            $entityClass = $filter;
            $filter      = '';
        }

        // Query string
        $queryString = '';

        // Determine query
        if (is_string($tableName)) {
            // Option 1: $tableName is a string

            // Append parentheses
            $tableName .= '()';

            // Build query
            $query = array();

            // Filter?
            if ($filter !== '') {
                $query[] = '$filter=' . rawurlencode($filter);
            }

            // Build queryString
            if (count($query) > 0) {
                $queryString = '?' . implode('&', $query);
            }
        } else if (get_class($tableName) == 'TableEntityQuery') {
            // Option 2: $tableName is a TableEntityQuery instance

            // Build queryString
            $queryString = $tableName->assembleQueryString(true);

            // Change $tableName
            $tableName = $tableName->assembleFrom(true);
        } else {
            throw new InvalidArgumentException('Invalid argument: $tableName');
        }

        // Add continuation querystring parameters?
        if ($nextPartitionKey !== null && $nextRowKey !== null) {
            if ($queryString !== '') {
                $queryString .= '&';
            }

            $queryString .=
                '&NextPartitionKey=' . rawurlencode($nextPartitionKey) . '&NextRowKey=' . rawurlencode($nextRowKey);
        }

        // Perform request
        $response = null;
        if ($this->isInBatch() && $this->getCurrentBatch()->getOperationCount() == 0) {
            $this->getCurrentBatch()
                ->enlistOperation($tableName, $queryString, Request::METHOD_GET, array(), true, null);
            $response = $this->getCurrentBatch()->commit();

            // Get inner response (multipart)
            $innerResponse = $response->getBody();
            $innerResponse = substr($innerResponse, strpos($innerResponse, 'HTTP/1.1 200 OK'));
            $innerResponse = substr($innerResponse, 0, strpos($innerResponse, '--batchresponse'));
            $response      = Response::fromString($innerResponse);
        } else {
            $response = $this->_performRequest($tableName, $queryString, Request::METHOD_GET, array(), true, null);
        }

        if ($response->isSuccess()) {
            // Parse result
            $result = $this->_parseResponse($response);
            if (!$result) {
                return array();
            }

            $entries = null;
            if ($result->entry) {
                if (count($result->entry) > 1) {
                    $entries = $result->entry;
                } else {
                    $entries = array($result->entry);
                }
            } else {
                // This one is tricky... If we have properties defined, we have an entity.
                $properties = $result->xpath('//m:properties');
                if ($properties) {
                    $entries = array($result);
                } else {
                    return array();
                }
            }

            // Create return value
            $returnValue = array();
            foreach ($entries as $entry) {
                // Parse properties
                $properties = $entry->xpath('.//m:properties');
                $properties = $properties[0]->children('http://schemas.microsoft.com/ado/2007/08/dataservices');

                // Create entity
                $entity = new $entityClass('', '');
                $entity->setAzureValues((array)$properties, true);

                // If we have a DynamicTableEntity, make sure all property types are OK
                if ($entity instanceof DynamicTableEntity) {
                    foreach ($properties as $key => $value) {
                        $attributes = $value->attributes('http://schemas.microsoft.com/ado/2007/08/dataservices/metadata');
                        $type       = (string)$attributes['type'];
                        if ($type !== '') {
                            $entity->setAzurePropertyType($key, $type);
                        }
                    }
                }

                // Update etag
                $etag = $entry->attributes('http://schemas.microsoft.com/ado/2007/08/dataservices/metadata');
                $etag = (string)$etag['etag'];
                $entity->setEtag($etag);

                // Add to result
                $returnValue[] = $entity;
            }

            // More entities?
            if ($response->headers()->get('x-ms-continuation-NextPartitionKey') !== null &&
                $response->headers()->get('x-ms-continuation-NextRowKey') !== null
            ) {
                if (strpos($queryString, '$top') === false) {
                    $returnValue = array_merge($returnValue, $this->retrieveEntities($tableName, $filter, $entityClass,
                                                                                     $response->headers()
                                                                                         ->get('x-ms-continuation-NextPartitionKey'),
                                                                                     $response->headers()
                                                                                         ->get('x-ms-continuation-NextRowKey')));
                }
            }

            // Return
            return $returnValue;
        } else {
            throw new RuntimeException($this->_getErrorMessage($response, 'Resource could not be accessed.'));
        }
    }

    /**
     * Update entity by replacing it
     *
     * @param string        $tableName   Table name
     * @param TableEntity   $entity      Entity to update
     * @param boolean       $verifyEtag  Verify etag of the entity (used for concurrency)
     * @return null|TableEntity
     * @throws RuntimeException
     */
    public function updateEntity($tableName = '', TableEntity $entity = null, $verifyEtag = false)
    {
        return $this->_changeEntity(Request::METHOD_PUT, $tableName, $entity, $verifyEtag);
    }

    /**
     * Update entity by adding or updating properties
     *
     * @param string      $tableName   Table name
     * @param TableEntity $entity      Entity to update
     * @param boolean     $verifyEtag  Verify etag of the entity (used for concurrency)
     * @param array       $properties  Properties to merge. All properties will be used when omitted.
     * @return null|TableEntity
     * @throws RuntimeException
     */
    public function mergeEntity($tableName = '', TableEntity $entity = null, $verifyEtag = false, $properties = array())
    {
        $mergeEntity = null;
        if (is_array($properties) && count($properties) > 0) {
            // Build a new object
            $mergeEntity = new DynamicTableEntity($entity->getPartitionKey(), $entity->getRowKey());

            // Keep only values mentioned in $properties
            $azureValues = $entity->getAzureValues();
            foreach ($azureValues as $value) {
                if (in_array($value->Name, $properties)) {
                    $mergeEntity->setAzureProperty($value->Name, $value->Value, $value->Type);
                }
            }
        } else {
            $mergeEntity = $entity;
        }

        return $this->_changeEntity(Request::MERGE, $tableName, $mergeEntity, $verifyEtag);
    }

    /**
     * Get error message from Response
     *
     * @param Response $response         Response
     * @param string   $alternativeError Alternative error message
     * @return string
     */
    protected function _getErrorMessage(Response $response, $alternativeError = 'Unknown error.')
    {
        $response = $this->_parseResponse($response);
        if ($response && $response->message) {
            return (string)$response->message;
        } else {
            return $alternativeError;
        }
    }

    /**
     * Update entity / merge entity
     *
     * @param string       $httpVerb    HTTP verb to use (PUT = update, MERGE = merge)
     * @param string       $tableName   Table name
     * @param TableEntity  $entity      Entity to update
     * @param boolean      $verifyEtag  Verify etag of the entity (used for concurrency)
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @return null|\Zend\Service\WindowsAzure\Storage\TableEntity
     */
    protected function _changeEntity($httpVerb = Request::METHOD_PUT, $tableName, TableEntity $entity,
                                     $verifyEtag = false)
    {
        if ($tableName === '') {
            throw new InvalidArgumentException('Table name is not specified.');
        }
        if ($entity === null) {
            throw new InvalidArgumentException('Entity is not specified.');
        }

        // Add header information
        $headers                   = array();
        $headers['Content-Type']   = 'application/atom+xml';
        $headers['Content-Length'] = 0;
        if (!$verifyEtag) {
            $headers['If-Match'] = '*';
        } else {
            $headers['If-Match'] = $entity->getEtag();
        }

        // Generate request body
        $requestBody = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>
                        <entry xmlns:d="http://schemas.microsoft.com/ado/2007/08/dataservices" xmlns:m="http://schemas.microsoft.com/ado/2007/08/dataservices/metadata" xmlns="http://www.w3.org/2005/Atom">
                          <title />
                          <updated>{tpl:Updated}</updated>
                          <author>
                            <name />
                          </author>
                          <id />
                          <content type="application/xml">
                            <m:properties>
                              {tpl:Properties}
                            </m:properties>
                          </content>
                        </entry>';

        $requestBody = $this->_fillTemplate($requestBody, array(
                                                               'Updated'    => $this->isoDate(),
                                                               'Properties' => $this->_generateAzureRepresentation($entity)
                                                          ));

        // Add header information
        $headers                 = array();
        $headers['Content-Type'] = 'application/atom+xml';
        if (!$verifyEtag) {
            $headers['If-Match'] = '*';
        } else {
            $headers['If-Match'] = $entity->getEtag();
        }

        // Perform request
        $response = null;
        if ($this->isInBatch()) {
            $this->getCurrentBatch()->enlistOperation(
                $tableName . '(PartitionKey=\'' . $entity->getPartitionKey() . '\', RowKey=\'' . $entity->getRowKey() .
                '\')', '', $httpVerb, $headers, true, $requestBody);
            return null;
        } else {
            $response = $this->_performRequest(
                $tableName . '(PartitionKey=\'' . $entity->getPartitionKey() . '\', RowKey=\'' . $entity->getRowKey() .
                '\')', '', $httpVerb, $headers, true, $requestBody);
        }
        if ($response->isSuccess()) {
            // Update properties
            $entity->setEtag($response->headers()->get('Etag'));
            $entity->setTimestamp($response->headers()->get('Last-modified'));

            return $entity;
        } else {
            throw new RuntimeException($this->_getErrorMessage($response, 'Resource could not be accessed.'));
        }
    }

    /**
     * Generate RFC 1123 compliant date string
     *
     * @return string
     */
    protected function _rfcDate()
    {
        return gmdate('D, d M Y H:i:s', time()) . ' GMT'; // RFC 1123
    }

    /**
     * Fill text template with variables from key/value array
     *
     * @param string $templateText Template text
     * @param array  $variables    Array containing key/value pairs
     * @return string
     */
    protected function _fillTemplate($templateText, $variables = array())
    {
        foreach ($variables as $key => $value) {
            $templateText = str_replace('{tpl:' . $key . '}', $value, $templateText);
        }
        return $templateText;
    }

    /**
     * Generate Azure representation from entity (creates atompub markup from properties)
     *
     * @param TableEntity $entity
     * @return string
     */
    protected function _generateAzureRepresentation(TableEntity $entity = null)
    {
        // Generate Azure representation from entity
        $azureRepresentation = array();
        $azureValues         = $entity->getAzureValues();
        foreach ($azureValues as $azureValue) {
            $value   = array();
            $value[] = '<d:' . $azureValue->Name;
            if ($azureValue->Type != '') {
                $value[] = ' m:type="' . $azureValue->Type . '"';
            }
            if ($azureValue->Value === null) {
                $value[] = ' m:null="true"';
            }
            $value[] = '>';

            if ($azureValue->Value !== null) {
                if (strtolower($azureValue->Type) == 'edm.boolean') {
                    $value[] = ($azureValue->Value == true ? '1' : '0');
                } else {
                    $value[] = htmlspecialchars($azureValue->Value);
                }
            }

            $value[]               = '</d:' . $azureValue->Name . '>';
            $azureRepresentation[] = implode('', $value);
        }

        return implode('', $azureRepresentation);
    }
}
