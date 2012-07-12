<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cloud
 */

namespace Zend\Cloud\DocumentService;

use Countable;
use IteratorAggregate;

/**
 * Class encapsulating a set of documents
 *
 * @category   Zend
 * @package    Zend_Cloud
 * @subpackage DocumentService
 */
class DocumentSet implements
    Countable,
    IteratorAggregate
{
    /** @var int */
    protected $_documentCount;

    /** @var \ArrayIterator */
    protected $_documents;

    /**
     * Constructor
     *
     * @param  array $documents
     * @return void
     */
    public function __construct(array $documents)
    {
        $this->_documentCount = count($documents);
        $this->_documents     = new \ArrayIterator($documents);
    }

    /**
     * Countable: number of documents in set
     *
     * @return int
     */
    public function count()
    {
        return $this->_documentCount;
    }

    /**
     * IteratorAggregate: retrieve iterator
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        return $this->_documents;
    }
}
