<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Paginator
 */

namespace Zend\Paginator\Adapter;

use Zend\Paginator;

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Iterator implements AdapterInterface
{
    /**
     * Iterator which implements Countable
     *
     * @var Iterator
     */
    protected $_iterator = null;

    /**
     * Item count
     *
     * @var integer
     */
    protected $_count = null;

    /**
     * Constructor.
     *
     * @param  \Iterator $iterator Iterator to paginate
     * @throws \Zend\Paginator\Adapter\Exception\InvalidArgumentException
     */
    public function __construct(\Iterator $iterator)
    {
        if (!$iterator instanceof \Countable) {
            throw new Exception\InvalidArgumentException('Iterator must implement Countable');
        }

        $this->_iterator = $iterator;
        $this->_count = count($iterator);
    }

    /**
     * Returns an iterator of items for a page, or an empty array.
     *
     * @param  integer $offset Page offset
     * @param  integer $itemCountPerPage Number of items per page
     * @return array|\Zend\Paginator\SerializableLimitIterator
     */
    public function getItems($offset, $itemCountPerPage)
    {
        if ($this->_count == 0) {
            return array();
        }
        return new Paginator\SerializableLimitIterator($this->_iterator, $offset, $itemCountPerPage);
    }

    /**
     * Returns the total number of rows in the collection.
     *
     * @return integer
     */
    public function count()
    {
        return $this->_count;
    }
}
