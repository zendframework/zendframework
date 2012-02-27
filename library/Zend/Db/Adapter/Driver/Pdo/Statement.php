<?php

namespace Zend\Db\Adapter\Driver\Pdo;

use Zend\Db\Adapter\Driver\StatementInterface,
    Zend\Db\Adapter\ParameterContainerInterface,
    Zend\Db\Adapter\ParameterContainer,
    Zend\Db\Adapter\Exception;

class Statement implements StatementInterface
{

    /**
     * @var \PDO
     */
    protected $pdo = null;

    /**
     * @var Pdo
     */
    protected $driver = null;
    protected $sql = '';
    protected $isQuery = null;
    protected $parameterContainer = null;
    
    /**
     * @var \PDOStatement
     */
    protected $resource = null;

    protected $isPrepared = false;

    public function setDriver(Pdo $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    public function initialize(\PDO $connectionResource)
    {
        $this->pdo = $connectionResource;
        return $this;
    }

    /*
    public function setParameterContainer(ParameterContainer $parameterContainer)
    {
        $this->parameterContainer = $parameterContainer;
    }
    */

    public function setResource(\PDOStatement $pdoStatement)
    {
        $this->resource = $pdoStatement;
        return $this;
    }
    
    public function getResource()
    {
        return $this->resource;
    }


    /**
     * @param string $sql
     */
    public function setSql($sql)
    {
        $this->sql = $sql;
        return $this;
    }

    public function getSql()
    {
        return $this->sql;
    }


    /**
     * @param ParameterContainerInterface $parameterContainer
     */
    public function setParameterContainer(ParameterContainerInterface $parameterContainer)
    {
        $this->parameterContainer = $parameterContainer;
        return $this;
    }

    /**
     * @return ParameterContainerInterface
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
        if ($this->isPrepared) {
            throw new \Exception('This statement has been prepared already');
        }

        if ($sql == null) {
            $sql = $this->sql;
        }

        $this->resource = $this->pdo->prepare($sql);

        if ($this->resource === false) {
            $error = $this->pdo->errorInfo();
            var_dump($error);
            throw new \Exception($error[2]);
        }

        $this->isPrepared = true;
    }

    /**
     * @return bool
     */
    public function isPrepared()
    {
        return $this->isPrepared;
    }


    /**
     * @todo  Should this use the ability of PDOStatement to return objects of a specified class?
     * @param mixed $parameters
     * @return Result
     */
    public function execute($parameters = null)
    {
        if (!$this->isPrepared) {
            $this->prepare();
        }

        $parameters = ($parameters) ?: $parameters = $this->parameterContainer;

        if ($parameters != null) {
            if (is_array($parameters)) {
                $parameters = new ParameterContainer($parameters);
            }
            if (!$parameters instanceof ParameterContainerInterface) {
                throw new \InvalidArgumentException('ParameterContainer expected');
            }
            $this->bindParametersFromContainer($parameters);
        }

        if ($this->resource->execute() === false) {
            throw new Exception\InvalidQueryException($this->resource->error);
        }

        $result = $this->driver->createResult($this->resource);
        return $result;
    }


    protected function bindParametersFromContainer(ParameterContainerInterface $container)
    {
        $parameters = $container->toArray();
        foreach ($parameters as $position => &$value) {
            $type = \PDO::PARAM_STR;
            if ($container->offsetHasErrata($position)) {
                switch ($container->offsetGetErrata($position)) {
                    case ParameterContainerInterface::TYPE_INTEGER:
                        $type = \PDO::PARAM_INT;
                        break;
                    case ParameterContainerInterface::TYPE_NULL:
                        $type = \PDO::PARAM_NULL;
                        break;
                    case ParameterContainerInterface::TYPE_LOB:
                        $type = \PDO::PARAM_LOB;
                        break;
                    case (is_bool($value)):
                        $type = \PDO::PARAM_BOOL;
                        break;
                }
            }

            // position is named or positional, value is reference
            $this->resource->bindParam((is_int($position) ? ($position + 1) : $position), $value, $type);
        }
    }

}
