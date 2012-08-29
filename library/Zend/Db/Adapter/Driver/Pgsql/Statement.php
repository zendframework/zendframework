<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Adapter\Driver\Pgsql;

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
     * @var int
     */
    protected static $statementIndex = 0;

    /**
     * @var string
     */
    protected $statementName = '';

    /**
     * @var Pgsql
     */
    protected $driver = null;

    /**
     * @var resource
     */
    protected $pgsql = null;

    /**
     * @var resource
     */
    protected $resource = null;

    /**
     * @var string
     */
    protected $sql;

    /**
     * @var ParameterContainer
     */
    protected $parameterContainer;

    /**
     * @param  Pgsql $driver
     * @return Statement
     */
    public function setDriver(Pgsql $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * @param  resource $pgsql
     * @return void
     * @throws Exception\RuntimeException for invalid or missing postgresql connection
     */
    public function initialize($pgsql)
    {
        if (!is_resource($pgsql) || get_resource_type($pgsql) !== 'pgsql link') {
            throw new Exception\RuntimeException(sprintf(
                '%s: Invalid or missing postgresql connection; received "%s"',
                __METHOD__,
                get_resource_type($pgsql)
            ));
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
     * @param  null $parameters
     * @return Result
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

        $resultResource = pg_execute($this->pgsql, $this->statementName, (array) $parameters);

        if ($resultResource === false) {
            throw new Exception\InvalidQueryException(pg_last_error());
        }

        $result = $this->driver->createResult($resultResource);
        return $result;
    }
}
