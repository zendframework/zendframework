<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link           http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright      Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license        http://framework.zend.com/license/new-bsd New BSD License
 * @package        Zend_Service
 */

namespace ZendTest\Stdlib;

use Zend\Stdlib\Hydrator\Filter\FilterComposite;

class ValidationCompositeTest extends \PHPUnit_Framework_TestCase
{
    protected $validatorComposite;

    public function setUp()
    {
        $this->validatorComposite = new FilterComposite();
    }

    public function testValidationAdd()
    {
        $this->assertTrue($this->validatorComposite->filter("foo"));
        $this->validatorComposite->addFilter("has",
            function($property) {
                return false;
            }
        );
        $this->assertFalse($this->validatorComposite->filter("foo"));
    }

    public function testValidationRemove()
    {
        $this->validatorComposite->addFilter("has",
            function($property) {
                return false;
            }
        );
        $this->assertFalse($this->validatorComposite->filter("foo"));
        $this->validatorComposite->removeFilter("has");
        $this->assertTrue($this->validatorComposite->filter("foo"));
    }

    public function testValidationHas()
    {
        $this->validatorComposite->addFilter("has",
            function($property) {
                return false;
            }
        );
        $this->assertFalse($this->validatorComposite->filter("foo"));
        $this->assertTrue($this->validatorComposite->hasFilter("has"));
    }

    public function testComplexValidation()
    {
        $this->validatorComposite->addFilter("has", new \Zend\Stdlib\Hydrator\Filter\HasFilter());
        $this->validatorComposite->addFilter("get", new \Zend\Stdlib\Hydrator\Filter\GetFilter());
        $this->validatorComposite->addFilter("is", new \Zend\Stdlib\Hydrator\Filter\IsFilter());

        $this->validatorComposite->addFilter("exclude",
            function($property) {
                $method = substr($property, strpos($property, '::'));

                if ($method === 'getServiceLocator') {
                    return false;
                }

                return true;
            }, FilterComposite::CONDITION_AND
        );

        $this->assertTrue($this->validatorComposite->filter('getFooBar'));
        $this->assertFalse($this->validatorComposite->filter('getServiceLocator'));
    }
}
