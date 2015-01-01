<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form\TestAsset;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class FileInputFilterProviderFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->add(array(
            'type' => 'file',
            'name' => 'file_field',
            'options' => array(
                'label' => 'File Label',
            ),
        ));

    }

    public function getInputFilterSpecification()
    {
        return array(
            'file_field' => array(
                'type'     => 'Zend\InputFilter\FileInput',
                'filters'  => array(
                    array(
                        'name' => 'filerenameupload',
                        'options' => array(
                            'target'    => __FILE__,
                            'randomize' => true,
                        ),
                    ),
                ),
            ),
        );
    }
}
