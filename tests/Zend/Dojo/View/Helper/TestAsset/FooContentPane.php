<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Dojo
 */

namespace ZendTest\Dojo\View\Helper\TestAsset;

use Zend\Dojo\View\Helper\CustomDijit as CustomDijitHelper;

class FooContentPane extends CustomDijitHelper
{
    protected $_defaultDojoType = 'foo.ContentPane';

    public function direct($id = null, $value = null, array $params = array(), array $attribs = array())
    {
        return parent::direct($id, $value, $params, $attribs);
    }
}
