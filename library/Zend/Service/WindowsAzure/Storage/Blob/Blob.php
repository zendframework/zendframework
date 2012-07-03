<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service_WindowsAzure
 */

namespace Zend\Service\WindowsAzure\Storage\Blob;

use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Service\WindowsAzure\Credentials;
use Zend\Service\WindowsAzure\Exception\DomainException;
use Zend\Service\WindowsAzure\Exception\InvalidArgumentException;
use Zend\Service\WindowsAzure\Exception\RuntimeException;
use Zend\Service\WindowsAzure\Storage;
use Zend\Service\WindowsAzure\RetryPolicy;

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage Storage
 */
class Blob extends Storage\Storage
{
    /**
     * ACL - Private access
     */
    const ACL_PRIVATE = false;

    /**
     * ACL - Public access
     */
    const ACL_PUBLIC = true;

    /**
     * Maximal blob size (in bytes)
     */
    const MAX_BLOB_SIZE = 67108864;

    /**
     * Maximal blob transfer size (in bytes)
     */
    const MAX_BLOB_TRANSFER_SIZE = 4194304;

    /**
     * Stream wrapper clients
     *
     * @var array
     */
    protected static $_wrapperClients = array();

    /**
     * SharedAccessSignature credentials
     *
     * @var Credentials\SharedAccessSignature
     */
    private $_sharedAccessSignatureCredentials = null;

    /**
     * Creates a new Blob instance
     *
     * @param string                          $host            Storage host name
     * @param string                          $accountName     Account name for Windows Azure
     * @param string                          $accountKey      Account key for Windows Azure
     * @param boolean                         $usePathStyleUri Use path-style URI's
     * @param RetryPolicy\AbstractRetryPolicy $retryPolicy     Retry policy to use when making requests
     */
    public function __construct($host = Storage\Storage::URL_DEV_BLOB,
                                $accountName = Credentials\AbstractCredentials::DEVSTORE_ACCOUNT,
                                $accountKey = Credentials\AbstractCredentials::DEVSTORE_KEY, $usePathStyleUri = false,
                                RetryPolicy\AbstractRetryPolicy $retryPolicy = null)
    {
        parent::__construct($host, $accountName, $accountKey, $usePathStyleUri, $retryPolicy);

        // API version
        $this->_apiVersion = '2009-07-17';

        // SharedAccessSignature credentials
        $this->_sharedAccessSignatureCredentials = new Credentials\SharedAccessSignature($accountName, $accountKey, $usePathStyleUri);
    }

    /**
     * Check if a blob exists
     *
     * @param string $containerName Container name
     * @param string $blobName      Blob name
     * @throws InvalidArgumentException
     * @throws DomainException
     * @return boolean
     */
    public function blobExists($containerName, $blobName)
    {
        if (!self::isValidContainerName($containerName)) {
            throw new DomainException(
                'Container name does not adhere to container naming conventions. '
                . 'See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
        }
        if ($blobName === '') {
            throw new InvalidArgumentException('Blob name is not specified.');
        }

        // List blobs
        $blobs = $this->listBlobs($containerName, $blobName, '', 1);
        foreach ($blobs as $blob) {
            if ($blob->Name == $blobName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a container exists
     *
     * @param string $containerName Container name
     * @throws DomainException
     * @return boolean
     */
    public function containerExists($containerName)
    {
        if (!self::isValidContainerName($containerName)) {
            throw new DomainException(
                'Container name does not adhere to container naming conventions. '
                . 'See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
        }

        // List containers
        $containers = $this->listContainers($containerName, 1);
        foreach ($containers as $container) {
            if ($container->Name == $containerName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create container
     *
     * @param string $containerName Container name
     * @param array  $metadata      Key/value pairs of meta data
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws DomainException
     * @return object Container properties
     */
    public function createContainer($containerName, $metadata = array())
    {
        if (!self::isValidContainerName($containerName)) {
            throw new DomainException(
                'Container name does not adhere to container naming conventions. '
                . 'See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
        }
        if (!is_array($metadata)) {
            throw new InvalidArgumentException('Meta data should be an array of key and value pairs.');
        }

        // Create metadata headers
        $headers = array();
        $headers = array_merge($headers, $this->_generateMetadataHeaders($metadata));

        // Perform request
        $response = $this->_performRequest($containerName, '?restype=container', Request::METHOD_PUT, $headers, false,
                                           null, Storage\Storage::RESOURCE_CONTAINER,
                                           Credentials\AbstractCredentials::PERMISSION_WRITE);
        if ($response->isSuccess()) {
            return new Storage\BlobContainer(
                $containerName,
                $response->getHeaders()->get('Etag'),
                $response->getHeaders()->get('Last-modified'),
                $metadata
            );
        } else {
            throw new RuntimeException($this->_getErrorMessage($response, 'Resource could not be accessed.'));
        }
    }

    /**
     * Get container ACL
     *
     * @param string $containerName     Container name
     * @param bool   $signedIdentifiers Display only public/private or display signed identifiers?
     * @throws RuntimeException
     * @throws DomainException
     * @return bool Acl, to be compared with Blob::ACL_*
     */
    public function getContainerAcl($containerName, $signedIdentifiers = false)
    {
        if (!self::isValidContainerName($containerName)) {
            throw new DomainException(
                'Container name does not adhere to container naming conventions. '
                . 'See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
        }

        // Perform request
        $response = $this->_performRequest($containerName, '?restype=container&comp=acl', Request::METHOD_GET, array(),
                                           false, null, Storage\Storage::RESOURCE_CONTAINER,
                                           Credentials\AbstractCredentials::PERMISSION_READ);
        if ($response->isSuccess()) {
            if ($signedIdentifiers == false) {
                // Only public/private
                return $response->getHeaders()->get('x-ms-prop-publicaccess') == 'True';
            } else {
                // Parse result
                $result = $this->_parseResponse($response);
                if (!$result) {
                    return array();
                }

                $entries = null;
                if ($result->SignedIdentifier) {
                    if (count($result->SignedIdentifier) > 1) {
                        $entries = $result->SignedIdentifier;
                    } else {
                        $entries = array($result->SignedIdentifier);
                    }
                }

                // Return value
                $returnValue = array();
                foreach ($entries as $entry) {
                    $returnValue[] = new Storage\SignedIdentifier(
                        $entry->Id,
                        $entry->AccessPolicy ? $entry->AccessPolicy->Start ? $entry->AccessPolicy->Start : '' : '',
                        $entry->AccessPolicy ? $entry->AccessPolicy->Expiry ? $entry->AccessPolicy->Expiry : '' : '',
                        $entry->AccessPolicy ? $entry->AccessPolicy->Permission ? $entry->AccessPolicy->Permission : '' : ''
                    );
                }

                // Return
                return $returnValue;
            }
        } else {
            throw new RuntimeException($this->_getErrorMessage($response, 'Resource could not be accessed.'));
        }
    }

    /**
     * Set container ACL
     *
     * @param string $containerName     Container name
     * @param bool   $acl               Blob::ACL_*
     * @param array  $signedIdentifiers Signed identifiers
     * @throws RuntimeException
     * @throws DomainException
     */
    public function setContainerAcl($containerName, $acl = self::ACL_PRIVATE, $signedIdentifiers = array())
    {
        if (!self::isValidContainerName($containerName)) {
            throw new DomainException(
                'Container name does not adhere to container naming conventions. '
                . 'See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
        }

        // Policies
        $policies = null;
        if (is_array($signedIdentifiers) && count($signedIdentifiers) > 0) {
            $policies = '';
            $policies .= '<?xml version="1.0" encoding="utf-8"?>' . "\r\n";
            $policies .= '<SignedIdentifiers>' . "\r\n";
            foreach ($signedIdentifiers as $signedIdentifier) {
                $policies .= '  <SignedIdentifier>' . "\r\n";
                $policies .= '    <Id>' . $signedIdentifier->Id . '</Id>' . "\r\n";
                $policies .= '    <AccessPolicy>' . "\r\n";
                if ($signedIdentifier->Start != '') {
                    $policies .= '      <Start>' . $signedIdentifier->Start . '</Start>' . "\r\n";
                }
                if ($signedIdentifier->Expiry != '') {
                    $policies .= '      <Expiry>' . $signedIdentifier->Expiry . '</Expiry>' . "\r\n";
                }
                if ($signedIdentifier->Permissions != '') {
                    $policies .=
                        '      <Permission>' . $signedIdentifier->Permissions . '</Permission>' . "\r\n";
                }
                $policies .= '    </AccessPolicy>' . "\r\n";
                $policies .= '  </SignedIdentifier>' . "\r\n";
            }
            $policies .= '</SignedIdentifiers>' . "\r\n";
        }

        // Perform request
        $response = $this->_performRequest($containerName, '?restype=container&comp=acl', Request::METHOD_PUT,
                                           array('x-ms-prop-publicaccess' => $acl), false, $policies,
                                           Storage\Storage::RESOURCE_CONTAINER,
                                           Credentials\AbstractCredentials::PERMISSION_WRITE);
        if (!$response->isSuccess()) {
            throw new RuntimeException($this->_getErrorMessage($response, 'Resource could not be accessed.'));
        }
    }

    /**
     * Get container
     *
     * @param string $containerName  Container name
     * @throws RuntimeException
     * @throws DomainException
     * @return Storage\BlobContainer
     */
    public function getContainer($containerName)
    {
        if (!self::isValidContainerName($containerName)) {
            throw new DomainException(
                'Container name does not adhere to container naming conventions. '
                . 'See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
        }

        // Perform request
        $response = $this->_performRequest($containerName, '?restype=container', Request::METHOD_GET, array(), false,
                                           null, Storage\Storage::RESOURCE_CONTAINER,
                                           Credentials\AbstractCredentials::PERMISSION_READ);
        if ($response->isSuccess()) {
            // Parse metadata
            $metadata = $this->_parseMetadataHeaders($response->getHeaders()->toArray());

            // Return container
            return new Storage\BlobContainer(
                $containerName,
                $response->getHeaders()->get('Etag'),
                $response->getHeaders()->get('Last-modified'),
                $metadata
            );
        } else {
            throw new RuntimeException($this->_getErrorMessage($response, 'Resource could not be accessed.'));
        }
    }

    /**
     * Get container metadata
     *
     * @param string $containerName  Container name
     * @throws DomainException
     * @return array Key/value pairs of meta data
     */
    public function getContainerMetadata($containerName)
    {
        if (!self::isValidContainerName($containerName)) {
            throw new DomainException(
                'Container name does not adhere to container naming conventions. '
                . 'See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
        }

        return $this->getContainer($containerName)->Metadata;
    }

    /**
     * Set container metadata
     *
     * Calling the Set Container Metadata operation overwrites all existing metadata that is associated with the container. It's not possible to modify an individual name/value pair.
     *
     * @param string $containerName      Container name
     * @param array  $metadata           Key/value pairs of meta data
     * @param array  $additionalHeaders  Additional headers. See http://msdn.microsoft.com/en-us/library/dd179371.aspx for more information.
     * @throws RuntimeException
     * @throws DomainException
     * @return
     */
    public function setContainerMetadata($containerName, array $metadata = array(), $additionalHeaders = array())
    {
        if (!self::isValidContainerName($containerName)) {
            throw new DomainException(
                'Container name does not adhere to container naming conventions. '
                . 'See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
        }
        if (count($metadata) == 0) {
            return;
        }

        // Create metadata headers
        $headers = array();
        $headers = array_merge($headers, $this->_generateMetadataHeaders($metadata));

        // Additional headers?
        foreach ($additionalHeaders as $key => $value) {
            $headers[$key] = $value;
        }

        // Perform request
        $response = $this->_performRequest($containerName, '?restype=container&comp=metadata', Request::METHOD_PUT,
                                           $headers, false, null, Storage\Storage::RESOURCE_CONTAINER,
                                           Credentials\AbstractCredentials::PERMISSION_WRITE);
        if (!$response->isSuccess()) {
            throw new RuntimeException($this->_getErrorMessage($response, 'Resource could not be accessed.'));
        }
    }

    /**
     * Delete container
     *
     * @param string $containerName      Container name
     * @param array  $additionalHeaders  Additional headers. See http://msdn.microsoft.com/en-us/library/dd179371.aspx for more information.
     * @throws RuntimeException
     * @throws DomainException
     */
    public function deleteContainer($containerName, $additionalHeaders = array())
    {
        if (!self::isValidContainerName($containerName)) {
            throw new DomainException(
                'Container name does not adhere to container naming conventions. '
                . 'See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
        }

        // Additional headers?
        $headers = array();
        foreach ($additionalHeaders as $key => $value) {
            $headers[$key] = $value;
        }

        // Perform request
        $response = $this->_performRequest($containerName, '?restype=container', Request::METHOD_DELETE, $headers,
                                           false, null, Storage\Storage::RESOURCE_CONTAINER,
                                           Credentials\AbstractCredentials::PERMISSION_WRITE);
        if (!$response->isSuccess()) {
            throw new RuntimeException($this->_getErrorMessage($response, 'Resource could not be accessed.'));
        }
    }

    /**
     * List containers
     *
     * @param string $prefix             Optional. Filters the results to return only containers whose name begins with the specified prefix.
     * @param int    $maxResults         Optional. Specifies the maximum number of containers to return per call to Azure storage. This does NOT affect list size returned by this function. (maximum: 5000)
     * @param string $marker             Optional string value that identifies the portion of the list to be returned with the next list operation.
     * @param int    $currentResultCount Current result count (internal use)
     * @throws RuntimeException
     * @return array
     */
    public function listContainers($prefix = null, $maxResults = null, $marker = null, $currentResultCount = 0)
    {
        // Build query string
        $queryString = '?comp=list';
        if ($prefix !== null) {
            $queryString .= '&prefix=' . $prefix;
        }
        if ($maxResults !== null) {
            $queryString .= '&maxresults=' . $maxResults;
        }
        if ($marker !== null) {
            $queryString .= '&marker=' . $marker;
        }

        // Perform request
        $response = $this->_performRequest('', $queryString, Request::METHOD_GET, array(), false, null,
                                           Storage\Storage::RESOURCE_CONTAINER,
                                           Credentials\AbstractCredentials::PERMISSION_LIST);
        if ($response->isSuccess()) {
            $xmlContainers = $this->_parseResponse($response)->Containers->Container;
            $xmlMarker     = (string)$this->_parseResponse($response)->NextMarker;

            $containers = array();
            if ($xmlContainers !== null) {
                for ($i = 0; $i < count($xmlContainers); $i++) {
                    $containers[] = new Storage\BlobContainer(
                        (string)$xmlContainers[$i]->Name,
                        (string)$xmlContainers[$i]->Etag,
                        (string)$xmlContainers[$i]->LastModified
                    );
                }
            }
            $currentResultCount = $currentResultCount + count($containers);
            if ($maxResults !== null && $currentResultCount < $maxResults) {
                if ($xmlMarker !== null && $xmlMarker != '') {
                    $containers = array_merge($containers, $this->listContainers($prefix, $maxResults, $xmlMarker,
                                                                                 $currentResultCount));
                }
            }
            if ($maxResults !== null && count($containers) > $maxResults) {
                $containers = array_slice($containers, 0, $maxResults);
            }

            return $containers;
        } else {
            throw new RuntimeException($this->_getErrorMessage($response, 'Resource could not be accessed.'));
        }
    }

    /**
     * Put blob
     *
     * @param string $containerName      Container name
     * @param string $blobName           Blob name
     * @param string $localFileName      Local file name to be uploaded
     * @param array  $metadata           Key/value pairs of meta data
     * @param array  $additionalHeaders  Additional headers. See http://msdn.microsoft.com/en-us/library/dd179371.aspx for more information.
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws DomainException
     * @return object Partial blob properties
     */
    public function putBlob($containerName, $blobName, $localFileName, $metadata = array(),
                            $additionalHeaders = array())
    {
        if (!self::isValidContainerName($containerName)) {
            throw new DomainException(
                'Container name does not adhere to container naming conventions. '
                . 'See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
        }
        if ($blobName === '') {
            throw new InvalidArgumentException('Blob name is not specified.');
        }
        if (!file_exists($localFileName)) {
            throw new InvalidArgumentException('Local file not found.');
        }
        if ($containerName === '$root' && strpos($blobName, '/') !== false) {
            throw new DomainException(
                'Blobs stored in the root container can not have a name containing a forward slash (/).'
            );
        }

        // Check file size
        if (filesize($localFileName) >= self::MAX_BLOB_SIZE) {
            return $this->putLargeBlob($containerName, $blobName, $localFileName, $metadata);
        }

        // Create metadata headers
        $headers = array();
        $headers = array_merge($headers, $this->_generateMetadataHeaders($metadata));

        // Additional headers?
        foreach ($additionalHeaders as $key => $value) {
            $headers[$key] = $value;
        }

        // File contents
        $fileContents = file_get_contents($localFileName);

        // Resource name
        $resourceName = self::createResourceName($containerName, $blobName);

        // Perform request
        $response = $this->_performRequest($resourceName, '', Request::METHOD_PUT, $headers, false, $fileContents,
                                           Storage\Storage::RESOURCE_BLOB,
                                           Credentials\AbstractCredentials::PERMISSION_WRITE);
        if ($response->isSuccess()) {
            return new Storage\BlobInstance(
                $containerName,
                $blobName,
                $response->getHeaders()->get('Etag'),
                $response->getHeaders()->get('Last-modified'),
                $this->getBaseUrl() . '/' . $containerName . '/' . $blobName,
                strlen($fileContents),
                '',
                '',
                '',
                false,
                $metadata
            );
        } else {
            throw new RuntimeException($this->_getErrorMessage($response, 'Resource could not be accessed.'));
        }
    }

    /**
     * Put large blob (> 64 MB)
     *
     * @param string $containerName Container name
     * @param string $blobName      Blob name
     * @param string $localFileName Local file name to be uploaded
     * @param array  $metadata      Key/value pairs of meta data
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws DomainException
     * @return object Partial blob properties
     */
    public function putLargeBlob($containerName, $blobName, $localFileName, $metadata = array())
    {
        if (!self::isValidContainerName($containerName)) {
            throw new DomainException(
                'Container name does not adhere to container naming conventions. '
                . 'See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
        }
        if ($blobName === '') {
            throw new InvalidArgumentException('Blob name is not specified.');
        }
        if (!file_exists($localFileName)) {
            throw new InvalidArgumentException('Local file not found.');
        }
        if ($containerName === '$root' && strpos($blobName, '/') !== false) {
            throw new DomainException(
                'Blobs stored in the root container can not have a name containing a forward slash (/).'
            );
        }

        // Check file size
        if (filesize($localFileName) < self::MAX_BLOB_SIZE) {
            return $this->putBlob($containerName, $blobName, $localFileName, $metadata);
        }

        // Determine number of parts
        $numberOfParts = ceil(filesize($localFileName) / self::MAX_BLOB_TRANSFER_SIZE);

        // Generate block id's
        $blockIdentifiers = array();
        for ($i = 0; $i < $numberOfParts; $i++) {
            $blockIdentifiers[] = $this->_generateBlockId($i);
        }

        // Open file
        $fp = fopen($localFileName, 'r');
        if ($fp === false) {
            throw new RuntimeException('Could not open local file.');
        }

        // Upload parts
        for ($i = 0; $i < $numberOfParts; $i++) {
            // Seek position in file
            fseek($fp, $i * self::MAX_BLOB_TRANSFER_SIZE);

            // Read contents
            $fileContents = fread($fp, self::MAX_BLOB_TRANSFER_SIZE);

            // Put block
            $this->putBlock($containerName, $blobName, $blockIdentifiers[$i], $fileContents);

            // Dispose file contents
            $fileContents = null;
            unset($fileContents);
        }

        // Close file
        fclose($fp);

        // Put block list
        $this->putBlockList($containerName, $blobName, $blockIdentifiers, $metadata);

        // Return information of the blob
        return $this->getBlobInstance($containerName, $blobName);
    }

    /**
     * Put large blob block
     *
     * @param string       $containerName Container name
     * @param string       $blobName      Blob name
     * @param string       $identifier    Block ID
     * @param array|string $contents      Contents of the block
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function putBlock($containerName, $blobName = '', $identifier, $contents = '')
    {
        if (!self::isValidContainerName($containerName)) {
            throw new DomainException(
                'Container name does not adhere to container naming conventions. '
                . 'See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
        }
        if ($identifier === '') {
            throw new InvalidArgumentException('Block identifier is not specified.');
        }
        if (strlen($contents) > self::MAX_BLOB_TRANSFER_SIZE) {
            throw new DomainException('Block size is too big.');
        }
        if ($containerName === '$root' && strpos($blobName, '/') !== false) {
            throw new DomainException(
                'Blobs stored in the root container can not have a name containing a forward slash (/).'
            );
        }

        // Resource name
        $resourceName = self::createResourceName($containerName, $blobName);

        // Upload
        $response = $this->_performRequest($resourceName, '?comp=block&blockid=' . base64_encode($identifier),
                                           Request::METHOD_PUT, array(), false, $contents, Storage\Storage::RESOURCE_BLOB,
                                           Credentials\AbstractCredentials::PERMISSION_WRITE);
        if (!$response->isSuccess()) {
            throw new RuntimeException($this->_getErrorMessage($response, 'Resource could not be accessed.'));
        }
    }

    /**
     * Put block list
     *
     * @param string $containerName       Container name
     * @param string $blobName            Blob name
     * @param array  $blockList           Array of block identifiers
     * @param array  $metadata            Key/value pairs of meta data
     * @param array  $additionalHeaders   Additional headers. See http://msdn.microsoft.com/en-us/library/dd179371.aspx for more information.
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function putBlockList($containerName, $blobName, array $blockList, $metadata = array(),
                                 array $additionalHeaders = array())
    {
        if (!self::isValidContainerName($containerName)) {
            throw new DomainException(
                'Container name does not adhere to container naming conventions. '
                . 'See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
        }
        if ($blobName === '') {
            throw new InvalidArgumentException('Blob name is not specified.');
        }
        if (count($blockList) == 0) {
            throw new DomainException('Block list does not contain any elements.');
        }
        if ($containerName === '$root' && strpos($blobName, '/') !== false) {
            throw new DomainException(
                'Blobs stored in the root container can not have a name containing a forward slash (/).'
            );
        }

        // Generate block list
        $blocks = '';
        foreach ($blockList as $block) {
            $blocks .= '  <Latest>' . base64_encode($block) . '</Latest>' . "\n";
        }

        // Generate block list request
        $fileContents = utf8_encode(implode("\n", array(
                                                       '<?xml version="1.0" encoding="utf-8"?>',
                                                       '<BlockList>',
                                                       $blocks,
                                                       '</BlockList>'
                                                  )));

        // Create metadata headers
        $headers = array();
        $headers = array_merge($headers, $this->_generateMetadataHeaders($metadata));

        // Additional headers?
        foreach ($additionalHeaders as $key => $value) {
            $headers[$key] = $value;
        }

        // Resource name
        $resourceName = self::createResourceName($containerName, $blobName);

        // Perform request
        $response = $this->_performRequest($resourceName, '?comp=blocklist', Request::METHOD_PUT, $headers, false,
                                           $fileContents, Storage\Storage::RESOURCE_BLOB,
                                           Credentials\AbstractCredentials::PERMISSION_WRITE);
        if (!$response->isSuccess()) {
            throw new RuntimeException($this->_getErrorMessage($response, 'Resource could not be accessed.'));
        }
    }

    /**
     * Get block list
     *
     * @param string  $containerName Container name
     * @param string  $blobName      Blob name
     * @param integer $type          Type of block list to retrieve. 0 = all, 1 = committed, 2 = uncommitted
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws DomainException
     * @return array
     */
    public function getBlockList($containerName, $blobName, $type = 0)
    {
        if (!self::isValidContainerName($containerName)) {
            throw new DomainException(
                'Container name does not adhere to container naming conventions. '
                . 'See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
        }
        if ($blobName === '') {
            throw new InvalidArgumentException('Blob name is not specified.');
        }
        if ($type < 0 || $type > 2) {
            throw new DomainException('Invalid type of block list to retrieve.');
        }

        // Set $blockListType
        $blockListType = 'all';
        if ($type == 1) {
            $blockListType = 'committed';
        }
        if ($type == 2) {
            $blockListType = 'uncommitted';
        }

        // Resource name
        $resourceName = self::createResourceName($containerName, $blobName);

        // Perform request
        $response = $this->_performRequest($resourceName, '?comp=blocklist&blocklisttype=' . $blockListType,
                                           Request::METHOD_GET, array(), false, null, Storage\Storage::RESOURCE_BLOB,
                                           Credentials\AbstractCredentials::PERMISSION_READ);
        if ($response->isSuccess()) {
            // Parse response
            $blockList = $this->_parseResponse($response);

            // Create return value
            $returnValue = array();
            if ($blockList->CommittedBlocks) {
                foreach ($blockList->CommittedBlocks->Block as $block) {
                    $returnValue['CommittedBlocks'][] = (object)array(
                        'Name' => (string)$block->Name,
                        'Size' => (string)$block->Size
                    );
                }
            }
            if ($blockList->UncommittedBlocks) {
                foreach ($blockList->UncommittedBlocks->Block as $block) {
                    $returnValue['UncommittedBlocks'][] = (object)array(
                        'Name' => (string)$block->Name,
                        'Size' => (string)$block->Size
                    );
                }
            }

            return $returnValue;
        } else {
            throw new RuntimeException($this->_getErrorMessage($response, 'Resource could not be accessed.'));
        }
    }

    /**
     * Copy blob
     *
     * @param string $sourceContainerName       Source container name
     * @param string $sourceBlobName            Source blob name
     * @param string $destinationContainerName  Destination container name
     * @param string $destinationBlobName       Destination blob name
     * @param array  $metadata                  Key/value pairs of meta data
     * @param array  $additionalHeaders         Additional headers. See http://msdn.microsoft.com/en-us/library/dd894037.aspx for more information.
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws DomainException
     * @return object Partial blob properties
     */
    public function copyBlob($sourceContainerName, $sourceBlobName, $destinationContainerName,
                             $destinationBlobName, array $metadata = array(), array $additionalHeaders = array())
    {
        if (!self::isValidContainerName($sourceContainerName)) {
            throw new DomainException(
                'Source container name does not adhere to container naming conventions. '
                . 'See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
        }
        if ($sourceBlobName === '') {
            throw new InvalidArgumentException('Source blob name is not specified.');
        }
        if (!self::isValidContainerName($destinationContainerName)) {
            throw new DomainException(
                'Destination container name does not adhere to container naming conventions. '
                . 'See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
        }
        if ($destinationBlobName === '') {
            throw new InvalidArgumentException('Destination blob name is not specified.');
        }
        if ($sourceBlobName === '$root' && strpos($sourceBlobName, '/') !== false) {
            throw new DomainException(
                'Blobs stored in the root container can not have a name containing a forward slash (/).'
            );
        }
        if ($destinationBlobName === '$root' && strpos($destinationBlobName, '/') !== false) {
            throw new DomainException(
                'Blobs stored in the root container can not have a name containing a forward slash (/).'
            );
        }

        // Create metadata headers
        $headers = array();
        $headers = array_merge($headers, $this->_generateMetadataHeaders($metadata));

        // Additional headers?
        foreach ($additionalHeaders as $key => $value) {
            $headers[$key] = $value;
        }

        // Resource names
        $sourceResourceName      = self::createResourceName($sourceContainerName, $sourceBlobName);
        $destinationResourceName = self::createResourceName($destinationContainerName, $destinationBlobName);

        // Set source blob
        $headers["x-ms-copy-source"] = '/' . $this->_accountName . '/' . $sourceResourceName;

        // Perform request
        $response = $this->_performRequest($destinationResourceName, '', Request::METHOD_PUT, $headers, false, null,
                                           Storage\Storage::RESOURCE_BLOB,
                                           Credentials\AbstractCredentials::PERMISSION_WRITE);
        if ($response->isSuccess()) {
            return new Storage\BlobInstance(
                $destinationContainerName,
                $destinationBlobName,
                $response->getHeaders()->get('Etag'),
                $response->getHeaders()->get('Last-modified'),
                $this->getBaseUrl() . '/' . $destinationContainerName . '/' . $destinationBlobName,
                0,
                '',
                '',
                '',
                false,
                $metadata
            );
        } else {
            throw new RuntimeException($this->_getErrorMessage($response, 'Resource could not be accessed.'));
        }
    }

    /**
     * Get blob
     *
     * @param string $containerName      Container name
     * @param string $blobName           Blob name
     * @param string $localFileName      Local file name to store downloaded blob
     * @param array  $additionalHeaders  Additional headers. See http://msdn.microsoft.com/en-us/library/dd179371.aspx for more information.
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function getBlob($containerName, $blobName, $localFileName, $additionalHeaders = array())
    {
        if (!self::isValidContainerName($containerName)) {
            throw new DomainException(
                'Container name does not adhere to container naming conventions. '
                . 'See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
        }
        if ($blobName === '') {
            throw new InvalidArgumentException('Blob name is not specified.');
        }
        if ($localFileName === '') {
            throw new InvalidArgumentException('Local file name is not specified.');
        }

        // Additional headers?
        $headers = array();
        foreach ($additionalHeaders as $key => $value) {
            $headers[$key] = $value;
        }

        // Resource name
        $resourceName = self::createResourceName($containerName, $blobName);

        // Perform request
        $response = $this->_performRequest($resourceName, '', Request::METHOD_GET, $headers, false, null,
                                           Storage\Storage::RESOURCE_BLOB,
                                           Credentials\AbstractCredentials::PERMISSION_READ);
        if ($response->isSuccess()) {
            file_put_contents($localFileName, $response->getBody());
        } else {
            throw new RuntimeException($this->_getErrorMessage($response, 'Resource could not be accessed.'));
        }
    }

    /**
     * Get container
     *
     * @param string $containerName      Container name
     * @param string $blobName           Blob name
     * @param array  $additionalHeaders  Additional headers. See http://msdn.microsoft.com/en-us/library/dd179371.aspx for more information.
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws DomainException
     * @return Storage\BlobInstance
     */
    public function getBlobInstance($containerName, $blobName, array $additionalHeaders = array())
    {
        if (!self::isValidContainerName($containerName)) {
            throw new DomainException(
                'Container name does not adhere to container naming conventions. '
                . 'See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
        }
        if ($blobName === '') {
            throw new InvalidArgumentException('Blob name is not specified.');
        }
        if ($containerName === '$root' && strpos($blobName, '/') !== false) {
            throw new DomainException(
                'Blobs stored in the root container can not have a name containing a forward slash (/).'
            );
        }

        // Additional headers?
        $headers = array();
        foreach ($additionalHeaders as $key => $value) {
            $headers[$key] = $value;
        }

        // Resource name
        $resourceName = self::createResourceName($containerName, $blobName);

        // Perform request
        $response = $this->_performRequest($resourceName, '', Request::METHOD_HEAD, $headers, false, null,
                                           Storage\Storage::RESOURCE_BLOB,
                                           Credentials\AbstractCredentials::PERMISSION_READ);
        if ($response->isSuccess()) {
            // Parse metadata
            $metadata = $this->_parseMetadataHeaders($response->getHeaders()->toArray());

            // Return blob
            return new Storage\BlobInstance(
                $containerName,
                $blobName,
                $response->getHeaders()->get('Etag'),
                $response->getHeaders()->get('Last-modified'),
                $this->getBaseUrl() . '/' . $containerName . '/' . $blobName,
                $response->getHeaders()->get('Content-Length'),
                $response->getHeaders()->get('Content-Type'),
                $response->getHeaders()->get('Content-Encoding'),
                $response->getHeaders()->get('Content-Language'),
                false,
                $metadata
            );
        } else {
            throw new RuntimeException($this->_getErrorMessage($response, 'Resource could not be accessed.'));
        }
    }

    /**
     * Get blob metadata
     *
     * @param string $containerName  Container name
     * @param string $blobName       Blob name
     * @throws InvalidArgumentException
     * @throws DomainException
     * @return array Key/value pairs of meta data
     */
    public function getBlobMetadata($containerName, $blobName)
    {
        if (!self::isValidContainerName($containerName)) {
            throw new DomainException(
                'Container name does not adhere to container naming conventions. '
                . 'See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
        }
        if ($blobName === '') {
            throw new InvalidArgumentException('Blob name is not specified.');
        }
        if ($containerName === '$root' && strpos($blobName, '/') !== false) {
            throw new DomainException(
                'Blobs stored in the root container can not have a name containing a forward slash (/).'
            );
        }

        return $this->getBlobInstance($containerName, $blobName)->Metadata;
    }

    /**
     * Set blob metadata
     *
     * Calling the Set Blob Metadata operation overwrites all existing metadata that is associated with the blob. It's not possible to modify an individual name/value pair.
     *
     * @param string $containerName      Container name
     * @param string $blobName           Blob name
     * @param array  $metadata           Key/value pairs of meta data
     * @param array  $additionalHeaders  Additional headers. See http://msdn.microsoft.com/en-us/library/dd179371.aspx for more information.
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws DomainException
     * @return
     */
    public function setBlobMetadata($containerName, $blobName, array $metadata, $additionalHeaders = array())
    {
        if (!self::isValidContainerName($containerName)) {
            throw new DomainException(
                'Container name does not adhere to container naming conventions. '
                . 'See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
        }
        if ($blobName === '') {
            throw new InvalidArgumentException('Blob name is not specified.');
        }
        if ($containerName === '$root' && strpos($blobName, '/') !== false) {
            throw new DomainException(
                'Blobs stored in the root container can not have a name containing a forward slash (/).'
            );
        }
        if (count($metadata) == 0) {
            return;
        }

        // Create metadata headers
        $headers = array();
        $headers = array_merge($headers, $this->_generateMetadataHeaders($metadata));

        // Additional headers?
        foreach ($additionalHeaders as $key => $value) {
            $headers[$key] = $value;
        }

        // Perform request
        $response = $this->_performRequest(
            $containerName . '/' . $blobName, '?comp=metadata', Request::METHOD_PUT, $headers, false, null,
            Storage\Storage::RESOURCE_BLOB, Credentials\AbstractCredentials::PERMISSION_WRITE);
        if (!$response->isSuccess()) {
            throw new RuntimeException($this->_getErrorMessage($response, 'Resource could not be accessed.'));
        }
    }

    /**
     * Delete blob
     *
     * @param string $containerName      Container name
     * @param string $blobName           Blob name
     * @param array  $additionalHeaders  Additional headers. See http://msdn.microsoft.com/en-us/library/dd179371.aspx for more information.
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function deleteBlob($containerName, $blobName, $additionalHeaders = array())
    {
        if (!self::isValidContainerName($containerName)) {
            throw new DomainException(
                'Container name does not adhere to container naming conventions. '
                . 'See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
        }
        if ($blobName === '') {
            throw new InvalidArgumentException('Blob name is not specified.');
        }
        if ($containerName === '$root' && strpos($blobName, '/') !== false) {
            throw new DomainException(
                'Blobs stored in the root container can not have a name containing a forward slash (/).'
            );
        }

        // Additional headers?
        $headers = array();
        foreach ($additionalHeaders as $key => $value) {
            $headers[$key] = $value;
        }

        // Resource name
        $resourceName = self::createResourceName($containerName, $blobName);

        // Perform request
        $response = $this->_performRequest($resourceName, '', Request::METHOD_DELETE, $headers, false, null,
                                           Storage\Storage::RESOURCE_BLOB,
                                           Credentials\AbstractCredentials::PERMISSION_WRITE);
        if (!$response->isSuccess()) {
            throw new RuntimeException($this->_getErrorMessage($response, 'Resource could not be accessed.'));
        }
    }

    /**
     * List blobs
     *
     * @param string $containerName      Container name
     * @param string $prefix             Optional. Filters the results to return only blobs whose name begins with the specified prefix.
     * @param string $delimiter          Optional. Delimiter, i.e. '/', for specifying folder hierarchy
     * @param int    $maxResults         Optional. Specifies the maximum number of blobs to return per call to Azure storage. This does NOT affect list size returned by this function. (maximum: 5000)
     * @param string $marker             Optional string value that identifies the portion of the list to be returned with the next list operation.
     * @param int    $currentResultCount Current result count (internal use)
     * @throws RuntimeException
     * @throws DomainException
     * @return array
     */
    public function listBlobs($containerName, $prefix = '', $delimiter = '', $maxResults = null, $marker = null,
                              $currentResultCount = 0)
    {
        if (!self::isValidContainerName($containerName)) {
            throw new DomainException(
                'Container name does not adhere to container naming conventions. '
                . 'See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
        }

        // Build query string
        $queryString = '?restype=container&comp=list';
        if ($prefix !== null) {
            $queryString .= '&prefix=' . $prefix;
        }
        if ($delimiter !== '') {
            $queryString .= '&delimiter=' . $delimiter;
        }
        if ($maxResults !== null) {
            $queryString .= '&maxresults=' . $maxResults;
        }
        if ($marker !== null) {
            $queryString .= '&marker=' . $marker;
        }

        // Perform request
        $response = $this->_performRequest($containerName, $queryString, Request::METHOD_GET, array(), false, null,
                                           Storage\Storage::RESOURCE_BLOB,
                                           Credentials\AbstractCredentials::PERMISSION_LIST);
        if ($response->isSuccess()) {
            // Return value
            $blobs = array();

            // Blobs
            $xmlBlobs = $this->_parseResponse($response)->Blobs->Blob;
            if ($xmlBlobs !== null) {
                for ($i = 0; $i < count($xmlBlobs); $i++) {
                    $blobs[] = new Storage\BlobInstance(
                        $containerName,
                        (string)$xmlBlobs[$i]->Name,
                        (string)$xmlBlobs[$i]->Etag,
                        (string)$xmlBlobs[$i]->LastModified,
                        (string)$xmlBlobs[$i]->Url,
                        (string)$xmlBlobs[$i]->Size,
                        (string)$xmlBlobs[$i]->ContentType,
                        (string)$xmlBlobs[$i]->ContentEncoding,
                        (string)$xmlBlobs[$i]->ContentLanguage,
                        false
                    );
                }
            }

            // Blob prefixes (folders)
            $xmlBlobs = $this->_parseResponse($response)->Blobs->BlobPrefix;

            if ($xmlBlobs !== null) {
                for ($i = 0; $i < count($xmlBlobs); $i++) {
                    $blobs[] = new Storage\BlobInstance(
                        $containerName,
                        (string)$xmlBlobs[$i]->Name,
                        '',
                        '',
                        '',
                        0,
                        '',
                        '',
                        '',
                        true
                    );
                }
            }

            // More blobs?
            $xmlMarker          = (string)$this->_parseResponse($response)->NextMarker;
            $currentResultCount = $currentResultCount + count($blobs);
            if ($maxResults !== null && $currentResultCount < $maxResults) {
                if ($xmlMarker !== null && $xmlMarker != '') {
                    $blobs = array_merge($blobs,
                                         $this->listBlobs($containerName, $prefix, $delimiter, $maxResults, $marker,
                                                          $currentResultCount));
                }
            }
            if ($maxResults !== null && count($blobs) > $maxResults) {
                $blobs = array_slice($blobs, 0, $maxResults);
            }

            return $blobs;
        } else {
            throw new RuntimeException($this->_getErrorMessage($response, 'Resource could not be accessed.'));
        }
    }

    /**
     * Generate shared access URL
     *
     * @param string $containerName  Container name
     * @param string $blobName       Blob name
     * @param string $resource       Signed resource - container (c) - blob (b)
     * @param string $permissions    Signed permissions - read (r), write (w), delete (d) and list (l)
     * @param string $start          The time at which the Shared Access Signature becomes valid.
     * @param string $expiry         The time at which the Shared Access Signature becomes invalid.
     * @param string $identifier     Signed identifier
     * @throws DomainException
     * @return string
     */
    public function generateSharedAccessUrl($containerName, $blobName = '', $resource = 'b', $permissions = 'r',
                                            $start = '', $expiry = '', $identifier = '')
    {
        if (!self::isValidContainerName($containerName)) {
            throw new DomainException(
                'Container name does not adhere to container naming conventions. '
                . 'See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
        }

        // Resource name
        $resourceName = self::createResourceName($containerName, $blobName);

        // Generate URL
        return $this->getBaseUrl() . '/' . $resourceName . '?' .
               $this->_sharedAccessSignatureCredentials->createSignedQueryString(
                   $resourceName,
                   '',
                   $resource,
                   $permissions,
                   $start,
                   $expiry,
                   $identifier);
    }

    /**
     * Register this object as stream wrapper client
     *
     * @param  string $name Protocol name
     * @return Blob
     */
    public function registerAsClient($name)
    {
        self::$_wrapperClients[$name] = $this;
        return $this;
    }

    /**
     * Unregister this object as stream wrapper client
     *
     * @param  string $name Protocol name
     * @return Blob
     */
    public function unregisterAsClient($name)
    {
        unset(self::$_wrapperClients[$name]);
        return $this;
    }

    /**
     * Get wrapper client for stream type
     *
     * @param  string $name Protocol name
     * @return Blob
     */
    public static function getWrapperClient($name)
    {
        return self::$_wrapperClients[$name];
    }

    /**
     * Register this object as stream wrapper
     *
     * @param  string $name Protocol name
     */
    public function registerStreamWrapper($name = 'azure')
    {
        stream_register_wrapper($name, 'Blob_Stream');
        $this->registerAsClient($name);
    }

    /**
     * Unregister this object as stream wrapper
     *
     * @param  string $name Protocol name
     * @return Blob
     */
    public function unregisterStreamWrapper($name = 'azure')
    {
        stream_wrapper_unregister($name);
        $this->unregisterAsClient($name);
    }

    /**
     * Create resource name
     *
     * @param string $containerName  Container name
     * @param string $blobName       Blob name
     * @return string
     */
    public static function createResourceName($containerName = '', $blobName = '')
    {
        // Resource name
        $resourceName = $containerName . '/' . $blobName;
        if ($containerName === '' || $containerName === '$root') {
            $resourceName = $blobName;
        }
        if ($blobName === '') {
            $resourceName = $containerName;
        }

        return $resourceName;
    }

    /**
     * Is valid container name?
     *
     * @param string $containerName Container name
     * @return boolean
     */
    public static function isValidContainerName($containerName = '')
    {
        if ($containerName == '$root') {
            return true;
        }

        if (preg_match("/^[a-z0-9][a-z0-9-]*$/", $containerName) === 0) {
            return false;
        }

        if (strpos($containerName, '--') !== false) {
            return false;
        }

        if (strtolower($containerName) != $containerName) {
            return false;
        }

        if (strlen($containerName) < 3 || strlen($containerName) > 63) {
            return false;
        }

        if (substr($containerName, -1) == '-') {
            return false;
        }

        return true;
    }

    /**
     * Get error message from Zend\Http\Response
     *
     * @param Response $response         Response
     * @param string   $alternativeError Alternative error message
     * @return string
     */
    protected function _getErrorMessage(Response $response, $alternativeError = 'Unknown error.')
    {
        $response = $this->_parseResponse($response);
        if ($response && $response->Message) {
            return (string)$response->Message;
        } else {
            return $alternativeError;
        }
    }

    /**
     * Generate block id
     *
     * @param int $part Block number
     * @return string Windows Azure Blob Storage block number
     */
    protected function _generateBlockId($part = 0)
    {
        $returnValue = $part;
        while (strlen($returnValue) < 64) {
            $returnValue = '0' . $returnValue;
        }

        return $returnValue;
    }
}
