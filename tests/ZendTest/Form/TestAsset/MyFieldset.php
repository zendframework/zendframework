<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form\TestAsset;

use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Fieldset;

class MyFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('my-fieldset');
        $this->add(array(
            'type' => 'Email',
            'name' => 'email',
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'email' => array(
                'required' => false,
            ),
        );
    }
}
