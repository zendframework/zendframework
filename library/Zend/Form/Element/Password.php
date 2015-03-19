<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form\Element;

use Zend\Form\Element;
use Zend\Form\ElementPrepareAwareInterface;
use Zend\Form\FieldsetInterface;

class Password extends Element implements ElementPrepareAwareInterface
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'password',
    );

    /**
     * Remove the password before rendering if the form fails in order to avoid any security issue
     *
     * @param  FieldsetInterface $form
     * @return mixed
     */
    public function prepareElement(FieldsetInterface $form)
    {
        $this->setValue('');
    }
}
