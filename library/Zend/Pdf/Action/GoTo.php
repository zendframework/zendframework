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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** Zend_Pdf_Action */
require_once 'Zend/Pdf/Action.php';

/** Zend_Pdf_Destination */
require_once 'Zend/Pdf/Destination.php';


/**
 * PDF 'Go to' action
 *
 * @package    Zend_Pdf
 * @subpackage Actions
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Pdf_Action_GoTo extends Zend_Pdf_Action
{
	/**
	 * Create new Zend_Pdf_Action_GoTo object using specified destination
	 *
	 * Object is created based on a provided dictionary. That allows to choose if it's
	 * a direct or inderect object attached to some objects factory
	 *
	 * @param Zend_Pdf_Element|Zend_Pdf_Destination|string $destination
	 * @param Zend_Pdf_ElementFactory $objectFactory
	 * @return Zend_Pdf_Action_GoTo
	 * @throws Zend_Pdf_Exception
	 */
	public static function create($destination, $objectFactory = null)
    {
    	if ($objectFactory === null) {
    		$dictionary = new Zend_Pdf_Element_Dictionary();
    	} else {
    		$dictionary = $objectFactory->newObject(new Zend_Pdf_Element_Dictionary());
    	}

        if ($dictionary->getType() != Zend_Pdf_Element::TYPE_DICTIONARY) {
            require_once 'Zend/Pdf/Exception.php';
            throw new Zend_Pdf_Exception('$dictionary mast be a direct or an indirect dictionary object.');
        }
    	$dictionary->Type = new Zend_Pdf_Element_Name('Action');
    	$dictionary->S    = new Zend_Pdf_Element_Name('GoTo');
    	$dictionary->Next = null;

    	if (is_string($destination)) {
            // Named destination
            $dictionary->D = new Zend_Pdf_Element_String($destination);
    	} else if ($destination instanceof Zend_Pdf_Element_Array) {
    		// DestinationArray
            $dictionary->D = $destination;
    	} else if ($destination instanceof Zend_Pdf_Destination) {
    		$dictionary->D = $destination->getDestinationArray();
    	} else {
    		require_once 'Zend/Pdf/Exception.php';
    		throw new Zend_Pdf_Exception('Wrong $destination parameter type');
    	}

    	return new Zend_Pdf_Action_GoTo($dictionary, null, new SplObjectStorage());
    }

	/**
	 * Returns goto action destination
	 * (Zend_Pdf_Element_Name or Zend_Pdf_Element_String for named destinations
	 * or Zend_Pdf_Array for explicit destinations)
	 *
	 * @return Zend_Pdf_Destination|string
	 */
	public function getDestination()
	{
		$destination = $this->_actionDictionary->D;

		if ($destination instanceof Zend_Pdf_Element_Name  ||  $destination instanceof Zend_Pdf_Element_String) {
			return $destination->value;
        }

		return Zend_Pdf_Destination::load($this->_actionDictionary->D);
	}
}

