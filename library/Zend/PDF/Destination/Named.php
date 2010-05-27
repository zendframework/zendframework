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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\PDF\Destination;
use Zend\PDF\InternalType;
use Zend\PDF;

/**
 * Destination array: [page /Fit]
 *
 * Display the page designated by page, with its contents magnified just enough
 * to fit the entire page within the window both horizontally and vertically. If
 * the required horizontal and vertical magnification factors are different, use
 * the smaller of the two, centering the page within the window in the other
 * dimension.
 *
 * @uses       \Zend\PDF\Destination\AbstractDestination
 * @uses       \Zend\PDF\InternalType\AbstractTypeObject
 * @uses       \Zend\PDF\InternalType\StringObject
 * @uses       \Zend\PDF\Exception
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Destination
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Named extends AbstractDestination
{
    /**
     * Destination name
     *
     * @var \Zend\PDF\InternalType\NameObject|\Zend\PDF\InternalType\StringObject
     */
    protected $_nameElement;

    /**
     * Named destination object constructor
     *
     * @param $resource
     * @throws \Zend\PDF\Exception
     */
    public function __construct(InternalType\AbstractTypeObject $resource)
    {
        if ($resource->getType() != InternalType\AbstractTypeObject::TYPE_NAME  &&  $resource->getType() != InternalType\AbstractTypeObject::TYPE_STRING) {
            throw new PDF\Exception('Named destination resource must be a PDF name or a PDF string.');
        }

        $this->_nameElement = $resource;
    }

    /**
     * Create named destination object
     *
     * @param string $name
     * @return \Zend\PDF\Destination\Named
     */
    public static function create($name)
    {
        return new self(new InternalType\StringObject($name));
    }

    /**
     * Get name
     *
     * @return \Zend\PDF\InternalType\AbstractTypeObject
     */
    public function getName()
    {
        return $this->_nameElement->value;
    }

    /**
     * Get resource
     *
     * @internal
     * @return \Zend\PDF\InternalType\AbstractTypeObject
     */
    public function getResource()
    {
        return $this->_nameElement;
    }
}
