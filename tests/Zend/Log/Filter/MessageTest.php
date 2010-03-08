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
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/** Zend_Log */

/** Zend_Log_Filter_Message */

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Log
 */
class Zend_Log_Filter_MessageTest extends PHPUnit_Framework_TestCase
{
    public function testMessageFilterRecognizesInvalidRegularExpression()
    {
        try {
            $filter = new Zend_Log_Filter_Message('invalid regexp');
            $this->fail();
        } catch (Exception $e) {
            $this->assertType('Zend_Log_Exception', $e);
            $this->assertRegexp('/invalid reg/i', $e->getMessage());
        }
    }

    public function testMessageFilter()
    {
        $filter = new Zend_Log_Filter_Message('/accept/');
        $this->assertTrue($filter->accept(array('message' => 'foo accept bar')));
        $this->assertFalse($filter->accept(array('message' => 'foo reject bar')));
    }
    
    public function testFactory()
    {
        $cfg = array('log' => array('memory' => array(
            'writerName'   => "Mock", 
            'filterName'   => "Message", 
            'filterParams' => array(
                'regexp'   => "/42/" 
             ),        
        )));

        $logger = Zend_Log::factory($cfg['log']);
        $this->assertTrue($logger instanceof Zend_Log);
    }

    public function testFactoryWithConfig()
    {
        $config = new Zend_Config(array('log' => array('memory' => array(
            'writerName'   => "Mock", 
            'filterName'   => "Message", 
            'filterParams' => array(
                'regexp'   => "/42/" 
             ),        
        ))));

        $filter = Zend_Log_Filter_Message::factory($config->log->memory->filterParams);
        $this->assertTrue($filter instanceof Zend_Log_Filter_Message);
    }
}
