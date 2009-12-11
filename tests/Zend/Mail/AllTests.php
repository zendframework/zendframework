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
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Mail_AllTests::main');
}

require_once 'Zend/Mail/MboxTest.php';
require_once 'Zend/Mail/MboxMessageOldTest.php';
require_once 'Zend/Mail/MboxFolderTest.php';
require_once 'Zend/Mail/MaildirTest.php';
require_once 'Zend/Mail/MaildirMessageOldTest.php';
require_once 'Zend/Mail/MaildirFolderTest.php';
require_once 'Zend/Mail/MaildirWritableTest.php';
require_once 'Zend/Mail/Pop3Test.php';
require_once 'Zend/Mail/ImapTest.php';
require_once 'Zend/Mail/InterfaceTest.php';
require_once 'Zend/Mail/MessageTest.php';
require_once 'Zend/Mail/SmtpTest.php';

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Mail
 */
class Zend_Mail_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Mail');

        $suite->addTestSuite('Zend_Mail_MessageTest');
        $suite->addTestSuite('Zend_Mail_InterfaceTest');
        $suite->addTestSuite('Zend_Mail_MboxTest');
        $suite->addTestSuite('Zend_Mail_MboxMessageOldTest');
        $suite->addTestSuite('Zend_Mail_MboxFolderTest');
        if (defined('TESTS_ZEND_MAIL_POP3_ENABLED') && constant('TESTS_ZEND_MAIL_POP3_ENABLED') == true) {
            $suite->addTestSuite('Zend_Mail_Pop3Test');
        }
        if (defined('TESTS_ZEND_MAIL_IMAP_ENABLED') && constant('TESTS_ZEND_MAIL_IMAP_ENABLED') == true) {
            $suite->addTestSuite('Zend_Mail_ImapTest');
        }
        if (defined('TESTS_ZEND_MAIL_MAILDIR_ENABLED') && constant('TESTS_ZEND_MAIL_MAILDIR_ENABLED')) {
            $suite->addTestSuite('Zend_Mail_MaildirTest');
            $suite->addTestSuite('Zend_Mail_MaildirMessageOldTest');
            $suite->addTestSuite('Zend_Mail_MaildirFolderTest');
            $suite->addTestSuite('Zend_Mail_MaildirWritableTest');
        }
        if (defined('TESTS_ZEND_MAIL_SMTP_ENABLED') && constant('TESTS_ZEND_MAIL_SMTP_ENABLED') == true) {
            $suite->addTestSuite('Zend_Mail_SmtpTest');
        }

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Mail_AllTests::main') {
    Zend_Mail_AllTests::main();
}
