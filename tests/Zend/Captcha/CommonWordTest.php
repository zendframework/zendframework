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
 * @package    Zend_Captcha
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Captcha;

/**
 * @category   Zend
 * @package    Zend_Captcha
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Captcha
 */
abstract class CommonWordTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Word adapter class name
     *
     * @var string
     */
    protected $wordClass;

    public function testLoadInvalidSessionClass()
    {
        try {
            $wordAdapter = new $this->wordClass;
            $wordAdapter->setSessionClass('ZendTest\Captcha\InvalidClassName');
            $wordAdapter->getSession();
            $this->fail('Setting undefined class should fail');
        } catch (\Zend\Captcha\Exception\InvalidArgumentException $e) {
            $this->assertRegExp('/^Session class .* not found$/', $e->getMessage());
        }
    }
}
