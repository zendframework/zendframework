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
use Zend\Form\Element;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use ZendTest\Form\TestAsset\Entity\Phone;

class PhoneFieldset extends Fieldset
{
    public function __construct()
    {
        parent::__construct('phones');

        $this->setHydrator(new ClassMethodsHydrator)
             ->setObject(new Phone());

        $id = new Element\Hidden('id');
        $this->add($id);

        $number = new Element\Text('number');
        $number->setLabel('Number')
               ->setAttribute('class', 'form-control');
        $this->add($number);
    }
}
