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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Pdf\Action;
use Zend\Pdf\Exception;
use Zend\Pdf\ObjectFactory;
use Zend\Pdf\InternalType;
use Zend\Pdf;

/**
 * Abstract PDF action representation class
 *
 * @uses       Countable
 * @uses       RecursiveIterator
 * @uses       SplObjectStorage
 * @uses       \Zend\Pdf\Action\GoToAction
 * @uses       \Zend\Pdf\Action\GoTo3DView
 * @uses       \Zend\Pdf\Action\GoToE
 * @uses       \Zend\Pdf\Action\GoToR
 * @uses       \Zend\Pdf\Action\Hide
 * @uses       \Zend\Pdf\Action\ImportData
 * @uses       \Zend\Pdf\Action\JavaScript
 * @uses       \Zend\Pdf\Action\Launch
 * @uses       \Zend\Pdf\Action\Movie
 * @uses       \Zend\Pdf\Action\Named
 * @uses       \Zend\Pdf\Action\Rendition
 * @uses       \Zend\Pdf\Action\ResetForm
 * @uses       \Zend\Pdf\Action\SetOCGState
 * @uses       \Zend\Pdf\Action\Sound
 * @uses       \Zend\Pdf\Action\SubmitForm
 * @uses       \Zend\Pdf\Action\Thread
 * @uses       \Zend\Pdf\Action\Trans
 * @uses       \Zend\Pdf\Action\Unknown
 * @uses       \Zend\Pdf\Action\Uri
 * @uses       \Zend\Pdf\InternalType\AbstractTypeObject
 * @uses       \Zend\Pdf\InternalType\ArrayObject
 * @uses       \Zend\Pdf\Exception
 * @uses       \Zend\Pdf\InternalStructure\NavigationTarget
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Action
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractAction extends Pdf\InternalStructure\NavigationTarget implements \RecursiveIterator, \Countable
{
    /**
     * Action dictionary
     *
     * @var   \Zend\Pdf\InternalType\DictionaryObject
     *      | \Zend\Pdf\InternalType\IndirectObject
     *      | \Zend\Pdf\InternalType\IndirectObjectReference
     */
    protected $_actionDictionary;


    /**
     * An original list of chained actions
     *
     * @var array  Array of \Zend\Pdf\Action\AbstractAction objects
     */
    protected $_originalNextList;

    /**
     * A list of next actions in actions tree (used for actions chaining)
     *
     * @var array  Array of \Zend\Pdf\Action\AbstractAction objects
     */
    public $next = array();

    /**
     * Object constructor
     *
     * @param \Zend\Pdf\InternalType\DictionaryObject $dictionary
     * @param SplObjectStorage      $processedActions  list of already processed action dictionaries,
     *                                                 used to avoid cyclic references
     * @throws \Zend\Pdf\Exception
     */
    public function __construct(InternalType\AbstractTypeObject $dictionary, \SplObjectStorage $processedActions)
    {
        if ($dictionary->getType() != InternalType\AbstractTypeObject::TYPE_DICTIONARY) {
            throw new Exception\CorruptedPdfException('$dictionary mast be a direct or an indirect dictionary object.');
        }

        $this->_actionDictionary = $dictionary;

        if ($dictionary->Next !== null) {
            if ($dictionary->Next instanceof InternalType\DictionaryObject) {
                // Check if dictionary object is not already processed
                if (!$processedActions->contains($dictionary->Next)) {
                    $processedActions->attach($dictionary->Next);
                    $this->next[] = self::load($dictionary->Next, $processedActions);
                }
            } else if ($dictionary->Next instanceof InternalType\ArrayObject) {
                foreach ($dictionary->Next->items as $chainedActionDictionary) {
                    // Check if dictionary object is not already processed
                    if (!$processedActions->contains($chainedActionDictionary)) {
                        $processedActions->attach($chainedActionDictionary);
                        $this->next[] = self::load($chainedActionDictionary, $processedActions);
                    }
                }
            } else {
                throw new Exception\CorruptedPdfException('PDF Action dictionary Next entry must be a dictionary or an array.');
            }
        }

        $this->_originalNextList = $this->next;
    }

    /**
     * Load PDF action object using specified dictionary
     *
     * @internal
     * @param \Zend\Pdf\InternalType\AbstractTypeObject $dictionary (It's actually Dictionary or Dictionary Object or Reference to a Dictionary Object)
     * @param SplObjectStorage $processedActions  list of already processed action dictionaries, used to avoid cyclic references
     * @return \Zend\Pdf\Action\AbstractAction
     * @throws \Zend\Pdf\Exception
     */
    public static function load(InternalType\AbstractTypeObject $dictionary, \SplObjectStorage $processedActions = null)
    {
        if ($processedActions === null) {
            $processedActions = new \SplObjectStorage();
        }

        if ($dictionary->getType() != InternalType\AbstractTypeObject::TYPE_DICTIONARY) {
            throw new Exception\CorruptedPdfException('$dictionary mast be a direct or an indirect dictionary object.');
        }
        if (isset($dictionary->Type)  &&  $dictionary->Type->value != 'Action') {
            throw new Exception\CorruptedPdfException('Action dictionary Type entry must be set to \'Action\'.');
        }

        if ($dictionary->S === null) {
            throw new Exception\CorruptedPdfException('Action dictionary must contain S entry');
        }

        switch ($dictionary->S->value) {
            case 'GoTo':
                return new GoToAction($dictionary, $processedActions);

            case 'GoToR':
                return new GoToR($dictionary, $processedActions);

            case 'GoToE':
                return new GoToE($dictionary, $processedActions);

            case 'Launch':
                return new Launch($dictionary, $processedActions);

            case 'Thread':
                return new Thread($dictionary, $processedActions);

            case 'URI':
                return new Uri($dictionary, $processedActions);

            case 'Sound':
                return new Sound($dictionary, $processedActions);

            case 'Movie':
                return new Movie($dictionary, $processedActions);

            case 'Hide':
                return new Hide($dictionary, $processedActions);

            case 'Named':
                return new Named($dictionary, $processedActions);

            case 'SubmitForm':
                return new SubmitForm($dictionary, $processedActions);

            case 'ResetForm':
                return new ResetForm($dictionary, $processedActions);

            case 'ImportData':
                return new ImportData($dictionary, $processedActions);

            case 'JavaScript':
                return new JavaScript($dictionary, $processedActions);

            case 'SetOCGState':
                return new SetOCGState($dictionary, $processedActions);

            case 'Rendition':
                return new Rendition($dictionary, $processedActions);

            case 'Trans':
                return new Trans($dictionary, $processedActions);

            case 'GoTo3DView':
                return new GoTo3DView($dictionary, $processedActions);

            default:
                return new Unknown($dictionary, $processedActions);
        }
    }

    /**
     * Get resource
     *
     * @internal
     * @return \Zend\Pdf\InternalType\AbstractTypeObject
     */
    public function getResource()
    {
        return $this->_actionDictionary;
    }

    /**
     * Dump Action and its child actions into PDF structures
     *
     * Returns dictionary indirect object or reference
     *
     * @internal
     * @param \Zend\Pdf\ObjectFactory $factory    Object factory for newly created indirect objects
     * @param SplObjectStorage $processedActions  list of already processed actions
     *                                            (used to prevent infinity loop caused by cyclic references)
     * @return \Zend\Pdf\InternalType\IndirectObject|\Zend\Pdf\InternalType\IndirectObjectReference
     */
    public function dumpAction(ObjectFactory $factory, \SplObjectStorage $processedActions = null)
    {
        if ($processedActions === null) {
            $processedActions = new \SplObjectStorage();
        }
        if ($processedActions->contains($this)) {
            throw new Exception\CorruptedPdfException('Action chain cyclyc reference is detected.');
        }
        $processedActions->attach($this);

        $childListUpdated = false;
        if (count($this->_originalNextList) != count($this->next)) {
            // If original and current children arrays have different size then children list was updated
            $childListUpdated = true;
        } else if ( !(array_keys($this->_originalNextList) === array_keys($this->next)) ) {
            // If original and current children arrays have different keys (with a glance to an order) then children list was updated
            $childListUpdated = true;
        } else {
            foreach ($this->next as $key => $childAction) {
                if ($this->_originalNextList[$key] !== $childAction) {
                    $childListUpdated = true;
                    break;
                }
            }
        }

        if ($childListUpdated) {
            $this->_actionDictionary->touch();
            switch (count($this->next)) {
                case 0:
                    $this->_actionDictionary->Next = null;
                    break;

                case 1:
                    $child = reset($this->next);
                    $this->_actionDictionary->Next = $child->dumpAction($factory, $processedActions);
                    break;

                default:
                    $pdfChildArray = new InternalType\ArrayObject();
                    foreach ($this->next as $child) {

                        $pdfChildArray->items[] = $child->dumpAction($factory, $processedActions);
                    }
                    $this->_actionDictionary->Next = $pdfChildArray;
                    break;
            }
        } else {
            foreach ($this->next as $child) {
                $child->dumpAction($factory, $processedActions);
            }
        }

        if ($this->_actionDictionary instanceof InternalType\DictionaryObject) {
            // It's a newly created action. Register it within object factory and return indirect object
            return $factory->newObject($this->_actionDictionary);
        } else {
            // It's a loaded object
            return $this->_actionDictionary;
        }
    }


    ////////////////////////////////////////////////////////////////////////
    //  RecursiveIterator interface methods
    //////////////

    /**
     * Returns current child action.
     *
     * @return \Zend\Pdf\Action\AbstractAction
     */
    public function current()
    {
        return current($this->next);
    }

    /**
     * Returns current iterator key
     *
     * @return integer
     */
    public function key()
    {
        return key($this->next);
    }

    /**
     * Go to next child
     */
    public function next()
    {
        return next($this->next);
    }

    /**
     * Rewind children
     */
    public function rewind()
    {
        return reset($this->next);
    }

    /**
     * Check if current position is valid
     *
     * @return boolean
     */
    public function valid()
    {
        return current($this->next) !== false;
    }

    /**
     * Returns the child action.
     *
     * @return \Zend\Pdf\Action\AbstractAction|null
     */
    public function getChildren()
    {
        return current($this->next);
    }

    /**
     * Implements RecursiveIterator interface.
     *
     * @return bool  whether container has any pages
     */
    public function hasChildren()
    {
        return count($this->next) > 0;
    }


    ////////////////////////////////////////////////////////////////////////
    //  Countable interface methods
    //////////////

    /**
     * count()
     *
     * @return int
     */
    public function count()
    {
        return count($this->childOutlines);
    }
}
