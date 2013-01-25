<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Sql\Predicate;

class Like implements PredicateInterface
{

    /**
     * @var string
     */
    protected $specification = '%1$s LIKE %2$s';

    /**
     * @var string
     */
    protected $identifier = '';

    /**
     * @var string
     */
    protected $like = '';

    /**
     * @param string $identifier
     * @param string $like
     */
    public function __construct($identifier = null, $like = null)
    {
        if ($identifier) {
            $this->setIdentifier($identifier);
        }
        if ($like) {
            $this->setLike($like);
        }
    }

    /**
     * @param $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param $like
     */
    public function setLike($like)
    {
        $this->like = $like;
    }

    /**
     * @return string
     */
    public function getLike()
    {
        return $this->like;
    }

    /**
     * @param $specification
     */
    public function setSpecification($specification)
    {
        $this->specification = $specification;
    }

    /**
     * @return string
     */
    public function getSpecification()
    {
        return $this->specification;
    }

    /**
     * @return array
     */
    public function getExpressionData()
    {
        return array(
            array($this->specification, array($this->identifier, $this->like), array(self::TYPE_IDENTIFIER, self::TYPE_VALUE))
        );
    }
}
