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
 * @package    Zend_Service_LiveDocx
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_LiveDocx_LiveDocxTest::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/** Zend_Service_LiveDocx_MailMerge */
require_once 'Zend/Service/LiveDocx/MailMerge.php';

/**
 * Zend_Service_LiveDocx test case
 *
 * @category   Zend
 * @package    Zend_Service_LiveDocx
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */
class Zend_Service_LiveDocX_LiveDocxTest extends PHPUnit_Framework_TestCase
{
    public $phpLiveDocx;

    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        if (!constant('TESTS_ZEND_SERVICE_LIVEDOCX_USERNAME')
            || !constant('TESTS_ZEND_SERVICE_LIVEDOCX_PASSWORD')
        ) {
            $this->markTestSkipped('LiveDocx tests disabled');
            return;
        }
        
        $this->phpLiveDocx = new Zend_Service_LiveDocx_MailMerge();
        $this->phpLiveDocx->setUsername(TESTS_ZEND_SERVICE_LIVEDOCX_USERNAME)
                          ->setPassword(TESTS_ZEND_SERVICE_LIVEDOCX_PASSWORD);

        foreach ($this->phpLiveDocx->listTemplates() as $template) {
            $this->phpLiveDocx->deleteTemplate($template['filename']);
        }        
    }
    
    public function tearDown ()
    {
        foreach ($this->phpLiveDocx->listTemplates() as $template) {
            $this->phpLiveDocx->deleteTemplate($template['filename']);
        }   
        unset($this->phpLiveDocx);
    }

    public function testGetFormat ()
    {
        $this->assertEquals('',    $this->phpLiveDocx->getFormat('document'));
        $this->assertEquals('doc', $this->phpLiveDocx->getFormat('document.doc'));
        $this->assertEquals('doc', $this->phpLiveDocx->getFormat('document-123.doc'));
        $this->assertEquals('doc', $this->phpLiveDocx->getFormat('document123.doc'));
        $this->assertEquals('doc', $this->phpLiveDocx->getFormat('document.123.doc'));
    }
    
    public function testGetVersion ()
    {
        $expectedResults = '1.2';
        $this->assertEquals($expectedResults, $this->phpLiveDocx->getVersion());
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Service_LiveDocx_LiveDocxTest::main') {
    Zend_Service_LiveDocx_LiveDocxTest::main();
}
