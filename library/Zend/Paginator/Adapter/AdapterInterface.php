<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @package   Zend_Paginator
 */

namespace Zend\Paginator\Adapter;

use Countable;

/**
 * Interface for pagination adapters.
 *
 * @category   Zend
 * @package    Paginator
 */
interface AdapterInterface extends Countable
{
    /**
     * Returns an collection of items for a page.
     *
     * @param  integer $offset Page offset
     * @param  integer $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage);
}
