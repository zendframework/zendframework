<?php

namespace ZendTest\Mvc\Controller\TestAsset;

use Zend\Mvc\Controller\AbstractRestfulController;

class RestfulTestController extends AbstractRestfulController
{
    public $entities = array();
    public $entity   = array();

    /**
     * Return list of resources
     * 
     * @return mixed
     */
    public function getList()
    {
        return array('entities' => $this->entities);
    }

    /**
     * Return single resource
     * 
     * @param  mixed $id 
     * @return mixed
     */
    public function get($id)
    {
        return array('entity' => $this->entity);
    }

    /**
     * Create a new resource
     * 
     * @param  mixed $data 
     * @return mixed
     */
    public function create($data)
    {
        return array('entity' => $data);
    }

    /**
     * Update an existing resource
     * 
     * @param  mixed $id 
     * @param  mixed $data 
     * @return mixed
     */
    public function update($id, $data)
    {
        $data['id'] = $id;
        return array('entity' => $data);
    }

    /**
     * Delete an existing resource
     * 
     * @param  mixed $id 
     * @return mixed
     */
    public function delete($id)
    {
        $this->entity = array();
        return array();
    }

    public function editAction()
    {
        return array('content' => __FUNCTION__);
    }

    public function testSomeStrangelySeparatedWordsAction()
    {
        return array('content' => 'Test Some Strangely Separated Words');
    }
}
