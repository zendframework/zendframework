<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Captcha
 */

namespace ZendTest\Captcha\TestAsset;

use Zend\Captcha\AdapterInterface;

/**
 * @category   Zend
 * @package    Zend_Captcha
 * @subpackage UnitTest
 */
class MockCaptcha implements AdapterInterface
{
    public $name;
    public $options = array();

    public function __construct($options = null)
    {
        $this->options = $options;
    }

    public function generate()
    {
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getHelperName()
    {
        return 'doctype';
    }

    public function isValid($value)
    {
        return true;
    }

    public function getMessages()
    {
        return array();
    }
}
