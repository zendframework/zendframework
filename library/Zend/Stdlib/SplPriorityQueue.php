<?php
namespace Zend\Stdlib;

class SplPriorityQueue extends \SplPriorityQueue
{
    /**
     * @var array Used for serialization
     */
    private $_data = array();

    /**
     * Serialize to an array
     *
     * Array will be priority => data pairs
     * 
     * @return array
     */
    public function toArray()
    {
        $this->setExtractFlags(self::EXTR_BOTH);
        $array = array();
        while ($this->valid()) {
            $array[] = $this->current();
            $this->next();
        }
        $this->setExtractFlags(self::EXTR_DATA);

        // Iterating through a priority queue removes items
        foreach ($array as $item) {
            $this->insert($item['data'], $item['priority']);
        }

        // Return only the data
        $return = array();
        foreach ($array as $item) {
            $return[$item['priority']] = $item['data'];
        }

        return $return;
    }

    /**
     * Serialize
     * 
     * @return array
     */
    public function __sleep()
    {
        $this->_data = array();
        $this->setExtractFlags(self::EXTR_BOTH);
        while ($this->valid()) {
            $this->_data[] = $this->current();
            $this->next();
        }
        $this->setExtractFlags(self::EXTR_DATA);

        // Iterating through a priority queue removes items
        foreach ($this->_data as $item) {
            $this->insert($item['data'], $item['priority']);
        }

        return array('_data');
    }

    /**
     * Deserialize
     * 
     * @return void
     */
    public function __wakeup()
    {
        foreach ($this->_data as $item) {
            $this->insert($item['data'], $item['priority']);
        }
        $this->_data = array();
    }
}
