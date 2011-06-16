<?php
/**
 * @category   Zend
 * @package    Zend\Cloud
 * @subpackage Infrastructure
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * namespace
 */
namespace Zend\Cloud\Infrastructure;

use Zend\Cloud\Infrastructure\Exception;

/**
 * Instance of an infrastructure service
 * 
 * @package    Zend\Cloud
 * @subpackage Infrastructure
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Image 
{
    const IMAGE_ID           = 'imageId';
    const IMAGE_OWNERID      = 'ownerId';
    const IMAGE_NAME         = 'name';
    const IMAGE_DESCRIPTION  = 'description';
    const IMAGE_PLATFORM     = 'platform';
    const IMAGE_ARCHITECTURE = 'architecture';
    const ARCH_32BIT         = 'i386';
    const ARCH_64BIT         = 'x86_64';
    const IMAGE_WINDOWS      = 'windows';
    const IMAGE_LINUX        = 'linux';

    /**
     * Image's attributes
     * 
     * @var array
     */
    protected $attributes= array();

    /**
     * The Image adapter (if exists)
     * 
     * @var object
     */
    protected $adapter;

    /**
     * Required attributes
     * 
     * @var array
     */
    protected $attributeRequired = array(
        self::IMAGE_ID, self::IMAGE_OWNERID,
        self::IMAGE_DESCRIPTION, self::IMAGE_PLATFORM,
        self::IMAGE_ARCHITECTURE, self::IMAGE_NAME,
    );

    /**
     * Constructor
     * 
     * @param array $data
     * @param object $adapter 
     */
    public function __construct($data, $adapter=null) 
    {
        if (empty($data) || !is_array($data)) {
            throw new Exception\InvalidArgumentException('You must pass an array of parameters');
        }

        foreach ($this->attributeRequired as $key) {
            if (empty($data[$key])) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'The param "%s" is a required paramater for class %s',
                    $key, __CLASS__
                ));
            }
        }

        $this->attributes = $data;
        $this->adapter    = $adapter;
    }

    /**
     * Get Attribute with a specific key
     *
     * @param array $data
     * @return misc|boolean
     */
    public function getAttribute($key) 
    {
        if (!empty($this->attributes[$key])) {
            return $this->attributes[$key];
        }
        return false;
    }

    /**
     * Get all the attributes
     * 
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get the image ID
     * 
     * @return string
     */
    public function getId()
    {
        return $this->attributes[self::IMAGE_ID];
    }

    /**
     * Get the Owner ID
     * 
     * @return string
     */
    public function getOwnerId()
    {
        return $this->attributes[self::IMAGE_OWNERID];
    }

    /**
     * Get the name
     * 
     * @return string 
     */
    public function getName()
    {
        return $this->attributes[self::IMAGE_NAME];
    }

    /**
     * Get the description
     * 
     * @return string 
     */
    public function getDescription()
    {
        return $this->attributes[self::IMAGE_DESCRIPTION];
    }

    /**
     * Get the platform
     * 
     * @return string 
     */
    public function getPlatform()
    {
        return $this->attributes[self::IMAGE_PLATFORM];
    }

    /**
     * Get the architecture
     * 
     * @return string 
     */
    public function getArchitecture()
    {
        return $this->attributes[self::IMAGE_ARCHITECTURE];
    }
}
