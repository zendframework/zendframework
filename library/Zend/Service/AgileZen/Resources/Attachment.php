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
 * @subpackage AgileZen_Resources
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\AgileZen\Resources;

use Zend\Service\AgileZen\AgileZen,
    Zend\Service\AgileZen\Entity;

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AgileZen_Resources
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Attachment extends Entity
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
