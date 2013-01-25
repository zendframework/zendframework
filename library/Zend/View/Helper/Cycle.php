<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
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
    protected $pointers = array(self::DEFAULT_NAME =>-1);

    /**
     * Array of values
     *
     * @var array
     */
    protected $data = array(self::DEFAULT_NAME=>array());

    /**
     * Actual name of cycle
     *
     * @var string
     */
    protected $name = self::DEFAULT_NAME;

    /**
     * Add elements to alternate
     *
     * @param array $data
     * @param string $name
     * @return \Zend\View\Helper\Cycle
     */
    public function __invoke(array $data = array(), $name = self::DEFAULT_NAME)
    {
        if (!empty($data))
           $this->data[$name] = $data;

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
        $this->data[$name] = $data;
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
       $this->name = $name;

       if (!isset($this->data[$this->name]))
         $this->data[$this->name] = array();

       if (!isset($this->pointers[$this->name]))
         $this->rewind();

       return $this;
    }

    /**
     * Gets actual name of cycle
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Return all elements
     *
     * @return array
     */
    public function getAll()
    {
        return $this->data[$this->name];
    }

    /**
     * Turn helper into string
     *
     * @return string
     */
    public function toString()
    {
        return (string) $this->data[$this->name][$this->key()];
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
        $count = count($this->data[$this->name]);
        if ($this->pointers[$this->name] == ($count - 1))
            $this->pointers[$this->name] = 0;
        else
            $this->pointers[$this->name] = ++$this->pointers[$this->name];
        return $this;
    }

    /**
     * Move to previous value
     *
     * @return \Zend\View\Helper\Cycle
     */
    public function prev()
    {
        $count = count($this->data[$this->name]);
        if ($this->pointers[$this->name] <= 0)
            $this->pointers[$this->name] = $count - 1;
        else
            $this->pointers[$this->name] = --$this->pointers[$this->name];
        return $this;
    }

    /**
     * Return iteration number
     *
     * @return int
     */
    public function key()
    {
        if ($this->pointers[$this->name] < 0)
            return 0;
        else
            return $this->pointers[$this->name];
    }

    /**
     * Rewind pointer
     *
     * @return \Zend\View\Helper\Cycle
     */
    public function rewind()
    {
        $this->pointers[$this->name] = -1;
        return $this;
    }

    /**
     * Check if element is valid
     *
     * @return bool
     */
    public function valid()
    {
        return isset($this->data[$this->name][$this->key()]);
    }

    /**
     * Return  current element
     *
     * @return mixed
     */
    public function current()
    {
        return $this->data[$this->name][$this->key()];
    }
}
