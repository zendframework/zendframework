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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Captcha
 */
class DumbTest extends CommonWordTest
{
    protected $wordClass = '\Zend\Captcha\Dumb';

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        if (isset($this->word)) {
            unset($this->word);
        }

        $this->element = new \Zend\Form\Element\Captcha(
            'captchaD',
            array(
                'captcha' => array(
                    'Dumb',
                    'sessionClass' => 'ZendTest\\Captcha\\TestAsset\\SessionContainer'
                )
            )
        );
        $this->captcha =  $this->element->getCaptcha();
    }

    public function testRendersWordInReverse()
    {
        $id   = $this->captcha->generate('test');
        $word = $this->captcha->getWord();
        $html = $this->captcha->render(new \Zend\View\Renderer\PhpRenderer);
        $this->assertContains(strrev($word), $html);
        $this->assertNotContains($word, $html);
    }
}
