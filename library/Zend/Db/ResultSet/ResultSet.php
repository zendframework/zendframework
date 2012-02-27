<?php

namespace Zend\Db\ResultSet;

use Iterator,
    IteratorAggregate;

class ResultSet implements Iterator /*, ResultSetInterface */
{
    const TYPE_OBJECT = 'object';
    const TYPE_ARRAY  = 'array';

    /**
     * @var RowObjectInterface
     */
    protected $rowObjectPrototype = null;
    protected $returnType = self::TYPE_OBJECT;

    /**
     * @var \Iterator|\IteratorAggregate
     */
    protected $dataSource = null;

    public function __construct(RowObjectInterface $rowObjectPrototype = null)
    {
        $this->setRowObjectPrototype(($rowObjectPrototype) ?: new Row);
    }

    public function setRowObjectPrototype(RowObjectInterface $rowObjectPrototype)
    {
        $this->rowObjectPrototype = $rowObjectPrototype;
        return $this;
    }

    public function getRowObjectPrototype()
    {
        return $this->rowObjectPrototype;
    }

    public function setReturnType($returnType)
    {
        $this->returnType = $returnType;
    }

    public function getReturnType()
    {
        return $this->returnType;
    }

    public function setDataSource($dataSource)
    {
        if ($dataSource instanceof Iterator) {
            $this->dataSource = $dataSource;
        } elseif ($dataSource instanceof IteratorAggregate) {
            $this->dataSource->getIterator();
        } else {
            throw new \Exception('DataSource provided implements proper interface but does not implement \Iterator nor \IteratorAggregate');
        }
    }

    public function getDataSource()
    {
        return $this->dataSource;
    }

    public function getFieldCount()
    {
        // @todo
    }
    
    public function next()
    {
        return $this->dataSource->next();
    }

    public function key()
    {
        return $this->dataSource->key();
    }
    
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
    
    public function valid()
    {
        return $this->dataSource->valid();
    }

    public function rewind()
    {
        return $this->dataSource->rewind();
    }

    public function count()
    {
        return count($this->dataSource);
    }

    public function toArray()
    {
        $return = array();
        foreach ($this as $row) {
            if (is_array($row)) {
                $return[] = $row;
            } elseif (method_exists($row, 'toArray')) {
                $return[] = $row->toArray();
            } elseif ($row instanceof \ArrayObject) {
                $return[] = $row->getArrayCopy();
            } else {
                throw new \RuntimeException('Rows as part of this datasource cannot be cast to an array.');
            }
        }
        return $return;
    }



}
