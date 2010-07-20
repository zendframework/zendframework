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
 * @package    Zend_LDAP
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Ldap\Node;
use Zend\Ldap\Node\RootDse;

/**
 * Zend_LDAP_OnlineTestCase
 */

/**
 * @category   Zend
 * @package    Zend_LDAP
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_LDAP
 * @group      Zend_LDAP_Node
 */
class RootDseTest extends \ZendTest\Ldap\OnlineTestCase
{
    public function testLoadRootDseNode()
    {
        $root1=$this->_getLDAP()->getRootDse();
        $root2=$this->_getLDAP()->getRootDse();

        $this->assertEquals($root1, $root2);
        $this->assertSame($root1, $root2);
    }

    public function testSupportCheckMethods()
    {
        $root=$this->_getLDAP()->getRootDse();

        $this->assertType('boolean', $root->supportsSaslMechanism('GSSAPI'));
        $this->assertType('boolean', $root->supportsSaslMechanism(array('GSSAPI', 'DIGEST-MD5')));
        $this->assertType('boolean', $root->supportsVersion('3'));
        $this->assertType('boolean', $root->supportsVersion(3));
        $this->assertType('boolean', $root->supportsVersion(array('3', '2')));
        $this->assertType('boolean', $root->supportsVersion(array(3, 2)));

        switch ($root->getServerType()) {
            case RootDse::SERVER_TYPE_ACTIVEDIRECTORY:
                $this->assertType('boolean', $root->supportsControl('1.2.840.113556.1.4.319'));
                $this->assertType('boolean', $root->supportsControl(array('1.2.840.113556.1.4.319',
                    '1.2.840.113556.1.4.473')));
                $this->assertType('boolean', $root->supportsCapability('1.3.6.1.4.1.4203.1.9.1.1'));
                $this->assertType('boolean', $root->supportsCapability(array('1.3.6.1.4.1.4203.1.9.1.1',
                    '2.16.840.1.113730.3.4.18')));
                $this->assertType('boolean', $root->supportsPolicy('unknown'));
                $this->assertType('boolean', $root->supportsPolicy(array('unknown', 'unknown')));
                break;
            case RootDse::SERVER_TYPE_EDIRECTORY:
                $this->assertType('boolean', $root->supportsExtension('1.3.6.1.4.1.1466.20037'));
                $this->assertType('boolean', $root->supportsExtension(array('1.3.6.1.4.1.1466.20037',
                    '1.3.6.1.4.1.4203.1.11.1')));
                break;
            case RootDse::SERVER_TYPE_OPENLDAP:
                $this->assertType('boolean', $root->supportsControl('1.3.6.1.4.1.4203.1.9.1.1'));
                $this->assertType('boolean', $root->supportsControl(array('1.3.6.1.4.1.4203.1.9.1.1',
                    '2.16.840.1.113730.3.4.18')));
                $this->assertType('boolean', $root->supportsExtension('1.3.6.1.4.1.1466.20037'));
                $this->assertType('boolean', $root->supportsExtension(array('1.3.6.1.4.1.1466.20037',
                    '1.3.6.1.4.1.4203.1.11.1')));
                $this->assertType('boolean', $root->supportsFeature('1.3.6.1.1.14'));
                $this->assertType('boolean', $root->supportsFeature(array('1.3.6.1.1.14',
                    '1.3.6.1.4.1.4203.1.5.1')));
                break;
        }
    }

    public function testGetters()
    {
        $root=$this->_getLDAP()->getRootDse();

        $this->assertType('array', $root->getNamingContexts());
        $this->assertType('string', $root->getSubschemaSubentry());

        switch ($root->getServerType()) {
            case RootDse::SERVER_TYPE_ACTIVEDIRECTORY:
                $this->assertType('string', $root->getConfigurationNamingContext());
                $this->assertType('string', $root->getCurrentTime());
                $this->assertType('string', $root->getDefaultNamingContext());
                $this->assertType('string', $root->getDnsHostName());
                $this->assertType('string', $root->getDomainControllerFunctionality());
                $this->assertType('string', $root->getDomainFunctionality());
                $this->assertType('string', $root->getDsServiceName());
                $this->assertType('string', $root->getForestFunctionality());
                $this->assertType('string', $root->getHighestCommittedUSN());
                $this->assertType('boolean', $root->getIsGlobalCatalogReady());
                $this->assertType('boolean', $root->getIsSynchronized());
                $this->assertType('string', $root->getLDAPServiceName());
                $this->assertType('string', $root->getRootDomainNamingContext());
                $this->assertType('string', $root->getSchemaNamingContext());
                $this->assertType('string', $root->getServerName());
                break;
            case RootDse::SERVER_TYPE_EDIRECTORY:
                $this->assertType('string', $root->getVendorName());
                $this->assertType('string', $root->getVendorVersion());
                $this->assertType('string', $root->getDsaName());
                $this->assertType('string', $root->getStatisticsErrors());
                $this->assertType('string', $root->getStatisticsSecurityErrors());
                $this->assertType('string', $root->getStatisticsChainings());
                $this->assertType('string', $root->getStatisticsReferralsReturned());
                $this->assertType('string', $root->getStatisticsExtendedOps());
                $this->assertType('string', $root->getStatisticsAbandonOps());
                $this->assertType('string', $root->getStatisticsWholeSubtreeSearchOps());
                break;
            case RootDse::SERVER_TYPE_OPENLDAP:
                $this->_assertNullOrString($root->getConfigContext());
                $this->_assertNullOrString($root->getMonitorContext());
                break;
        }
    }

    protected function _assertNullOrString($value)
    {
        if ($value===null) {
            $this->assertNull($value);
        } else {
            $this->assertType('string', $value);
        }
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testSetterWillThrowException()
    {
          $root=$this->_getLDAP()->getRootDse();
          $root->objectClass='illegal';
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testOffsetSetWillThrowException()
    {
          $root=$this->_getLDAP()->getRootDse();
          $root['objectClass']='illegal';
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testUnsetterWillThrowException()
    {
          $root=$this->_getLDAP()->getRootDse();
          unset($root->objectClass);
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testOffsetUnsetWillThrowException()
    {
          $root=$this->_getLDAP()->getRootDse();
          unset($root['objectClass']);
    }
}
