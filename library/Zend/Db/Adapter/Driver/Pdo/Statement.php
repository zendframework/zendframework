<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Db\Adapter\Driver\Pdo;

use Zend\Db\Adapter\Driver\StatementInterface,
    Zend\Db\Adapter\ParameterContainerInterface,
    Zend\Db\Adapter\ParameterContainer,
    Zend\Db\Adapter\Exception;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
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
    /**
     *
     * @var string
     */
    protected $sql = '';
    /**
     *
     * @var boolean 
     */
    protected $isQuery = null;
    /**
     *
     * @var ParameterContainer 
     */
    protected $parameterContainer = null;
    
    /**
     * @var \PDOStatement
     */
    protected $resource = null;

    /**
     *
     * @var boolean
     */
    protected $isPrepared = false;
    /**
     * Set driver
     * 
     * @param  Pdo $driver
     * @return Statement 
     */
    public function setDriver(Pdo $driver)
    {
        $this->driver = $driver;
        return $this;
    }
    /**
     * Initialize
     * 
     * @param  \PDO $connectionResource
     * @return Statement 
     */
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
    /**
     * Set resource
     * 
     * @param  \PDOStatement $pdoStatement
     * @return Statement 
     */
    public function setResource(\PDOStatement $pdoStatement)
    {
        $this->resource = $pdoStatement;
        return $this;
    }
    /**
     * Get resource
     * 
     * @return mixed 
     */
    public function getResource()
    {
        return $this->resource;
    }


    /**
     * Set sql
     * 
     * @param string $sql
     */
    public function setSql($sql)
    {
        $this->sql = $sql;
        return $this;
    }
    /**
     * Get sql
     * 
     * @return string 
     */
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

    /**
     * Bind parameters from container
     * 
     * @param ParameterContainerInterface $container 
     */
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
