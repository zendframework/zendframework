<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Helper;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\View\Helper\Plural as PluralHelper;

/**
 * Test class for Zend_View_Helper_Json
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class PluralTest extends TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->helper = new PluralHelper();
    }

    public function testVerifySingularString()
    {
        $result = $this->helper->__invoke(1, 'one car', 'two cars');
        $this->assertEquals('one car', $result);
    }

    public function testVerifyPluralString()
    {
        $result = $this->helper->__invoke(2, 'one car', 'two cars');
        $this->assertEquals('two cars', $result);
    }
}
