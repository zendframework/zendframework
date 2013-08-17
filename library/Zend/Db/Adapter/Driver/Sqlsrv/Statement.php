<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Adapter\Driver\Sqlsrv;

use Zend\Db\Adapter\Driver\StatementInterface;
use Zend\Db\Adapter\Exception;
use Zend\Db\Adapter\ParameterContainer;
use Zend\Db\Adapter\Profiler;

class Statement implements StatementInterface, Profiler\ProfilerAwareInterface
{

    /**
     * @var resource
     */
    protected $sqlsrv = null;

    /**
     * @var Sqlsrv
     */
    protected $driver = null;

    /**
     * @var Profiler\ProfilerInterface
     */
    protected $profiler = null;

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
     * @var ParameterContainer
     */
    protected $parameterContainer = null;

    /**
     * @var resource
     */
    protected $resource = null;

    /**
     *
     * @var bool
     */
    protected $isPrepared = false;

    /**
     *
     * @var array
     */
    protected $options = array();

    /**
     * Set driver
     *
     * @param  Sqlsrv $driver
     * @return Statement
     */
    public function setDriver(Sqlsrv $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * @param Profiler\ProfilerInterface $profiler
     * @return Statement
     */
    public function setProfiler(Profiler\ProfilerInterface $profiler)
    {
        $this->profiler = $profiler;
        return $this;
    }

    /**
     * @return null|Profiler\ProfilerInterface
     */
    public function getProfiler()
    {
        return $this->profiler;
    }

    /**
     *
     * One of two resource types will be provided here:
     * a) "SQL Server Connection" when a prepared statement needs to still be produced
     * b) "SQL Server Statement" when a prepared statement has been already produced
     * (there will need to already be a bound param set if it applies to this query)
     *
     * @param resource $resource
     * @throws Exception\InvalidArgumentException
     * @return Statement
     */
    public function initialize($resource)
    {
        $resourceType = get_resource_type($resource);

        if ($resourceType == 'SQL Server Connection') {
            $this->sqlsrv = $resource;
        } elseif ($resourceType == 'SQL Server Statement') {
            $this->resource = $resource;
            $this->isPrepared = true;
        } else {
            throw new Exception\InvalidArgumentException('Invalid resource provided to ' . __CLASS__);
        }

        return $this;
    }

    /**
     * Set parameter container
     *
     * @param ParameterContainer $parameterContainer
     * @return Statement
     */
    public function setParameterContainer(ParameterContainer $parameterContainer)
    {
        $this->parameterContainer = $parameterContainer;
        return $this;
    }

    /**
     * @return ParameterContainer
     */
    public function getParameterContainer()
    {
        return $this->parameterContainer;
    }

    /**
     * @param $resource
     * @return Statement
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
        return $this;
    }

    /**
     * Get resource
     *
     * @return resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @param string $sql
     * @return Statement
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
     * @param  string $sql
     * @throws Exception\RuntimeException
     * @return Statement
     */
    public function prepare($sql = null)
    {
        if ($this->isPrepared) {
            throw new Exception\RuntimeException('Already prepared');
        }
        $sql = ($sql) ?: $this->sql;

        $pRef = &$this->parameterReferences;

        $this->resource = sqlsrv_prepare($this->sqlsrv, $sql, $pRef, $this->options);

        $this->isPrepared = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPrepared()
    {
        return $this->isPrepared;
    }

    /**
     * Execute
     *
     * @param  array|ParameterContainer $parameters
     * @throws Exception\RuntimeException
     * @return Result
     */
    public function execute($parameters = null)
    {

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
        if (!$this->isPrepared) {
            $this->prepare();
        }

        if ($this->profiler) {
            $this->profiler->profilerStart($this);
        }

        $resultValue = sqlsrv_execute($this->resource);

        if ($this->profiler) {
            $this->profiler->profilerFinish();
        }

        if ($resultValue === false) {
            $errors = sqlsrv_errors();
            // ignore general warnings
            if ($errors[0]['SQLSTATE'] != '01000') {
                throw new Exception\RuntimeException($errors[0]['message']);
            }
        }

        $result = $this->driver->createResult($this->resource);
        return $result;
    }

    /**
     * Bind parameters from container
     *
     */
    protected function bindParametersFromContainer()
    {
        $parameters = $this->parameterContainer->getNamedArray();

        $position = 0;
        foreach ($parameters as $key => &$value) {
            if ($this->parameterContainer->offsetHasErrata($key)) {
                $errata = $this->parameterContainer->offsetGetErrata($key);
                switch ($errata) {
                    case ParameterContainer::TYPE_BINARY:
                        $params = array();
                        $params[] = $value;
                        $params[] = \SQLSRV_PARAM_IN;
                        $params[] = \SQLSRV_PHPTYPE_STREAM(\SQLSRV_ENC_BINARY);
                        $params[] = \SQLSRV_SQLTYPE_VARBINARY('max');
                        $this->parameterReferences[$position++] = $params;
                        break;
                    default:
                            $params = array($value, \SQLSRV_PARAM_IN, null, null);
                            $this->parameterReferences[$position++] = $params;
                        }
            } elseif (is_array($value)) {
                $this->parameterReferences[$position++] = $value;
            } else {
                $params = array($value, \SQLSRV_PARAM_IN, null, null);
                $this->parameterReferences[$position++] = $params;
            }
        }
    }

    public function setQueryTimeout($queryTimeout)
    {
        if (is_int($queryTimeout)) {
            $this->options['QueryTimeout'] = $queryTimeout;
        } else {
            $message = 'Invalid argument provided to ';
            $message.=  __METHOD__ . ' method in class ' . __CLASS__;
            throw new Exception\InvalidArgumentException($message);
        }
    }

    public function setSendStreamParamsAtExec($sendStreamParamsAtExec)
    {
        if (is_bool($sendStreamParamsAtExec)) {
            $this->options['SendStreamParamsAtExec'] = $sendStreamParamsAtExec;
        } else {
            $message = 'Invalid argument provided to ';
            $message.=  __METHOD__ . ' method in class ' . __CLASS__;
            throw new Exception\InvalidArgumentException($message);
        }
    }

    public function setScrollable($scrollable)
    {
        switch ($scrollable) {
            case \SQLSRV_CURSOR_FORWARD:
            case \SQLSRV_CURSOR_STATIC:
            case \SQLSRV_CURSOR_DYNAMIC:
            case \SQLSRV_CURSOR_KEYSET:
                $this->options['Scrollable'] = $scrollable;
                break;
            default:
                $message = 'Invalid argument provided to ';
                $message.=  __METHOD__ . ' method in class ' . __CLASS__;
                throw new Exception\InvalidArgumentException($message);
        }
    }

}
