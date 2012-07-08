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
use Zend\Stdlib\PriorityQueue;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Collection extends Fieldset
{
    /**
     * Default template placeholder
     */
    const DEFAULT_TEMPLATE_PLACEHOLDER = '__index__';

    /**
     * Element used in the collection
     *
     * @var ElementInterface
     */
    protected $targetElement;

    /**
     * Initial count of target element
     *
     * @var int
     */
    protected $count = 1;

    /**
     * Are new elements allowed to be added dynamically ?
     *
     * @var bool
     */
    protected $allowAdd = true;

    /**
     * Is the template generated ?
     *
     * @var bool
     */
    protected $shouldCreateTemplate = false;

    /**
     * Placeholder used in template content for making your life easier with JavaScript
     *
     * @var string
     */
    protected $templatePlaceholder = self::DEFAULT_TEMPLATE_PLACEHOLDER;

    /**
     * Element used as a template
     *
     * @var ElementInterface|FieldsetInterface
     */
    protected $templateElement;


    /**
     * Accepted options for Collection:
     * - target_element: an array or element used in the collection
     * - count: number of times the element is added initially
     * - allow_add: if set to true, elements can be added to the form dynamically (using JavaScript)
     * - should_create_template: if set to true, a template is generated (inside a <span>)
     * - template_placeholder: placeholder used in the data template
     *
     * @param array|\Traversable $options
     * @return Collection
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($options['target_element'])) {
            $this->setTargetElement($options['target_element']);
        }

        if (isset($options['count'])) {
            $this->setCount($options['count']);
        }

        if (isset($options['allow_add'])) {
            $this->setAllowAdd($options['allow_add']);
        }

        if (isset($options['should_create_template'])) {
            $this->setShouldCreateTemplate($options['should_create_template']);
        }

        if (isset($options['template_placeholder'])) {
            $this->setTemplatePlaceholder($options['template_placeholder']);
        }

        return $this;
    }

    /**
     * Populate values
     *
     * @param array|\Traversable $data
     */
    public function populateValues($data)
    {
        if ($this->targetElement instanceof FieldsetInterface) {
            foreach ($this->byName as $name => $fieldset) {
                $fieldset->populateValues($data[$name]);
                unset($data[$name]);
            }
        } else {
            foreach ($this->byName as $name => $element) {
                $element->setAttribute('value', $data[$name]);
                unset($data[$name]);
            }
        }

        // If there are still data, this means that elements or fieldsets were dynamically added. If allowed by the user, add them
        if (!empty($data) && $this->allowAdd) {
            foreach ($data as $key => $value) {
                $elementOrFieldset = $this->createNewTargetElementInstance();
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
        $this->count = $count > 0 ? $count : 0;
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
     * @param ElementInterface|array|Traversable $elementOrFieldset
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
     * @return ElementInterface|null
     */
    public function getTargetElement()
    {
        return $this->targetElement;
    }

    /**
     * Get allow add
     *
     * @param bool $allowAdd
     * @return Collection
     */
    public function setAllowAdd($allowAdd)
    {
        $this->allowAdd = (bool)$allowAdd;
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
     * If set to true, a template prototype is automatically added to the form to ease the creation of dynamic elements through JavaScript
     *
     * @param bool $shouldCreateTemplate
     * @return Collection
     */
    public function setShouldCreateTemplate($shouldCreateTemplate)
    {
        $this->shouldCreateTemplate = (bool)$shouldCreateTemplate;

        return $this;
    }

    /**
     * Get if the collection should create a template
     *
     * @return bool
     */
    public function shouldCreateTemplate()
    {
        return $this->shouldCreateTemplate;
    }

    /**
     * Set the placeholder used in the template generated to help create new elements in JavaScript
     *
     * @param string $templatePlaceholder
     * @return Collection
     */
    public function setTemplatePlaceholder($templatePlaceholder)
    {
        if (is_string($templatePlaceholder)) {
            $this->templatePlaceholder = $templatePlaceholder;
        }

        return $this;
    }

    /**
     * Get the template placeholder
     *
     * @return string
     */
    public function getTemplatePlaceholder()
    {
        return $this->templatePlaceholder;
    }

    /**
     * Get a template element used for rendering purposes only
     *
     * @return null|ElementInterface|FieldsetInterface
     */
    public function getTemplateElement()
    {
        if ($this->templateElement === null) {
            $this->templateElement = $this->createTemplateElement();
        }

        return $this->templateElement;
    }

    /**
     * Prepare the collection by adding a dummy template element if the user want one
     *
     * @param Form $form
     * @return mixed|void
     */
    public function prepareElement(Form $form)
    {
        // Create a template that will also be prepared
        if ($this->shouldCreateTemplate) {
            $templateElement = $this->getTemplateElement();
            $this->add($templateElement);
        }

        parent::prepareElement($form);

        // The template element has been prepared, but we don't want it to be rendered nor validated, so remove it from the list
        if ($this->shouldCreateTemplate) {
            $this->remove($this->templatePlaceholder);
        }
    }

    /**
     * If both count and targetElement are set, add them to the fieldset
     *
     * @return void
     */
    protected function prepareCollection()
    {
        if ($this->targetElement !== null) {
            for ($i = 0 ; $i != $this->count ; ++$i) {
                $elementOrFieldset = $this->createNewTargetElementInstance();
                $elementOrFieldset->setName($i);

                $this->add($elementOrFieldset);
            }
        }
    }

    /**
     * Create a new instance of the target element
     *
     * @return ElementInterface
     */
    protected function createNewTargetElementInstance()
    {
        return clone $this->targetElement;
    }

    /**
     * Create a dummy template element
     *
     * @return null|ElementInterface|FieldsetInterface
     */
    protected function createTemplateElement()
    {
        if (!$this->shouldCreateTemplate) {
            return null;
        }

        if ($this->templateElement) {
            return $this->templateElement;
        }

        $elementOrFieldset = $this->createNewTargetElementInstance();
        $elementOrFieldset->setName($this->templatePlaceholder);

        return $elementOrFieldset;
    }
}
