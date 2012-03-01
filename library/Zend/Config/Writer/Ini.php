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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Config\Writer;

use Zend\Config\Exception;

/**
 * @category   Zend
 * @package    Zend_Config
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Ini extends AbstractWriter
{
    /**
     * Separator for nesting levels of configuration data identifiers.
     *
     * @var string
     */
    protected $nestSeparator = '.';

    /**
     * If true the INI string is rendered in the global namespace without
     * sections.
     *
     * @var bool
     */
    protected $renderWithoutSections = false;

    /**
     * Set nest separator.
     *
     * @param  stirng $separator
     * @return self
     */
    public function setNestSeparator($separator)
    {
        $this->nestSeparator = $separator;
        return $this;
    }

    /**
     * Get nest separator.
     *
     * @return string
     */
    public function getNestSeparator()
    {
        return $this->nestSeparator;
    }

    /**
     * Set if rendering should occour without sections or not.
     *
     * If set to true, the INI file is rendered without sections completely
     * into the global namespace of the INI file.
     *
     * @param  bool $withoutSections
     * @return Ini
     */
    public function setRenderWithoutSectionsFlags($withoutSections)
    {
        $this->renderWithoutSections = (bool) $withoutSections;
        return $this;
    }

    /**
     * Return whether the writer should render without sections.
     *
     * @return boolean
     */
    public function shouldRenderWithoutSections()
    {
        return $this->renderWithoutSections;
    }

    /**
     * processConfig(): defined by AbstractWriter.
     *
     * @param  array $config
     * @return string
     */
    public function processConfig(array $config)
    {
        $iniString = '';

        if ($this->shouldRenderWithoutSections()) {
            $iniString .= $this->addBranch($config);
        } else {
            $config = $this->sortRootElements($config);

            foreach ($config as $sectionName => $data) {
                if (!is_array($data)) {
                    $iniString .= $sectionName
                               .  ' = '
                               .  $this->prepareValue($data)
                               .  "\n";
                } else {
                    $iniString .= '[' . $sectionName . ']' . "\n"
                               .  $this->addBranch($data)
                               .  "\n";
                }
            }
        }

        return $iniString;
    }

    /**
     * Add a branch to an INI string recursively.
     *
     * @param  array $config
     * @return string
     */
    protected function addBranch(array $config, $parents = array())
    {
        $iniString = '';

        foreach ($config as $key => $value) {
            $group = array_merge($parents, array($key));

            if (is_array($value)) {
                $iniString .= $this->addBranch($value, $group);
            } else {
                $iniString .= implode($this->nestSeparator, $group)
                           .  ' = '
                           .  $this->prepareValue($value)
                           .  "\n";
            }
        }

        return $iniString;
    }

    /**
     * Prepare a value for INI.
     *
     * @param  mixed $value
     * @return string
     */
    protected function prepareValue($value)
    {
        if (is_integer($value) || is_float($value)) {
            return $value;
        } elseif (is_bool($value)) {
            return ($value ? 'true' : 'false');
        } elseif (false === strpos($value, '"')) {
            return '"' . $value .  '"';
        } else {
            throw new Exception\RuntimeException('Value can not contain double quotes');
        }
    }

    /**
     * Root elements that are not assigned to any section needs to be on the
     * top of config.
     *
     * @param  array $config
     * @return array
     */
    protected function sortRootElements(array $config)
    {
        $sections = array();

        // Remove sections from config array.
        foreach ($config as $key => $value) {
            if (is_array($value)) {
                $sections[$key] = $value;
                unset($config[$key]);
            }
        }

        // Read sections to the end.
        foreach ($sections as $key => $value) {
            $config[$key] = $value;
        }

        return $config;
    }
}
