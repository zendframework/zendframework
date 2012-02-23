<?php

namespace Zend\Db\Adapter;

interface DriverStatementInterface
{
//    /**
//     * @param string $sql
//     */
//    public function initialize($sql);

//    /**
//     * @param Driver $driver
//     */
//    public function setDriver(Driver $driver);

//    /**
//     * @param resource $resource
//     */
//    public function setResource($resource);

//    /**
//     * @param ParameterContainer\ParameterContainer $parameterContainer
//     */
//    public function setParameterContainer(ParameterContainer\ParameterContainer $parameterContainer);

    /**
     * @return resource
     */
    public function getResource();
    public function getSQL();
    public function isQuery();
    public function execute($parameters = null);
}
