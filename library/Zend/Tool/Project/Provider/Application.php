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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Model.php 18386 2009-09-23 20:44:43Z ralph $
 */

/**
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Tool_Project_Provider_Application 
    extends Zend_Tool_Project_Provider_Abstract
    implements Zend_Tool_Framework_Provider_Pretendable
{
    
    protected $_specialties = array('ClassNamePrefix');
    
    public function changeClassNamePrefix($classNamePrefix, $force = false)
    {
        $profile = $this->_loadProfile(self::NO_PROFILE_THROW_EXCEPTION);
        
        $configFileResource = $profile->search('ApplicationConfigFile');
        $zc = $configFileResource->getAsZendConfig('production');
        if ($zc->appnamespace == $classNamePrefix) {
            throw new Zend_Tool_Project_Exception('The requested name ' . $classNamePrefix . ' is already the prefix.');
        }

        // remove the old
        $configFileResource->removeStringItem('appnamespace', 'production');
        $configFileResource->create();
        
        // add the new
        $configFileResource->addStringItem('appnamespace', $classNamePrefix, 'production', true);
        $configFileResource->create();
        
        // update the project profile
        $applicationDirectory = $profile->search('ApplicationDirectory');
        $applicationDirectory->setClassNamePrefix($classNamePrefix);

        // note to the user
        $this->_registry->getResponse()->appendContent('application.ini updated with new appnamespace ' . $classNamePrefix);
        $this->_registry->getResponse()->appendContent('Note: All existing models will need to be altered to this new namespace by hand', array('color' => 'yellow'));
        
        // store profile
        $this->_storeProfile();
    }
    
}
