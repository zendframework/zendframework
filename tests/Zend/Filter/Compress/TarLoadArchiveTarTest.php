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
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Filter\Compress;

use Zend\Filter\Compress\Tar as TarCompression;
use Zend\Filter\Exception\ExtensionNotLoadedException;
use Zend\Loader\StandardAutoloader;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class TarLoadArchveTarTest extends \PHPUnit_Framework_TestCase
{
    public function testArchiveTarNotLoaded()
    {
        if (class_exists('Archive_Tar')) {
            $this->markTestSkipped('PEAR Archive_Tar is present; skipping test that expects its absence');
        }
        try {
            $tar = new TarCompression;
            $this->fail('ExtensionNotLoadedException was expected but not thrown');
        } catch(ExtensionNotLoadedException $e) {
        }
    }
}
