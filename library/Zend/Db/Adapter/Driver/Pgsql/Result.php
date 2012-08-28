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

use Zend\Db\Adapter\Driver\ResultInterface;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 */
class Result implements ResultInterface
{

    /**
     * @var resource
     */
    protected $resource = null;

    protected $position = 0;

    protected $count = 0;

    protected $generatedValue = null;

    public function initialize($resource, $generatedValue)
    {
        if (!is_resource($resource) || get_resource_type($resource) != 'pgsql result') {
            throw new \Exception('Resource not of the correct type.');
        }

        $this->resource = $resource;
        $this->count = pg_num_rows($this->resource);
        $this->generatedValue = $generatedValue;
    }

    public function current()
    {
        if ($this->count === 0) {
            return false;
        }
        return pg_fetch_assoc($this->resource, $this->position);
    }

    public function next()
    {
        $this->position++;
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        return ($this->position < $this->count);
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function buffer()
    {
        return null;
    }

    public function isBuffered()
    {
        return false;
    }

    public function isQueryResult()
    {
        return (pg_num_fields($this->resource) > 0);
    }

    public function getAffectedRows()
    {
        return pg_affected_rows($this->resource);
    }

    public function getGeneratedValue()
    {
        return $this->generatedValue;
    }

    public function getResource()
    {
        // TODO: Implement getResource() method.
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return $this->count;
    }

    public function getFieldCount()
    {
        return pg_num_fields($this->resource);
    }
}
