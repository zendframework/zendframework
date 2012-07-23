<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace Zend\Service\AgileZen\Resources;

use Zend\Service\AgileZen\AbstractEntity;
use Zend\Service\AgileZen\AgileZen;

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AgileZen_Resources
 */
class Attachment extends AbstractEntity
{
    /**
     * File name
     *
     * @var string
     */
    protected $fileName;

    /**
     * Size
     *
     * @var integer
     */
    protected $size;

    /**
     * Content type
     *
     * @var string
     */
    protected $contentType;

    /**
     * Token
     *
     * @var string
     */
    protected $token;

    /**
     * Service
     *
     * @var AgileZen
     */
    protected $service;

    /**
     * Project Id
     *
     * @var integer
     */
    protected $projectId;

    /**
     * Constructor
     *
     * @param AgileZen $service
     * @param array $data
     */
    public function __construct(AgileZen $service, array $data)
    {
        if (!array_key_exists('id', $data)) {
             throw new Exception\InvalidArgumentException("You must pass the id of the attachment");
        }

        $this->fileName    = $data['fileName'];
        $this->size        = $data['sizeInBytes'];
        $this->contentType = $data['contentType'];
        $this->token       = $data['token'];
        $this->projectId   = $data['projectId'];
        $this->service     = $service;

        parent::__construct($data['id']);
    }

    /**
     * Get file name
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Get size
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Get content type
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Get the project's Id
     *
     * @return integer
     */
    public function getProjectId()
    {
        return $this->projectId;
    }
}
