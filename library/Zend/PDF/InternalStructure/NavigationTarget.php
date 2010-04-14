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
namespace Zend\PDF\InternalStructure;
use Zend\PDF\Action;
use Zend\PDF\Destination;
use Zend\PDF\InternalType;
use Zend\PDF;

/**
 * PDF target (action or destination)
 *
 * @uses       \Zend\PDF\Action\AbstractAction
 * @uses       \Zend\PDF\Destination\AbstractDestination
 * @uses       \Zend\PDF\InternalType\AbstractTypeObject
 * @uses       \Zend\PDF\Exception
 * @package    Zend_PDF
 * @package    Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class NavigationTarget
{
    /**
     * Parse resource and return it as an Action or Explicit Destination
     *
     * $param \Zend\PDF\InternalType $resource
     * @return \Zend\PDF\Destination\AbstractDestination|\Zend\PDF\Action\AbstractAction
     * @throws \Zend\PDF\Exception
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
                throw new PDF\Exception('Wrong resource type.');
            }
        }

        if ($resource->getType() == InternalType\AbstractTypeObject::TYPE_ARRAY  ||
            $resource->getType() == InternalType\AbstractTypeObject::TYPE_NAME   ||
            $resource->getType() == InternalType\AbstractTypeObject::TYPE_STRING) {
            // Resource is an array, just treat it as an explicit destination array
            return Destination\AbstractDestination::load($resource);
        } else {
            throw new PDF\Exception('Wrong resource type.');
        }
    }

    /**
     * Get resource
     *
     * @internal
     * @return \Zend\PDF\InternalType\AbstractTypeObject
     */
    abstract public function getResource();
}
