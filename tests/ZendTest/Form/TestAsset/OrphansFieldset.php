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

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ArraySerializable;
use ZendTest\Form\TestAsset\Entity\Orphan;

class OrphansFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct( $name = null, $options = array() )
    {
        parent::__construct( $name, $options );

        $this->setHydrator(new ArraySerializable())
                ->setObject(new Orphan());

        $this->add(array(
                        'name' => 'name',
                        'options' => array('label' => 'Name field'),
                   ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'name' => array(
                'required' => false,
                'filters' => array(),
                'validators' => array(),
            )
        );
    }
}
