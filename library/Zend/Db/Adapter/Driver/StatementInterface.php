<?php

namespace Zend\Db\Adapter\Driver;

use Zend\Db\Adapter\ParameterContainerInterface;

interface StatementInterface
{
    /**
     * @return resource
     */
    public function getResource();

    /**
     * @abstract
     * @param string $sql
     */
    public function setSql($sql);

    /**
     * @abstract
     * @return string
     */
    public function getSql();

    /**
     * @abstract
     * @param ParameterContainerInterface $parameterContainer
     */
    public function setParameterContainer(ParameterContainerInterface $parameterContainer);

    /**
     * @abstract
     * @return ParameterContainerInterface
     */
    public function getParameterContainer();

    /**
     * @abstract
     * @return bool
     */
    // public function isQuery();

    /**
     * @abstract
     * @param string $sql
     */
    public function prepare($sql = null);

    /**
     * @abstract
     * @return bool
     */
    public function isPrepared();

    /**
     * @abstract
     * @param null $parameters
     * @return ResultInterface
     */
    public function execute($parameters = null);
}
