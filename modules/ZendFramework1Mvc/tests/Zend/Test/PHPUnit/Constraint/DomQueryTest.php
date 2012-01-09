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
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Test\PHPUnit\Constraint;
use Zend\Test\PHPUnit\Constraint;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Test
 * @group      Zend_Test_PHPUnit
 */
class DomQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group ZF-4010
     */
    public function testShouldAllowMatchingOfAttributeValues()
    {
        $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <title>ZF Issue ZF-4010</title>
    </head>
    <body>
        <form>
            <fieldset id="fieldset-input"><legend>Inputs</legend>
                <ol>
                    <li><input type="text" name="input[0]" id="input-0" value="value1" /></li>
                    <li><input type="text" name="input[1]" id="input-1" value="value2" /></li>
                    <li><input type="text" name="input[2]" id="input-2" value="" /></li>
                </ol>
            </fieldset>
        </form>
    </body>
</html>';
        $assertion = new Constraint\DomQuery('input#input-0 @value');
        $result = $assertion->evaluate($html,
            Constraint\DomQuery::ASSERT_CONTENT_CONTAINS, 'value1');
        $this->assertTrue($result);
    }
}
