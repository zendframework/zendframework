<?php
namespace Zend\Service\GoGrid;
class Object
{
    private $_attributes= array();

    public function __construct($data=array()) {
        if (!empty($data) && is_array($data)) {
            $this->_attributes= $data;
        } 
    }
    public function getAttribute($id) {
        if (!empty($this->_attributes) && key_exists($id,$this->_attributes)) {
            return $this->_attributes[$id];
        }
        return false;
    }
    public function isSuccessful() {
        return !empty($this->_attribute);
    }
}
