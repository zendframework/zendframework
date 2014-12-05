<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Filter;

use Zend\Filter\Whitelist as WhitelistFilter;

/**
 * @group      Zend_Filter
 */
class WhitelistTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorOptions()
    {
        $filter = new WhitelistFilter(array(
            'type'    => WhitelistFilter::TYPE_BLACKLIST,
            'list'    => array('test', 1),
        ));

        $this->assertEquals(WhitelistFilter::TYPE_BLACKLIST, $filter->getType());
        $this->assertEquals($filter->getList(), array('test', 1));
    }

    public function testConstructorDefaults()
    {
        $filter = new WhitelistFilter();

        $this->assertEquals(WhitelistFilter::TYPE_WHITELIST, $filter->getType());
        $this->assertEquals($filter->getList(), array());
    }

    /**
     * @param mixed $value
     * @param bool  $expected
     * @dataProvider defaultTestProvider
     */
    public function testDefault($value, $expected)
    {
        $filter = new WhitelistFilter();
        $this->assertSame($expected, $filter->filter($value));
    }

    /**
     * @param int $type
     * @param array $testData
     * @dataProvider typeTestProvider
     */
    public function testTypes($type, $testData)
    {
        $filter = new WhitelistFilter(array(
            'type' => $type
        ));
        foreach ($testData as $data) {
            list($value, $expected) = $data;
            $message = sprintf(
                '%s (%s) is not filtered as %s; type = %s',
                var_export($value, true),
                gettype($value),
                var_export($expected, true),
                $type
            );
            $this->assertSame($expected, $filter->filter($value), $message);
        }
    }

    public function testSettingFalseType()
    {
        $filter = new WhitelistFilter();
        $this->setExpectedException('\Zend\Filter\Exception\InvalidArgumentException', 'Unknown type value');
        $filter->setType(false);
    }

    public function testSettingTypeByName()
    {
        $filter = new WhitelistFilter(array(
            'type' => 'blacklist',
        ));
        $this->assertEquals(WhitelistFilter::TYPE_BLACKLIST, $filter->getType());
    }

    public function testGettingDefaultType()
    {
        $filter = new WhitelistFilter();
        $this->assertEquals(1, $filter->getType());
    }

    public static function defaultTestProvider()
    {
        return array(
            array('test', null),
            array(0, null),
            array(0.1, null),
            array(array(), null),
            array(null, null),
        );
    }

    public static function typeTestProvider()
    {
        return array(
            array('blacklist', array(
                array('test', 'test'),
                array(0, 0),
                array(0.1, 0.1),
                array(array(), array()),
                array(null, null),
            )),
            array('whitelist', array(
                array('test', null),
                array(0, null),
                array(0.1, null),
                array(array(), null),
                array(null, null),
            )),
        );
    }

}
