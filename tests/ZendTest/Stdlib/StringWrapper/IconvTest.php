<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 * @package    Zend_Stdlib
 * @subpackage StringWrapper
 */

namespace ZendTest\Stdlib\StringWrapper;

use Zend\Stdlib\Exception;
use Zend\Stdlib\StringWrapper\Iconv;

class IconvTest extends CommonStringWrapperTest
{

    public function setUp()
    {
        if (!extension_loaded('iconv')) {
            try {
                new Iconv();
                $this->fail('Missing expected Zend\Stdlib\Exception\ExtensionNotLoadedException');
            } catch (Exception\ExtensionNotLoadedException $e) {
                $this->markTestSkipped('Missing ext/iconv');
            }
        }

        $this->stringWrapper = new Iconv();
        parent::setUp();
    }
}
