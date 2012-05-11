<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Paginator
 */

namespace Zend\Paginator;

use Zend\Paginator\ScrollingStyle\ScrollingStyleInterface,
    Zend\Paginator\Adapter\AdapterInterface,
    ArrayIterator,
    Countable,
    Iterator,
    IteratorAggregate,
    Traversable,
    Zend\Cache\Storage\Adapter\AdapterInterface as CacheAdapter,
    Zend\Db\Table\AbstractRowset as DbAbstractRowset,
    Zend\Db\Table\Select as DbTableSelect,
    Zend\Db\Sql,
    Zend\Filter\FilterInterface,
    Zend\Json\Json,
    Zend\Stdlib\ArrayUtils,
    Zend\View;

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Paginator implements Countable, IteratorAggregate
{
    /**
     * Specifies that the factory should try to detect the proper adapter type first
     *
     * @var string
     */
    const INTERNAL_ADAPTER = 'Zend\Paginator\Adapter\Internal';

    /**
     * The cache tag prefix used to namespace Paginator results in the cache
     *
     */
    const CACHE_TAG_PREFIX = 'Zend_Paginator_';

    /**
     * Adapter broker
     *
     * @var AdapterBroker
     */
    protected static $_adapterBroker = null;

    /**
     * Configuration file
     *
     * @var array|null
     */
    protected static $_config = null;

    /**
     * Default scrolling style
     *
     * @var string
     */
    protected static $_defaultScrollingStyle = 'Sliding';

    /**
     * Default item count per page
     *
     * @var int
     */
    protected static $_defaultItemCountPerPage = 10;

    /**
     * Scrolling style plugin loader
     *
     * @var ScrollingStyleBroker
     */
    protected static $_scrollingStyleBroker = null;

    /**
     * Cache object
     *
     * @var CacheAdapter
     */
    protected static $_cache;

    /**
     * Enable or disable the cache by Zend\Paginator\Paginator instance
     *
     * @var bool
     */
    protected $_cacheEnabled = true;

    /**
     * Adapter
     *
     * @var AdapterInterface
     */
    protected $_adapter = null;

    /**
     * Number of items in the current page
     *
     * @var integer
     */
    protected $_currentItemCount = null;

    /**
     * Current page items
     *
     * @var Traversable
     */
    protected $_currentItems = null;

    /**
     * Current page number (starting from 1)
     *
     * @var integer
     */
    protected $_currentPageNumber = 1;

    /**
     * Result filter
     *
     * @var FilterInterface
     */
    protected $_filter = null;

    /**
     * Number of items per page
     *
     * @var integer
     */
    protected $_itemCountPerPage = null;

    /**
     * Number of pages
     *
     * @var integer
     */
    protected $_pageCount = null;

    /**
     * Number of local pages (i.e., the number of discrete page numbers
     * that will be displayed, including the current page number)
     *
     * @var integer
     */
    protected $_pageRange = 10;

    /**
     * Pages
     *
     * @var array
     */
    protected $_pages = null;

    /**
     * View instance used for self rendering
     *
     * @var \Zend\View\Renderer\RendererInterface
     */
    protected $_view = null;

    /**
     * Factory.
     *
     * @param  mixed  $data
     * @param  string $adapter
     * @param  array  $prefixPaths
     * @throws Exception\InvalidArgumentException
     * @return Paginator
     */
    public static function factory($data, $adapter = self::INTERNAL_ADAPTER,
                                   array $prefixPaths = null)
    {
        if ($data instanceof AdapterAggregateInterface) {
            return new self($data->getPaginatorAdapter());
        }

        if ($adapter == self::INTERNAL_ADAPTER) {
            if (is_array($data)) {
                $adapter = 'array';
            } else if ($data instanceof DbTableSelect) {
                $adapter = 'db_table_select';
            } else if ($data instanceof DbSelect) {
                $adapter = 'db_select';
            } else if ($data instanceof Iterator) {
                $adapter = 'iterator';
            } else if (is_integer($data)) {
                $adapter = 'null';
            } else {
                $type = (is_object($data)) ? get_class($data) : gettype($data);
                throw new Exception\InvalidArgumentException('No adapter for type ' . $type);
            }
        }

        $broker  = self::getAdapterBroker();
        $adapter = $broker->load($adapter, array($data));
        return new self($adapter);
    }

    /**
     * Set the adapter broker
     *
     * @param string|\Zend\Loader\PluginBroker $broker
     * @throws Exception\InvalidArgumentException
     */
    public static function setAdapterBroker($broker)
    {
        if (is_string($broker)) {
            if (!class_exists($broker)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Unable to locate adapter broker of class "%s"',
                    $broker
                ));
            }
            $broker = new $broker();
        }
        if (!$broker instanceof AdapterBroker) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Pagination adapter broker must extend AdapterBroker; received "%s"',
                (is_object($broker) ? get_class($broker) : gettype($broker))
            ));
        }
        self::$_adapterBroker = $broker;
    }

    /**
     * Returns the adapter broker.  If it doesn't exist it's created.
     *
     * @return AdapterBroker
     */
    public static function getAdapterBroker()
    {
        if (self::$_adapterBroker === null) {
            self::setAdapterBroker(new AdapterBroker());
        }

        return self::$_adapterBroker;
    }

    /**
     * Set a global config
     *
     * @param array|\Traversable $config
     * @throws Exception\InvalidArgumentException
     */
    public static function setOptions($config)
    {
        if ($config instanceof Traversable) {
            $config = ArrayUtils::iteratorToArray($config);
        }
        if (!is_array($config)) {
            throw new Exception\InvalidArgumentException(__METHOD__ . ' expects an array or Traversable');
        }

        self::$_config = $config;

        if (isset($config['adapter_broker']) 
            && null !== ($broker = $config['adapter_broker'])
        ) {
            self::setAdapterBroker($broker);
        }

        if (isset($config['scrolling_style_broker']) 
            && null !== ($broker = $config['scrolling_style_broker'])
        ) {
            self::setScrollingStyleBroker($broker);
        }

        $scrollingStyle = isset($config['scrolling_style']) ? $config['scrolling_style'] : null;

        if ($scrollingStyle != null) {
            self::setDefaultScrollingStyle($scrollingStyle);
        }
    }

    /**
     * Returns the default scrolling style.
     *
     * @return  string
     */
    public static function getDefaultScrollingStyle()
    {
        return self::$_defaultScrollingStyle;
    }

    /**
     * Get the default item count per page
     *
     * @return int
     */
    public static function getDefaultItemCountPerPage()
    {
        return self::$_defaultItemCountPerPage;
    }

    /**
     * Set the default item count per page
     *
     * @param int $count
     */
    public static function setDefaultItemCountPerPage($count)
    {
        self::$_defaultItemCountPerPage = (int) $count;
    }

    /**
     * Sets a cache object
     *
     * @param CacheAdapter $cache
     */
    public static function setCache(CacheAdapter $cache)
    {
        self::$_cache = $cache;
    }

    /**
     * Sets the default scrolling style.
     *
     * @param  string $scrollingStyle
     */
    public static function setDefaultScrollingStyle($scrollingStyle = 'Sliding')
    {
        self::$_defaultScrollingStyle = $scrollingStyle;
    }

    public static function setScrollingStyleBroker($broker)
    {
        if (is_string($broker)) {
            if (!class_exists($broker)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Unable to locate scrolling style broker of class "%s"',
                    $broker
                ));
            }
            $broker = new $broker();
        }
        if (!$broker instanceof ScrollingStyleBroker) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Pagination scrolling-style broker must extend ScrollingStyleBroker; received "%s"',
                (is_object($broker) ? get_class($broker) : gettype($broker))
            ));
        }
        self::$_scrollingStyleBroker = $broker;
    }

    /**
     * Returns the scrolling style broker.  If it doesn't exist it's
     * created.
     *
     * @return ScrollingStyleBroker
     */
    public static function getScrollingStyleBroker()
    {
        if (self::$_scrollingStyleBroker === null) {
            self::$_scrollingStyleBroker = new ScrollingStyleBroker();
        }

        return self::$_scrollingStyleBroker;
    }

    /**
     * Constructor.
     *
     * @param AdapterInterface|AdapterAggregateInterface $adapter
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($adapter)
    {
        if ($adapter instanceof AdapterInterface) {
            $this->_adapter = $adapter;
        } else if ($adapter instanceof AdapterAggregateInterface) {
            $this->_adapter = $adapter->getPaginatorAdapter();
        } else {
            throw new Exception\InvalidArgumentException(
                'Zend_Paginator only accepts instances of the type ' .
                'Zend\Paginator\Adapter\AdapterInterface or Zend\Paginator\AdapterAggregateInterface.'
            );
        }

        $config = self::$_config;

        if (!empty($config)) {
            $setupMethods = array('ItemCountPerPage', 'PageRange');

            foreach ($setupMethods as $setupMethod) {
                $key   = strtolower($setupMethod);
                $value = isset($config[$key]) ? $config[$key] : null;

                if ($value != null) {
                    $setupMethod = 'set' . $setupMethod;
                    $this->$setupMethod($value);
                }
            }
        }
    }

    /**
     * Serializes the object as a string.  Proxies to {@link render()}.
     *
     * @return string
     */
    public function __toString()
    {
        try {
            $return = $this->render();
            return $return;
        } catch (\Exception $e) {
            trigger_error($e->getMessage(), E_USER_WARNING);
        }

        return '';
    }

    /**
     * Enables/Disables the cache for this instance
     *
     * @param bool $enable
     * @return Paginator
     */
    public function setCacheEnabled($enable)
    {
        $this->_cacheEnabled = (bool)$enable;
        return $this;
    }

    /**
     * Returns the number of pages.
     *
     * @return integer
     */
    public function count()
    {
        if (!$this->_pageCount) {
            $this->_pageCount = $this->_calculatePageCount();
        }

        return $this->_pageCount;
    }

    /**
     * Returns the total number of items available.
     *
     * @return integer
     */
    public function getTotalItemCount()
    {
        return count($this->getAdapter());
    }

    /**
     * Clear the page item cache.
     *
     * @param int $pageNumber
     * @return Paginator
     */
    public function clearPageItemCache($pageNumber = null)
    {
        if (!$this->_cacheEnabled()) {
            return $this;
        }

        if (null === $pageNumber) {
            self::$_cache->find(CacheAdapter::MATCH_TAGS_OR, array('tags' => array(
                $this->_getCacheInternalId()
            )));
            $cacheIds = array();
            while (($item = self::$_cache->fetch()) !== false) {
                $cacheIds[] = $item['key'];
            }
            foreach ($cacheIds as $id) {
                if (preg_match('|'.self::CACHE_TAG_PREFIX."(\d+)_.*|", $id, $page)) {
                    self::$_cache->removeItem($this->_getCacheId($page[1]));
                }
            }
        } else {
            $cleanId = $this->_getCacheId($pageNumber);
            self::$_cache->removeItem($cleanId);
        }
        return $this;
    }

    /**
     * Returns the absolute item number for the specified item.
     *
     * @param  integer $relativeItemNumber Relative item number
     * @param  integer $pageNumber Page number
     * @return integer
     */
    public function getAbsoluteItemNumber($relativeItemNumber, $pageNumber = null)
    {
        $relativeItemNumber = $this->normalizeItemNumber($relativeItemNumber);

        if ($pageNumber == null) {
            $pageNumber = $this->getCurrentPageNumber();
        }

        $pageNumber = $this->normalizePageNumber($pageNumber);

        return (($pageNumber - 1) * $this->getItemCountPerPage()) + $relativeItemNumber;
    }

    /**
     * Returns the adapter.
     *
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * Returns the number of items for the current page.
     *
     * @return integer
     */
    public function getCurrentItemCount()
    {
        if ($this->_currentItemCount === null) {
            $this->_currentItemCount = $this->getItemCount($this->getCurrentItems());
        }

        return $this->_currentItemCount;
    }

    /**
     * Returns the items for the current page.
     *
     * @return Traversable
     */
    public function getCurrentItems()
    {
        if ($this->_currentItems === null) {
            $this->_currentItems = $this->getItemsByPage($this->getCurrentPageNumber());
        }

        return $this->_currentItems;
    }

    /**
     * Returns the current page number.
     *
     * @return integer
     */
    public function getCurrentPageNumber()
    {
        return $this->normalizePageNumber($this->_currentPageNumber);
    }

    /**
     * Sets the current page number.
     *
     * @param  integer $pageNumber Page number
     * @return Paginator $this
     */
    public function setCurrentPageNumber($pageNumber)
    {
        $this->_currentPageNumber = (integer) $pageNumber;
        $this->_currentItems      = null;
        $this->_currentItemCount  = null;

        return $this;
    }

    /**
     * Get the filter
     *
     * @return FilterInterface
     */
    public function getFilter()
    {
        return $this->_filter;
    }

    /**
     * Set a filter chain
     *
     * @param  FilterInterface $filter
     * @return Paginator
     */
    public function setFilter(FilterInterface $filter)
    {
        $this->_filter = $filter;

        return $this;
    }

    /**
     * Returns an item from a page.  The current page is used if there's no
     * page specified.
     *
     * @param  integer $itemNumber Item number (1 to itemCountPerPage)
     * @param  integer $pageNumber
     * @throws Exception\InvalidArgumentException
     * @return mixed
     */
    public function getItem($itemNumber, $pageNumber = null)
    {
        if ($pageNumber == null) {
            $pageNumber = $this->getCurrentPageNumber();
        } else if ($pageNumber < 0) {
            $pageNumber = ($this->count() + 1) + $pageNumber;
        }

        $page = $this->getItemsByPage($pageNumber);
        $itemCount = $this->getItemCount($page);

        if ($itemCount == 0) {
            throw new Exception\InvalidArgumentException('Page ' . $pageNumber . ' does not exist');
        }

        if ($itemNumber < 0) {
            $itemNumber = ($itemCount + 1) + $itemNumber;
        }

        $itemNumber = $this->normalizeItemNumber($itemNumber);

        if ($itemNumber > $itemCount) {
            throw new Exception\InvalidArgumentException('Page ' . $pageNumber . ' does not'
                                             . ' contain item number ' . $itemNumber);
        }

        return $page[$itemNumber - 1];
    }

    /**
     * Returns the number of items per page.
     *
     * @return integer
     */
    public function getItemCountPerPage()
    {
        if (empty($this->_itemCountPerPage)) {
            $this->_itemCountPerPage = self::getDefaultItemCountPerPage();
        }

        return $this->_itemCountPerPage;
    }

    /**
     * Sets the number of items per page.
     *
     * @param  integer $itemCountPerPage
     * @return Paginator $this
     */
    public function setItemCountPerPage($itemCountPerPage = -1)
    {
        $this->_itemCountPerPage = (integer) $itemCountPerPage;
        if ($this->_itemCountPerPage < 1) {
            $this->_itemCountPerPage = $this->getTotalItemCount();
        }
        $this->_pageCount        = $this->_calculatePageCount();
        $this->_currentItems     = null;
        $this->_currentItemCount = null;

        return $this;
    }

    /**
     * Returns the number of items in a collection.
     *
     * @param  mixed $items Items
     * @return integer
     */
    public function getItemCount($items)
    {
        $itemCount = 0;

        if (is_array($items) || $items instanceof Countable) {
            $itemCount = count($items);
        } elseif($items instanceof Traversable) { // $items is something like LimitIterator
            $itemCount = iterator_count($items);
        }

        return $itemCount;
    }

    /**
     * Returns the items for a given page.
     *
     * @param integer $pageNumber
     * @return mixed
     */
    public function getItemsByPage($pageNumber)
    {
        $pageNumber = $this->normalizePageNumber($pageNumber);

        if ($this->_cacheEnabled()) {
            $data = self::$_cache->getItem($this->_getCacheId($pageNumber));
            if ($data) {
                return $data;
            }
        }

        $offset = ($pageNumber - 1) * $this->getItemCountPerPage();

        $items = $this->_adapter->getItems($offset, $this->getItemCountPerPage());

        $filter = $this->getFilter();

        if ($filter !== null) {
            $items = $filter->filter($items);
        }

        if (!$items instanceof Traversable) {
            $items = new ArrayIterator($items);
        }

        if ($this->_cacheEnabled()) {
            self::$_cache->setItem(
                $this->_getCacheId($pageNumber), 
                $items, 
                array('tags' => array($this->_getCacheInternalId()))
            );
        }

        return $items;
    }

    /**
     * Returns a foreach-compatible iterator.
     *
     * @throws Exception\RuntimeException
     * @return Traversable
     */
    public function getIterator()
    {
        try {
            return $this->getCurrentItems();
        } catch (\Exception $e) {
            throw new Exception\RuntimeException('Error producing an iterator', null, $e);
        }
    }

    /**
     * Returns the page range (see property declaration above).
     *
     * @return integer
     */
    public function getPageRange()
    {
        return $this->_pageRange;
    }

    /**
     * Sets the page range (see property declaration above).
     *
     * @param  integer $pageRange
     * @return Paginator $this
     */
    public function setPageRange($pageRange)
    {
        $this->_pageRange = (integer) $pageRange;

        return $this;
    }

    /**
     * Returns the page collection.
     *
     * @param  string $scrollingStyle Scrolling style
     * @return array
     */
    public function getPages($scrollingStyle = null)
    {
        if ($this->_pages === null) {
            $this->_pages = $this->_createPages($scrollingStyle);
        }

        return $this->_pages;
    }

    /**
     * Returns a subset of pages within a given range.
     *
     * @param  integer $lowerBound Lower bound of the range
     * @param  integer $upperBound Upper bound of the range
     * @return array
     */
    public function getPagesInRange($lowerBound, $upperBound)
    {
        $lowerBound = $this->normalizePageNumber($lowerBound);
        $upperBound = $this->normalizePageNumber($upperBound);

        $pages = array();

        for ($pageNumber = $lowerBound; $pageNumber <= $upperBound; $pageNumber++) {
            $pages[$pageNumber] = $pageNumber;
        }

        return $pages;
    }

    /**
     * Returns the page item cache.
     *
     * @return array
     */
    public function getPageItemCache()
    {
        $data = array();
        if ($this->_cacheEnabled()) {
            $cacheIds = self::$_cache->find(CacheAdapter::MATCH_TAGS_OR, array(
                'tags' => array($this->_getCacheInternalId()),
            ));
            $cacheIds = array();
            while (($item = self::$_cache->fetch()) !== false) {
                $cacheIds[] = $item['key'];
            }
            foreach ($cacheIds as $id) {
                if (preg_match('|'.self::CACHE_TAG_PREFIX."(\d+)_.*|", $id, $page)) {
                    $data[$page[1]] = self::$_cache->getItem($this->_getCacheId($page[1]));
                }
            }
        }
        return $data;
    }

    /**
     * Retrieves the view instance.  
     *
     * If none registered, instantiates a PhpRenderer instance.
     *
     * @return \Zend\View\Renderer\RendererInterface|null
     */
    public function getView()
    {
        if ($this->_view === null) {
            $this->setView(new View\Renderer\PhpRenderer());
        }

        return $this->_view;
    }

    /**
     * Sets the view object.
     *
     * @param  \Zend\View\Renderer\RendererInterface $view
     * @return Paginator
     */
    public function setView(View\Renderer\RendererInterface $view = null)
    {
        $this->_view = $view;

        return $this;
    }

    /**
     * Brings the item number in range of the page.
     *
     * @param  integer $itemNumber
     * @return integer
     */
    public function normalizeItemNumber($itemNumber)
    {
        $itemNumber = (integer) $itemNumber;

        if ($itemNumber < 1) {
            $itemNumber = 1;
        }

        if ($itemNumber > $this->getItemCountPerPage()) {
            $itemNumber = $this->getItemCountPerPage();
        }

        return $itemNumber;
    }

    /**
     * Brings the page number in range of the paginator.
     *
     * @param  integer $pageNumber
     * @return integer
     */
    public function normalizePageNumber($pageNumber)
    {
        $pageNumber = (integer) $pageNumber;

        if ($pageNumber < 1) {
            $pageNumber = 1;
        }

        $pageCount = $this->count();

        if ($pageCount > 0 && $pageNumber > $pageCount) {
            $pageNumber = $pageCount;
        }

        return $pageNumber;
    }

    /**
     * Renders the paginator.
     *
     * @param  \Zend\View\Renderer\RendererInterface $view
     * @return string
     */
    public function render(View\Renderer\RendererInterface $view = null)
    {
        if (null !== $view) {
            $this->setView($view);
        }

        $view = $this->getView();

        return $view->paginationControl($this);
    }

    /**
     * Returns the items of the current page as JSON.
     *
     * @return string
     */
    public function toJson()
    {
        $currentItems = $this->getCurrentItems();

        if ($currentItems instanceof DbAbstractRowset) {
            return Json::encode($currentItems->toArray());
        } else {
            return Json::encode($currentItems);
        }
    }

    /**
     * Tells if there is an active cache object
     * and if the cache has not been disabled
     *
     * @return bool
     */
    protected function _cacheEnabled()
    {
        return ((self::$_cache !== null) && $this->_cacheEnabled);
    }

    /**
     * Makes an Id for the cache
     * Depends on the adapter object and the page number
     *
     * Used to store item in cache from that Paginator instance
     *  and that current page
     *
     * @param int $page
     * @return string
     */
    protected function _getCacheId($page = null)
    {
        if ($page === null) {
            $page = $this->getCurrentPageNumber();
        }
        return self::CACHE_TAG_PREFIX . $page . '_' . $this->_getCacheInternalId();
    }

    /**
     * Get the internal cache id
     * Depends on the adapter and the item count per page
     *
     * Used to tag that unique Paginator instance in cache
     *
     * @return string
     */
    protected function _getCacheInternalId()
    {
        return md5(serialize(array(
            spl_object_hash($this->getAdapter()),
            $this->getItemCountPerPage()
        )));
    }

    /**
     * Calculates the page count.
     *
     * @return integer
     */
    protected function _calculatePageCount()
    {
        return (integer) ceil($this->getAdapter()->count() / $this->getItemCountPerPage());
    }

    /**
     * Creates the page collection.
     *
     * @param  string $scrollingStyle Scrolling style
     * @return stdClass
     */
    protected function _createPages($scrollingStyle = null)
    {
        $pageCount         = $this->count();
        $currentPageNumber = $this->getCurrentPageNumber();

        $pages = new \stdClass();
        $pages->pageCount        = $pageCount;
        $pages->itemCountPerPage = $this->getItemCountPerPage();
        $pages->first            = 1;
        $pages->current          = $currentPageNumber;
        $pages->last             = $pageCount;

        // Previous and next
        if ($currentPageNumber - 1 > 0) {
            $pages->previous = $currentPageNumber - 1;
        }

        if ($currentPageNumber + 1 <= $pageCount) {
            $pages->next = $currentPageNumber + 1;
        }

        // Pages in range
        $scrollingStyle = $this->_loadScrollingStyle($scrollingStyle);
        $pages->pagesInRange     = $scrollingStyle->getPages($this);
        $pages->firstPageInRange = min($pages->pagesInRange);
        $pages->lastPageInRange  = max($pages->pagesInRange);

        // Item numbers
        if ($this->getCurrentItems() !== null) {
            $pages->currentItemCount = $this->getCurrentItemCount();
            $pages->itemCountPerPage = $this->getItemCountPerPage();
            $pages->totalItemCount   = $this->getTotalItemCount();
            $pages->firstItemNumber  = (($currentPageNumber - 1) * $this->getItemCountPerPage()) + 1;
            $pages->lastItemNumber   = $pages->firstItemNumber + $pages->currentItemCount - 1;
        }

        return $pages;
    }

    /**
     * Loads a scrolling style.
     *
     * @param string $scrollingStyle
     * @return ScrollingStyleInterface
     * @throws Exception\InvalidArgumentException
     */
    protected function _loadScrollingStyle($scrollingStyle = null)
    {
        if ($scrollingStyle === null) {
            $scrollingStyle = self::$_defaultScrollingStyle;
        }

        switch (strtolower(gettype($scrollingStyle))) {
            case 'object':
                if (!$scrollingStyle instanceof ScrollingStyleInterface) {
                    throw new Exception\InvalidArgumentException(
                        'Scrolling style must implement Zend\Paginator\ScrollingStyle\ScrollingStyleInterface'
                    );
                }

                return $scrollingStyle;

            case 'string':
                return self::getScrollingStyleBroker()->load($scrollingStyle);

            case 'null':
                // Fall through to default case

            default:
                throw new Exception\InvalidArgumentException(
                    'Scrolling style must be a class ' .
                    'name or object implementing Zend\Paginator\ScrollingStyle\ScrollingStyleInterface'
                );
        }
    }
}
