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
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see TestHelper.php
 */
require_once dirname(__FILE__) . '/../../../../TestHelper.php';

require_once 'Zend/Tool/Framework/Registry.php';
require_once 'Zend/Tool/Framework/Client/Request.php';
require_once 'Zend/Tool/Framework/Client/Response.php';

require_once '_files/ProviderFullFeatured.php';

/**
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group Zend_Tool
 * @group Zend_Tool_Framework
 * @group Zend_Tool_Framework_Provider
 */
class Zend_Tool_Framework_Provider_AbstractTest extends PHPUnit_Framework_TestCase
{

    protected $_request = null;
    protected $_response = null;
    protected $_registry = null;

    public function setup()
    {
        $this->_request = new Zend_Tool_Framework_Client_Request();
        $this->_response = new Zend_Tool_Framework_Client_Response();
        $this->_registry = new Zend_Tool_Framework_Registry();

        $this->_registry->setRequest($this->_request);
        $this->_registry->setResponse($this->_response);
    }

    public function testAbsractReturnsRequestAndResponse()
    {
        $provider = new Zend_Tool_Framework_Provider_ProviderFullFeatured();
        $provider->setRegistry($this->_registry);
        $returnInternals = $provider->_testReturnInternals();
        $this->assertTrue(array_shift($returnInternals) === $this->_request);
        $this->assertTrue(array_shift($returnInternals) === $this->_response);
    }

}
