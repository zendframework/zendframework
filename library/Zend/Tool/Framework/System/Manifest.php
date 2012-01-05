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
 * @subpackage Framework
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Tool\Framework\System;

/**
 * @uses       \Zend\Tool\Framework\Manifest\ActionManifestable
 * @uses       \Zend\Tool\Framework\Manifest\ProviderManifestable
 * @uses       \Zend\Tool\Framework\System\Action\Create
 * @uses       \Zend\Tool\Framework\System\Action\Delete
 * @uses       \Zend\Tool\Framework\System\Provider\Config
 * @uses       \Zend\Tool\Framework\System\Provider\Manifest
 * @uses       \Zend\Tool\Framework\System\Provider\Phpinfo
 * @uses       \Zend\Tool\Framework\System\Provider\Version
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Manifest
    implements \Zend\Tool\Framework\Manifest\ProviderManifestable, 
               \Zend\Tool\Framework\Manifest\ActionManifestable
{

    public function getProviders()
    {
        $providers = array(
            new Provider\Version(),
            new Provider\Config(),
            new Provider\Phpinfo(),
            new Provider\Manifest()
            );

        return $providers;
    }

    public function getActions()
    {
        $actions = array(
            new Action\Create(),
            new Action\Delete()
            );

        return $actions;
    }
}
