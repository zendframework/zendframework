<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Soap
 */

namespace ZendTest\Soap\Wsdl;

use Zend\Soap\Wsdl\ComplexTypeStrategy\DefaultComplexType;
use Zend\Soap\Wsdl;

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @group      Zend_Soap
 * @group      Zend_Soap_Wsdl
 */
class DefaultComplexTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Soap_Wsdl
     */
    private $wsdl;

    /**
     * @var Zend_Soap_Wsdl_Strategy_DefaultComplexType
     */
    private $strategy;

    public function setUp()
    {
        $this->strategy = new DefaultComplexType();
        $this->wsdl = new Wsdl("TestService", "http://framework.zend.com/soap/unittests");
        $this->wsdl->setComplexTypeStrategy($this->strategy);
        $this->strategy->setContext($this->wsdl);
    }

    /**
     * @group ZF-5944
     */
    public function testOnlyPublicPropertiesAreDiscoveredByStrategy()
    {
        $this->strategy->addComplexType('\ZendTest\Soap\Wsdl\PublicPrivateProtected');

        $xml = $this->wsdl->toXML();
        $this->assertNotContains( PublicPrivateProtected::PROTECTED_VAR_NAME, $xml);
        $this->assertNotContains( PublicPrivateProtected::PRIVATE_VAR_NAME, $xml);
    }
}

class PublicPrivateProtected
{
    const PROTECTED_VAR_NAME = 'bar';
    const PRIVATE_VAR_NAME = 'baz';

    /**
     * @var string
     */
    public $foo;

    /**
     * @var string
     */
    protected $bar;

    /**
     * @var string
     */
    private $baz;
}
