<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Cache\Storage\Adapter;

class MongoDBOptions extends AdapterOptions
{
    /**
     * connectString
     *
     * @var string
     */
    protected $connectString;

    /**
     * collection
     *
     * @var string
     */
    protected $collection;

    /**
     * database
     *
     * @var string
     */
    protected $database;

    /**
     * setConnectString
     *
     * @param string $connectString
     * @return $this
     */
    public function setConnectString($connectString)
    {
        $this->connectString = $connectString;

        return $this;
    }

    /**
     * getConnectString
     *
     * @return string
     */
    public function getConnectString()
    {
        return $this->connectString;
    }

    /**
     * setCollection
     *
     * @param string $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * getCollection
     *
     * @return string
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * setDatabase
     *
     * @param string $database
     * @return $this
     */
    public function setDatabase($database)
    {
        $this->database = $database;

        return $this;
    }

    /**
     * getDatabase
     *
     * @return string
     */
    public function getDatabase()
    {
        return $this->database;
    }
}
