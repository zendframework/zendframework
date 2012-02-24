<?php

namespace Zend\Db\Adapter\Driver;

interface StatementInterface
{
    /**
     * @return resource
     */
    public function getResource();
    public function getSQL();
    public function isQuery();
    public function execute($parameters = null);
}
