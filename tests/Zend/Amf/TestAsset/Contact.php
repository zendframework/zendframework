<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace ZendTest\Amf\TestAsset;

/**
 * Test Class for class mapping tests.
 *
 * @category   Zend
 * @package    Zend_Amf
 * @subpackage UnitTests
 */
class Contact
{
    public $_explicitType = 'ContactVO';
    public $id = 0;
    public $firstname = "";
    public $lastname = "";
    public $email = "";
    public $mobile = "";

    public function getASClassName()
    {
        return 'ContactVO';
    }
}

