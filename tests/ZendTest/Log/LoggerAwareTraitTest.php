<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace ZendTest\Log;

use \PHPUnit_Framework_TestCase as TestCase;
use \Zend\Log\Logger;

/**
 * @requires PHP 5.4
 */
class LoggerAwareTraitTest extends TestCase
{
    public function testSetLogger()
    {
        $object = $this->getObjectForTrait('\Zend\Log\LoggerAwareTrait');

        $this->assertAttributeEquals(null, 'logger', $object);

        $logger = new Logger;

        $object->setLogger($logger);

        $this->assertAttributeEquals($logger, 'logger', $object);
    }
}
