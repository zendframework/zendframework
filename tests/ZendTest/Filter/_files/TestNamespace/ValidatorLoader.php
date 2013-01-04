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

use Zend\Validator\ValidatorLoader as BaseValidatorLoader;

require_once __DIR__ . '/MyDigits.php';
require_once __DIR__ . '/StringEquals.php';

class ValidatorLoader extends BaseValidatorLoader
{
    public function __construct($map = null)
    {
        $this->plugins['mydigits']     = 'TestNamespace\MyDigits';
        $this->plugins['stringequals'] = 'TestNamespace\StringEquals';
        parent::__construct($map);
    }
}
