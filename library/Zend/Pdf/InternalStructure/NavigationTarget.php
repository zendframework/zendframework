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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Pdf\InternalStructure;
use Zend\Pdf\Exception;
use Zend\Pdf\Action;
use Zend\Pdf\Destination;
use Zend\Pdf\InternalType;
use Zend\Pdf;

/**
 * PDF target (action or destination)
 *
 * @uses       \Zend\Pdf\Action\AbstractAction
 * @uses       \Zend\Pdf\Destination\AbstractDestination
 * @uses       \Zend\Pdf\InternalType\AbstractTypeObject
 * @uses       \Zend\Pdf\Exception
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class NavigationTarget
{
    /**
     * Parse resource and return it as an Action or Explicit Destination
     *
     * $param \Zend\Pdf\InternalType $resource
     * @return \Zend\Pdf\Destination\AbstractDestination|\Zend\Pdf\Action\AbstractAction
     * @throws \Zend\Pdf\Exception
     */
    public static function load(InternalType\AbstractTypeObject $resource)
    {
        if ($resource->getType() == InternalType\AbstractTypeObject::TYPE_DICTIONARY) {
            if (($resource->Type === null  ||  $resource->Type->value =='Action')  &&  $resource->S !== null) {
                // It's a well-formed action, load it
                return Action\AbstractAction::load($resource);
            } else if ($resource->D !== null) {
                // It's a destination
                $resource = $resource->D;
            } else {
                throw new Exception\CorruptedPdfException('Wrong resource type.');
            }
        }

        if ($resource->getType() == InternalType\AbstractTypeObject::TYPE_ARRAY  ||
            $resource->getType() == InternalType\AbstractTypeObject::TYPE_NAME   ||
            $resource->getType() == InternalType\AbstractTypeObject::TYPE_STRING) {
            // Resource is an array, just treat it as an explicit destination array
            return Destination\AbstractDestination::load($resource);
        } else {
            throw new Exception\CorruptedPdfException('Wrong resource type.');
        }
    }

    /**
     * Get resource
     *
     * @internal
     * @return \Zend\Pdf\InternalType\AbstractTypeObject
     */
    abstract public function getResource();
}
