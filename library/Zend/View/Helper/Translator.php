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
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\View\Helper;
use Zend\View;
use Zend;

/**
 * Translation view helper
 *
 * @uses      \Zend\Locale\Locale
 * @uses      \Zend\Registry
 * @uses      \Zend\View\Exception
 * @uses      \Zend\View\Helper\AbstractHelper
 * @category  Zend
 * @package   Zend_View
 * @copyright Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Translator extends AbstractHelper
{
    /**
     * Translation object
     *
     * @var \Zend\Translator\Adapter\Adapter
     */
    protected $_translator;

    /**
     * Constructor for manually handling
     *
     * @param \Zend\Translator\Translator|\Zend\Translator\Translator_Adapter $translate Instance of \Zend\Translator\Translator
     */
    public function __construct($translate = null)
    {
        if ($translate !== null) {
            $this->setTranslator($translate);
        }
    }

    /**
     * Translate a message
     * You can give multiple params or an array of params.
     * If you want to output another locale just set it as last single parameter
     * Example 1: translate('%1\$s + %2\$s', $value1, $value2, $locale);
     * Example 2: translate('%1\$s + %2\$s', array($value1, $value2), $locale);
     *
     * @param  string $messageid Id of the message to be translated
     * @return string|\Zend\View\Helper\Translator Translated message
     */
    public function __invoke($messageid = null)
    {
        if ($messageid === null) {
            return $this;
        }

        $translate = $this->getTranslator();
        $options   = func_get_args();

        array_shift($options);
        $count  = count($options);
        $locale = null;
        if ($count > 0) {
            if (\Zend\Locale\Locale::isLocale($options[($count - 1)]) !== false) {
                $locale = array_pop($options);
            }
        }

        if ((count($options) === 1) and (is_array($options[0]) === true)) {
            $options = $options[0];
        }

        if ($translate !== null) {
            $messageid = $translate->translate($messageid, $locale);
        }

        if (count($options) === 0) {
            return $messageid;
        }

        return vsprintf($messageid, $options);
    }

    /**
     * Sets a translation Adapter for translation
     *
     * @param  \Zend\Translator\Translator|\Zend\Translator\Translator_Adapter $translate Instance of \Zend\Translator\Translator
     * @throws \Zend\View\Exception When no or a false instance was set
     * @return \Zend\View\Helper\Translator
     */
    public function setTranslator($translate)
    {
        if ($translate instanceof \Zend\Translator\Adapter\AbstractAdapter) {
            $this->_translator = $translate;
        } else if ($translate instanceof \Zend\Translator\Translator) {
            $this->_translator = $translate->getAdapter();
        } else {
            $e = new View\Exception('You must set an instance of Zend_Translator or Zend_Translator_Adapter');
            $e->setView($this->view);
            throw $e;
        }

        return $this;
    }

    /**
     * Retrieve translation object
     *
     * @return \Zend\Translator\Adapter\Adapter|null
     */
    public function getTranslator()
    {
        if ($this->_translator === null) {
            if (\Zend\Registry::isRegistered('Zend_Translator')) {
                $this->setTranslator(\Zend\Registry::get('Zend_Translator'));
            }
        }

        return $this->_translator;
    }

    /**
     * Set's an new locale for all further translations
     *
     * @param  string|\Zend\Locale\Locale $locale New locale to set
     * @throws Zend_View_Exception When no \Zend\Translator\Translator instance was set
     * @return \Zend\View\Helper\Translator
     */
    public function setLocale($locale = null)
    {
        $translate = $this->getTranslator();
        if ($translate === null) {
            $e = new View\Exception('You must set an instance of Zend_Translator or Zend_Translator_Adapter');
            $e->setView($this->view);
            throw $e;
        }

        $translate->setLocale($locale);
        return $this;
    }

    /**
     * Returns the set locale for translations
     *
     * @throws Zend_View_Exception When no \Zend\Translator\Translator instance was set
     * @return string|\Zend\Locale\Locale
     */
    public function getLocale()
    {
        $translate = $this->getTranslator();
        if ($translate === null) {
            $e = new View\Exception('You must set an instance of Zend_Translator or Zend_Translator_Adapter');
            $e->setView($this->view);
            throw $e;
        }

        return $translate->getLocale();
    }
}
