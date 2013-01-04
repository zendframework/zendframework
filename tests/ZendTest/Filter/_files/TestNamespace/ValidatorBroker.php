<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Filter
 */

namespace TestNamespace;

use Zend\Validator\ValidatorBroker as BaseValidatorBroker;

require_once __DIR__ . '/ValidatorLoader.php';

class ValidatorBroker extends BaseValidatorBroker
{
    protected $defaultClassLoader = 'TestNamespace\ValidatorLoader';
}
