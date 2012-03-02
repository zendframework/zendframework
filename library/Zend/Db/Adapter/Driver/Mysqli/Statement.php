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

namespace Zend\Db\Adapter\Driver\Mysqli;

use Zend\Db\Adapter\Driver\StatementInterface,
    Zend\Db\Adapter\Exception,
    Zend\Db\Adapter\ParameterContainer,
    Zend\Db\Adapter\ParameterContainerInterface;

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
     * @var \mysqli
     */
    protected $mysqli = null;

    /**
     * @var Mysqli
     */
    protected $driver = null;

    /**
     * @var string
     */
    protected $sql = '';

    /**
     * Parameter container
     * 
     * @var ParameterContainerInterface 
     */
    protected $parameterContainer = null;
    
    /**
     * @var \mysqli_stmt
     */
    protected $resource = null;

    /**
     * Is prepared
     * 
     * @var boolean 
     */
    protected $isPrepared = false;

    /**
     * Set driver
     * 
     * @param  Mysqli $driver
     * @return Statement 
     */
    public function setDriver(Mysqli $driver)
    {
        $this->driver = $driver;
        return $this;
    }
    /**
     * Initialize
     * 
     * @param  \mysqli $mysqli
     * @return Statement 
     */
    public function initialize(\mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
        return $this;
    }
    /**
     * Set sql
     * 
     * @param  string $sql
     * @return Statement 
     */
    public function setSql($sql)
    {
        $this->sql = $sql;
        return $this;
    }
    /**
     * Set Parameter container
     * 
     * @param ParameterContainerInterface $parameterContainer 
     */
    public function setParameterContainer(ParameterContainerInterface $parameterContainer)
    {
        $this->parameterContainer = $parameterContainer;
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
     * Set resource
     * 
     * @param  \mysqli_stmt $mysqliStatement
     * @return Statement 
     */
    public function setResource(\mysqli_stmt $mysqliStatement)
    {
        $this->resource = $mysqliStatement;
        $this->isPrepared = true;
        return $this;
    }
    /**
     * Get sql
     * 
     * @return string 
     */
    public function getSQL()
    {
        return $this->sql;
    }

    /**
     * @return ParameterContainer
     */
    public function getParameterContainer()
    {
        return $this->parameterContainer;
    }

    /**
     * @return bool
     */
    public function isPrepared()
    {
        return $this->isPrepared;
    }

    /**
     * @param string $sql
     */
    public function prepare($sql = null)
    {
        if ($this->isPrepared) {
            throw new \Exception('This statement has already been prepared');
        }

        $sql = ($sql) ?: $this->sql;

        $this->resource = $this->mysqli->prepare($this->sql);
        if (!$this->resource instanceof \mysqli_stmt) {
            throw new Exception\InvalidQueryException(
                'Statement couldn\'t be produced with sql: "' . $sql . '"',
                null,
                new ErrorException($this->mysqli->error, $this->mysqli->errno)
            );
        }

        $this->isPrepared = true;
    }

    /**
     * Execute
     * 
     * @param  ParameterContainer $parameters
     * @return mixed 
     */
    public function execute($parameters = null)
    {
        if (!$this->isPrepared) {
            $this->prepare();
        }

        $parameters = ($parameters) ?: $this->parameterContainer;

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
            throw new \RuntimeException($this->resource->error);
        }

        $result = $this->driver->createResult($this->resource);
        return $result;
    }
    /**
     * Bind parameters from container
     * 
     * @param ParameterContainerInterface $pContainer 
     */
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

        if ($args) {
            array_unshift($args, $type);
            call_user_func_array(array($this->resource, 'bind_param'), $args);
        }
    }

}
