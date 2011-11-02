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
     * Contains which config sections were loaded.
     * 
     * This is null if all sections were loaded, a string name if one section is
     * loaded and an array of string names if multiple sections were loaded.
     *
     * @var mixed
     */
    protected $loadedSection;

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
     * Returns the section name(s) loaded.
     *
     * @return mixed
     */
    public function getSectionName()
    {
        if (is_array($this->loadedSection) && count($this->_loadedSection) === 1) {
            $this->loadedSection = $this->loadedSection[0];
        }
        
        return $this->loadedSection;
    }

    /**
     * Returns true if all sections were loaded
     *
     * @return boolean
     */
    public function areAllSectionsLoaded()
    {
        return $this->loadedSection === null;
    }
    
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

    /**
     * Throws an exception if $extendingSection may not extend $extendedSection,
     * and tracks the section extension if it is valid.
     *
     * @param  string $extendingSection
     * @param  string $extendedSection
     * @throws \Zend\Config\Exception
     * @return void
     */
    protected function assertValidExtend($extendingSection, $extendedSection)
    {
        // Detect circular section inheritance
        $currentExtendedSection = $extendedSection;
        
        while (array_key_exists($currentExtendedSection, $this->extends)) {
            if ($this->extends[$currentExtendedSection] == $extendingSection) {
                throw new Exception\RuntimeException('Illegal circular inheritance detected');
            }
            
            $currentExtendedSection = $this->_extends[$currentExtendedSection];
        }
        
        // Remember that this section extends another section
        $this->_extends[$extendingSection] = $extendedSection;
    }
}
