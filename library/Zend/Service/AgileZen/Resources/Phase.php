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
 * @package    Zend\Service\AgileZen
 * @subpackage Resources
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
namespace Zend\Service\AgileZen\Resources;

use Zend\Service\AgileZen\AgileZen,
    Zend\Service\AgileZen\Entity;

/**
 * @category   Zend
 * @package    Zend\Service\AgileZen
 * @subpackage Resources
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Phase extends Entity
{
    /**
     * Name
     * 
     * @var string 
     */
    protected $name;
    /**
     * Description
     * 
     * @var string 
     */
    protected $description;
    /**
     * Index
     * 
     * @var string 
     */
    protected $index;
    /**
     * Service
     * 
     * @var Zend\Service\AgileZen\AgileZen 
     */
    protected $service;
    /**
     * Constructor
     * 
     * @param AgileZen $service
     * @param array $data 
     */
    public function __construct(AgileZen $service,$data)
    {
        if (!($service instanceof AgileZen) || !is_array($data)) {
             throw new Exception\InvalidArgumentException("You must pass a AgileZen object and an array");
        }
        if (!array_key_exists('id', $data)) {
             throw new Exception\InvalidArgumentException("You must pass the id of the phase");
        }
        if (!array_key_exists('name', $data)) {
             throw new Exception\InvalidArgumentException("You must pass the name of the phase");
        }
        
        $this->name = $data['name'];
        $this->description = $data['description'];
        $this->index= $data['index'];
        $this->service= $service;
        
        parent::__construct($data['id']);
    }
    /**
     * Get name
     * 
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Get description
     * 
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * Get index
     * 
     * @return string 
     */
    public function getIndex()
    {
        return $this->index;
    }
}