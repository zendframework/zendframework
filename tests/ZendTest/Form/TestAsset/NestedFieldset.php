<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ZendTest\Form\TestAsset;

use Zend\Form\Element;
use Zend\Form\Fieldset;

class NestedFieldset extends Fieldset implements \Zend\InputFilter\InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('nested_fieldset');

        $field = new Element('anotherField', array('label' => 'Name'));
        $field->setAttribute('type', 'text');

        $this->add($field);
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            'anotherField' => array(
                'required' => true
            )
        );
    }
}
