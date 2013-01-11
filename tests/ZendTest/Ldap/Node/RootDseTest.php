<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Ldap
 */

namespace ZendTest\Ldap\Node;

use Zend\Ldap\Node;
use ZendTest\Ldap as TestLdap;

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @group      Zend_Ldap
 * @group      Zend_Ldap_Node
 */
class RootDseTest extends TestLdap\AbstractOnlineTestCase
{
    public function testLoadRootDseNode()
    {
        $root1 = $this->getLDAP()->getRootDse();
        $root2 = $this->getLDAP()->getRootDse();

        $this->assertEquals($root1, $root2);
        $this->assertSame($root1, $root2);
    }

    public function testSupportCheckMethods()
    {
        $root = $this->getLDAP()->getRootDse();

        $this->assertInternalType('boolean', $root->supportsSaslMechanism('GSSAPI'));
        $this->assertInternalType('boolean', $root->supportsSaslMechanism(array('GSSAPI', 'DIGEST-MD5')));
        $this->assertInternalType('boolean', $root->supportsVersion('3'));
        $this->assertInternalType('boolean', $root->supportsVersion(3));
        $this->assertInternalType('boolean', $root->supportsVersion(array('3', '2')));
        $this->assertInternalType('boolean', $root->supportsVersion(array(3, 2)));

        switch ($root->getServerType()) {
            case Node\RootDse::SERVER_TYPE_ACTIVEDIRECTORY:
                $this->assertInternalType('boolean', $root->supportsControl('1.2.840.113556.1.4.319'));
                $this->assertInternalType('boolean', $root->supportsControl(array('1.2.840.113556.1.4.319',
                                                                                 '1.2.840.113556.1.4.473')
                    )
                );
                $this->assertInternalType('boolean', $root->supportsCapability('1.3.6.1.4.1.4203.1.9.1.1'));
                $this->assertInternalType('boolean', $root->supportsCapability(array('1.3.6.1.4.1.4203.1.9.1.1',
                                                                                    '2.16.840.1.113730.3.4.18')
                    )
                );
                $this->assertInternalType('boolean', $root->supportsPolicy('unknown'));
                $this->assertInternalType('boolean', $root->supportsPolicy(array('unknown', 'unknown')));
                break;
            case Node\RootDse::SERVER_TYPE_EDIRECTORY:
                $this->assertInternalType('boolean', $root->supportsExtension('1.3.6.1.4.1.1466.20037'));
                $this->assertInternalType('boolean', $root->supportsExtension(array('1.3.6.1.4.1.1466.20037',
                                                                                   '1.3.6.1.4.1.4203.1.11.1')
                    )
                );
                break;
            case Node\RootDse::SERVER_TYPE_OPENLDAP:
                $this->assertInternalType('boolean', $root->supportsControl('1.3.6.1.4.1.4203.1.9.1.1'));
                $this->assertInternalType('boolean', $root->supportsControl(array('1.3.6.1.4.1.4203.1.9.1.1',
                                                                                 '2.16.840.1.113730.3.4.18')
                    )
                );
                $this->assertInternalType('boolean', $root->supportsExtension('1.3.6.1.4.1.1466.20037'));
                $this->assertInternalType('boolean', $root->supportsExtension(array('1.3.6.1.4.1.1466.20037',
                                                                                   '1.3.6.1.4.1.4203.1.11.1')
                    )
                );
                $this->assertInternalType('boolean', $root->supportsFeature('1.3.6.1.1.14'));
                $this->assertInternalType('boolean', $root->supportsFeature(array('1.3.6.1.1.14',
                                                                                 '1.3.6.1.4.1.4203.1.5.1')
                    )
                );
                break;
        }
    }

    public function testGetters()
    {
        $root = $this->getLDAP()->getRootDse();

        $this->assertInternalType('array', $root->getNamingContexts());
        $this->assertInternalType('string', $root->getSubschemaSubentry());

        switch ($root->getServerType()) {
            case Node\RootDse::SERVER_TYPE_ACTIVEDIRECTORY:
                $this->assertInternalType('string', $root->getConfigurationNamingContext());
                $this->assertInternalType('string', $root->getCurrentTime());
                $this->assertInternalType('string', $root->getDefaultNamingContext());
                $this->assertInternalType('string', $root->getDnsHostName());
                $this->assertInternalType('string', $root->getDomainControllerFunctionality());
                $this->assertInternalType('string', $root->getDomainFunctionality());
                $this->assertInternalType('string', $root->getDsServiceName());
                $this->assertInternalType('string', $root->getForestFunctionality());
                $this->assertInternalType('string', $root->getHighestCommittedUSN());
                $this->assertInternalType('boolean', $root->getIsGlobalCatalogReady());
                $this->assertInternalType('boolean', $root->getIsSynchronized());
                $this->assertInternalType('string', $root->getLDAPServiceName());
                $this->assertInternalType('string', $root->getRootDomainNamingContext());
                $this->assertInternalType('string', $root->getSchemaNamingContext());
                $this->assertInternalType('string', $root->getServerName());
                break;
            case Node\RootDse::SERVER_TYPE_EDIRECTORY:
                $this->assertInternalType('string', $root->getVendorName());
                $this->assertInternalType('string', $root->getVendorVersion());
                $this->assertInternalType('string', $root->getDsaName());
                $this->assertInternalType('string', $root->getStatisticsErrors());
                $this->assertInternalType('string', $root->getStatisticsSecurityErrors());
                $this->assertInternalType('string', $root->getStatisticsChainings());
                $this->assertInternalType('string', $root->getStatisticsReferralsReturned());
                $this->assertInternalType('string', $root->getStatisticsExtendedOps());
                $this->assertInternalType('string', $root->getStatisticsAbandonOps());
                $this->assertInternalType('string', $root->getStatisticsWholeSubtreeSearchOps());
                break;
            case Node\RootDse::SERVER_TYPE_OPENLDAP:
                $this->assertNullOrString($root->getConfigContext());
                $this->assertNullOrString($root->getMonitorContext());
                break;
        }
    }

    protected function assertNullOrString($value)
    {
        if ($value === null) {
            $this->assertNull($value);
        } else {
            $this->assertInternalType('string', $value);
        }
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testSetterWillThrowException()
    {
        $root              = $this->getLDAP()->getRootDse();
        $root->objectClass = 'illegal';
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testOffsetSetWillThrowException()
    {
        $root                = $this->getLDAP()->getRootDse();
        $root['objectClass'] = 'illegal';
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testUnsetterWillThrowException()
    {
        $root = $this->getLDAP()->getRootDse();
        unset($root->objectClass);
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testOffsetUnsetWillThrowException()
    {
        $root = $this->getLDAP()->getRootDse();
        unset($root['objectClass']);
    }
}
