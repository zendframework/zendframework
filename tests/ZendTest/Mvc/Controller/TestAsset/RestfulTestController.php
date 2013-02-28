<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc\Controller\TestAsset;

use Zend\Mvc\Controller\AbstractRestfulController;

class RestfulTestController extends AbstractRestfulController
{
    public $entities = array();
    public $entity   = array();

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

    /**
     * Delete the collection
     *
     * @return \Zend\Http\Response
     */
    public function deleteList()
    {
        $response = $this->getResponse();
        $response->setStatusCode(204);
        $response->getHeaders()->addHeaderLine('X-Deleted', 'true');
        return $response;
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
     * Return list of resources
     *
     * @return mixed
     */
    public function getList()
    {
        return array('entities' => $this->entities);
    }

    /**
     * Retrieve the headers for a given resource
     *
     * @return void
     */
    public function head($id = null)
    {
        if ($id) {
            $this->getResponse()->getHeaders()->addHeaderLine('X-ZF2-Id', $id);
        }
    }

    /**
     * Return list of allowed HTTP methods
     *
     * @return \Zend\Http\Response
     */
    public function options()
    {
        $response = $this->getResponse();
        $headers  = $response->getHeaders();
        $headers->addHeaderLine('Allow', 'GET, POST, PUT, DELETE, PATCH, HEAD, TRACE');
        return $response;
    }

    /**
     * Patch (partial update) an entity
     *
     * @param  int $id
     * @param  array $data
     * @return array
     */
    public function patch($id, $data)
    {
        $entity     = (array) $this->entity;
        $data['id'] = $id;
        $updated    = array_merge($entity, $data);
        return array('entity' => $updated);
    }

    /**
     * Replace the entire resource collection
     *
     * @param  array|\Traversable $items
     * @return array|\Traversable
     */
    public function replaceList($items)
    {
        return $items;
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

    public function editAction()
    {
        return array('content' => __FUNCTION__);
    }

    public function testSomeStrangelySeparatedWordsAction()
    {
        return array('content' => 'Test Some Strangely Separated Words');
    }

    public function describe()
    {
        return array('description' => __METHOD__);
    }
}
