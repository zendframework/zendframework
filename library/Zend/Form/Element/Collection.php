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
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form\Element;

use Traversable;
use Zend\Form\Element;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;
use Zend\Form\Fieldset;
use Zend\Form\FieldsetInterface;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Collection extends Fieldset implements InputFilterProviderInterface
{
    /**
     * Initial count of target element
     *
     * @var int
     */
    protected $count;

    /**
     * Element or fieldset that constitute one value of a collection
     *
     * @var ElementInterface
     */
    protected $targetElement;

    /**
     * If set to true, new elements (e.g. added by JavaScript) will be returned
     *
     * @var boolean
     */
    protected $allowAdd = true;

    /**
     * If set to true, a template prototype is automatically added to the form to ease the creation of dynamic elements through JavaScript
     *
     * @var boolean
     */
    protected $createTemplate = false;

    /**
     * Set a single element attribute
     *
     * @param string $key
     * @param mixed $value
     * @return Element|ElementInterface
     */
    public function setAttribute($key, $value)
    {
        switch(strtolower($key)) {
            case 'count':
                $this->setCount($value);
                break;
            case 'targetelement':
                $this->setTargetElement($value);
                break;
            case 'allowadd':
                $this->setAllowAdd($value);
                break;
            case 'createtemplate':
                $this->setCreateTemplate($value);
                break;
            default:
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * @param array|\Traversable $data
     */
    public function populateValues($data)
    {
        $count = $this->count;

        if ($this->targetElement instanceof FieldsetInterface) {
            foreach ($data as $key => $value) {
                if ($count-- > 0) {
                    $this->fieldsets[$key]->populateValues($value);
                    unset($data[$key]);
                }
            }
        } else {
            foreach ($data as $key => $value) {
                if ($count-- > 0) {
                    $this->elements[$key]->setAttribute('value', $value);
                    unset($data[$key]);
                }
            }
        }

        // If there are still data, this means that elements or fieldsets were dynamically added. If allowed by the user, add them
        if (!empty($data) && $this->allowAdd) {
            foreach ($data as $key => $value) {
                $elementOrFieldset = $this->createNewTargetElement();
                $elementOrFieldset->setName($key);

                if ($elementOrFieldset instanceof FieldsetInterface) {
                    $elementOrFieldset->populateValues($value);
                } else {
                    $elementOrFieldset->setAttribute('value', $value);
                }

                $this->add($elementOrFieldset);
            }
        }
    }

    /**
     * Set the initial count of target element
     *
     * @param $count
     * @return Collection
     */
    public function setCount($count)
    {
        $this->count = ($count > 0 ? $count : 0);

        return $this;
    }

    /**
     * Get the initial count of target element
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set the target element
     *
     * @param ElementInterface|string $elementOrFieldset
     * @return Collection
     * @throws \Zend\Form\Exception\InvalidArgumentException
     */
    public function setTargetElement($elementOrFieldset)
    {
        if (is_array($elementOrFieldset)
            || ($elementOrFieldset instanceof Traversable && !$elementOrFieldset instanceof ElementInterface)
        ) {
            $factory = $this->getFormFactory();
            $elementOrFieldset = $factory->create($elementOrFieldset);
        }

        if (!$elementOrFieldset instanceof ElementInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s requires that $elementOrFieldset be an object implementing %s; received "%s"',
                __METHOD__,
                __NAMESPACE__ . '\ElementInterface',
                (is_object($elementOrFieldset) ? get_class($elementOrFieldset) : gettype($elementOrFieldset))
            ));
        }

        $this->targetElement = $elementOrFieldset;

        return $this;
    }

    /**
     * Get target element
     *
     * @return \Zend\Form\ElementInterface
     */
    public function getTargetElement()
    {
        return $this->targetElement;
    }

    /**
     * Get allow add
     *
     * @param $allowAdd
     * @return Collection
     */
    public function setAllowAdd($allowAdd)
    {
        $this->allowAdd = $allowAdd;
        return $this;
    }

    /**
     * Get allow add
     *
     * @return bool
     */
    public function getAllowAdd()
    {
        return $this->allowAdd;
    }

    /**
     * @param $createTemplate
     * @return Collection
     */
    public function setCreateTemplate($createTemplate)
    {
        $this->createTemplate = $createTemplate;
        return $this;
    }

    /**
     * @param $createTemplate
     * @return bool
     */
    public function getCreateTemplate()
    {
        return $this->createTemplate;
    }

    /**
     * If both count and targetElement are set, add them to the fieldset
     *
     * @return void
     */
    protected function prepareCollection()
    {
        if ($this->count !== null && $this->targetElement !== null) {
            for ($i = 0 ; $i != $this->count ; ++$i) {
                $elementOrFieldset = clone $this->targetElement; //$this->createNewTargetElement();
                $elementOrFieldset->setName($i);

                $this->add($elementOrFieldset);
            }

            // If a template is wanted, we add a "dummy" element
            if ($this->createTemplate) {
                $elementOrFieldset = clone $this->targetElement; //$this->createNewTargetElement();
                $elementOrFieldset->setName('__index__');
                $elementOrFieldset->setAttribute('template', true);

                $this->add($elementOrFieldset);
            }
        }
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        // Ignore any template
        if ($this->createTemplate) {
            return array(
                '__index__' => array(
                    'required' => false
                )
            );
        }

        return array();
    }
}