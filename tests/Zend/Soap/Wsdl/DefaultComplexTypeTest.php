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
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(__FILE__)."/../../../TestHelper.php";

require_once "Zend/Soap/Wsdl/Strategy/DefaultComplexType.php";
require_once "Zend/Soap/Wsdl.php";

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Soap
 * @group      Zend_Soap_Wsdl
 */
class Zend_Soap_Wsdl_DefaultComplexTypeTest extends PHPUnit_Framework_TestCase
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
        $this->strategy = new Zend_Soap_Wsdl_Strategy_DefaultComplexType();
        $this->wsdl = new Zend_Soap_Wsdl("TestService", "http://framework.zend.com/soap/unittests");
        $this->wsdl->setComplexTypeStrategy($this->strategy);
        $this->strategy->setContext($this->wsdl);
    }

    /**
     * @group ZF-5944
     */
    public function testOnlyPublicPropertiesAreDiscoveredByStrategy()
    {
        $this->strategy->addComplexType("Zend_Soap_Wsdl_DefaultComplexTypeTest_PublicPrivateProtected");

        $xml = $this->wsdl->toXML();
        $this->assertNotContains( Zend_Soap_Wsdl_DefaultComplexTypeTest_PublicPrivateProtected::PROTECTED_VAR_NAME, $xml);
        $this->assertNotContains( Zend_Soap_Wsdl_DefaultComplexTypeTest_PublicPrivateProtected::PRIVATE_VAR_NAME, $xml);
    }
}

class Zend_Soap_Wsdl_DefaultComplexTypeTest_PublicPrivateProtected
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
