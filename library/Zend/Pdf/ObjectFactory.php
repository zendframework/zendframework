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
namespace Zend\Pdf;

/**
 * PDF element factory interface.
 * Responsibility is to log PDF changes
 *
 * @package    Zend_PDF
 * @package    Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface ObjectFactory
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
     * @return \Zend\Pdf\ObjectFactory
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
     * @param \Zend\Pdf\ObjectFactory $factory
     */
    public function attach(ObjectFactory $factory);

    /**
     * Calculate object enumeration shift.
     *
     * @param \Zend\Pdf\ObjectFactory $factory
     * @return integer
     */
    public function calculateShift(ObjectFactory $factory);

    /**
     * Clean enumeration shift cache.
     * Has to be used after PDF render operation to let followed updates be correct.
     *
     * @param \Zend\Pdf\ObjectFactory $factory
     * @return integer
     */
    public function cleanEnumerationShiftCache();

    /**
     * Retrive object enumeration shift.
     *
     * @param \Zend\Pdf\ObjectFactory $factory
     * @return integer
     * @throws \Zend\Pdf\Exception
     */
    public function getEnumerationShift(ObjectFactory $factory);

    /**
     * Mark object as modified in context of current factory.
     *
     * @param \Zend\Pdf\InternalType\IndirectObject $obj
     * @throws \Zend\Pdf\Exception
     */
    public function markAsModified(InternalType\IndirectObject $obj);

    /**
     * Remove object in context of current factory.
     *
     * @param \Zend\Pdf\InternalType\IndirectObject $obj
     * @throws \Zend\Pdf\Exception
     */
    public function remove(InternalType\IndirectObject $obj);

    /**
     * Generate new \Zend\Pdf\InternalType\IndirectObject
     *
     * @todo Reusage of the freed object. It's not a support of new feature, but only improvement.
     *
     * @param \Zend\Pdf\InternalType\AbstractTypeObject $objectValue
     * @return \Zend\Pdf\InternalType\IndirectObject
     */
    public function newObject(InternalType\AbstractTypeObject $objectValue);

    /**
     * Generate new \Zend\Pdf\InternalType\StreamObject
     *
     * @todo Reusage of the freed object. It's not a support of new feature, but only improvement.
     *
     * @param mixed $objectValue
     * @return \Zend\Pdf\InternalType\StreamObject
     */
    public function newStreamObject($streamValue);

    /**
     * Enumerate modified objects.
     * Returns array of Zend_PDF_UpdateInfoContainer
     *
     * @param \Zend\Pdf\ObjectFactory $rootFactory
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
