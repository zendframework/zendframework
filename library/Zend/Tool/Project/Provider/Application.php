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
 * @uses       \Zend\Tool\Framework\Provider\Pretendable
 * @uses       \Zend\Tool\Project\Exception
 * @uses       \Zend\Tool\Project\Provider\AbstractProvider
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Application 
    extends AbstractProvider
    implements \Zend\Tool\Framework\Provider\Pretendable
{
    
    protected $_specialties = array('ClassNamePrefix');
    
    /**
     * 
     * @param $classNamePrefix Prefix of classes
     * @param $force
     */
    public function changeClassNamePrefix($classNamePrefix /* , $force = false */)
    {
        $profile = $this->_loadProfile(self::NO_PROFILE_THROW_EXCEPTION);
        
        $originalClassNamePrefix = $classNamePrefix;
        
        if (substr($classNamePrefix, -1) != '\\') {
            $classNamePrefix .= '\\';
        }
        
        $configFileResource = $profile->search('ApplicationConfigFile');
        $zc = $configFileResource->getAsZendConfig('production');
        if ($zc->appnamespace == $classNamePrefix) {
            throw new \Zend\Tool\Project\Exception('The requested name ' . $classNamePrefix . ' is already the prefix.');
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

        $response = $this->_registry->getResponse();
        
        if ($originalClassNamePrefix !== $classNamePrefix) {
            $response->appendContent(
                'Note: the name provided "' . $originalClassNamePrefix . '" was'
                    . ' altered to "' . $classNamePrefix . '" for correctness.',
                array('color' => 'yellow')
                );
        } 
        
        // note to the user
        $response->appendContent('Note: All existing models will need to be altered to this new namespace by hand', array('color' => 'yellow'));
        $response->appendContent('application.ini updated with new appnamespace ' . $classNamePrefix);
        
        // store profile
        $this->_storeProfile();
    }
}