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
     * Constructor
     */
    public function __construct()
    {
        $this->setCount(1);
        $this->setAllowAdd(true);
        $this->setTemplatePlaceholder('__index__');

        parent::__construct();
    }

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
                return $this;
            case 'targetelement':
                $this->setTargetElement($value);
                return $this;
            case 'allowadd':
                $this->setAllowAdd($value);
                return $this;
            case 'shouldcreatetemplate':
                $this->setShouldCreateTemplate($value);
                return $this;
            case 'templateplaceholder':
                $this->setTemplatePlaceholder($value);
                return $this;
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Populate values
     *
     * @param array|\Traversable $data
     */
    public function populateValues($data)
    {
        $count = $this->getCount();

        if ($this->getTargetElement() instanceof FieldsetInterface) {
            foreach ($data as $key => $value) {
                if ($count > 0) {
                    $this->fieldsets[$key]->populateValues($value);
                    unset($data[$key]);

                }

                $count--;
            }
        } else {
            foreach ($data as $key => $value) {
                if ($count > 0) {
                    $this->elements[$key]->setAttribute('value', $value);
                    unset($data[$key]);

                }

                $count--;
            }
        }

        // If there are still data, this means that elements or fieldsets were dynamically added. If allowed by the user, add them
        if (!empty($data) && $this->getAllowAdd()) {
            foreach ($data as $key => $value) {
                $elementOrFieldset = clone $this->getTargetElement();
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
        $this->attributes['count'] = $count > 0 ? $count : 0;
        return $this;
    }

    /**
     * Get the initial count of target element
     *
     * @return int
     */
    public function getCount()
    {
        return $this->getAttribute('count');
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

        $this->attributes['targetElement'] = $elementOrFieldset;

        return $this;
    }

    /**
     * Get target element
     *
     * @return ElementInterface|null
     */
    public function getTargetElement()
    {
        return $this->getAttribute('targetElement');
    }

    /**
     * Get allow add
     *
     * @param bool $allowAdd
     * @return Collection
     */
    public function setAllowAdd($allowAdd)
    {
        $this->attributes['allowAdd'] = (bool)$allowAdd;
        return $this;
    }

    /**
     * Get allow add
     *
     * @return bool
     */
    public function getAllowAdd()
    {
        return $this->getAttribute('allowAdd');
    }

    /**
     * If set to true, a template prototype is automatically added to the form to ease the creation of dynamic elements through JavaScript
     *
     * @param bool $shouldCreateTemplate
     * @return Collection
     */
    public function setShouldCreateTemplate($shouldCreateTemplate)
    {
        $this->attributes['shouldCreateTemplate'] = (bool)$shouldCreateTemplate;

        // If it doesn't exist yet, create it
        if ($shouldCreateTemplate && !$this->has($this->getTemplatePlaceholder())) {
            $this->addTemplateElement();
        }

        return $this;
    }

    /**
     * Get if the collection should create a template
     *
     * @return bool
     */
    public function shouldCreateTemplate()
    {
        return $this->getAttribute('shouldCreateTemplate');
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
            $this->attributes['templatePlaceholder'] = $templatePlaceholder;
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
        return $this->attributes['templatePlaceholder'];
    }

    /**
     * If both count and targetElement are set, add them to the fieldset
     *
     * @return void
     */
    protected function prepareCollection()
    {
        if ($this->getTargetElement() !== null) {
            for ($i = 0 ; $i != $this->getCount() ; ++$i) {
                $elementOrFieldset = clone $this->getTargetElement();
                $elementOrFieldset->setName($i);

                $this->add($elementOrFieldset);
            }

            // If a template is wanted, we add a "dummy" element
            if ($this->shouldCreateTemplate()) {
                $this->addTemplateElement();
            }
        }
    }

    /**
     * Add a "dummy" template element to be used with JavaScript
     *
     * @return Collection
     */
    protected function addTemplateElement()
    {
        if ($this->getTargetElement() !== null) {
            $elementOrFieldset = clone $this->getTargetElement();
            $elementOrFieldset->setName($this->getTemplatePlaceholder());
            $this->add($elementOrFieldset);
        }

        return $this;
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
        if ($this->shouldCreateTemplate()) {
            return array(
                $this->getTemplatePlaceholder() => array(
                    'required' => false
                )
            );
        }

        return array();
    }
}