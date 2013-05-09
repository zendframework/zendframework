<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Captcha
 */

namespace ZendTest\Captcha;

/**
 * @category   Zend
 * @package    Zend_Captcha
 * @subpackage UnitTests
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

    /**
     * @group ZF2-91
     */
    public function testLoadInvalidSessionClass()
    {
        $wordAdapter = new $this->wordClass;
        $wordAdapter->setSessionClass('ZendTest\Captcha\InvalidClassName');
        $this->setExpectedException('Zend\Captcha\Exception\InvalidArgumentException', 'not found');
        $wordAdapter->getSession();
    }

    public function testErrorMessages()
    {
        $wordAdapter = new $this->wordClass;
        $this->assertFalse($wordAdapter->isValid('foo'));
        $messages = $wordAdapter->getMessages();
        $this->assertFalse(empty($messages));
    }
}
