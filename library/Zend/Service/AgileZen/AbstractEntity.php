<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace Zend\Service\AgileZen;

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AgileZen
 */
abstract class AbstractEntity
{
    /**
     * Id of the entity
     *
     * @var string
     */
    protected $id;

    /**
     * Get the Id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Constructor
     *
     * @param string $id
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($id)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('The id is required for the entity');
        }
        $this->id = $id;
    }
}
