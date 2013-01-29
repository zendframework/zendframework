<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Stdlib
 */

namespace ZendTest\Stdlib\TestAsset;

use Zend\Stdlib\Hydrator\Filter\FilterComposite;
use Zend\Stdlib\Hydrator\Filter\FilterProviderInterface;
use Zend\Stdlib\Hydrator\Filter\MethodMatchFilter;
use Zend\Stdlib\Hydrator\Filter\GetFilter;

class ClassMethodsFilterProviderInterface implements FilterProviderInterface
{
    public function getBar()
    {
        return "foo";
    }

    public function getFoo()
    {
        return "bar";
    }

    public function isScalar($foo)
    {
        return false;
    }

    public function hasFooBar()
    {
        return true;
    }

    public function getServiceManager()
    {
        return "servicemanager";
    }

    public function getEventManager()
    {
        return "eventmanager";
    }

    public function getFilter()
    {
        $filterComposite = new FilterComposite();

        $filterComposite->addFilter("get", new GetFilter());
        $excludes = new FilterComposite();
        $excludes->addFilter(
            "servicemanager",
            new MethodMatchFilter("getServiceManager"),
            FilterComposite::CONDITION_AND
        );
        $excludes->addFilter(
            "eventmanager",
            new MethodMatchFilter("getEventManager"),
            FilterComposite::CONDITION_AND
        );
        $filterComposite->addFilter("excludes", $excludes, FilterComposite::CONDITION_AND);

        return $filterComposite;
    }
}
