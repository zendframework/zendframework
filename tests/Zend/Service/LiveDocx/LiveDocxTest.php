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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */


/**
 * @namespace
 */
namespace ZendTest\Service;
namespace Zend\Service\LiveDocx;
use Zend\Validator;

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_LiveDocx_LiveDocxTest::main');
}


/**
 * Zend_Service_LiveDocx test case
 *
 * @category   Zend
 * @package    Zend_Service_LiveDocx
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_LiveDocx
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */
class LiveDocxTest extends \PHPUnit_Framework_TestCase
{
    public $mailMerge;

    public static function main()
    {
        $suite  = new \PHPUnit_Framework_TestSuite(__CLASS__);
        $result = \PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        if (!constant('TESTS_ZEND_SERVICE_LIVEDOCX_USERNAME')
                || !constant('TESTS_ZEND_SERVICE_LIVEDOCX_PASSWORD')
        ) {
            $this->markTestSkipped('LiveDocx tests disabled');
            return;
        }

        $this->mailMerge = new MailMerge();
        $this->mailMerge->setUsername(TESTS_ZEND_SERVICE_LIVEDOCX_USERNAME)
                        ->setPassword(TESTS_ZEND_SERVICE_LIVEDOCX_PASSWORD);

        foreach ($this->mailMerge->listTemplates() as $template) {
            $this->mailMerge->deleteTemplate($template['filename']);
        }        
    }
    
    public function tearDown ()
    {
	if (isset($this->mailMerge)) {
	    foreach ($this->mailMerge->listTemplates() as $template) {
		$this->mailMerge->deleteTemplate($template['filename']);
	    }   
	    unset($this->mailMerge);
	}
    }

    public function testGetFormat ()
    {
        $this->assertEquals('',    $this->mailMerge->getFormat('document'));
        $this->assertEquals('doc', $this->mailMerge->getFormat('document.doc'));
        $this->assertEquals('doc', $this->mailMerge->getFormat('document-123.doc'));
        $this->assertEquals('doc', $this->mailMerge->getFormat('document123.doc'));
        $this->assertEquals('doc', $this->mailMerge->getFormat('document.123.doc'));
    }
    
    public function testGetVersion ()
    {
        $expectedResults = '1.2';
        $this->assertEquals($expectedResults, $this->mailMerge->getVersion());
    }
}

if (PHPUnit_MAIN_METHOD == 'LiveDocxTest::main') {
    LiveDocxTest::main();
}
