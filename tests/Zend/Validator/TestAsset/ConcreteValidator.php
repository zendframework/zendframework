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
 * @package    Zend_Validator
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Validator\TestAsset;

use Zend\Validator\AbstractValidator;

class ConcreteValidator extends AbstractValidator
{
    const FOO_MESSAGE = 'fooMessage';

    protected $messageTemplates = array(
        'fooMessage' => '%value% was passed',
    );

    public function isValid($value)
    {
        $this->setValue($value);
        $this->error(self::FOO_MESSAGE);
        return false;
    }
}