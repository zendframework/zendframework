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

require_once 'Zend/Tool/Framework/Manifest/ActionManifestable.php';
require_once 'Zend/Tool/Framework/Manifest/ProviderManifestable.php';
require_once 'Zend/Tool/Framework/Manifest/MetadataManifestable.php';
require_once 'Zend/Tool/Framework/Manifest/Indexable.php';
require_once 'Zend/Tool/Framework/Metadata/Basic.php';

require_once 'ProviderOne.php';
require_once 'ActionOne.php';

/**
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Tool_Framework_Manifest_ManifestGoodOne
    implements Zend_Tool_Framework_Manifest_ActionManifestable,
        Zend_Tool_Framework_Manifest_ProviderManifestable,
        Zend_Tool_Framework_Manifest_MetadataManifestable,
        Zend_Tool_Framework_Manifest_Indexable
{

    public function getIndex()
    {
        return 5;
    }

    public function getProviders()
    {
        return new Zend_Tool_Framework_Manifest_ProviderOne();
    }

    public function getActions()
    {
        return new Zend_Tool_Framework_Manifest_ActionOne();
    }

    public function getMetadata()
    {
        return new Zend_Tool_Framework_Metadata_Basic(array('name' => 'FooOne', 'value' => 'Bar'));
    }

}
