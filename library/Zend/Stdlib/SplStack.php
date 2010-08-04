<?php
namespace Zend\Stdlib;

class SplStack extends \SplStack
{
    /**
     * @var array Used in serialization
     */
    private $_data = array();

    /**
     * Serialize to an array representing the stack
     * 
     * @return void
     */
    public function toArray()
    {
        $array = array();
        foreach ($this as $item) {
            $array[] = $item;
        }
        return $array;
    }

    /**
     * Serialize
     * 
     * @return array
     */
    public function __sleep()
    {
        $this->_data = $this->toArray();
        return array('_data');
    }

    /**
     * Unserialize
     * 
     * @return void
     */
    public function __wakeup()
    {
        foreach ($this->_data as $item) {
            $this->unshift($item);
        }
        $this->_data = array();
    }
}
