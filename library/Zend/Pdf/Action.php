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

/** Zend_Pdf_ElementFactory */
require_once 'Zend/Pdf/ElementFactory.php';

/** Zend_Pdf_Target */
require_once 'Zend/Pdf/Target.php';


/**
 * Abstract PDF action representation class
 *
 * @package    Zend_Pdf
 * @subpackage Actions
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Pdf_Action extends Zend_Pdf_Target
{
	/**
	 * Action dictionary
	 *
	 * @var Zend_Pdf_Element_Dictionary|Zend_Pdf_Element_Object|Zend_Pdf_Element_Reference
	 */
	protected $_actionDictionary;

	/**
	 * A list of next actions in actions tree (used for actions chaining)
	 *
	 * @var SplObjectStorage  Contains Zend_Pdf_Action objects
	 */
	protected $_next;

	/**
	 * Parent object in Actions tree (or null if it's a root object)
	 *
	 * @var Zend_Pdf_Action
	 */
	protected $_parent;

	/**
     * Object constructor
     *
     * @param Zend_Pdf_Element_Dictionary $dictionary
     * @param Zend_Pdf_Action|null        $parentAction
     * @param SplObjectStorage            $processedActions  list of already processed action dictionaries, used to avoid cyclic references
     * @throws Zend_Pdf_Exception
	 */
	public function __construct(Zend_Pdf_Element $dictionary, $parentAction, SplObjectStorage $processedActions)
	{
        if ($dictionary->getType() != Zend_Pdf_Element::TYPE_DICTIONARY) {
            require_once 'Zend/Pdf/Exception.php';
            throw new Zend_Pdf_Exception('$dictionary mast be a direct or an indirect dictionary object.');
        }
        if ($parentAction !== null  &&  !$parentAction instanceof Zend_Pdf_Action) {
            require_once 'Zend/Pdf/Exception.php';
            throw new Zend_Pdf_Exception('Zend_Pdf_Action constructor $parentAction parameter must be a Zend_Pdf_Action object.');
        }

        $this->_actionDictionary = $dictionary;
		$this->_parent           = $parentAction;
        $this->_next             = new SplObjectStorage();

		if ($dictionary->Next !== null) {
			if ($dictionary->Next instanceof Zend_Pdf_Element_Dictionary) {
				// Check if dictionary object is not already processed
				if (!$processedActions->contains($dictionary->Next)) {
					$processedActions->attach($dictionary->Next);
					$this->_next->attach(Zend_Pdf_Action::load($dictionary->Next, $this, $processedActions));
				}
			} else if ($dictionary->Next instanceof Zend_Pdf_Element_Array) {
				foreach ($dictionary->Next->items as $chainedActionDictionary) {
					// Check if dictionary object is not already processed
					if (!$processedActions->contains($chainedActionDictionary)) {
                        $processedActions->attach($chainedActionDictionary);
                        $this->_next->attach(Zend_Pdf_Action::load($chainedActionDictionary, $this, $processedActions));
					}
				}
			} else {
                require_once 'Zend/Pdf/Exception.php';
                throw new Zend_Pdf_Exception('PDF Action dictionary Next entry must be a dictionary or an array.');
			}
		}
	}

	/**
	 * Load PDF action object using specified dictionary
	 *
     * @param Zend_Pdf_Element $dictionary (It's actually Dictionary or Dictionary Object or Reference to a Dictionary Object)
     * @param Zend_Pdf_Action  $parentAction
     * @param SplObjectStorage $processedActions  list of already processed action dictionaries, used to avoid cyclic references
	 * @return Zend_Pdf_Action
	 * @throws Zend_Pdf_Exception
	 */
	public static function load(Zend_Pdf_Element $dictionary, $parentAction = null, SplObjectStorage $processedActions = null)
	{
        if ($processedActions === null) {
            $processedActions = new SplObjectStorage();
        }

        if ($dictionary->getType() != Zend_Pdf_Element::TYPE_DICTIONARY) {
            require_once 'Zend/Pdf/Exception.php';
            throw new Zend_Pdf_Exception('$dictionary mast be a direct or an indirect dictionary object.');
        }
        if (isset($dictionary->Type)  &&  $dictionary->Type->value != 'Action') {
            require_once 'Zend/Pdf/Exception.php';
            throw new Zend_Pdf_Exception('Action dictionary Type entry must be set to \'Action\'.');
        }

        if ($dictionary->S === null) {
			require_once 'Zend/Pdf/Exception.php';
			throw new Zend_Pdf_Exception('Action dictionary must have S entry');
		}

		switch ($dictionary->S->value) {
			case 'GoTo':
				require_once 'Zend/Pdf/Action/GoTo.php';
				return new Zend_Pdf_Action_GoTo($dictionary, $parentAction, $processedActions);
				brake;

            case 'GoToR':
                require_once 'Zend/Pdf/Action/GoToR.php';
                return new Zend_Pdf_Action_GoToR($dictionary, $parentAction, $processedActions);
                brake;

            case 'GoToE':
                require_once 'Zend/Pdf/Action/GoToE.php';
                return new Zend_Pdf_Action_GoToE($dictionary, $parentAction, $processedActions);
                brake;

            case 'Launch':
                require_once 'Zend/Pdf/Action/Launch.php';
                return new Zend_Pdf_Action_Launch($dictionary, $parentAction, $processedActions);
                brake;

            case 'Thread':
                require_once 'Zend/Pdf/Action/Thread.php';
                return new Zend_Pdf_Action_Thread($dictionary, $parentAction, $processedActions);
                brake;

            case 'URI':
                require_once 'Zend/Pdf/Action/URI.php';
                return new Zend_Pdf_Action_URI($dictionary, $parentAction, $processedActions);
                brake;

            case 'Sound':
                require_once 'Zend/Pdf/Action/Sound.php';
                return new Zend_Pdf_Action_Sound($dictionary, $parentAction, $processedActions);
                brake;

            case 'Movie':
                require_once 'Zend/Pdf/Action/Movie.php';
                return new Zend_Pdf_Action_Movie($dictionary, $parentAction, $processedActions);
                brake;

            case 'Hide':
                require_once 'Zend/Pdf/Action/Hide.php';
                return new Zend_Pdf_Action_Hide($dictionary, $parentAction, $processedActions);
                brake;

            case 'Named':
                require_once 'Zend/Pdf/Action/Named.php';
                return new Zend_Pdf_Action_Named($dictionary, $parentAction, $processedActions);
                brake;

            case 'SubmitForm':
                require_once 'Zend/Pdf/Action/SubmitForm.php';
                return new Zend_Pdf_Action_SubmitForm($dictionary, $parentAction, $processedActions);
                brake;

            case 'ResetForm':
                require_once 'Zend/Pdf/Action/ResetForm.php';
                return new Zend_Pdf_Action_ResetForm($dictionary, $parentAction, $processedActions);
                brake;

            case 'ImportData':
                require_once 'Zend/Pdf/Action/ImportData.php';
                return new Zend_Pdf_Action_ImportData($dictionary, $parentAction, $processedActions);
                brake;

            case 'JavaScript':
                require_once 'Zend/Pdf/Action/JavaScript.php';
                return new Zend_Pdf_Action_JavaScript($dictionary, $parentAction, $processedActions);
                brake;

            case 'SetOCGState':
                require_once 'Zend/Pdf/Action/SetOCGState.php';
                return new Zend_Pdf_Action_SetOCGState($dictionary, $parentAction, $processedActions);
                brake;

            case 'Rendition':
                require_once 'Zend/Pdf/Action/Rendition.php';
                return new Zend_Pdf_Action_Rendition($dictionary, $parentAction, $processedActions);
                brake;

            case 'Trans':
                require_once 'Zend/Pdf/Action/Trans.php';
                return new Zend_Pdf_Action_Trans($dictionary, $parentAction, $processedActions);
                brake;

            case 'GoTo3DView':
                require_once 'Zend/Pdf/Action/GoTo3DView.php';
                return new Zend_Pdf_Action_GoTo3DView($dictionary, $parentAction, $processedActions);
                brake;

            default:
                require_once 'Zend/Pdf/Action/Unknown.php';
                return new Zend_Pdf_Action_Unknown($dictionary, $parentAction, $processedActions);
                brake;
		}
	}

	/**
	 * Extract action from the chain
	 *
	 * Returns root of the updated actions tree
	 *
	 * @return Zend_Pdf_Action
	 */
	public function extract()
	{
		if (($parent = $this->_parent) !== null) {
			$parent->_next->detach($this);

            foreach ($this->_next as $chainedAction) {
            	$parent->_next->attach($chainedAction);
            	$chainedAction->_parent = $parent;
            }

            $this->_parent = null;
            $this->_next   = new SplObjectStorage();

            return $parent->getRoot();
		} else {
			// This is a root node. Treat first subaction as a new root

			if ($this->_next->count() == 0) {
				// There is no any action in a tree now
				return null;
			}

			$this->_next->rewind();
			$root = $this->_next->current();
			$this->_next->detach($root);

			$root->_parent = null;

			foreach ($this->_next as $chainedAction) {
				$root->_next->attach($chainedAction);
				$chainedAction->_parent = $root;
			}

            $this->_next = new SplObjectStorage();

			return $root;
		}
	}

	/**
	 * Destroy actions subtree
	 *
	 * Method has to be used to clean up resources after actions tree usage
	 * since PHP doesn't do it automatically for objects with cyclic references
	 */
	public function clean()
	{
		$this->_parent = null;

		foreach ($this->_next as $chainedAction) {
			$chainedAction->clean();
		}

		$this->_next = new SplObjectStorage();
	}

	/**
	 * Get root of actions tree
	 *
	 * @return Zend_Pdf_Action
	 */
	public function getRoot()
	{
		$root = $this;
		while ($root->_parent !== null) {
			$root = $root->_parent;
		}
		return $root;
	}

	/**
	 * Return all subactions including this one
	 *
	 * @return SplObjectStorage
	 */
	public function getAllActions()
	{
		$actions = new SplObjectStorage();

		$actions->attach($this);

		foreach ($this->_next as $chainedAction) {
			/** @todo Change  to $actions->addAll($subAction->allActions()) when PHP 5.3.0+ is required for ZF */
			foreach ($chainedAction->getAllActions() as $action) {
				$actions->attach($action);
			}
		}

		return $actions;
	}

    /**
     * Get handler
     *
     * @param string $property
     * @return Zend_Pdf_Element | null
     */
    public function __get($property)
    {
        return $this->_actionDictionary->$property;
    }

    /**
     * Set handler
     *
     * @param string $property
     * @param  mixed $value
     */
    public function __set($item, $value)
    {
        $this->_actionDictionary->$property = $value;
    }

    /**
     * Attach chained action
     *
     * @param Zend_Pdf_Action $action
     */
    public function attach(Zend_Pdf_Action $action)
    {
    	$this->_next->attach($action);
    	$action->_parent = $this;
    }

    /**
     * Rebuild PDF dictionaries corresponding to the current tree structure
     */
    public function rebuildSubtree()
    {
    	switch (count($this->_next)) {
    		case 0:
    			$this->_actionDictionary->Next = null;
    			break;

    		case 1:
    			$this->_next->rewind();
    			$chainedAction = $this->_next->current();
                $chainedAction->rebuildSubtree();
    			$this->_actionDictionary->Next = $chainedAction->_actionDictionary;
    			break;

    		default:
    			$nextArray = new Zend_Pdf_Element_Array();
    			foreach ($this->_next as $chainedAction) {
                    $chainedAction->rebuildSubtree();
    				$nextArray->items[] = $chainedAction->_actionDictionary;
    			}
    			$this->_actionDictionary->Next = $nextArray;
    			break;
    	}
    }

    /**
     * Get resource
     *
     * @internal
     * @return Zend_Pdf_Element
     */
    public function getResource()
    {
    	return $this->_actionDictionary;
    }
}
