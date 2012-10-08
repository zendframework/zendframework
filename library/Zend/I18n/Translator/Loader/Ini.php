<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace Zend\I18n\Translator\Loader;

use Zend\Config\Reader\Ini as IniReader;
use Zend\I18n\Exception;
use Zend\I18n\Translator\Plural\Rule as PluralRule;
use Zend\I18n\Translator\TextDomain;

/**
 * PHP INI format loader.
 *
 * @category   Zend
 * @package    Zend_I18n
 * @subpackage Translator
 */
class Ini implements FileLoaderInterface
{
    /**
     * load(): defined by FileLoaderInterface.
     *
     * @see    FileLoaderInterface::load()
     * @param  string $locale
     * @param  string $filename
     * @return TextDomain|null
     * @throws Exception\InvalidArgumentException
     */
    public function load($locale, $filename)
    {
        if (!is_file($filename) || !is_readable($filename)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Could not open file %s for reading',
                $filename
            ));
        }
        
        $messages = array();
        $iniReader = new IniReader();
        $messagesNamespaced = $iniReader->fromFile($filename);
        
        $list = $messagesNamespaced;
        if(isset($messagesNamespaced['translate'])) {
           $list = $messagesNamespaced['translate'];
        }
        foreach($list as $message) {
            $messages[$message['message']] = $message['translate'];
        }

        if (!is_array($messages)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected an array, but received %s',
                gettype($messages)
            ));
        }

        $textDomain = new TextDomain($messages);
        
        if (array_key_exists('plural', $messagesNamespaced)) {
            if (isset($messagesNamespaced['plural']['plural_forms'])) {
                
                $textDomain->setPluralRule(
                    PluralRule::fromString($messagesNamespaced['plural']['plural_forms'])
                );
            }
        }

        return $textDomain;
    }
}
