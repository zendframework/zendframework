<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_EventManager
 */

namespace ZendTest\Stdlib;

use Zend\EventManager\FilterChain;
use Zend\Stdlib\CallbackHandler;

/**
 * @category   Zend
 * @package    Zend_Stdlib
 * @subpackage UnitTests
 * @group      Zend_Stdlib
 */
class FilterChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FilterChain
     */
    protected $filterchain;

    public function setUp()
    {
        if (isset($this->message)) {
            unset($this->message);
        }
        $this->filterchain = new FilterChain;
    }

    public function testSubscribeShouldReturnCallbackHandler()
    {
        $handle = $this->filterchain->attach(array( $this, __METHOD__ ));
        $this->assertTrue($handle instanceof CallbackHandler);
    }

    public function testSubscribeShouldAddCallbackHandlerToFilters()
    {
        $handler  = $this->filterchain->attach(array($this, __METHOD__));
        $handlers = $this->filterchain->getFilters();
        $this->assertEquals(1, count($handlers));
        $this->assertTrue($handlers->contains($handler));
    }

    public function testDetachShouldRemoveCallbackHandlerFromFilters()
    {
        $handle = $this->filterchain->attach(array( $this, __METHOD__ ));
        $handles = $this->filterchain->getFilters();
        $this->assertTrue($handles->contains($handle));
        $this->filterchain->detach($handle);
        $handles = $this->filterchain->getFilters();
        $this->assertFalse($handles->contains($handle));
    }

    public function testDetachShouldReturnFalseIfCallbackHandlerDoesNotExist()
    {
        $handle1 = $this->filterchain->attach(array( $this, __METHOD__ ));
        $this->filterchain->clearFilters();
        $handle2 = $this->filterchain->attach(array( $this, 'handleTestTopic' ));
        $this->assertFalse($this->filterchain->detach($handle1));
    }

    public function testRetrievingAttachedFiltersShouldReturnEmptyArrayWhenNoFiltersExist()
    {
        $handles = $this->filterchain->getFilters();
        $this->assertEquals(0, count($handles));
    }

    public function testFilterChainShouldReturnLastResponse()
    {
        $this->filterchain->attach(function ($context, $params, $chain) {
            if (isset($params['string'])) {
                $params['string'] = trim($params['string']);
            }
            $return =  $chain->next($context, $params, $chain);
            return $return;
        });
        $this->filterchain->attach(function ($context, array $params) {
            $string = isset($params['string']) ? $params['string'] : '';
            return str_rot13($string);
        });
        $value = $this->filterchain->run($this, array('string' => ' foo '));
        $this->assertEquals(str_rot13(trim(' foo ')), $value);
    }

    public function testFilterIsPassedContextAndArguments()
    {
        $this->filterchain->attach(array( $this, 'filterTestCallback1' ));
        $obj = (object) array('foo' => 'bar', 'bar' => 'baz');
        $value = $this->filterchain->run($this, array('object' => $obj));
        $this->assertEquals('filtered', $value);
        $this->assertEquals('filterTestCallback1', $this->message);
        $this->assertEquals('foobarbaz', $obj->foo);
    }

    public function testInterceptingFilterShouldReceiveChain()
    {
        $this->filterchain->attach(array($this, 'filterReceivalCallback'));
        $this->filterchain->run($this);
    }

    public function testFilteringStopsAsSoonAsAFilterFailsToCallNext()
    {
        $this->filterchain->attach(function ($context, $params, $chain) {
            if (isset($params['string'])) {
                $params['string'] = trim($params['string']);
            }
            return $chain->next($context, $params, $chain);
        }, 10000);
        $this->filterchain->attach(function ($context, array $params) {
            $string = isset($params['string']) ? $params['string'] : '';
            return str_rot13($string);
        }, 1000);
        $this->filterchain->attach(function ($context, $params, $chain) {
            $string = isset($params['string']) ? $params['string'] : '';
            return hash('md5', $string);
        }, 100);
        $value = $this->filterchain->run($this, array('string' => ' foo '));
        $this->assertEquals(str_rot13(trim(' foo ')), $value);
    }

    public function handleTestTopic($message)
    {
        $this->message = $message;
    }

    public function filterTestCallback1($context, array $params)
    {
        $context->message = __FUNCTION__;
        if (isset($params['object']) && is_object($params['object'])) {
            $params['object']->foo = 'foobarbaz';
        }
        return 'filtered';
    }

    public function filterReceivalCallback($context, array $params, $chain)
    {
        $this->assertInstanceOf('Zend\EventManager\Filter\FilterIterator', $chain);
    }
}
