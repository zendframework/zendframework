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
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Form\Element\TestAsset\TransferAdapter;

use Zend\File\Transfer\Adapter\AbstractAdapter;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Foo extends AbstractAdapter
{
    public $received = false;

    public function send($options = null)
    {
        return;
    }

    public function receive($options = null)
    {
        $this->received = true;
        return;
    }

    public function isSent($file = null)
    {
        return false;
    }

    public function isReceived($file = null)
    {
        return $this->received;
    }

    public function isUploaded($files = null)
    {
        return true;
    }

    public function isFiltered($files = null)
    {
        return true;
    }

    public static function getProgress()
    {
        return;
    }
}
