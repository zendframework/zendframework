<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cloud
 */

namespace Zend\Cloud\DocumentService\Adapter;

use Zend\Cloud\DocumentService\Document;
use Zend\Cloud\DocumentService\DocumentSet;
use Zend\Cloud\DocumentService\Query;

/**
 * Abstract document service adapter
 *
 * Provides functionality surrounding setting classes for each of:
 * - document objects
 * - document set objects
 * - query class objects
 *
 * @category   Zend
 * @package    Zend_Cloud_DocumentService
 * @subpackage Adapter
 */
abstract class AbstractAdapter implements AdapterInterface
{
    const DOCUMENT_CLASS    = 'document_class';
    const DOCUMENTSET_CLASS = 'documentset_class';
    const QUERY_CLASS       = 'query_class';

    /**
     * Class to utilize for new document objects
     * @var string
     */
    protected $_documentClass = 'Zend\Cloud\DocumentService\Document';

    /**
     * Class to utilize for new document set objects
     * @var string
     */
    protected $_documentSetClass = 'Zend\Cloud\DocumentService\DocumentSet';

    /**
     * Class to utilize for new query objects
     *
     * @var string
     */
    protected $_queryClass = 'Zend\Cloud\DocumentService\Query';

    /**
     * Set the class for document objects
     *
     * @param  string $class
     * @return \Zend\Cloud\DocumentService\Adapter\AbstractAdapter
     */
    public function setDocumentClass($class)
    {
        $this->_documentClass = (string) $class;
        return $this;
    }

    /**
     * Get the class for document objects
     *
     * @return string
     */
    public function getDocumentClass()
    {
        return $this->_documentClass;
    }

    /**
     * Set the class for document set objects
     *
     * @param  string $class
     * @return \Zend\Cloud\DocumentService\Adapter\AbstractAdapter
     */
    public function setDocumentSetClass($class)
    {
        $this->_documentSetClass = (string) $class;
        return $this;
    }

    /**
     * Get the class for document set objects
     *
     * @return string
     */
    public function getDocumentSetClass()
    {
        return $this->_documentSetClass;
    }

    /**
     * Set the query class for query objects
     *
     * @param  string $class
     * @return \Zend\Cloud\DocumentService\Adapter\AbstractAdapter
     */
    public function setQueryClass($class)
    {
        $this->_queryClass = (string) $class;
        return $this;
    }

    /**
     * Get the class for query objects
     *
     * @return string
     */
    public function getQueryClass()
    {
        return $this->_queryClass;
    }
}
