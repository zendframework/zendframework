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
 * @subpackage Amazon_S3
 * @copyright  Copyright (c) 2009, Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: S3.php 14190 2009-02-28 04:32:18Z jplock $
 */

/**
 * @see Zend_Service_Amazon_Abstract
 */
require_once 'Zend/Service/Amazon/Abstract.php';

/**
 * @see Zend_Crypt_Hmac
 */
require_once 'Zend/Crypt/Hmac.php';

/**
 * Amazon S3 PHP connection class
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Amazon_S3
 * @copyright  Copyright (c) 2005-2008, Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Amazon_S3 extends Zend_Service_Amazon_Abstract
{
    /**
     * Store for stream wrapper clients
     *
     * @var array
     */
    protected static $_wrapperClients = array();

    const S3_ENDPOINT = 's3.amazonaws.com';

    const S3_ACL_PRIVATE = 'private';
    const S3_ACL_PUBLIC_READ = 'public-read';
    const S3_ACL_PUBLIC_WRITE = 'public-read-write';
    const S3_ACL_AUTH_READ = 'authenticated-read';

    const S3_ACL_HEADER = 'x-amz-acl';
    const S3_CONTENT_TYPE_HEADER = 'Content-Type';

    /**
     * Add a new bucket
     *
     * @param  string $bucket
     * @return boolean
     */
    public function createBucket($bucket)
    {
        $len = strlen($bucket);
        if ($len < 3 || $len > 255) {
            /**
             * @see Zend_Service_Amazon_S3_Exception
             */
            require_once 'Zend/Service/Amazon/S3/Exception.php';
            throw new Zend_Service_Amazon_S3_Exception("Bucket name \"$bucket\" must be between 3 and 255 characters long");
        }

        if (preg_match('/[^a-z0-9\._-]/', $bucket)) {
            /**
             * @see Zend_Service_Amazon_S3_Exception
             */
            require_once 'Zend/Service/Amazon/S3/Exception.php';
            throw new Zend_Service_Amazon_S3_Exception("Bucket name \"$bucket\" contains invalid characters");
        }

        if (preg_match('/(\d+).(\d+).(\d+).(\d+)/', $bucket)) {
            /**
             * @see Zend_Service_Amazon_S3_Exception
             */
            require_once 'Zend/Service/Amazon/S3/Exception.php';
            throw new Zend_Service_Amazon_S3_Exception("Bucket name \"$bucket\" cannot be an IP address");
        }

        $response = $this->_makeRequest('PUT', $bucket);

        return ($response->getStatus() == 200);
    }

    /**
     * Checks if a given bucket name is available
     *
     * @param  string $bucket
     * @return boolean
     */
    public function isBucketAvailable($bucket)
    {
        $response = $this->_makeRequest('HEAD', $bucket, array('max-keys'=>0));

        return ($response->getStatus() != 404);
    }

    /**
     * Checks if a given object exists
     *
     * @param  string $object
     * @return boolean
     */
    public function isObjectAvailable($object)
    {
        $response = $this->_makeRequest('HEAD', $object);

        return ($response->getStatus() == 200);
    }

    /**
     * Remove a given bucket. All objects in the bucket must be removed prior
     * to removing the bucket.
     *
     * @param  string $bucket
     * @return boolean
     */
    public function removeBucket($bucket)
    {
        $response = $this->_makeRequest('DELETE', $bucket);

        // Look for a 204 No Content response
        return ($response->getStatus() == 204);
    }

    /**
     * Get metadata information for a given object
     *
     * @param  string $object
     * @return array|false
     */
    public function getInfo($object)
    {
        $info = array();

        $object = $this->_fixupObjectName($object);
        $response = $this->_makeRequest('HEAD', $object);

        if ($response->getStatus() == 200) {
            $info['type'] = $response->getHeader('Content-type');
            $info['size'] = $response->getHeader('Content-length');
            $info['mtime'] = strtotime($response->getHeader('Last-modified'));
            $info['etag'] = $response->getHeader('ETag');
        }
        else {
            return false;
        }

        return $info;
    }

    /**
     * List the S3 buckets
     *
     * @return array|false
     */
    public function getBuckets()
    {
        $response = $this->_makeRequest('GET');

        if ($response->getStatus() != 200) {
            return false;
        }

        $xml = new SimpleXMLElement($response->getBody());

        $buckets = array();
        foreach ($xml->Buckets->Bucket as $bucket) {
            $buckets[] = (string)$bucket->Name;
        }

        return $buckets;
    }

    /**
     * Remove all objects in the bucket.
     *
     * @param string $bucket
     * @return boolean
     */
    public function cleanBucket($bucket)
    {
        $objects = $this->getObjectsByBucket($bucket);
        if (!$objects) {
            return false;
        }

        foreach ($objects as $object) {
            $this->removeObject("$bucket/$object");
        }
        return true;
    }

    /**
     * List the objects in a bucket.
     *
     * Provides the list of object keys that are contained in the bucket.
     *
     * @param  string $bucket
     * @return array|false
     */
    public function getObjectsByBucket($bucket)
    {
        $response = $this->_makeRequest('GET', $bucket);

        if ($response->getStatus() != 200) {
            return false;
        }

        $xml = new SimpleXMLElement($response->getBody());

        $objects = array();
        if (isset($xml->Contents)) {
            foreach ($xml->Contents as $contents) {
                foreach ($contents->Key as $object) {
                    $objects[] = (string)$object;
                }
            }
        }

        return $objects;
    }

    protected function _fixupObjectName($object)
    {
        $nameparts = explode('/', $object, 2);

        if (preg_match('/[^a-z0-9\._-]/', $nameparts[0])) {
            /**
             * @see Zend_Service_Amazon_S3_Exception
             */
            require_once 'Zend/Service/Amazon/S3/Exception.php';
            throw new Zend_Service_Amazon_S3_Exception("Bucket name contains invalid characters");
        }

        if(empty($nameparts[1])) {
            return $object;
        }

        return $nameparts[0].'/'.urlencode($nameparts[1]);
    }

    /**
     * Get an object
     *
     * @param  string $object
     * @return string|false
     */
    public function getObject($object)
    {
        $object = $this->_fixupObjectName($object);
        $response = $this->_makeRequest('GET', $object);

        if ($response->getStatus() != 200) {
            return false;
        }

        return $response->getBody();
    }

    /**
     * Upload an object by a PHP string
     *
     * @param  string $object Object name
     * @param  string $data   Object data
     * @param  array  $meta   Metadata
     * @return boolean
     */
    public function putObject($object, $data, $meta=null)
    {
        $object = $this->_fixupObjectName($object);
        $headers = (is_array($meta)) ? $meta : array();

        $headers['Content-MD5'] = base64_encode(md5($data, true));
        $headers['Expect'] = '100-continue';

        if (!isset($headers[self::S3_CONTENT_TYPE_HEADER])) {
            $headers[self::S3_CONTENT_TYPE_HEADER] = self::getMimeType($object);
        }

        $response = $this->_makeRequest('PUT', $object, null, $headers, $data);

        // Check the MD5 Etag returned by S3 against and MD5 of the buffer
        if ($response->getStatus() == 200) {
            // It is escaped by double quotes for some reason
            $etag = str_replace('"', '', $response->getHeader('Etag'));

            if ($etag == md5($data)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Put file to S3 as object
     *
     * @param string $path   File name
     * @param string $object Object name
     * @param array  $meta   Metadata
     * @return boolean
     */
    public function putFile($path, $object, $meta=null)
    {
        $object = $this->_fixupObjectName($object);
        $data = @file_get_contents($path);
        if ($data === false) {
            /**
             * @see Zend_Service_Amazon_S3_Exception
             */
            require_once 'Zend/Service/Amazon/S3/Exception.php';
            throw new Zend_Service_Amazon_S3_Exception("Cannot read file $path");
        }

        if (!is_array($meta)) {
            $meta = array();
        }

        if (!isset($meta[self::S3_CONTENT_TYPE_HEADER])) {
           $meta[self::S3_CONTENT_TYPE_HEADER] = self::getMimeType($path);
        }

        return $this->putObject($object, $data, $meta);
    }

    /**
     * Remove a given object
     *
     * @param  string $object
     * @return boolean
     */
    public function removeObject($object)
    {
        $object = $this->_fixupObjectName($object);
        $response = $this->_makeRequest('DELETE', $object);

        // Look for a 204 No Content response
        return ($response->getStatus() == 204);
    }

    /**
     * Make a request to Amazon S3
     *
     * TODO: support bucket.s3.amazon.com style
     *
     * @param  string $method
     * @param  string $path
     * @param  array  $params
     * @param  array  $headers
     * @param  string $data
     * @return Zend_Http_Response
     */
    public function _makeRequest($method, $path='', $params=null, $headers=array(), $data=null)
    {
        $retry_count = 0;

        if (!is_array($headers)) {
            $headers = array($headers);
        }

        $headers['Date'] = gmdate(DATE_RFC1123, time());

        self::addSignature($method, $path, $headers);

        $client = self::getHttpClient();

        $client->resetParameters();
        // Work around buglet in HTTP client - it doesn't clean headers
        // Remove when ZHC is fixed
        $client->setHeaders(array('Content-MD5' => null,
                                  'Expect'      => null,
                                  'Range'       => null,
                                  'x-amz-acl'   => null));

        // build the end point out
        $parts = explode('/', $path);
        if(count($parts) > 1) {
            $endpoint = 'http://' . $parts[0] . '.' . self::S3_ENDPOINT;
            array_shift($parts);
            $client->setUri($endpoint . '/' . implode('/', $parts));
        } else {
            $client->setUri('http://' . self::S3_ENDPOINT.'/'.$path);
        }
        $client->setHeaders($headers);

        if (is_array($params)) {
            foreach ($params as $name=>$value) {
                $client->setParameterGet($name, $value);
            }
         }

         if (($method == 'PUT') && ($data !== null)) {
             if (!isset($headers['Content-type'])) {
                 $headers['Content-type'] = self::getMimeType($path);
             }
             $client->setRawData($data, $headers['Content-type']);
         }

         do {
            $retry = false;

            $response = $client->request($method);
            $response_code = $response->getStatus();

            // Some 5xx errors are expected, so retry automatically
            if ($response_code >= 500 && $response_code < 600 && $retry_count <= 5) {
                $retry = true;
                $retry_count++;
                sleep($retry_count / 4 * $retry_count);
            }
            else if ($response_code == 307) {
                // Need to redirect, new S3 endpoint given
                // This should never happen as Zend_Http_Client will redirect automatically
            }
            else if ($response_code == 100) {
                echo 'OK to Continue';
            }
        } while ($retry);

        return $response;
    }

    /**
     * Add the S3 Authorization signature to the request headers
     *
     * @param  string $method
     * @param  string $pathhttp://
     * @param  array &$headers
     * @return string
     */
    protected function addSignature($method, $path, &$headers)
    {
        if (!is_array($headers)) {
            $headers = array($headers);
        }

        $type = $md5 = $date = '';

        // Search for the Content-type, Content-MD5 and Date headers
        foreach ($headers as $key=>$val) {
            if (strcasecmp($key, 'content-type') == 0) {
                $type = $val;
            }
            else if (strcasecmp($key, 'content-md5') == 0) {
                $md5 = $val;
            }
            else if (strcasecmp($key, 'date') == 0) {
                $date = $val;
            }
        }

        // If we have an x-amz-date header, use that instead of the normal Date
        if (isset($headers['x-amz-date']) && isset($date)) {
            $date = '';
        }

        $sig_str = "$method\n$md5\n$type\n$date\n";
        // For x-amz- headers, combine like keys, lowercase them, sort them
        // alphabetically and remove excess spaces around values
        $amz_headers = array();
        foreach ($headers as $key=>$val) {
            $key = strtolower($key);
            if (substr($key, 0, 6) == 'x-amz-') {
                if (is_array($val)) {
                    $amz_headers[$key] = $val;
                }
                else {
                    $amz_headers[$key][] = preg_replace('/\s+/', ' ', $val);
                }
            }
        }
        if (!empty($amz_headers)) {
            ksort($amz_headers);
            foreach ($amz_headers as $key=>$val) {
                $sig_str .= $key.':'.implode(',', $val)."\n";
            }
        }

        $sig_str .= '/'.parse_url($path, PHP_URL_PATH);
        if (strpos($path, '?location') !== false) {
            $sig_str .= '?location';
        }
        else if (strpos($path, '?acl') !== false) {
            $sig_str .= '?acl';
        }
        else if (strpos($path, '?torrent') !== false) {
            $sig_str .= '?torrent';
        }

        $signature = base64_encode(Zend_Crypt_Hmac::compute($this->_getSecretKey(), 'sha1', utf8_encode($sig_str), Zend_Crypt_Hmac::BINARY));
        $headers['Authorization'] = 'AWS '.$this->_getAccessKey().':'.$signature;

        return $sig_str;
    }

    /**
     * Attempt to get the content-type of a file based on the extension
     *
     * TODO: move this to Zend_Mime
     *
     * @param  string $path
     * @return string
     */
    public static function getMimeType($path)
    {
        $ext = substr(strrchr($path, '.'), 1);

        if(!$ext) {
            // shortcut
            return 'binary/octet-stream';
        }

        switch ($ext) {
            case 'xls':
                $content_type = 'application/excel';
                break;
            case 'hqx':
                $content_type = 'application/macbinhex40';
                break;
            case 'doc':
            case 'dot':
            case 'wrd':
                $content_type = 'application/msword';
                break;
            case 'pdf':
                $content_type = 'application/pdf';
                break;
            case 'pgp':
                $content_type = 'application/pgp';
                break;
            case 'ps':
            case 'eps':
            case 'ai':
                $content_type = 'application/postscript';
                break;
            case 'ppt':
                $content_type = 'application/powerpoint';
                break;
            case 'rtf':
                $content_type = 'application/rtf';
                break;
            case 'tgz':
            case 'gtar':
                $content_type = 'application/x-gtar';
                break;
            case 'gz':
                $content_type = 'application/x-gzip';
                break;
            case 'php':
            case 'php3':
            case 'php4':
                $content_type = 'application/x-httpd-php';
                break;
            case 'js':
                $content_type = 'application/x-javascript';
                break;
            case 'ppd':
            case 'psd':
                $content_type = 'application/x-photoshop';
                break;
            case 'swf':
            case 'swc':
            case 'rf':
                $content_type = 'application/x-shockwave-flash';
                break;
            case 'tar':
                $content_type = 'application/x-tar';
                break;
            case 'zip':
                $content_type = 'application/zip';
                break;
            case 'mid':
            case 'midi':
            case 'kar':
                $content_type = 'audio/midi';
                break;
            case 'mp2':
            case 'mp3':
            case 'mpga':
                $content_type = 'audio/mpeg';
                break;
            case 'ra':
                $content_type = 'audio/x-realaudio';
                break;
            case 'wav':
                $content_type = 'audio/wav';
                break;
            case 'bmp':
                $content_type = 'image/bitmap';
                break;
            case 'gif':
                $content_type = 'image/gif';
                break;
            case 'iff':
                $content_type = 'image/iff';
                break;
            case 'jb2':
                $content_type = 'image/jb2';
                break;
            case 'jpg':
            case 'jpe':
            case 'jpeg':
                $content_type = 'image/jpeg';
                break;
            case 'jpx':
                $content_type = 'image/jpx';
                break;
            case 'png':
                $content_type = 'image/png';
                break;
            case 'tif':
            case 'tiff':
                $content_type = 'image/tiff';
                break;
            case 'wbmp':
                $content_type = 'image/vnd.wap.wbmp';
                break;
            case 'xbm':
                $content_type = 'image/xbm';
                break;
            case 'css':
                $content_type = 'text/css';
                break;
            case 'txt':
                $content_type = 'text/plain';
                break;
            case 'htm':
            case 'html':
                $content_type = 'text/html';
                break;
            case 'xml':
                $content_type = 'text/xml';
                break;
            case 'xsl':
                $content_type = 'text/xsl';
                break;
            case 'mpg':
            case 'mpe':
            case 'mpeg':
                $content_type = 'video/mpeg';
                break;
            case 'qt':
            case 'mov':
                $content_type = 'video/quicktime';
                break;
            case 'avi':
                $content_type = 'video/x-ms-video';
                break;
            case 'eml':
                $content_type = 'message/rfc822';
                break;
            default:
                $content_type = 'binary/octet-stream';
                break;
        }

        return $content_type;
    }

    /**
     * Register this object as stream wrapper client
     *
     * @param  string $name
     * @return Zend_Service_Amazon_S3
     */
    public function registerAsClient($name)
    {
        self::$_wrapperClients[$name] = $this;
        return $this;
    }

    /**
     * Unregister this object as stream wrapper client
     *
     * @param  string $name
     * @return Zend_Service_Amazon_S3
     */
    public function unregisterAsClient($name)
    {
        unset(self::$_wrapperClients[$name]);
        return $this;
    }

    /**
     * Get wrapper client for stream type
     *
     * @param  string $name
     * @return Zend_Service_Amazon_S3
     */
    public static function getWrapperClient($name)
    {
        return self::$_wrapperClients[$name];
    }

    /**
     * Register this object as stream wrapper
     *
     * @param  string $name
     * @return Zend_Service_Amazon_S3
     */
    public function registerStreamWrapper($name='s3')
    {
        /**
         * @see Zend_Service_Amazon_S3_Stream
         */
        require_once 'Zend/Service/Amazon/S3/Stream.php';

        stream_register_wrapper($name, 'Zend_Service_Amazon_S3_Stream');
        $this->registerAsClient($name);
    }

    /**
     * Unregister this object as stream wrapper
     *
     * @param  string $name
     * @return Zend_Service_Amazon_S3
     */
    public function unregisterStreamWrapper($name='s3')
    {
        stream_wrapper_unregister($name);
        $this->unregisterAsClient($name);
    }
}
