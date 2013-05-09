<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Text
 */

namespace ZendTest\Text;

use Zend\Text;

/**
 * @category   Zend
 * @package    Zend_Text
 * @subpackage UnitTests
 * @group      Zend_Text
 */
class MultiByteTest extends \PHPUnit_Framework_TestCase
{
    public function testWordWrapTriggersDeprecatedError()
    {
        $this->setExpectedException('PHPUnit_Framework_Error_Deprecated');
        $line = Text\MultiByte::wordWrap('äbüöcß', 2, ' ', true);
    }

    public function testStrPadTriggersDeprecatedError()
    {
        $this->setExpectedException('PHPUnit_Framework_Error_Deprecated');
        $text = Text\MultiByte::strPad('äääöö', 2, 'ö');
    }
}
