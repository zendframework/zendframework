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
use Zend\View\PhpRenderer as View;

/**
 * @category   Zend
 * @package    Zend_Captcha
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Captcha
 */
class FigletTest extends \PHPUnit_Framework_TestCase
{
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
            'captchaF',
            array(
                'captcha' => array(
                    'Figlet',
                    'sessionClass' => 'ZendTest\\Captcha\\TestAsset\\SessionContainer'
                )
            )
        );
        $this->captcha =  $this->element->getCaptcha();
    }

    public function testCaptchaAdapterCreated()
    {
        $this->assertTrue($this->element->getCaptcha() instanceof \Zend\Captcha\Adapter);
    }

    public function getView()
    {
        $view = new View();
        return $view;
    }

    public function testCaptchaIsRendered()
    {
        $html = $this->element->render($this->getView());
        $this->assertContains($this->element->getName(), $html);
    }

    public function testCaptchaHasIdAndInput()
    {
        $html = $this->element->render($this->getView());
        $expect = sprintf('type="hidden" name="%s\[id\]" value="%s"', $this->element->getName(), $this->captcha->getId());
        $this->assertRegexp("/<input[^>]*?$expect/", $html, $html);
        $expect = sprintf('type="text" name="%s\[input\]"', $this->element->getName());
        $this->assertRegexp("/<input[^>]*?$expect/", $html, $html);
    }

    /*
     * @group ZF-8268
     */
    public function testLabelIdIsCorrect()
    {
        $form = new \Zend\Form\Form();
        $form->setElementsBelongTo('comment');
        $this->element->setLabel("My Captcha");
        $form->addElement($this->element);
        $html = $form->render($this->getView());
        $expect = sprintf('for="comment-%s-input"', $this->element->getName());
        $this->assertRegexp("/<label [^>]*?$expect/", $html, $html);
    }

    public function testTimeoutPopulatedByDefault()
    {
        $ttl = $this->captcha->getTimeout();
        $this->assertFalse(empty($ttl));
        $this->assertTrue(is_int($ttl));
    }

    public function testCanSetTimeout()
    {
        $ttl = $this->captcha->getTimeout();
        $this->captcha->setTimeout(3600);
        $this->assertNotEquals($ttl, $this->captcha->getTimeout());
        $this->assertEquals(3600, $this->captcha->getTimeout());
    }

    public function testGenerateReturnsId()
    {
        $id = $this->captcha->generate();
        $this->assertFalse(empty($id));
        $this->assertTrue(is_string($id));
        $this->id = $id;
    }

    public function testGetWordReturnsWord()
    {
        $this->captcha->generate();
        $word = $this->captcha->getWord();
        $this->assertFalse(empty($word));
        $this->assertTrue(is_string($word));
        $this->assertTrue(strlen($word) == 8);
        $this->word = $word;
    }

    public function testGetWordLength()
    {
        $this->captcha->setWordLen(4);
        $this->captcha->generate();
        $word = $this->captcha->getWord();
        $this->assertTrue(is_string($word));
        $this->assertTrue(strlen($word) == 4);
        $this->word = $word;
    }

    public function testAdapterElementName()
    {
        $this->assertEquals($this->captcha->getName(),
                    $this->element->getName());
    }

    public function testGenerateIsRandomised()
    {
        $id1 = $this->captcha->generate();
        $word1 = $this->captcha->getWord();
        $id2 = $this->captcha->generate();
        $word2 = $this->captcha->getWord();

        $this->assertFalse(empty($id1));
        $this->assertFalse(empty($id2));
        $this->assertFalse($id1 == $id2);
        $this->assertFalse($word1 == $word2);
    }

    public function testRenderSetsValue()
    {
        $this->testCaptchaIsRendered();
        $this->assertEquals($this->captcha->getId(),
                $this->element->getValue());
    }

    public function testLabelIsNull()
    {
        $this->assertNull($this->element->getLabel());
    }

    public function testRenderInitializesSessionData()
    {
        $this->testCaptchaIsRendered();
        $session = $this->captcha->getSession();
        $this->assertEquals($this->captcha->getTimeout(), $session->setExpirationSeconds);
        $this->assertEquals(1, $session->setExpirationHops);
        $this->assertEquals($this->captcha->getWord(), $session->word);
    }

    public function testWordValidates()
    {
        $this->testCaptchaIsRendered();
        $input = array($this->element->getName() => array("id" => $this->captcha->getId(), "input" => $this->captcha->getWord()));
        $this->assertTrue($this->element->isValid("", $input));
    }

    public function testMissingNotValid()
    {
        $this->testCaptchaIsRendered();
        $this->assertFalse($this->element->isValid("", array()));
        $input = array($this->element->getName() => array("input" => "blah"));
        $this->assertFalse($this->element->isValid("", $input));
    }

    public function testWrongWordNotValid()
    {
        $this->testCaptchaIsRendered();
        $input = array($this->element->getName() => array("id" => $this->captcha->getId(), "input" => "blah"));
        $this->assertFalse($this->element->isValid("", $input));
    }

    public function testUsesWordCaptchaDecoratorByDefault()
    {
        $this->assertEquals('Captcha\\Word', $this->element->getCaptcha()->getDecorator());
    }

    public function testCaptchaShouldBeConfigurableViaConfigObject()
    {
        $options = array(
            'name'         => 'foo',
            'sessionClass' => 'ZendTest\\Captcha\\TestAsset\\SessionContainer',
            'wordLen'      => 6,
            'timeout'      => 300,
        );
        $config  = new \Zend\Config\Config($options);
        $captcha = new \Zend\Captcha\Figlet($config);
        $test = $captcha->getOptions();
        $this->assertEquals($options, $test);
    }

    public function testShouldAllowFigletsLargerThanFourteenCharacters()
    {
        $this->captcha->setName('foo')
                      ->setWordLen(14);
        $id = $this->captcha->generate();
    }

    public function testShouldNotValidateEmptyInputAgainstEmptySession()
    {
        // Regression Test for ZF-4245
        $this->captcha->setName('foo')
                      ->setWordLen(6)
                      ->setTimeout(300);
        $id = $this->captcha->generate();
        // Unset the generated word
        // we have to reset $this->captcha for that
        $this->captcha->getSession()->word = null;
        $this->setUp();
        $this->captcha->setName('foo')
                      ->setWordLen(6)
                      ->setTimeout(300);
        $empty = array($this->captcha->getName() => array('id' => $id, 'input' => ''));
        $this->assertEquals(false, $this->captcha->isValid(null, $empty));
    }

    /**
     * @group ZF-3995
     */
    public function testIsValidShouldAllowPassingArrayValueAndOmittingContext()
    {
        $this->testCaptchaIsRendered();
        $input = array($this->element->getName() => array("id" => $this->captcha->getId(), "input" => $this->captcha->getWord()));
        $this->assertTrue($this->element->isValid($input));
    }

    /**
     * @group ZF-3995
     */
    public function testIsValidShouldNotRequireValueToBeNestedArray()
    {
        $this->testCaptchaIsRendered();
        $input = array("id" => $this->captcha->getId(), "input" => $this->captcha->getWord());
        $this->assertTrue($this->element->isValid($input));
    }

    /**
     * @group ZF-5728
     */
    public function testSetSessionWorks()
    {
        if(headers_sent($file, $line)) {
            $this->markTestSkipped("Cannot use sessions because headers already sent");
        }
        $session = new \Zend\Session\Container('captcha');
        $this->captcha->setSession($session);
        $this->testCaptchaIsRendered();
        $input = array("id" => $this->captcha->getId(), "input" => $this->captcha->getWord());
        $this->assertTrue($this->element->isValid($input));
        $this->assertEquals($session->word, $this->captcha->getWord());
    }
}
