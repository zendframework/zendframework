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
 * @category                       Zend
 * @package                        Zend_Service_WindowsAzure
 * @subpackage                     Storage
 *
 * @property string $Name          Name of the container
 * @property string $Etag          Etag of the container
 * @property string $LastModified  Last modified date of the container
 * @property array  $Metadata      Key/value pairs of meta data
 */
class BlobContainer
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
     * @param string $name          Name
     * @param string $etag          Etag
     * @param string $lastModified  Last modified date
     * @param array  $metadata      Key/value pairs of meta data
     */
    public function __construct($name, $etag, $lastModified, $metadata = array())
    {
        $this->_data = array(
            'name'         => $name,
            'etag'         => $etag,
            'lastmodified' => $lastModified,
            'metadata'     => $metadata
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
