<?php

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
