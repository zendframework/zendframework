<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Log\Filter;

use Zend\Log\Filter\Validator;
use Zend\Validator\ValidatorChain;
use Zend\Validator\Digits as DigitsFilter;
use Zend\Validator\NotEmpty as NotEmptyFilter;

/**
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
        $validatorChain->attach(new NotEmptyFilter());
        $validatorChain->attach(new DigitsFilter());
        $filter = new Validator($validatorChain);
        $this->assertTrue($filter->filter(array('message' => '123')));
        $this->assertFalse($filter->filter(array('message' => 'test')));
    }
}
