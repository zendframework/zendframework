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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Tool_Framework_AllTests::main');
}

require_once 'Zend/Tool/Framework/RegistryTest.php';
require_once 'Zend/Tool/Framework/Action/BaseTest.php';
require_once 'Zend/Tool/Framework/Action/RepositoryTest.php';
require_once 'Zend/Tool/Framework/Client/RequestTest.php';
require_once 'Zend/Tool/Framework/Client/ResponseTest.php';
//require_once 'Zend/Tool/Framework/Manifest/MetadataTest.php';
//require_once 'Zend/Tool/Framework/Manifest/ProviderMetadataTest.php';
//require_once 'Zend/Tool/Framework/Manifest/ActionMetadataTest.php';
require_once 'Zend/Tool/Framework/Manifest/RepositoryTest.php';
require_once 'Zend/Tool/Framework/Provider/AbstractTest.php';
require_once 'Zend/Tool/Framework/Provider/RepositoryTest.php';
require_once 'Zend/Tool/Framework/Provider/SignatureTest.php';

/**
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Tool
 * @group      Zend_Tool_Framework
 */
class Zend_Tool_Framework_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Tool_Framework');

        // suites and tests here
        //
        $suite->addTestSuite('Zend_Tool_Framework_RegistryTest');
        $suite->addTestSuite('Zend_Tool_Framework_Action_BaseTest');
        $suite->addTestSuite('Zend_Tool_Framework_Action_RepositoryTest');
        $suite->addTestSuite('Zend_Tool_Framework_Client_RequestTest');
        $suite->addTestSuite('Zend_Tool_Framework_Client_ResponseTest');
        //$suite->addTestSuite('Zend_Tool_Framework_Manifest_MetadataTest');
        //$suite->addTestSuite('Zend_Tool_Framework_Manifest_ProviderMetadataTest');
        //$suite->addTestSuite('Zend_Tool_Framework_Manifest_ActionMetadataTest');
        $suite->addTestSuite('Zend_Tool_Framework_Manifest_RepositoryTest');
        $suite->addTestSuite('Zend_Tool_Framework_Provider_AbstractTest');
        $suite->addTestSuite('Zend_Tool_Framework_Provider_RepositoryTest');
        $suite->addTestSuite('Zend_Tool_Framework_Provider_SignatureTest');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Tool_Framework_AllTests::main') {
    Zend_Tool_Framework_AllTests::main();
}
