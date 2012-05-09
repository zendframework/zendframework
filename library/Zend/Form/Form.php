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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form;

use Traversable;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputProviderInterface;

/**
 * @category   Zend
 * @package    Zend_Form
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Form extends BaseForm implements FormFactoryAwareInterface
{
    /**
     * @var Factory
     */
    protected $factory;

    /**
     * Compose a form factory to use when calling add() with a non-element/fieldset
     * 
     * @param  Factory $factory 
     * @return Form
     */
    public function setFormFactory(Factory $factory)
    {
        $this->factory = $factory;
        return $this;
    }

    /**
     * Retrieve composed form factory
     *
     * Lazy-loads one if none present.
     * 
     * @return Factory
     */
    public function getFormFactory()
    {
        if (null === $this->factory) {
            $this->setFormFactory(new Factory());
        }
        return $this->factory;
    }

    /**
     * Add an element or fieldset
     *
     * If $elementOrFieldset is an array or Traversable, passes the argument on
     * to the composed factory to create the object before attaching it.
     *
     * $flags could contain metadata such as the alias under which to register 
     * the element or fieldset, order in which to prioritize it, etc.
     * 
     * @param  array|Traversable|ElementInterface $elementOrFieldset 
     * @param  array $flags 
     * @return Form
     */
    public function add($elementOrFieldset, array $flags = array())
    {
        if (is_array($elementOrFieldset) 
            || ($elementOrFieldset instanceof Traversable && !$elementOrFieldset instanceof ElementInterface)
        ) {
            $factory = $this->getFormFactory();
            $elementOrFieldset = $factory->create($elementOrFieldset);
        }
        return parent::add($elementOrFieldset, $flags);
    }

    /**
     * Retrieve input filter used by this form.
     *
     * Attaches defaults from attached elements, if no corresponding input
     * exists for the given element in the input filter.
     * 
     * @return InputFilterInterface
     */
    public function getInputFilter()
    {
        $filter = parent::getInputFilter();
        if ($filter instanceof InputFilterInterface) {
            $this->attachInputFilterDefaults($filter);
        }
        return $this->filter;
    }

    /**
     * Attach defaults provided by the elements to the input filter
     * 
     * @param  InputFilterInterface $inputFilter 
     * @return void
     */
    public function attachInputFilterDefaults(InputFilterInterface $inputFilter)
    {
        $formFactory  = $this->getFormFactory();
        $inputFactory = $formFactory->getInputFilterFactory();
        foreach ($this->getElements() as $element) {
            if (!$element instanceof InputProviderInterface) {
                // only interested in the element if it provides input information
                continue;
            }

            $name = $element->getName();
            if ($inputFilter->has($name)) {
                // if we already have an input by this name, use it
                continue;
            }

            // Create an input based on the specification returned from the element
            $spec  = $element->getInputSpecification();
            $input = $inputFactory->createInput($spec);
            $inputFilter->add($input, $name);
        }
    }
}
