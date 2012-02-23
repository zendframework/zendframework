<?php

namespace Zend\Db\Adapter\Driver\Mysqli;

use Zend\Db\Adapter,
    Zend\Db\Adapter\DriverStatementInterface,
    Zend\Db\Adapter\ParameterContainer,
    Zend\Db\Adapter\ParameterContainerInterface;

class Statement implements DriverStatementInterface
{
    /**
     * @var \Zend\Db\Adapter\Driver\Mysqli
     */
    protected $driver = null;
    protected $sql = false;
    protected $isQuery = null;
    protected $parameterContainer = null;
    
    /**
     * @var \mysqli_stmt
     */
    protected $resource = null;
    
    public function setDriver(Adapter\DriverInterface $driver)
    {
        $this->driver = $driver;
        return $this;
    }
    
    public function initialize(\mysqli $mysqli, $sql)
    {
        $this->sql = $sql;
        $this->resource = $mysqli->prepare($sql);
        if (!$this->resource instanceof \mysqli_stmt) {
            throw new \RuntimeException('Statement couldn\'t be produced');
        }
        return $this;
    }
    
    public function setSql($sql)
    {
        $this->sql = $sql;
        if (strpos(ltrim($sql), 'SELECT') === 0) {
            $this->isQuery = true;
        }
        return $this;
    }
    
    public function setParameterContainer(ParameterContainerInterface $parameterContainer)
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
        if ($parameters != null) {
            if (is_array($parameters)) {
                $parameters = new ParameterContainer($parameters);
            }
            if (!$parameters instanceof ParameterContainerInterface) {
                throw new \InvalidArgumentException('ParameterContainer expected');
            }
            $this->bindParametersFromContainer($parameters);
        }
            
        if ($x = $this->resource->execute() === false) {
            throw new \RuntimeException($this->resource->error);
        }

        $result = $this->driver->createResult($this->resource);
        return $result;
    }
    
    protected function bindParametersFromContainer(ParameterContainerInterface $pContainer)
    {
        $parameters = $pContainer->toArray();
        $type = '';
        $args = array();

        foreach ($parameters as $position => &$value) {
            switch ($pContainer->offsetGetErrata($position)) {
                case ParameterContainerInterface::TYPE_DOUBLE:
                    $type .= 'd';
                    break;
                case ParameterContainerInterface::TYPE_NULL:
                    $value = null; // as per @see http://www.php.net/manual/en/mysqli-stmt.bind-param.php#96148
                case ParameterContainerInterface::TYPE_INTEGER:
                    $type .= 'i';
                    break;
                case ParameterContainerInterface::TYPE_STRING:
                default:
                    $type .= 's';
                    break;
            }
            $args[] = &$value;
        }
        array_unshift($args, $type);

        call_user_func_array(array($this->resource, 'bind_param'), $args);
    }
}
