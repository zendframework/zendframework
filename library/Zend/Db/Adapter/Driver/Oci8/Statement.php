<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Adapter\Driver\Oci8;

use Zend\Db\Adapter\Driver\StatementInterface;
use Zend\Db\Adapter\Exception;
use Zend\Db\Adapter\ParameterContainer;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 */
class Statement implements StatementInterface
{

    /**
     * @var resource
     */
    protected $oci8 = null;

    /**
     * @var Oci8
     */
    protected $driver = null;

    /**
     * @var string
     */
    protected $sql = '';

    /**
     * Parameter container
     *
     * @var ParameterContainer
     */
    protected $parameterContainer = null;

    /**
     * @var resource
     */
    protected $resource = null;

    /**
     * Is prepared
     *
     * @var boolean
     */
    protected $isPrepared = false;

    /**
     * @var bool
     */
    protected $bufferResults = false;

    /**
     * Set driver
     *
     * @param  Oci8 $driver
     * @return Statement
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * Initialize
     *
     * @param  resource $oci8
     * @return Statement
     */
    public function initialize($oci8)
    {
        $this->oci8 = $oci8;
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
     * @param ParameterContainer $parameterContainer
     */
    public function setParameterContainer(ParameterContainer $parameterContainer)
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
    public function getSql()
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
            throw new Exception\RuntimeException('This statement has already been prepared');
        }

        $sql = ($sql) ?: $this->sql;

        // get oci8 statement resource
        $this->resource = oci_parse($this->oci8, $sql);

        if (!$this->resource) {
            $e = oci_error($this->oci8);
            throw new Exception\InvalidQueryException(
                'Statement couldn\'t be produced with sql: ' . $sql,
                null,
                new Exception\ErrorException($e['message'], $e['code'])
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
            $this->bindParametersFromContainer();
        }
        /** END Standard ParameterContainer Merging Block */

        $ret = @oci_execute($this->resource);
        if ($ret === false) {
            $e = oci_error($this->resource);
            throw new Exception\RuntimeException($e['message'], $e['code']);
        }

        $result = $this->driver->createResult($this->resource);
        return $result;
    }

    /**
     * Bind parameters from container
     *
     * @param ParameterContainer $pContainer
     */
    protected function bindParametersFromContainer()
    {
        $parameters = $this->parameterContainer->getNamedArray();

        foreach ($parameters as $name => &$value) {
            if ($this->parameterContainer->offsetHasErrata($name)) {
                switch ($this->parameterContainer->offsetGetErrata($name)) {
                    case ParameterContainer::TYPE_DOUBLE:
                        $type = SQLT_LNG;
                        break;
                    case ParameterContainer::TYPE_NULL:
                        $value = null;
                        break;
                    case ParameterContainer::TYPE_INTEGER:
                        $type = SQLT_INT;
                        break;
                    case ParameterContainer::TYPE_STRING:
                    default:
                        $type = SQLT_CHR;
                        break;
                }
            } else {
                $type = SQLT_CHR;
            }
            oci_bind_by_name($this->resource, ':' . $name, $value, -1, $type);
        }
    }

}
