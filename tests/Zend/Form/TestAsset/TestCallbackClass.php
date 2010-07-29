<?php

namespace ZendTest\Form\TestAsset;

class TestCallbackClass
{
    public static function direct($content, $element, array $options)
    {
        $name  = $element->getName();
        $label = '';
        if (method_exists($element, 'getLabel')) {
            $label = $element->getLabel();
        }
        $html =<<<EOH
Item "$label": $name

EOH;
        return $html;
    }
}
