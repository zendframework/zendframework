<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\Mvc\Controller\Plugin;

use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\NonPersistent as NonPersistentStorage;
use Zend\Mvc\Controller\Plugin\Identity as IdentityPlugin;

/**
 * Tests Identity plugin
 *
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTests
 */
class IdentityTest extends \PHPUnit_Framework_TestCase
{
    public function testGetIdentity()
    {
        $identity = new TestAsset\IdentityObject();
        $identity->setUsername('a username');
        $identity->setPassword('a password');

        $authenticationService = new AuthenticationService(new NonPersistentStorage, new TestAsset\AuthenticationAdapter);

        $identityPlugin = new IdentityPlugin;
        $identityPlugin->setAuthenticationService($authenticationService);

        $this->assertNull($identityPlugin());

        $this->assertFalse($authenticationService->hasIdentity());

        $authenticationService->getAdapter()->setIdentity($identity);
        $result = $authenticationService->authenticate();
        $this->assertTrue($result->isValid());

        $this->assertEquals($identity, $identityPlugin());
    }
}
