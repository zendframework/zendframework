<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mail\Header;

use Zend\Mail\Header;

/**
 * This test is primarily to test that AbstractAddressList headers perform
 * header folding and MIME encoding properly.
 *
 * @group      Zend_Mail
 */
class ToTest extends \PHPUnit_Framework_TestCase
{
    public function testHeaderFoldingOccursProperly()
    {
        $header = new Header\To();
        $list   = $header->getAddressList();
        for ($i = 0; $i < 10; $i++) {
            $list->add($i . '@zend.com');
        }
        $string = $header->getFieldValue();
        $emails = explode("\r\n ", $string);
        $this->assertEquals(10, count($emails));
    }
}
