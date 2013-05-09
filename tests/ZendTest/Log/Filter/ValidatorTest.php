<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace ZendTest\Log\Filter;

use Zend\Log\Filter\Validator;
use Zend\Validator\ValidatorChain;
use Zend\Validator\Digits as DigitsFilter;
use Zend\I18n\Validator\Int;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @group      Zend_Log
 */
class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testValidatorFilter()
    {
        $filter = new Validator(new DigitsFilter());
        $this->assertTrue($filter->filter(array('message' => '123')));
        $this->assertFalse($filter->filter(array('message' => 'test')));
        $this->assertFalse($filter->filter(array('message' => 'test123')));
        $this->assertFalse($filter->filter(array('message' => '(%$')));
    }

    public function testValidatorChain()
    {
        $validatorChain = new ValidatorChain();
        $validatorChain->attach(new DigitsFilter());
        $validatorChain->attach(new Int());
        $filter = new Validator($validatorChain);
        $this->assertTrue($filter->filter(array('message' => '123')));
        $this->assertFalse($filter->filter(array('message' => 'test')));
    }
}
