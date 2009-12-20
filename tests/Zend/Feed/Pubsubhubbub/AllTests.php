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
 * @package    UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Feed_Pubsubhubbub_AllTests::main');
}

require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'Zend/Feed/Pubsubhubbub/PubsubhubbubTest.php';
require_once 'Zend/Feed/Pubsubhubbub/PublisherTest.php';
require_once 'Zend/Feed/Pubsubhubbub/SubscriberTest.php';
require_once 'Zend/Feed/Pubsubhubbub/SubscriberHttpTest.php';
require_once 'Zend/Feed/Pubsubhubbub/Subscriber/CallbackTest.php';


/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @group      Zend_Feed
 * @group      Zend_Feed_Subsubhubbub
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Feed_Pubsubhubbub_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend');

        $suite->addTestSuite('Zend_Feed_Pubsubhubbub_PubsubhubbubTest');
        $suite->addTestSuite('Zend_Feed_Pubsubhubbub_PublisherTest');
        $suite->addTestSuite('Zend_Feed_Pubsubhubbub_SubscriberTest');
        $suite->addTestSuite('Zend_Feed_Pubsubhubbub_SubscriberHttpTest');
        $suite->addTestSuite('Zend_Feed_Pubsubhubbub_Subscriber_CallbackTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Feed_Pubsubhubbub_AllTests::main') {
    Zend_Feed_Pubsubhubbub_AllTests::main();
}
