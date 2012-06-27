<?php

namespace Zend\Db\Adapter\Driver\Pgsql;

use Zend\Db\Adapter\Driver\StatementInterface,
    Zend\Db\Adapter\ParameterContainer,
    Zend\Db\Adapter\Exception;

class Statement implements StatementInterface
{

    protected static $statementIndex = 0;

    protected $statementName = '';

    /**
     * @var Pgsql
     */
    protected $driver = null;

    protected $pgsql = null;

    protected $resource = null;

    protected $sql;

    /**
     * @var ParameterContainer
     */
    protected $parameterContainer;

    public function setDriver(Pgsql $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    public function initialize($pgsql)
    {
        if (!is_resource($pgsql) || get_resource_type($pgsql) !== 'pgsql link') {
            die('what is this?' . get_resource_type($pgsql));
        }
        $this->pgsql = $pgsql;
    }

    /**
     * @return resource
     */
    public function getResource()
    {
        // TODO: Implement getResource() method.
    }

    /**
     * @param string $sql
     */
    public function setSql($sql)
    {
        $this->sql = $sql;
    }

    /**
     * @return string
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * @param ParameterContainer $parameterContainer
     */
    public function setParameterContainer(ParameterContainer $parameterContainer)
    {
        $this->parameterContainer = $parameterContainer;
    }

    /**
     * @return ParameterContainer
     */
    public function getParameterContainer()
    {
        return $this->parameterContainer;
    }

    /**
     * @param string $sql
     */
    public function prepare($sql = null)
    {
        $sql = ($sql) ?: $this->sql;

        $pCount = 1;
        $sql = preg_replace_callback(
            '#\$\##', function ($foo) use (&$pCount) {
                return '$' . $pCount++;
            },
            $sql
        );

        $this->sql = $sql;
        $this->statementName = 'statement' . ++self::$statementIndex;
        $this->resource = pg_prepare($this->pgsql, $this->statementName, $sql);
    }

    /**
     * @return bool
     */
    public function isPrepared()
    {
        return isset($this->resource);
    }

    /**
     * @param null $parameters
     * @return ResultInterface
     */
    public function execute($parameters = null)
    {
        if (!$this->isPrepared()) {
            $this->prepare();
        }

        /** START Standard ParameterContainer Merging Block */
        if (!$this->parameterContainer instanceof ParameterContainer) {
            if ($parameters instanceof ParameterContainer) {
                $this->parameterContainer = $parameters;
                $parameters = null;
            } else {
                $this->parameterContainer = new ParameterContainer();
            }
        }

        if (is_array($parameters)) {
            $this->parameterContainer->setFromArray($parameters);
        }

        if ($this->parameterContainer->count() > 0) {
            $parameters = $this->parameterContainer->getPositionalArray();
        }
        /** END Standard ParameterContainer Merging Block */

        $resultResource = pg_execute($this->pgsql, $this->statementName, $parameters);

        if ($resultResource === false) {
            throw new Exception\InvalidQueryException(pg_last_error());
        }

        $result = $this->driver->createResult($resultResource);
        return $result;
    }

}
