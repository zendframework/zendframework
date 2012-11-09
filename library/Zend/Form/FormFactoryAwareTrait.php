<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace Zend\Form;

use \Zend\Form\Factory;

/**
 * @catagory Zend
 * @package  Zend_Form
 */
trait FormFactoryAwareTrait
{
    /**
     * @var Factory
     */
    protected $factory = null;

    /**
     * setFormFactory
     *
     * @param Factory $factory
     * @return
     */
    public function setFormFactory(Factory $factory)
    {
        $this->factory = $factory;

        return $this;
    }
}
