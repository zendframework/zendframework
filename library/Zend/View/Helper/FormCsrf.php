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
 * @package    Zend\View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\View\Helper;

use Zend\Form\Element\Hash as HashElement;

/**
 * Helper for rendering CSRF token elements outside of Zend\Form using 
 * Zend\Form\Element\Hash
 *
 * @package    Zend\View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FormCsrf extends AbstractHelper
{
    /**
     * @var array
     */
    protected $hashElements;

    /**
     * __invoke 
     * 
     * @param string $name 
     * @return string
     */
    public function __invoke($name = 'csrf')
    {
        if (!isset($this->hashElements[$name])) {
            $this->hashElements[$name] = new HashElement($name);
            $this->hashElements[$name]->setDecorators(array('ViewHelper'));
        }
        return trim($this->hashElements[$name]->render($this->getView()));
    }
}
