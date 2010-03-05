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
 * @package    Zend_Pdf
 * @subpackage Actions
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * PDF 'Go to' action
 *
 * @uses       SplObjectStorage
 * @uses       Zend_Pdf_Action
 * @uses       Zend_Pdf_Destination
 * @uses       Zend_Pdf_Destination_Named
 * @uses       Zend_Pdf_Element_Dictionary
 * @uses       Zend_Pdf_Element_Name
 * @uses       Zend_Pdf_Exception
 * @package    Zend_Pdf
 * @subpackage Actions
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Pdf_Action_GoTo extends Zend_Pdf_Action
{
    /**
     * GoTo Action destination
     *
     * @var Zend_Pdf_Destination
     */
    protected $_destination;


    /**
     * Object constructor
     *
     * @param Zend_Pdf_Element_Dictionary $dictionary
     * @param SplObjectStorage            $processedActions  list of already processed action dictionaries, used to avoid cyclic references
     */
    public function __construct(Zend_Pdf_Element $dictionary, SplObjectStorage $processedActions)
    {
        parent::__construct($dictionary, $processedActions);

        $this->_destination = Zend_Pdf_Destination::load($dictionary->D);
    }

    /**
     * Create new Zend_Pdf_Action_GoTo object using specified destination
     *
     * @param Zend_Pdf_Destination|string $destination
     * @return Zend_Pdf_Action_GoTo
     */
    public static function create($destination)
    {
        if (is_string($destination)) {
            $destination = Zend_Pdf_Destination_Named::create($destination);
        }

        if (!$destination instanceof Zend_Pdf_Destination) {
            throw new Zend_Pdf_Exception('$destination parameter must be a Zend_Pdf_Destination object or string.');
        }

        $dictionary       = new Zend_Pdf_Element_Dictionary();
        $dictionary->Type = new Zend_Pdf_Element_Name('Action');
        $dictionary->S    = new Zend_Pdf_Element_Name('GoTo');
        $dictionary->Next = null;
        $dictionary->D    = $destination->getResource();

        return new self($dictionary, new SplObjectStorage());
    }

    /**
     * Set goto action destination
     *
     * @param Zend_Pdf_Destination|string $destination
     * @return Zend_Pdf_Action_GoTo
     */
    public function setDestination(Zend_Pdf_Destination $destination)
    {
        $this->_destination = $destination;

        $this->_actionDictionary->touch();
        $this->_actionDictionary->D = $destination->getResource();

        return $this;
    }

    /**
     * Get goto action destination
     *
     * @return Zend_Pdf_Destination
     */
    public function getDestination()
    {
        return $this->_destination;
    }
}
