<?php

namespace Zend\Filter;

use Zend\ServiceManager\ConfigurationInterface;
use Zend\ServiceManager\ServiceManager as BaseServiceManager;

class ServiceManager extends BaseServiceManager
{
    protected $allowOverride   = true;
    protected $creationOptions = null;

    protected $aliases = array(
        'base_name'                      => 'basename',
        'compress_bz2'                   => 'compress\\bz2',
        'compress_gz'                    => 'compress\\gz',
        'compress_lzf'                   => 'compress\\lzf',
        'compress_rar'                   => 'compress\\rar',
        'compress_tar'                   => 'compress\\tar',
        'compress_zip'                   => 'compress\\zip',
        'encrypt_mcrypt'                 => 'encrypt\\mcrypt',
        'encrypt_openssl'                => 'encrypt\\openssl',
        'file_decrypt'                   => 'file\\decrypt',
        'file_encrypt'                   => 'file\\encrypt',
        'file\\lower_case'               => 'file\\lowercase',
        'file_lowercase'                 => 'file\\lowercase',
        'file_lower_case'                => 'file\\lowercase',
        'file_rename'                    => 'file\\rename',
        'file\\upper_case'               => 'file\\uppercase',
        'file_uppercase'                 => 'file\\uppercase',
        'file_upper_case'                => 'file\\uppercase',
        'html_entities'                  => 'htmlentities',
        'localized_to_normalized'        => 'localizedtonormalized',
        'normalized_to_localized'        => 'normalizedtolocalized',
        'preg_replace'                   => 'pregreplace',
        'real_path'                      => 'realpath',
        'string_to_lower'                => 'stringtolower',
        'string_to_upper'                => 'stringtoupper',
        'string_trim'                    => 'stringtrim',
        'strip_newlines'                 => 'stripnewlines',
        'strip_tags'                     => 'striptags',
        'word\\camel_case_to_dash'       => 'word\\camelcasetodash',
        'word_camelcasetodash'           => 'word\\camelcasetodash',
        'word_camel_case_to_dash'        => 'word\\camelcasetodash',
        'word\\camel_case_to_separator'  => 'word\\camelcasetoseparator',
        'word_camelcasetoseparator'      => 'word\\camelcasetoseparator',
        'word_camel_case_to_separator'   => 'word\\camelcasetoseparator',
        'word\\camel_case_to_underscore' => 'word\\camelcasetounderscore',
        'word_camelcasetounderscore'     => 'word\\camelcasetounderscore',
        'word_camel_case_to_underscore'  => 'word\\camelcasetounderscore',
        'word\\dash_to_camel_case'       => 'word\\dashtocamelcase',
        'word_dashtocamelcase'           => 'word\\dashtocamelcase',
        'word_dash_to_camel_case'        => 'word\\dashtocamelcase',
        'word\\dash_to_separator'        => 'word\\dashtoseparator',
        'word_dashtoseparator'           => 'word\\dashtoseparator',
        'word_dash_to_separator'         => 'word\\dashtoseparator',
        'word\\dash_to_underscore'       => 'word\\dashtounderscore',
        'word_dashtounderscore'          => 'word\\dashtounderscore',
        'word_dash_to_underscore'        => 'word\\dashtounderscore',
        'word\\separator_to_camel_case'  => 'word\\separatortocamelcase',
        'word_separatortocamelcase'      => 'word\\separatortocamelcase',
        'word_separator_to_camel_case'   => 'word\\separatortocamelcase',
        'word\\separator_to_dash'        => 'word\\separatortodash',
        'word_separatortodash'           => 'word\\separatortodash',
        'word_separator_to_dash'         => 'word\\separatortodash',
        'word\\separator_to_separator'   => 'word\\separatortoseparator',
        'word_separatortoseparator'      => 'word\\separatortoseparator',
        'word_separator_to_separator'    => 'word\\separatortoseparator',
        'word\\underscore_to_camel_case' => 'word\\underscoretocamelcase',
        'word_underscoretocamelcase'     => 'word\\underscoretocamelcase',
        'word_underscore_to_camel_case'  => 'word\\underscoretocamelcase',
        'word\\underscore_to_dash'       => 'word\\underscoretodash',
        'word_underscoretodash'          => 'word\\underscoretodash',
        'word_underscore_to_dash'        => 'word\\underscoretodash',
        'word\\underscore_to_separator'  => 'word\\underscoretoseparator',
        'word_underscoretoseparator'     => 'word\\underscoretoseparator',
        'word_underscore_to_separator'   => 'word\\underscoretoseparator',
    );

    protected $invokableClasses = array(
        'alnum'                          => 'Zend\Filter\Alnum',
        'alpha'                          => 'Zend\Filter\Alpha',
        'basename'                       => 'Zend\Filter\BaseName',
        'boolean'                        => 'Zend\Filter\Boolean',
        'callback'                       => 'Zend\Filter\Callback',
        'compress'                       => 'Zend\Filter\Compress',
        'compress\\bz2'                  => 'Zend\Filter\Compress\Bz2',
        'compress\\gz'                   => 'Zend\Filter\Compress\Gz',
        'compress\\lzf'                  => 'Zend\Filter\Compress\Lzf',
        'compress\\rar'                  => 'Zend\Filter\Compress\Rar',
        'compress\\tar'                  => 'Zend\Filter\Compress\Tar',
        'compress\\zip'                  => 'Zend\Filter\Compress\Zip',
        'decompress'                     => 'Zend\Filter\Decompress',
        'decrypt'                        => 'Zend\Filter\Decrypt',
        'digits'                         => 'Zend\Filter\Digits',
        'dir'                            => 'Zend\Filter\Dir',
        'encrypt'                        => 'Zend\Filter\Encrypt',
        'encrypt\\mcrypt'                => 'Zend\Filter\Encrypt\Mcrypt',
        'encrypt\\openssl'               => 'Zend\Filter\Encrypt\Openssl',
        'encrypt_openssl'                => 'Zend\Filter\Encrypt\Openssl',
        'file\\decrypt'                  => 'Zend\Filter\File\Decrypt',
        'file\\encrypt'                  => 'Zend\Filter\File\Encrypt',
        'file\\lowercase'                => 'Zend\Filter\File\LowerCase',
        'file\\rename'                   => 'Zend\Filter\File\Rename',
        'file\\uppercase'                => 'Zend\Filter\File\UpperCase',
        'htmlentities'                   => 'Zend\Filter\HtmlEntities',
        'inflector'                      => 'Zend\Filter\Inflector',
        'int'                            => 'Zend\Filter\Int',
        'localizedtonormalized'          => 'Zend\Filter\LocalizedToNormalized',
        'normalizedtolocalized'          => 'Zend\Filter\NormalizedToLocalized',
        'null'                           => 'Zend\Filter\Null',
        'pregreplace'                    => 'Zend\Filter\PregReplace',
        'realpath'                       => 'Zend\Filter\RealPath',
        'stringtolower'                  => 'Zend\Filter\StringToLower',
        'stringtoupper'                  => 'Zend\Filter\StringToUpper',
        'stringtrim'                     => 'Zend\Filter\StringTrim',
        'stripnewlines'                  => 'Zend\Filter\StripNewlines',
        'striptags'                      => 'Zend\Filter\StripTags',
        'word\\camelcasetodash'          => 'Zend\Filter\Word\CamelCaseToDash',
        'word\\camelcasetoseparator'     => 'Zend\Filter\Word\CamelCaseToSeparator',
        'word\\camelcasetounderscore'    => 'Zend\Filter\Word\CamelCaseToUnderscore',
        'word\\dashtocamelcase'          => 'Zend\Filter\Word\DashToCamelCase',
        'word\\dashtoseparator'          => 'Zend\Filter\Word\DashToSeparator',
        'word\\dashtounderscore'         => 'Zend\Filter\Word\DashToUnderscore',
        'word\\separatortocamelcase'     => 'Zend\Filter\Word\SeparatorToCamelCase',
        'word\\separatortodash'          => 'Zend\Filter\Word\SeparatorToDash',
        'word\\separatortoseparator'     => 'Zend\Filter\Word\SeparatorToSeparator',
        'word\\underscoretocamelcase'    => 'Zend\Filter\Word\UnderscoreToCamelCase',
        'word\\underscoretodash'         => 'Zend\Filter\Word\UnderscoreToDash',
        'word\\underscoretoseparator'    => 'Zend\Filter\Word\UnderscoreToSeparator',
    );

    public function __construct(ConfigurationInterface $configuration = null)
    {
        parent::__construct($configuration);
        $this->addInitializer(array($this, 'validatePlugin'), true);
    }

    public function validatePlugin($plugin)
    {
        if ($plugin instanceof FilterInterface) {
            // we're okay
            return;
        }
        if (is_callable($plugin)) {
            // also okay
            return;
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement %s\FilterInterface or be callable',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }

    public function get($name, $options = array(), $usePeeringServiceManagers = true)
    {
        $this->creationOptions = $options;
        $instance = parent::get($name, $usePeeringServiceManagers);
        $this->creationOptions = null;
        return $instance;
    }

    /**
     * Attempt to create an instance via an invokable class
     *
     * Overrides parent implementation by passing $creationOptions to the 
     * constructor, if non-null.
     * 
     * @param  string $canonicalName 
     * @param  string $requestedName 
     * @return null|\stdClass
     * @throws Exception\ServiceNotCreatedException If resolved class does not exist
     */
    protected function createFromInvokable($canonicalName, $requestedName)
    {
        $invokable = $this->invokableClasses[$canonicalName];
        if (!class_exists($invokable)) {
            throw new Exception\ServiceNotCreatedException(sprintf(
                '%s: failed retrieving "%s%s" via invokable class "%s"; class does not exist',
                __METHOD__,
                $canonicalName,
                ($requestedName ? '(alias: ' . $requestedName . ')' : ''),
                $canonicalName
            ));
        }

        if (null === $this->creationOptions) {
            $instance = new $invokable();
        } else {
            $instance = new $invokable($this->creationOptions);
        }

        return $instance;
    }
}
