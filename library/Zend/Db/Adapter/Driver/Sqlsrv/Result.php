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

namespace Zend\Db\Adapter\Driver\Sqlsrv;

use Zend\Db\Adapter\Driver\ResultInterface;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Result implements \Iterator, ResultInterface
{
    /**
     * @var resource
     */
    protected $resource = null;
    /**
     * @var boolean 
     */
    protected $currentData = false;
    /**
     *
     * @var boolean
     */
    protected $currentComplete = false;
    /**
     *
     * @var integer
     */
    protected $position = -1;
    /**
     * Initialize
     * 
     * @param  resource $resource
     * @return Result 
     */
    public function initialize($resource)
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
     * Current
     * 
     * @return mixed 
     */
    public function current()
    {
        if ($this->currentComplete) {
            return $this->currentData;
        }
        
        $this->load();
        return $this->currentData;
    }
    /**
     * Next
     * 
     * @return boolean
     */
    public function next()
    {
        $this->load();
        return true;
    }
    /**
     * Load
     * 
     * @param  string $row
     * @return mixed 
     */
    protected function load($row = SQLSRV_SCROLL_NEXT)
    {
        $this->currentData = sqlsrv_fetch_array($this->resource, SQLSRV_FETCH_ASSOC, $row);
        $this->currentComplete = true;
        $this->position++;
        return ($this->currentData);
    }
    /**
     * Key
     * 
     * @return mixed 
     */
    public function key()
    {
        return $this->position;
    }
    /**
     * Rewind
     * 
     * @return boolean 
     */
    public function rewind()
    {
        $this->position = 0;
        $this->load(SQLSRV_SCROLL_FIRST);
        return true;
    }
    /**
     * Valid
     * 
     * @return boolean 
     */
    public function valid()
    {
        if ($this->currentComplete && $this->currentData) {
            return true;
        }

        return $this->load();
    }
    /**
     * Count
     * 
     * @return integer
     */
    public function count()
    {
        return sqlsrv_num_rows($this->resource);
    }
    /**
     * Is query result
     * 
     * @return boolean 
     */
    public function isQueryResult()
    {
        if (is_bool($this->resource)) {
            return false;
        }
        return (sqlsrv_num_fields($this->resource) > 0);
    }
    /**
     * Get affected rows
     * 
     * @return integer 
     */
    public function getAffectedRows()
    {
        return sqlsrv_rows_affected($this->resource);
    }
}