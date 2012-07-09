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
 * @package    Zend_OAuth
 * @subpackage UnitTests
 * @version    $Id:$
 */

namespace ZendTest\OAuth\Config;


/**
 * @category   Zend
 * @package    Zend_OAuth
 * @subpackage UnitTests
 * @group      Zend_OAuth
 */
use Zend\OAuth\Config\StandardConfig;

class StandardConfigurationTest extends \PHPUnit_Framework_TestCase
{

    public function testSiteUrlArePropertlyBuiltFromDefaultPaths()
    {
    	$config = new StandardConfig(
    		array(
    			'siteUrl'	=> 'https://example.com/oauth/'
    		)
    	);
    	$this->assertEquals('https://example.com/oauth/authorize', $config->getAuthorizeUrl());
    	$this->assertEquals('https://example.com/oauth/request_token', $config->getRequestTokenUrl());
    	$this->assertEquals('https://example.com/oauth/access_token', $config->getAccessTokenUrl());
    	
    }

}

