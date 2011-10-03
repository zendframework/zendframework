<?php
namespace ZendTest\Mvc\Router\Http;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Mvc\Router\Http\Segment;

class SegmentTest extends TestCase
{
    public function testEventManagerIsLazyLoaded()
    {
        $route = new Segment(array(
            'defaults' => array(),
            'route'    => '/:foo[/:bar]'
        ));
    }
}
