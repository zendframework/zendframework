<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Filter\Word;

use Zend\Filter\Word\CamelCaseToUnderscore as CamelCaseToUnderscoreFilter;

/**
 * Test class for CamelCaseToUnderscoreFilter.
 *
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class CamelCaseToUnderscoreTest extends \PHPUnit_Framework_TestCase
{
    public function testFilterSeparatesCamelCasedWordsWithUnderscores()
    {
        $string   = 'CamelCasedWords';
        $filter   = new CamelCaseToUnderscoreFilter();
        $filtered = $filter($string);

        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('Camel_Cased_Words', $filtered);
    }

    public function testFilterSeperatingNumbersToUnterscore()
    {
        $string = 'PaTitle';
        $filter   = new CamelCaseToUnderscoreFilter();
        $filtered = $filter($string);

        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('Pa_Title', $filtered);

        $string = 'Pa2Title';
        $filter   = new CamelCaseToUnderscoreFilter();
        $filtered = $filter($string);

        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('Pa2_Title', $filtered);

        $string = 'Pa2aTitle';
        $filter   = new CamelCaseToUnderscoreFilter();
        $filtered = $filter($string);

        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('Pa2a_Title', $filtered);
    }
}
