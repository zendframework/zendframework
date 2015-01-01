<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form\TestAsset;

class ProductCategoriesFieldset extends ProductFieldset
{
    public function __construct()
    {
        parent::__construct();

        $template = new CategoryFieldset();

        $this->add(array(
            'name' => 'categories',
            'type' => 'collection',
            'options' => array(
                'label' => 'Categories',
                'should_create_template' => true,
                'allow_add' => true,
                'count' => 0,
                'target_element' => $template,
            ),
        ));
    }
}
