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
 * @subpackage Zend_PDF_Destination
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Pdf\Destination;
use Zend\Pdf\Exception;
use Zend\Pdf\InternalType;
use Zend\Pdf;

/**
 * Abstract PDF explicit destination representation class
 *
 * @uses       \Zend\Pdf\Destination\AbstractDestination
 * @uses       \Zend\Pdf\InternalType\AbstractTypeObject
 * @uses       \Zend\Pdf\Exception
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Destination
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Explicit extends AbstractDestination
{
    /**
     * Destination description array
     *
     * @var \Zend\Pdf\InternalType\ArrayObject
     */
    protected $_destinationArray;

    /**
     * True if it's a remote destination
     *
     * @var boolean
     */
    protected $_isRemote;

    /**
     * Explicit destination object constructor
     *
     * @param \Zend\Pdf\InternalType\AbstractTypeObject $destinationArray
     * @throws \Zend\Pdf\Exception
     */
    public function __construct(InternalType\AbstractTypeObject $destinationArray)
    {
        if ($destinationArray->getType() != InternalType\AbstractTypeObject::TYPE_ARRAY) {
            throw new Exception\CorruptedPdfException('Explicit destination resource Array must be a direct or an indirect array object.');
        }

        $this->_destinationArray = $destinationArray;

        switch (count($this->_destinationArray->items)) {
            case 0:
                throw new Exception\CorruptedPdfException('Destination array must contain a page reference.');
                break;

            case 1:
                throw new Exception\CorruptedPdfException('Destination array must contain a destination type name.');
                break;

            default:
                // Do nothing
                break;
        }

        switch ($this->_destinationArray->items[0]->getType()) {
            case InternalType\AbstractTypeObject::TYPE_NUMERIC:
                $this->_isRemote = true;
                break;

            case InternalType\AbstractTypeObject::TYPE_DICTIONARY:
                $this->_isRemote = false;
                break;

            default:
                throw new Exception\CorruptedPdfException('Destination target must be a page number or page dictionary object.');
                break;
        }
    }

    /**
     * Returns true if it's a remote destination
     *
     * @return boolean
     */
    public function isRemote()
    {
        return $this->_isRemote;
    }

    /**
     * Get resource
     *
     * @internal
     * @return \Zend\Pdf\InternalType\AbstractTypeObject
     */
    public function getResource()
    {
        return $this->_destinationArray;
    }
}
