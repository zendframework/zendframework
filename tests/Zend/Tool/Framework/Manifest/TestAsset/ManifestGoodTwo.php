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
use Zend\Tool\Framework\Metadata;

/**
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ManifestGoodTwo
    implements \Zend\Tool\Framework\Manifest\ActionManifestable,
        \Zend\Tool\Framework\Manifest\ProviderManifestable,
        \Zend\Tool\Framework\Manifest\MetadataManifestable,
        \Zend\Tool\Framework\Manifest\Indexable,
        \Zend\Tool\Framework\RegistryEnabled
{

    protected $_registry = null;

    public function setRegistry(\Zend\Tool\Framework\Registry $registry)
    {
        $this->_registry = $registry;
    }

    public function getIndex()
    {
        return 10;
    }

    public function getProviders()
    {
        return array(
            new ProviderTwo()
            );
    }

    public function getActions()
    {
        return array(
            new ActionTwo(),
            'Foo'
            );
    }

    public function getMetadata()
    {
        return array(
            new Metadata\Basic(array('name' => 'FooTwo', 'value' => 'Baz1')),
            new Metadata\Basic(array('name' => 'FooThree', 'value' => 'Baz2'))
            );

    }

}
