<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Soap\Wsdl;

use Zend\Soap\Wsdl\ComplexTypeStrategy\DefaultComplexType;
use ZendTest\Soap\TestAsset\PublicPrivateProtected;
use ZendTest\Soap\WsdlTestHelper;

require_once __DIR__ . '/../TestAsset/commontypes.php';

/**
 * @covers \Zend\Soap\Wsdl\ComplexTypeStrategy\DefaultComplexType
 */
class DefaultComplexTypeTest extends WsdlTestHelper
{
    /**
     * @var DefaultComplexType
     */
    protected $strategy;

    public function setUp()
    {
        $this->strategy = new DefaultComplexType();

        parent::setUp();
    }

    /**
     * @group ZF-5944
     */
    public function testOnlyPublicPropertiesAreDiscoveredByStrategy()
    {
        $this->strategy->addComplexType('ZendTest\Soap\TestAsset\PublicPrivateProtected');

        $nodes = $this->xpath->query('//xsd:element[@name="'.(PublicPrivateProtected::PROTECTED_VAR_NAME).'"]');
        $this->assertEquals(0, $nodes->length, 'Document should not contain protected fields');

        $nodes = $this->xpath->query('//xsd:element[@name="'.(PublicPrivateProtected::PRIVATE_VAR_NAME).'"]');
        $this->assertEquals(0, $nodes->length, 'Document should not contain private fields');

        $this->testDocumentNodes();
    }

    public function testDoubleClassesAreDiscoveredByStrategy()
    {
        $this->strategy->addComplexType('ZendTest\Soap\TestAsset\WsdlTestClass');
        $this->strategy->addComplexType('\ZendTest\Soap\TestAsset\WsdlTestClass');

        $nodes = $this->xpath->query('//xsd:complexType[@name="WsdlTestClass"]');
        $this->assertEquals(1, $nodes->length);

        $this->testDocumentNodes();
    }
}
