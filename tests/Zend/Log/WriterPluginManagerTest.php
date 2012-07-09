<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace ZendTest\Log;

use Zend\Log\WriterPluginManager;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @group      Zend_Log
 */
class WriterPluginManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->plugins = new WriterPluginManager();
    }

    public function testRegisteringInvalidWriterRaisesException()
    {
        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException', 'must implement');
        $this->plugins->setService('test', $this);
    }
}
