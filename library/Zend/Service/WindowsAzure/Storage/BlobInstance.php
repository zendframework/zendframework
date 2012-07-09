<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace Zend\Service\WindowsAzure\Storage;

use Zend\Service\WindowsAzure\Exception\UnknownPropertyException;

/**
 * @category                          Zend
 * @package                           Zend_Service_WindowsAzure
 * @subpackage                        Storage
 *
 * @property string  $Container       Container name
 * @property string  $Name            Name
 * @property string  $Etag            Etag
 * @property string  $LastModified    Last modified date
 * @property string  $Url             Url
 * @property int     $Size            Size
 * @property string  $ContentType     Content Type
 * @property string  $ContentEncoding Content Encoding
 * @property string  $ContentLanguage Content Language
 * @property boolean $IsPrefix        Is Prefix?
 * @property array   $Metadata        Key/value pairs of meta data
 */
class BlobInstance
{
    /**
     * Data
     *
     * @var array
     */
    protected $_data = null;

    /**
     * Constructor
     *
     * @param string  $containerName   Container name
     * @param string  $name            Name
     * @param string  $etag            Etag
     * @param string  $lastModified    Last modified date
     * @param string  $url             Url
     * @param int     $size            Size
     * @param string  $contentType     Content Type
     * @param string  $contentEncoding Content Encoding
     * @param string  $contentLanguage Content Language
     * @param boolean $isPrefix        Is Prefix?
     * @param array   $metadata        Key/value pairs of meta data
     */
    public function __construct($containerName, $name, $etag, $lastModified, $url = '', $size = 0, $contentType = '',
                                $contentEncoding = '', $contentLanguage = '', $isPrefix = false, $metadata = array())
    {
        $this->_data = array(
            'container'        => $containerName,
            'name'             => $name,
            'etag'             => $etag,
            'lastmodified'     => $lastModified,
            'url'              => $url,
            'size'             => $size,
            'contenttype'      => $contentType,
            'contentencoding'  => $contentEncoding,
            'contentlanguage'  => $contentLanguage,
            'isprefix'         => $isPrefix,
            'metadata'         => $metadata
        );
    }

    /**
     * Magic overload for setting properties
     *
     * @param string $name     Name of the property
     * @param string $value    Value to set
     * @throws UnknownPropertyException
     * @return
     */
    public function __set($name, $value)
    {
        if (array_key_exists(strtolower($name), $this->_data)) {
            $this->_data[strtolower($name)] = $value;
            return;
        }

        throw new UnknownPropertyException('Unknown property: ' . $name);
    }

    /**
     * Magic overload for getting properties
     *
     * @param string $name     Name of the property
     * @throws UnknownPropertyException
     * @return
     */
    public function __get($name)
    {
        if (array_key_exists(strtolower($name), $this->_data)) {
            return $this->_data[strtolower($name)];
        }

        throw new UnknownPropertyException('Unknown property: ' . $name);
    }
}
