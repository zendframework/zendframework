<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Filter;

use Zend\Filter\FilterPluginManager;

/**
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
        $this->assertInstanceOf('Zend\Filter\ToInt', $filter);
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

    /**
     * @group 7169
     */
    public function testFilterSuccessfullyConstructed()
    {
        $search_separator = ';';
        $replacement_separator = '|';
        $options = array(
            'search_separator'      => $search_separator,
            'replacement_separator' => $replacement_separator,
        );
        $filter = $this->filters->get('wordseparatortoseparator', $options);
        $this->assertInstanceOf('Zend\Filter\Word\SeparatorToSeparator', $filter);
        $this->assertEquals(';', $filter->getSearchSeparator());
        $this->assertEquals('|', $filter->getReplacementSeparator());
    }

    /**
     * @group 7169
     */
    public function testFiltersConstructedAreDifferent()
    {
        $filterOne = $this->filters->get(
            'wordseparatortoseparator',
            array(
                'search_separator'      => ';',
                'replacement_separator' => '|',
            )
        );
        $filterTwo = $this->filters->get(
            'wordseparatortoseparator',
            array(
                'search_separator'      => '.',
                'replacement_separator' => ',',
            )
        );

        $this->assertNotEquals($filterOne, $filterTwo);
    }
}
