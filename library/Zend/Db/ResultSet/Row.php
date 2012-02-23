<?php

namespace Zend\Db\ResultSet;

class Row implements RowObjectInterface
{
    /*
    const KEY_POSITION = 'position';
    const KEY_NAME     = 'name';
    */
    
    protected $data = null;
    
    public function __construct(array $data = array())
    {
        if ($data) {
            $this->setData($data);
        }
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }
    
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }
    
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }
    
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
        return $this;
    }
    
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
    
    public function count()
    {
        return count($this->data);
    }

}
