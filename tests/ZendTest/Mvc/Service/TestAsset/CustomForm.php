<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ZendTest\Mvc\Service\TestAsset;

use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class CustomForm extends Form
{
    public function __construct()
    {
        parent::__construct('test_form');

        $this->setAttribute('method', 'post')
             ->setHydrator(new ClassMethodsHydrator());

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit'
            )
        ));
    }
}
