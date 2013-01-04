<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Filter
 */

namespace ZendTest\Filter\Word;

use Zend\Filter\Word\DashToUnderscore as DashToUnderscoreFilter;

/**
 * Test class for Zend_Filter_Word_DashToUnderscore.
 *
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 */
class DashToUnderscoreTest extends \PHPUnit_Framework_TestCase
{
    public function testFilterSeparatesCamelCasedWordsWithDashes()
    {
        $string   = 'dash-separated-words';
        $filter   = new DashToUnderscoreFilter();
        $filtered = $filter($string);

        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('dash_separated_words', $filtered);
    }
}
