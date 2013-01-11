<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Filter
 */

namespace ZendTest\Filter\Compress;

use Zend\Filter\Compress\Tar as TarCompression;
use Zend\Filter\Exception\ExtensionNotLoadedException;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
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
        } catch (ExtensionNotLoadedException $e) {
        }
    }
}
