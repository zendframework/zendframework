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
 * @subpackage ResultSet
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Db\ResultSet;

use ArrayIterator,
    ArrayObject,
    Countable,
    Iterator,
    IteratorAggregate;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage ResultSet
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ResultSet implements Countable, Iterator /*, ResultSetInterface */
{
    const TYPE_OBJECT = 'object';
    const TYPE_ARRAY  = 'array';

    /**
     * Allowed return types
     * 
     * @var array
     */
    protected $allowedReturnTypes = array(
        self::TYPE_OBJECT,
        self::TYPE_ARRAY,
    );

    /**
     * @var RowObjectInterface
     */
    protected $rowObjectPrototype = null;

    /**
     * Return type to use when returning an object from the set
     * 
     * @var ResultSet::TYPE_OBJECT|ResultSet::TYPE_ARRAY
     */
    protected $returnType = self::TYPE_OBJECT;

    /**
     * @var null|int
     */
    protected $count;

    /**
     * @var Iterator|IteratorAggregate
     */
    protected $dataSource = null;

    /**
     * @var int
     */
    protected $fieldCount;

    /**
     * Constructor
     * 
     * @param  null|RowObjectInterface $rowObjectPrototype 
     * @return void
     */
    public function __construct(RowObjectInterface $rowObjectPrototype = null)
    {
        $this->setRowObjectPrototype(($rowObjectPrototype) ?: new Row);
    }

    /**
     * Set the row object prototype
     * 
     * @param  RowObjectInterface $rowObjectPrototype 
     * @return ResultSet
     */
    public function setRowObjectPrototype(RowObjectInterface $rowObjectPrototype)
    {
        $this->rowObjectPrototype = $rowObjectPrototype;
        return $this;
    }

    /**
     * Get the row object prototype
     * 
     * @return RowObjectInterface
     */
    public function getRowObjectPrototype()
    {
        return $this->rowObjectPrototype;
    }

    /**
     * Set the return type to use when returning objects from the set
     * 
     * @param  string $returnType 
     * @return ResultSet
     */
    public function setReturnType($returnType)
    {
        if (!in_array($returnType, $this->allowedReturnTypes, true)) {
            throw new Exception\InvalidArgumentException('Invalid return type provided');
        }
        $this->returnType = $returnType;
        return $this;
    }

    /**
     * Get the return type to use when returning objects from the set
     * 
     * @return string
     */
    public function getReturnType()
    {
        return $this->returnType;
    }

    /**
     * Set the data source for the result set
     * 
     * @param  Iterator|IteratorAggregate $dataSource 
     * @return ResultSet
     * @throws Exception\InvalidArgumentException
     */
    public function setDataSource($dataSource)
    {
        if (is_array($dataSource)) {
            $this->count      = count($dataSource);
            $this->dataSource = new ArrayIterator($dataSource);
        } elseif ($dataSource instanceof IteratorAggregate) {
            $this->count      = null;
            $this->dataSource = $dataSource->getIterator();
        } elseif ($dataSource instanceof Iterator) {
            $this->count      = null;
            $this->dataSource = $dataSource;
        } else {
            throw new Exception\InvalidArgumentException('DataSource provided does not implement Iterator nor IteratorAggregate');
        }

        $this->fieldCount = null;
        return $this;
    }

    /**
     * Get the data source used to create the result set
     * 
     * @return null|Iterator
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }

    /**
     * Retrieve count of fields in individual rows of the result set
     * 
     * @return int
     */
    public function getFieldCount()
    {
        if (null !== $this->fieldCount) {
            return $this->fieldCount;
        }

        $dataSource = $this->getDataSource();
        if (null === $dataSource) {
            return 0;
        }

        $dataSource->rewind();
        if (!$dataSource->valid()) {
            $this->fieldCount = 0;
            return 0;
        }

        $row = $dataSource->current();
        if (is_object($row) && $row instanceof Countable) {
            $this->fieldCount = $row->count();
            return $this->fieldCount;
        }

        $row = (array) $row;
        $this->fieldCount = count($row);
        return $this->fieldCount;
    }
    
    /**
     * Iterator: move pointer to next item
     * 
     * @return void
     */
    public function next()
    {
        return $this->dataSource->next();
    }

    /**
     * Iterator: retrieve current key
     * 
     * @return mixed
     */
    public function key()
    {
        return $this->dataSource->key();
    }
    
    /**
     * Iterator: get current item
     * 
     * @return array|RowObjectInterface
     */
    public function current()
    {
        $data = $this->dataSource->current();

        if ($this->returnType === self::TYPE_OBJECT && is_array($data)) {
            $row = clone $this->rowObjectPrototype;
            $row->exchangeArray($data);
            return $row;
        } else {
            return $data;
        }
    }
    
    /**
     * Iterator: is pointer valid?
     * 
     * @return bool
     */
    public function valid()
    {
        return $this->dataSource->valid();
    }

    /**
     * Iterator: rewind
     * 
     * @return void
     */
    public function rewind()
    {
        return $this->dataSource->rewind();
    }

    /**
     * Countable: return count of rows
     * 
     * @return int
     */
    public function count()
    {
        if ($this->count !== null) {
            return $this->count;
        }
        $this->count = count($this->dataSource);
        return $this->count;
    }

    /**
     * Cast result set to array of arrays
     * 
     * @return array
     * @throws Exception\RuntimeException if any row is not castable to an array
     */
    public function toArray()
    {
        $return = array();
        foreach ($this as $row) {
            if (is_array($row)) {
                $return[] = $row;
            } elseif (method_exists($row, 'toArray')) {
                $return[] = $row->toArray();
            } elseif ($row instanceof ArrayObject) {
                $return[] = $row->getArrayCopy();
            } else {
                throw new Exception\RuntimeException('Rows as part of this datasource cannot be cast to an array');
            }
        }
        return $return;
    }



}
