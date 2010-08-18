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
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Tool\Framework\Manifest\TestAsset;

/**
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ManifestGoodOne
    implements \Zend\Tool\Framework\Manifest\ActionManifestable,
        \Zend\Tool\Framework\Manifest\ProviderManifestable,
        \Zend\Tool\Framework\Manifest\MetadataManifestable,
        \Zend\Tool\Framework\Manifest\Indexable
{

    public function getIndex()
    {
        return 5;
    }

    public function getProviders()
    {
        return new ProviderOne();
    }

    public function getActions()
    {
        return new ActionOne();
    }

    public function getMetadata()
    {
        return new \Zend\Tool\Framework\Metadata\Basic(array('name' => 'FooOne', 'value' => 'Bar'));
    }

}
