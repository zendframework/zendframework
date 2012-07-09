<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace Zend\Pdf\InternalType;

use Zend\Pdf;

/**
 * PDF file 'stream' element implementation
 *
 * @category   Zend
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Internal
 */
class StreamContent extends AbstractTypeObject
{
    /**
     * Object value
     *
     * @var \Zend\Memory\Container\AbstractContainer
     */
    public $value;

    /**
     * Object constructor
     *
     * @param string $val
     */
    public function __construct($val)
    {
        $this->value = Pdf\PdfDocument::getMemoryManager()->create($val);
    }

    /**
     * Return type of the element.
     *
     * @return integer
     */
    public function getType()
    {
        return AbstractTypeObject::TYPE_STREAM;
    }

    /**
     * Stream length.
     * (Method is used to avoid string copying, which may occurs in some cases)
     *
     * @return integer
     */
    public function length()
    {
        return strlen($this->value->getRef());
    }

    /**
     * Clear stream
     *
     */
    public function clear()
    {
        $ref = &$this->value->getRef();
        $ref = '';
        $this->value->touch();
    }

    /**
     * Append value to a stream
     *
     * @param mixed $val
     */
    public function append($val)
    {
        $ref = &$this->value->getRef();
        $ref .= (string)$val;
        $this->value->touch();
    }

    /**
      * Detach PDF object from the factory (if applicable), clone it and attach to new factory.
      *
      * @param \Zend\Pdf\ObjectFactory $factory  The factory to attach
      * @param array &$processed  List of already processed indirect objects, used to avoid objects duplication
      * @param integer $mode  Cloning mode (defines filter for objects cloning)
      * @returns \Zend\Pdf\InternalType\AbstractTypeObject
      */
     public function makeClone(Pdf\ObjectFactory $factory, array &$processed, $mode)
     {
         return new self($this->value->getRef());
     }

    /**
     * Return object as string
     *
     * @param \Zend\Pdf\ObjectFactory $factory
     * @return string
     */
    public function toString(Pdf\ObjectFactory $factory = null)
    {
        return "stream\n" . $this->value->getRef() . "\nendstream";
    }
}
