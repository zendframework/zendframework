<?php

namespace ZendTest\View\Helper\TestAsset;

use Zend\I18n\Translator;

class ArrayTranslator implements Translator\Loader\LoaderInterface
{
    public $translations;

    public function load($filename, $locale)
    {
        $textDomain =  new Translator\TextDomain($this->translations);
        return $textDomain;
    }
}
