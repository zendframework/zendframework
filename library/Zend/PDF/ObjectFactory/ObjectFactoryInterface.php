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
namespace Zend\PDF\ObjectFactory;
use Zend\PDF\InternalType;

/**
 * PDF element factory interface.
 * Responsibility is to log PDF changes
 *
 * @package    Zend_PDF
 * @package    Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface ObjectFactoryInterface
{
    /**
     * Close factory and clean-up resources
     *
     * @internal
     */
    public function close();

    /**
     * Get source factory object
     *
     * @return \Zend\PDF\ObjectFactory\ObjectFactory
     */
    public function resolve();

    /**
     * Get factory ID
     *
     * @return integer
     */
    public function getId();

    /**
     * Set object counter
     *
     * @param integer $objCount
     */
    public function setObjectCount($objCount);

    /**
     * Get object counter
     *
     * @return integer
     */
    public function getObjectCount();

    /**
     * Attach factory to the current;
     *
     * @param \Zend\PDF\ObjectFactory\ObjectFactoryInterface $factory
     */
    public function attach(ObjectFactoryInterface $factory);

    /**
     * Calculate object enumeration shift.
     *
     * @param \Zend\PDF\ObjectFactory\ObjectFactoryInterface $factory
     * @return integer
     */
    public function calculateShift(ObjectFactoryInterface $factory);

    /**
     * Clean enumeration shift cache.
     * Has to be used after PDF render operation to let followed updates be correct.
     *
     * @param \Zend\PDF\ObjectFactory\ObjectFactoryInterface $factory
     * @return integer
     */
    public function cleanEnumerationShiftCache();

    /**
     * Retrive object enumeration shift.
     *
     * @param \Zend\PDF\ObjectFactory\ObjectFactoryInterface $factory
     * @return integer
     * @throws \Zend\PDF\Exception
     */
    public function getEnumerationShift(ObjectFactoryInterface $factory);

    /**
     * Mark object as modified in context of current factory.
     *
     * @param \Zend\PDF\InternalType\IndirectObject $obj
     * @throws \Zend\PDF\Exception
     */
    public function markAsModified(InternalType\IndirectObject $obj);

    /**
     * Remove object in context of current factory.
     *
     * @param \Zend\PDF\InternalType\IndirectObject $obj
     * @throws \Zend\PDF\Exception
     */
    public function remove(InternalType\IndirectObject $obj);

    /**
     * Generate new \Zend\PDF\InternalType\IndirectObject
     *
     * @todo Reusage of the freed object. It's not a support of new feature, but only improvement.
     *
     * @param \Zend\PDF\InternalType\AbstractTypeObject $objectValue
     * @return \Zend\PDF\InternalType\IndirectObject
     */
    public function newObject(InternalType\AbstractTypeObject $objectValue);

    /**
     * Generate new \Zend\PDF\InternalType\StreamObject
     *
     * @todo Reusage of the freed object. It's not a support of new feature, but only improvement.
     *
     * @param mixed $objectValue
     * @return \Zend\PDF\InternalType\StreamObject
     */
    public function newStreamObject($streamValue);

    /**
     * Enumerate modified objects.
     * Returns array of Zend_PDF_UpdateInfoContainer
     *
     * @param \Zend\PDF\ObjectFactory\ObjectFactory $rootFactory
     * @return array
     */
    public function listModifiedObjects($rootFactory = null);

    /**
     * Check if PDF file was modified
     *
     * @return boolean
     */
    public function isModified();
}
