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
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Form\Element;

use Zend\Form\Element\Hash as HashElement,
    Zend\Form\Element\Xhtml as XhtmlElement,
    Zend\Form\Element,
    Zend\View\Renderer\PhpRenderer as View;

/**
 * Test class for Zend_Form_Element_Hash
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class HashTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        if (isset($this->hash)) {
            unset($this->hash);
        }

        $session = new TestAsset\HashSessionContainer();
        $session->hash = null;

        $this->element = new HashElement('foo', array(
            'session' => $session,
        ));
    }

    public function testHashElementSubclassesXhtmlElement()
    {
        $this->assertTrue($this->element instanceof XhtmlElement);
    }

    public function testHashElementInstanceOfBaseElement()
    {
        $this->assertTrue($this->element instanceof Element);
    }

    public function testSaltPopulatedByDefault()
    {
        $salt = $this->element->getSalt();
        $this->assertFalse(empty($salt));
    }

    public function testCanSetSalt()
    {
        $salt = $this->element->getSalt();
        $this->element->setSalt('foobar');
        $this->assertNotEquals($salt, $this->element->getSalt());
        $this->assertEquals('foobar', $this->element->getSalt());
    }

    public function testTimeoutPopulatedByDefault()
    {
        $ttl = $this->element->getTimeout();
        $this->assertFalse(empty($ttl));
        $this->assertTrue(is_int($ttl));
    }

    public function testCanSetTimeout()
    {
        $ttl = $this->element->getTimeout();
        $this->element->setTimeout(3600);
        $this->assertNotEquals($ttl, $this->element->getTimeout());
        $this->assertEquals(3600, $this->element->getTimeout());
    }

    public function testGetHashReturnsHashValue()
    {
        $hash = $this->element->getHash();
        $this->assertFalse(empty($hash));
        $this->assertTrue(is_string($hash));
        $this->hash = $hash;
    }

    public function testGetHashSetsElementValueToHash()
    {
        $this->testGetHashReturnsHashValue();
        $this->assertEquals($this->hash, $this->element->getValue());
    }

    public function testHashIsMd5()
    {
        $this->testGetHashReturnsHashValue();
        $this->assertEquals(32, strlen($this->hash));
        $this->assertRegexp('/^[a-f0-9]{32}$/', $this->hash);
    }

    public function testLabelIsNull()
    {
        $this->assertNull($this->element->getLabel());
    }

    public function testSessionNameContainsSaltAndName()
    {
        $sessionName = $this->element->getSessionName();
        $this->assertContains($this->element->getSalt(), $sessionName);
        $this->assertContains($this->element->getName(), $sessionName);
    }

    public function getView()
    {
        $view = new View();
        return $view;
    }

    public function testValidatorTokenReceivesSessionHashWhenPresent()
    {
        $session = $this->element->getSession();
        $session->hash = $this->element->getHash();
        $element = new HashElement('foo', array('session' => $session));
        $validator = $element->getValidator('Identical');
        $this->assertEquals($session->hash, $validator->getToken());
    }

    public function testRenderInitializesSessionHashToken()
    {
        $session = $this->element->getSession();
        $this->assertNull($session->hash);
        $html = $this->element->render($this->getView());

        $this->assertEquals($this->element->getHash(), $session->hash);
        $this->assertEquals(1, $session->setExpirationHops);
        $this->assertEquals($this->element->getTimeout(), $session->setExpirationSeconds);
    }

    public function testHashTokenIsRendered()
    {
        $html = $this->element->render($this->getView());
        $this->assertContains($this->element->getHash(), $html);
    }

    public function testHiddenInputRenderedByDefault()
    {
        $html = $this->element->render($this->getView());
        $this->assertRegexp('/<input[^>]*?type="hidden"/', $html, $html);
    }

    /**
     * @group ZF-7404
     */
    public function testShouldRenderHashTokenIfRenderedThroughMagicCall()
    {
        $this->element->setView($this->getView());
        $html = $this->element->renderViewHelper();
        $this->assertContains($this->element->getHash(), $html, 'Html is: ' . $html);
    }

    public function testMutlipleHashElementsWithSameNameShareSingleHash()
    {
        $element = new HashElement('foo');
        $this->assertSame($this->element->getHash(), $element->getHash());
    }
}
