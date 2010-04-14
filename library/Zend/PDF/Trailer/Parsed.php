<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_PDF
 * @package    Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\PDF\Trailer;
use Zend\PDF;
use Zend\PDF\InternalType;

/**
 * PDF file trailer.
 * Stores and provides access to the trailer parced from a PDF file
 *
 * @uses       \Zend\PDF\Exception
 * @uses       \Zend\PDF\Trailer\AbstractTrailer
 * @uses       \Zend\PDF\InternalType\DirctionaryObject
 * @uses       \Zend\PDF\InternalType\IndirectObjectReference\Context
 * @package    Zend_PDF
 * @package    Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Parsed extends AbstractTrailer
{
    /**
     * Reference context
     *
     * @var \Zend\PDF\InternalType\IndirectObjectReference\Context
     */
    private $_context;

    /**
     * Previous trailer
     *
     * @var \Zend\PDF\Trailer\AbstractTrailer
     */
    private $_prev;


    /**
     * Object constructor
     *
     * @param \Zend\PDF\InternalType\DictionaryObject $dict
     * @param \Zend\PDF\InternalType\IndirectObjectReference\Context $context
     * @param \Zend\PDF\Trailer\AbstractTrailer $prev
     */
    public function __construct(InternalType\DictionaryObject $dict,
                                InternalType\IndirectObjectReference\Context $context,
                                AbstractTrailer $prev = null)
    {
        parent::__construct($dict);

        $this->_context = $context;
        $this->_prev    = $prev;
    }

    /**
     * Setter for $this->_prev
     *
     * @param \Zend\PDF\Trailer\Parsed $prev
     */
    public function setPrev(Parsed $prev)
    {
        $this->_prev = $prev;
    }

    /**
     * Getter for $this->_prev
     *
     * @return \Zend\PDF\Trailer\AbstractTrailer
     */
    public function getPrev()
    {
        return $this->_prev;
    }

    /**
     * Get length of source PDF
     *
     * @return string
     */
    public function getPDFLength()
    {
        return $this->_context->getParser()->getLength();
    }

    /**
     * Get PDF String
     *
     * @return string
     */
    public function getPDFString()
    {
        return $this->_context->getParser()->getString();
    }

    /**
     * Get reference table, which corresponds to the trailer.
     * Proxy to the $_context member methad call
     *
     * @return \Zend\PDF\InternalType\IndirectObjectReference\Context
     */
    public function getRefTable()
    {
        return $this->_context->getRefTable();
    }

    /**
     * Get header of free objects list
     * Returns object number of last free object
     *
     * @throws \Zend\PDF\Exception
     * @return integer
     */
    public function getLastFreeObject()
    {
        try {
            $this->_context->getRefTable()->getNextFree('0 65535 R');
        } catch (PDF\Exception $e) {
            if ($e->getMessage() == 'Object not found.') {
                /**
                 * Here is work around for some wrong generated PDFs.
                 * We have not found reference to the header of free object list,
                 * thus we treat it as there are no free objects.
                 */
                return 0;
            }

            throw new PDF\Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}
