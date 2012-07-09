<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InfoCard
 */

namespace ZendTest\InfoCard;
use Zend\InfoCard\XML\Assertion;

/**
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage UnitTests
 * @group      Zend_InfoCard
 */
class AssertionTest extends \PHPUnit_Framework_TestCase
{
    protected $_xmlDocument;

    /**
     * Stores the original set timezone
     * @var string
     */
    private $_originaltimezone;

    public function setUp()
    {
        $this->_originaltimezone = date_default_timezone_get();
        $this->tokenDocument = __DIR__ . '/_files/signedToken.xml';
        $this->sslPubKey     = __DIR__ . '/_files/ssl_pub.cert';
        $this->sslPrvKey     = __DIR__ . '/_files/ssl_private.cert';
        $this->loadXmlDocument();
    }

    /**
     * Teardown environment
     */
    public function tearDown()
    {
        date_default_timezone_set($this->_originaltimezone);
    }

    public function loadXmlDocument()
    {
        $this->_xmlDocument = file_get_contents($this->tokenDocument);
    }

    public function testAssertionProcess()
    {
        date_default_timezone_set("America/Los_Angeles");

        $assertions = Assertion\Factory::getInstance($this->_xmlDocument);

        $this->assertTrue($assertions instanceof Assertion\SAML);

        $this->assertSame($assertions->getMajorVersion(), 1);
        $this->assertSame($assertions->getMinorversion(), 1);
        $this->assertSame($assertions->getAssertionID(), "uuid:5cf2cd76-acf6-45ef-9059-a811801b80cc");
        $this->assertSame($assertions->getIssuer(), "http://schemas.xmlsoap.org/ws/2005/05/identity/issuer/self");
        $this->assertSame($assertions->getConfirmationMethod(), Assertion\SAML::CONFIRMATION_BEARER);
        $this->assertSame($assertions->getIssuedTimestamp(), 1190153823);

    }

    public function testAssertionThrowsExceptionOnBadInput1()
    {
        $this->setExpectedException('Zend\InfoCard\XML\Exception\InvalidArgumentException', 'Invalid Data provided to create instance');
        Assertion\Factory::getInstance(10);
    }

    public function testAssertionThrowsExceptionOnBadInput2()
    {
        $doc = file_get_contents(__DIR__ . '/_files/signedToken_bad_type.xml');

        $this->setExpectedException('Zend\InfoCard\XML\Exception\InvalidArgumentException', 'Unable to determine Assertion type by Namespace');
        $assertions = Assertion\Factory::getInstance($doc);
    }
}

