<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_OAuth
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

