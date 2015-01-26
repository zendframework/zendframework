<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Paginator;

use OuterIterator;

/**
 * Class allowing for the continuous iteration of a Zend\Paginator\Paginator instance.
 * Useful for representing remote paginated data sources as a single Iterator
 */
class PaginatorIterator implements OuterIterator
{
    /**
     * Internal Paginator for iteration
     *
     * @var Paginator $paginator
     */
    protected $paginator;

    /**
     * Value for valid method
     *
     * @var bool $valid
     */
    protected $valid = true;

    public function __construct(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->getInnerIterator()->current();
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $innerIterator = $this->getInnerIterator();
        $innerIterator->next();

        if ($innerIterator->valid()) {
            return;
        }

        $page = $this->paginator->getCurrentPageNumber();
        $nextPage = $page + 1;
        $this->paginator->setCurrentPageNumber($nextPage);

        $page = $this->paginator->getCurrentPageNumber();
        if ($page !== $nextPage) {
            $this->valid = false;
        }
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        $innerKey = $this->getInnerIterator()->key();
        $innerKey += 1; //Zend\Paginator\Paginator normalizes 0 to 1

        $page = $this->paginator->getCurrentPageNumber();
        return ($this->paginator->getAbsoluteItemNumber(
            $innerKey,
            $this->paginator->getCurrentPageNumber()
        )) - 1;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        if (count($this->paginator) < 1) {
            $this->valid = false;
        }
        return $this->valid;
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->paginator->setCurrentPageNumber(1);
        $this->valid = true;
    }

    /**
     * Returns the inner iterator for the current entry.
     * @link http://php.net/manual/en/outeriterator.getinneriterator.php
     * @return \Iterator The inner iterator for the current entry.
     */
    public function getInnerIterator()
    {
        return $this->paginator->getCurrentItems();
    }
}
