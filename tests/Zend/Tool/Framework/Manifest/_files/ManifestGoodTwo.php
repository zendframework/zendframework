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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'Zend/Tool/Framework/Manifest/ActionManifestable.php';
require_once 'Zend/Tool/Framework/Manifest/ProviderManifestable.php';
require_once 'Zend/Tool/Framework/Manifest/MetadataManifestable.php';
require_once 'Zend/Tool/Framework/Manifest/Indexable.php';
require_once 'Zend/Tool/Framework/Metadata/Basic.php';

require_once 'ProviderTwo.php';
require_once 'ActionTwo.php';

/**
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Tool_Framework_Manifest_ManifestGoodTwo
    implements Zend_Tool_Framework_Manifest_ActionManifestable,
        Zend_Tool_Framework_Manifest_ProviderManifestable,
        Zend_Tool_Framework_Manifest_MetadataManifestable,
        Zend_Tool_Framework_Manifest_Indexable,
        Zend_Tool_Framework_Registry_EnabledInterface
{

    protected $_registry = null;

    public function setRegistry(Zend_Tool_Framework_Registry_Interface $registry)
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
            new Zend_Tool_Framework_Manifest_ProviderTwo()
            );
    }

    public function getActions()
    {
        return array(
            new Zend_Tool_Framework_Manifest_ActionTwo(),
            'Foo'
            );
    }

    public function getMetadata()
    {
        return array(
            new Zend_Tool_Framework_Metadata_Basic(array('name' => 'FooTwo', 'value' => 'Baz1')),
            new Zend_Tool_Framework_Metadata_Basic(array('name' => 'FooThree', 'value' => 'Baz2'))
            );

    }

}
