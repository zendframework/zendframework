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
 * @package    Zend_Translator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Translator\Adapter;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\Translator\Adapter\AbstractAdapter,
    Zend\Translator\Exception\InvalidArgumentException;

/**
 * @category   Zend
 * @package    Zend_Translator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Csv extends AbstractAdapter
{
    /**
     * Generates the adapter
     *
     * @param  array|Traversable $options Translation content
     */
    public function __construct($options = array())
    {
        $this->_options['delimiter'] = ";";
        $this->_options['length']    = 0;
        $this->_options['enclosure'] = '"';

        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } else if (func_num_args() > 1) {
            $args               = func_get_args();
            $options            = array();
            $options['content'] = array_shift($args);

            if (!empty($args)) {
                $options['locale'] = array_shift($args);
            }

            if (!empty($args)) {
                $opt     = array_shift($args);
                $options = array_merge($opt, $options);
            }
        } else if (!is_array($options)) {
            $options = array('content' => $options);
        }

        parent::__construct($options);
    }

    /**
     * Load translation data
     *
     * @param  string|array  $filename  Filename and full path to the translation source
     * @param  string        $locale    Locale/Language to add data for, identical with locale identifier,
     *                                  see Zend_Locale for more information
     * @param  array         $option    OPTIONAL Options to use
     * @throws \Zend\Translator\Exception\InvalidArgumentException
     * @return array
     */
    protected function _loadTranslationData($filename, $locale, array $options = array())
    {
        $result = array();
        $options     = $options + $this->_options;
        $this->_file = @fopen($filename, 'rb');
        if (!$this->_file) {
            throw new InvalidArgumentException('Error opening translation file \'' . $filename . '\'.');
        }

        while(($data = fgetcsv($this->_file, $options['length'], $options['delimiter'], $options['enclosure'])) !== false) {
            if (substr($data[0], 0, 1) === '#') {
                continue;
            }

            if (!isset($data[1])) {
                continue;
            }

            if (count($data) == 2) {
                $result[$locale][$data[0]] = $data[1];
            } else {
                $singular = array_shift($data);
                $result[$locale][$singular] = $data;
            }
        }

        return $result;
    }

    /**
     * returns the adapters name
     *
     * @return string
     */
    public function toString()
    {
        return "Csv";
    }
}
