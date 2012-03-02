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

use Zend\Db\Adapter\Driver\ResultInterface,
    Iterator,
    PDO as PDOResource,
    PDOStatement;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @todo Use PDO's native interface for fetching into named objects?
 */
class Result implements Iterator, ResultInterface
{

    const STATEMENT_MODE_SCROLLABLE = 'scrollable';
    const STATEMENT_MODE_FORWARD    = 'forward';

    /**
     *
     * @var string
     */
    protected $statementMode = self::STATEMENT_MODE_FORWARD;

    /**
     * @var \PDOStatement
     */
    protected $resource = null;

    /**
     * @var array Result options
     */
    protected $options;

    /**
     * Is the current complete?
     * @var bool
     */
    protected $currentComplete = false;

    /**
     * Track current item in recordset
     * @var mixed
     */
    protected $currentData;

    /**
     * Current position of scrollable statement
     * @var int
     */
    protected $position = -1;
    
    /**
     * Initialize
     * 
     * @param  PDOStatement $resource
     * @return Result 
     */
    public function initialize(PDOStatement $resource)
    {
        $this->resource = $resource;
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
     * @todo Should we allow passing configuration flags to the fetch() call?
     */
    public function current()
    {
        if ($this->currentComplete) {
            return $this->currentData;
        }

        $this->currentData = $this->resource->fetch(\PDO::FETCH_ASSOC);
        return $this->currentData;
    }
    /**
     * Next
     * 
     * @return mixed 
     */
    public function next()
    {
        $this->currentData = $this->resource->fetch(\PDO::FETCH_ASSOC);
        $this->currentComplete = true;
        $this->position++;
        return $this->currentData;
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
     * @return void
     */
    public function rewind()
    {
        if ($this->statementMode == self::STATEMENT_MODE_FORWARD && $this->position > 0) {
            throw new \Exception('This result is a forward only result set, calling rewind() after moving forward is not supported');
        }
        $this->currentData = $this->resource->fetch(\PDO::FETCH_ASSOC);
        $this->currentComplete = true;
        $this->position = 0;
    }
    /**
     * Valid
     * 
     * @return boolean
     */
    public function valid()
    {
        return ($this->currentData != false);
    }
    /**
     * Count
     * 
     * @return integer 
     */
    public function count()
    {
        return $this->resource->rowCount();
    }
    /**
     * Is query result
     * 
     * @return boolean 
     */
    public function isQueryResult()
    {
        return ($this->resource->columnCount() > 0);
    }
    /**
     * Get affected rows
     * 
     * @return integer 
     */
    public function getAffectedRows()
    {
        return $this->resource->rowCount();
    }
}
