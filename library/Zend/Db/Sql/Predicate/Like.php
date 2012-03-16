<?php

namespace Zend\Db\Sql\Predicate;

class Like implements PredicateInterface
{

    protected $specification = '%1$s LIKE %2$s';
    protected $identifier = '';
    protected $like = '';

    public function __construct($identifier = null, $like = null)
    {
        if ($identifier) {
            $this->setIdentifier($identifier);
        }
        if ($like) {
            $this->setLike($like);
        }
    }

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function setLike($like)
    {
        $this->like = $like;
    }

    public function getLike()
    {
        return $this->like;
    }

    public function setSpecification($specification)
    {
        $this->specification = $specification;
    }

    public function getSpecification()
    {
        return $this->specification;
    }

    public function getWhereParts()
    {
        return array(
            array($this->specification, array($this->identifier, $this->like), array(self::TYPE_IDENTIFIER, self::TYPE_VALUE))
        );
    }
}