<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Paginator\Adapter;

use Zend\Stdlib\CallbackHandler;

class Callback implements AdapterInterface
{
    /**
     * Callback to be executed to retrieve the items for a page.
     *
     * @var CallbackHandler
     */
    protected $itemsCallback;

    /**
     * Callback to be executed to retrieve the total number of items.
     *
     * @var CallbackHandler
     */
    protected $countCallback;

    /**
     * Constructs instance.
     *
     * @param CallbackHandler|callable $itemsCallback Callback to be executed to retrieve the items for a page.
     * @param CallbackHandler|callable $countCallback Callback to be executed to retrieve the total number of items.
     */
    public function __construct($itemsCallback, $countCallback)
    {
        if (! $itemsCallback instanceof CallbackHandler) {
            $itemsCallback = new CallbackHandler($itemsCallback);
        }

        if (! $countCallback instanceof CallbackHandler) {
            $countCallback = new CallbackHandler($countCallback);
        }

        $this->itemsCallback = $itemsCallback;
        $this->countCallback = $countCallback;
    }

    /**
     * Returns an array of items for a page.
     *
     * Executes the {$itemsCallback}.
     *
     * @param  int $offset Page offset
     * @param  int $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        return $this->itemsCallback->call(array($offset, $itemCountPerPage));
    }

    /**
     * Returns the total number of items.
     *
     * Executes the {$countCallback}.
     *
     * @return int
     */
    public function count()
    {
        return $this->countCallback->call();
    }
}
