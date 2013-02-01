<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Filter
 */

namespace ZendTest\Filter;

use Zend\Filter\FilterPluginManager;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 */
class FilterPluginManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->filters = new FilterPluginManager();
    }

    public function testFilterSuccessfullyRetrieved()
    {
        $filter = $this->filters->get('int');
        $this->assertInstanceOf('Zend\Filter\Int', $filter);
    }

    public function testRegisteringInvalidFilterRaisesException()
    {
        $this->setExpectedException('Zend\Filter\Exception\RuntimeException');
        $this->filters->setService('test', $this);
    }

    public function testLoadingInvalidFilterRaisesException()
    {
        $this->filters->setInvokableClass('test', get_class($this));
        $this->setExpectedException('Zend\Filter\Exception\RuntimeException');
        $this->filters->get('test');
    }
}
