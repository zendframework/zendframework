<?php

namespace ZendTest\Code\Scanner\TestAsset\MapperExample;

class RepositoryA
{

    protected $mapper = null;

    public function __construct()
    {
    }

    public function setMapper(Mapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function find(/* $entityCriterion */)
    {
        // so something with criterion
        /*
        $data = $mapper->findByCriterion($entityCriterion);
        $entity = new EntityA();
        populate($entity);
        return $entity;
        */
        return new EntityA;
    }

    public function __toString()
    {
        return 'I am a ' . get_class($this) . ' object (hash ' . spl_object_hash($this) . '), using this mapper object ' . PHP_EOL . '    ' . $this->mapper;
    }

}
