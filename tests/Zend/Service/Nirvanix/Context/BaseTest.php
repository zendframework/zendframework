<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\Nirvanix\Context;

use ZendTest\Service\Nirvanix\FunctionalTestCase;

/**
 * @category   Zend
 * @package    Zend_Service_Nirvanix
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_Nirvanix
 */
class BaseTest extends FunctionalTestCase
{
    public function testGetHttpClient()
    {
        $foo = $this->nirvanix->getService('Foo');
        $this->assertSame($this->httpClient, $foo->getHttpClient());
    }
}
