<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mvc\Controller\Plugin\TestAsset;


use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class LinksFieldset extends Fieldset implements  InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('link');
        $this->add(array(
            'name' => 'foobar',
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
