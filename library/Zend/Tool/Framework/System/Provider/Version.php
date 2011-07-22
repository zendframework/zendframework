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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Tool\Framework\System\Provider;

use Zend\Tool\Framework\Provider,
    Zend\Tool\Framework\Registry,
    Zend\Tool\Framework\RegistryEnabled;

/**
 * Version Provider
 *
 * @uses       \Zend\Tool\Framework\Provider
 * @uses       \Zend\Tool\Framework\Registry\FrameworkRegistry
 * @uses       \Zend\Version
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Version implements Provider, RegistryEnabled
{

    /**
     * @var \Zend\Tool\Framework\Registry
     */
    protected $_registry = null;

    const MODE_MAJOR = 'major';
    const MODE_MINOR = 'minor';
    const MODE_MINI  = 'mini';

    protected $_specialties = array('MajorPart', 'MinorPart', 'MiniPart');

    public function setRegistry(Registry $registry)
    {
        $this->_registry = $registry;
        return $this;
    }

    /**
     * Show Action
     *
     * @param string $mode The mode switch can be one of: major, minor, or mini (default)
     * @param bool $nameIncluded
     */
    public function show($mode = self::MODE_MINI, $nameIncluded = true)
    {

        $versionInfo = $this->_splitVersion();

        switch($mode) {
            case self::MODE_MINOR:
                unset($versionInfo['mini']);
                break;
            case self::MODE_MAJOR:
                unset($versionInfo['mini'], $versionInfo['minor']);
                break;
        }

        $output = implode('.', $versionInfo);

        if ($nameIncluded) {
            $output = 'Zend Framework Version: ' . $output;
        }

        $this->_registry->response->appendContent($output);
    }

    public function showMajorPart($nameIncluded = true)
    {
        $versionNumbers = $this->_splitVersion();
        $output = (($nameIncluded == true) ? 'ZF Major Version: ' : null) . $versionNumbers['major'];
        $this->_registry->response->appendContent($output);
    }

    public function showMinorPart($nameIncluded = true)
    {
        $versionNumbers = $this->_splitVersion();
        $output = (($nameIncluded == true) ? 'ZF Minor Version: ' : null) . $versionNumbers['minor'];
        $this->_registry->response->appendContent($output);
    }

    public function showMiniPart($nameIncluded = true)
    {
        $versionNumbers = $this->_splitVersion();
        $output = (($nameIncluded == true) ? 'ZF Mini Version: ' : null)  . $versionNumbers['mini'];
        $this->_registry->response->appendContent($output);
    }

    protected function _splitVersion()
    {
        list($major, $minor, $mini) = explode('.', \Zend\Version::VERSION);
        return array('major' => $major, 'minor' => $minor, 'mini' => $mini);
    }

}
