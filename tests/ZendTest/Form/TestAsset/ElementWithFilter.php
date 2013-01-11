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
use Zend\InputFilter\InputProviderInterface;

class ElementWithFilter extends Element implements InputProviderInterface
{
    public function getInputSpecification()
    {
        return array(
            'name' => $this->getName(),
            'required' => true,
            'filters' => array(
                array('name' => 'Zend\Filter\StringTrim'),
            ),
            'validators' => array(
                array('name' => 'Zend\Validator\NotEmpty'),
                array('name' => 'Zend\I18n\Validator\Alnum'),
            ),
        );
    }
}
