<?php

namespace Zend\Db\Adapter\Driver\Sqlsrv;

use Zend\Db\Adapter\DriverStatementInterface,
    Zend\Db\Adapter\ParameterContainer,
    Zend\Db\Adapter\ParameterContainerInterface;

class Statement implements DriverStatementInterface
{

    /**
     * @var \Zend\Db\Adapter\DriverInterface
     */
    protected $driver = null;

    /**
     * @var string
     */
    protected $sql = null;

    /**
     * @var bool
     */
    protected $isQuery = null;

    /**
     * @var array
     */
    protected $parameterReferences = array();
    
    /**
     * @var Zend\Db\Adapter\ParameterContainer\ParameterContainer
     */
    protected $parameterContainer = null;
    
    /**
     * @var \Sqlsrv_stmt
     */
    protected $resource = null;

    public function setDriver(\Zend\Db\Adapter\DriverInterface $driver)
    {
        $this->driver = $driver;
        return $this;
    }
    
    /**
     * 
     * One of two resource types will be provided here:
     * a) "SQL Server Connection" when a prepared statement needs to still be produced
     * b) "SQL Server Statement" when a prepared statement has been already produced 
     * (there will need to already be a bound param set if it applies to this query)
     * 
     * @param resource
     */
    public function initialize($resource, $sql)
    {
        $pRef = &$this->parameterReferences;
        for ($position = 0; $position < substr_count($sql, '?'); $position++) {
            $pRef[$position] = array('', SQLSRV_PARAM_IN, null, null);
        }

        $statementResource = sqlsrv_prepare($resource, $sql, $pRef);

        $this->resource = $statementResource;
        $this->sql = $sql;
        if (strpos(ltrim($sql), 'SELECT') === 0) {
            $this->isQuery = true;
        }
        return $this;
    }

    public function setParameterContainer(DriverStatement\ParameterContainer $parameterContainer)
    {
        $this->parameterContainer = $parameterContainer;
    }
    
    public function isQuery()
    {
        return $this->isQuery;
    }
    
    public function getResource()
    {
        return $this->resource;
    }
    
    public function getSQL()
    {
        return $this->sql;
    }
    
    public function execute($parameters = null)
    {
        if ($parameters !== null) {
            if (is_array($parameters)) {
                $parameters = new ParameterContainer($parameters);
            }
            if (!$parameters instanceof ParameterContainerInterface) {
                throw new \InvalidArgumentException('ParameterContainer expected');
            }
            $this->parameterContainer = $parameters;
        }

        if ($this->parameterContainer) {
            $this->bindParametersFromContainer();
        }

        $resultValue = sqlsrv_execute($this->resource);

        if ($resultValue === false) {
            $errors = sqlsrv_errors();
            // ignore general warnings
            if ($errors[0]['SQLSTATE'] != '01000') {
                throw new \RuntimeException($errors[0]['message']);
            }
        }

        $result = $this->driver->createResult($this->resource);
        return $result;
    }
    
    protected function bindParametersFromContainer()
    {
        $values = $this->parameterContainer->toArray();
        $position = 0;
        foreach ($values as $value) {
            $this->parameterReferences[$position++][0] = $value;
        }

        // @todo bind errata
        //foreach ($this->parameterContainer as $name => &$value) {
        //    $p[$position][0] = $value;
        //    $position++;
        //    if ($this->parameterContainer->offsetHasErrata($name)) {
        //        $p[$position][3] = $this->parameterContainer->offsetGetErrata($name);
        //    }
        //}
    }
    
}
