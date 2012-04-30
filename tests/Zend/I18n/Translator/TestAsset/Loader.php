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
 * @package    ZendTest_I18n
 * @subpackage Translator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\I18n\Translator\TestAsset;

use Zend\I18n\Translator\Loader\LoaderInterface;

/**
 * Test loader.
 *
 * @category   Zend
 * @package    ZendTest_I18n
 * @subpackage Translator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Loader implements LoaderInterface
{
    public $textDomain;

    /**
     * load(): defined by LoaderInterface.
     *
     * @see    LoaderInterface::load()
     * @param  string $filename
     * @param  string $locale
     * @return TextDomain|null
     */
    public function load($filename, $locale)
    {
        return $this->textDomain;
    }
}
