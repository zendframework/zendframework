<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace Zend\View\Helper;

/**
 * Helper for alternating between set of values
 *
 * @package    Zend_View
 * @subpackage Helper
 */
class Cycle extends AbstractHelper implements \Iterator
{

    /**
     * Default name
     * @var string
     */
    const DEFAULT_NAME = 'default';

    /**
     * Pointers
     *
     * @var array
     */
    protected $_pointers = array(self::DEFAULT_NAME =>-1) ;

    /**
     * Array of values
     *
     * @var array
     */
    protected $_data = array(self::DEFAULT_NAME=>array());

    /**
     * Actual name of cycle
     *
     * @var string
     */
    protected $_name = self::DEFAULT_NAME;

    /**
     * Add elements to alternate
     *
     * @param array $data
     * @param string $name
     * @return \Zend\View\Helper\Cycle
     */
    public function __invoke(array $data = array(), $name = self::DEFAULT_NAME)
    {
        if(!empty($data))
           $this->_data[$name] = $data;

        $this->setName($name);
        return $this;
    }

    /**
     * Add elements to alternate
     *
     * @param array $data
     * @param string $name
     * @return \Zend\View\Helper\Cycle
     */
    public function assign(Array $data , $name = self::DEFAULT_NAME)
    {
        $this->setName($name);
        $this->_data[$name] = $data;
        $this->rewind();
        return $this;
    }

    /**
     * Sets actual name of cycle
     *
     * @param $name
     * @return \Zend\View\Helper\Cycle
     */
    public function setName($name = self::DEFAULT_NAME)
    {
       $this->_name = $name;

       if(!isset($this->_data[$this->_name]))
         $this->_data[$this->_name] = array();

       if(!isset($this->_pointers[$this->_name]))
         $this->rewind();

       return $this;
    }

    /**
     * Gets actual name of cycle
     *
     * @param $name
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }


    /**
     * Return all elements
     *
     * @return array
     */
    public function getAll()
    {
        return $this->_data[$this->_name];
    }

    /**
     * Turn helper into string
     *
     * @return string
     */
    public function toString()
    {
        return (string) $this->_data[$this->_name][$this->key()];
    }

    /**
     * Cast to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Move to next value
     *
     * @return \Zend\View\Helper\Cycle
     */
    public function next()
    {
        $count = count($this->_data[$this->_name]);
        if ($this->_pointers[$this->_name] == ($count - 1))
            $this->_pointers[$this->_name] = 0;
        else
            $this->_pointers[$this->_name] = ++$this->_pointers[$this->_name];
        return $this;
    }

    /**
     * Move to previous value
     *
     * @return \Zend\View\Helper\Cycle
     */
    public function prev()
    {
        $count = count($this->_data[$this->_name]);
        if ($this->_pointers[$this->_name] <= 0)
            $this->_pointers[$this->_name] = $count - 1;
        else
            $this->_pointers[$this->_name] = --$this->_pointers[$this->_name];
        return $this;
    }

    /**
     * Return iteration number
     *
     * @return int
     */
    public function key()
    {
        if ($this->_pointers[$this->_name] < 0)
            return 0;
        else
            return $this->_pointers[$this->_name];
    }

    /**
     * Rewind pointer
     *
     * @return \Zend\View\Helper\Cycle
     */
    public function rewind()
    {
        $this->_pointers[$this->_name] = -1;
        return $this;
    }

    /**
     * Check if element is valid
     *
     * @return bool
     */
    public function valid()
    {
        return isset($this->_data[$this->_name][$this->key()]);
    }

    /**
     * Return  current element
     *
     * @return mixed
     */
    public function current()
    {
        return $this->_data[$this->_name][$this->key()];
    }
}
