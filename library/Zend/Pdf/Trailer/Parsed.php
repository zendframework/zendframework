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
 * @subpackage Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Pdf\Trailer;
use Zend\Pdf\InternalType;
use Zend\Pdf\Exception;

/**
 * PDF file trailer.
 * Stores and provides access to the trailer parced from a PDF file
 *
 * @uses       \Zend\Pdf\Exception
 * @uses       \Zend\Pdf\Trailer\AbstractTrailer
 * @uses       \Zend\Pdf\InternalType\DirctionaryObject
 * @uses       \Zend\Pdf\InternalType\IndirectObjectReference\Context
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Parsed extends AbstractTrailer
{
    /**
     * Reference context
     *
     * @var \Zend\Pdf\InternalType\IndirectObjectReference\Context
     */
    private $_context;

    /**
     * Previous trailer
     *
     * @var \Zend\Pdf\Trailer\AbstractTrailer
     */
    private $_prev;


    /**
     * Object constructor
     *
     * @param \Zend\Pdf\InternalType\DictionaryObject $dict
     * @param \Zend\Pdf\InternalType\IndirectObjectReference\Context $context
     * @param \Zend\Pdf\Trailer\AbstractTrailer $prev
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
     * @param \Zend\Pdf\Trailer\Parsed $prev
     */
    public function setPrev(Parsed $prev)
    {
        $this->_prev = $prev;
    }

    /**
     * Getter for $this->_prev
     *
     * @return \Zend\Pdf\Trailer\AbstractTrailer
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
     * @return \Zend\Pdf\InternalType\IndirectObjectReference\Context
     */
    public function getRefTable()
    {
        return $this->_context->getRefTable();
    }

    /**
     * Get header of free objects list
     * Returns object number of last free object
     *
     * @throws \Zend\Pdf\Exception
     * @return integer
     */
    public function getLastFreeObject()
    {
        try {
            $this->_context->getRefTable()->getNextFree('0 65535 R');
        } catch (Exception $e) {
            if ($e->getMessage() == 'Object not found.') {
                /**
                 * Here is work around for some wrong generated PDFs.
                 * We have not found reference to the header of free object list,
                 * thus we treat it as there are no free objects.
                 */
                return 0;
            }

            throw $e;
        }
    }
}
