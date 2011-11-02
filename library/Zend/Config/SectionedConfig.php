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
 * @package    Zend_Config
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Config;

/**
 * @category   Zend
 * @package    Zend_Config
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SectionedConfig extends Config
{
    /**
     * This is used to track section inheritance.
     * 
     * The keys are names of sections that extend other sections, and the values
     * are the extended sections.
     *
     * @var array
     */
    protected $extends = array();
    
    /**
     * Get the current extends.
     *
     * @return array
     */
    public function getExtends()
    {
        return $this->extends;
    }

    /**
     * Set an extend for Zend\Config\Writer.
     *
     * @param  string $extendingSection
     * @param  string $extendedSection
     * @return void
     */
    public function setExtend($extendingSection, $extendedSection = null)
    {
        if ($extendedSection === null && isset($this->extends[$extendingSection])) {
            unset($this->extends[$extendingSection]);
        } else if ($extendedSection !== null) {
            $this->extends[$extendingSection] = $extendedSection;
        }
    }
}
