<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @package   Zend_Paginator
 */

namespace Zend\Paginator;

use Zend\Paginator\Adapter\AdapterInterface;

/**
 * Interface that aggregates a Zend\Paginator\Adapter\Abstract just like IteratorAggregate does for Iterators.
 *
 * @category   Zend
 * @package    Zend_Paginator
 * @subpackage Adapter
 */
interface AdapterAggregateInterface
{
    /**
     * Return a fully configured Paginator Adapter from this method.
     *
     * @return AdapterInterface
     */
    public function getPaginatorAdapter();
}
