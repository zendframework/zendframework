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
 * @subpackage Zend_PDF_Action
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\PDF\Action;
use Zend\PDF;
use Zend\PDF\Destination;
use Zend\PDF\InternalType;

/**
 * PDF 'Go to' action
 *
 * @uses       SplObjectStorage
 * @uses       \Zend\PDF\Action\AbstractAction
 * @uses       \Zend\PDF\Destination\AbstractDestination
 * @uses       \Zend\PDF\Destination\Named
 * @uses       \Zend\PDF\InternalType\DictionaryObject
 * @uses       \Zend\PDF\InternalType\NameObject
 * @uses       \Zend\PDF\Exception
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Action
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class GoToAction extends AbstractAction
{
    /**
     * GoTo Action destination
     *
     * @var \Zend\PDF\Destination\AbstractDestination
     */
    protected $_destination;


    /**
     * Object constructor
     *
     * @param \Zend\PDF\InternalType\DictionaryObject $dictionary
     * @param SplObjectStorage    $processedActions  list of already processed action dictionaries,
     *                                               used to avoid cyclic references
     */
    public function __construct(InternalType\AbstractTypeObject $dictionary, \SplObjectStorage $processedActions)
    {
        parent::__construct($dictionary, $processedActions);

        $this->_destination = Destination\AbstractDestination::load($dictionary->D);
    }

    /**
     * Create new Zend_PDF_Action_GoTo object using specified destination
     *
     * @param \Zend\PDF\Destination\AbstractDestination|string $destination
     * @return \Zend\PDF\Action\GoToAction
     */
    public static function create($destination)
    {
        if (is_string($destination)) {
            $destination = Destination\Named::create($destination);
        }

        if (!$destination instanceof Destination\AbstractDestination) {
            throw new PDF\Exception('$destination parameter must be a Zend_PDF_Destination object or string.');
        }

        $dictionary       = new InternalType\DictionaryObject();
        $dictionary->Type = new InternalType\NameObject('Action');
        $dictionary->S    = new InternalType\NameObject('GoTo');
        $dictionary->Next = null;
        $dictionary->D    = $destination->getResource();

        return new self($dictionary, new \SplObjectStorage());
    }

    /**
     * Set goto action destination
     *
     * @param \Zend\PDF\Destination\AbstractDestination|string $destination
     * @return \Zend\PDF\Action\GoToAction
     */
    public function setDestination(Destination\AbstractDestination $destination)
    {
        $this->_destination = $destination;

        $this->_actionDictionary->touch();
        $this->_actionDictionary->D = $destination->getResource();

        return $this;
    }

    /**
     * Get goto action destination
     *
     * @return \Zend\PDF\Destination\AbstractDestination
     */
    public function getDestination()
    {
        return $this->_destination;
    }
}
