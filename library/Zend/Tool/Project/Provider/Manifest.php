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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Tool\Project\Provider;

/**
 * @uses       \Zend\Tool\Framework\Manifest\ProviderManifestable
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Manifest 
    implements \Zend\Tool\Framework\Manifest\ProviderManifestable
{

    /**
     * getProviders()
     *
     * @return array Array of Providers
     */
    public function getProviders()
    {
        // the order here will represent what the output will look like when iterating a manifest
        
        return array(
            // top level project & profile providers
            'Zend\Tool\Project\Provider\Profile',
            'Zend\Tool\Project\Provider\Project',
        
            // app layer provider
            'Zend\Tool\Project\Provider\Application',
        
            // MVC layer providers
            'Zend\Tool\Project\Provider\Model',
            'Zend\Tool\Project\Provider\View',
            'Zend\Tool\Project\Provider\Controller',
            'Zend\Tool\Project\Provider\Action',
            
            // hMVC provider
            'Zend\Tool\Project\Provider\Module',
        
            // application problem providers
            'Zend\Tool\Project\Provider\Form',
            'Zend\Tool\Project\Provider\Layout',
            'Zend\Tool\Project\Provider\DbAdapter',
            'Zend\Tool\Project\Provider\DbTable',
            
            // provider within project provider
            'Zend\Tool\Project\Provider\ProjectProvider',
            
        );
    }
}
