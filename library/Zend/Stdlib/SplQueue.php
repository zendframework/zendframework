<?php
namespace Zend\Stdlib;

class SplQueue extends \SplQueue
{
    /**
     * @var array Used for serialization
     */
    private $_data = array();

    /**
     * Return an array representing the queue
     * 
     * @return array
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
            $this->push($item);
        }
        $this->_data = array();
    }
}
