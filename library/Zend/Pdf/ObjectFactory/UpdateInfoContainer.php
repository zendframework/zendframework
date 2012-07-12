<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace Zend\Pdf\ObjectFactory;

use Zend\Pdf;

/**
 * Container which collects updated object info.
 *
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Internal
 */
class UpdateInfoContainer
{
    /**
     * Object number
     *
     * @var integer
     */
    private $_objNum;

    /**
     * Generation number
     *
     * @var integer
     */
    private $_genNum;


    /**
     * Flag, which signals, that object is free
     *
     * @var boolean
     */
    private $_isFree;

    /**
     * String representation of the object
     *
     * @var \Zend\Memory\Container\AbstractContainer|null
     */
    private $_dump = null;

    /**
     * Object constructor
     *
     * @param integer $objCount
     */
    public function __construct($objNum, $genNum, $isFree, $dump = null)
    {
        $this->_objNum = $objNum;
        $this->_genNum = $genNum;
        $this->_isFree = $isFree;

        if ($dump !== null) {
            if (strlen($dump) > 1024) {
                $this->_dump = Pdf\PdfDocument::getMemoryManager()->create($dump);
            } else {
                $this->_dump = $dump;
            }
        }
    }


    /**
     * Get object number
     *
     * @return integer
     */
    public function getObjNum()
    {
        return $this->_objNum;
    }

    /**
     * Get generation number
     *
     * @return integer
     */
    public function getGenNum()
    {
        return $this->_genNum;
    }

    /**
     * Check, that object is free
     *
     * @return boolean
     */
    public function isFree()
    {
        return $this->_isFree;
    }

    /**
     * Get string representation of the object
     *
     * @return string
     */
    public function getObjectDump()
    {
        if ($this->_dump === null) {
            return '';
        }

        if (is_string($this->_dump)) {
            return $this->_dump;
        }

        return $this->_dump->getRef();
    }
}
