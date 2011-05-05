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
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Filter;

use Zend\Loader\PluginClassLoader;

/**
 * Plugin Class Loader implementation for filters.
 *
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FilterLoader extends PluginClassLoader
{
    /**
     * @var array Pre-aliased filter 
     */
    protected $plugins = array(
        'alnum'                         => 'Zend\Filter\Alnum',
        'alpha'                         => 'Zend\Filter\Alpha',
        'basename'                      => 'Zend\Filter\BaseName',
        'base_name'                     => 'Zend\Filter\BaseName',
        'boolean'                       => 'Zend\Filter\Boolean',
        'callback'                      => 'Zend\Filter\Callback',
        'compress'                      => 'Zend\Filter\Compress',
        'compress\bz2'                  => 'Zend\Filter\Compress\Bz2',
        'compress_bz2'                  => 'Zend\Filter\Compress\Bz2',
        'compress\gz'                   => 'Zend\Filter\Compress\Gz',
        'compress_gz'                   => 'Zend\Filter\Compress\Gz',
        'compress\lzf'                  => 'Zend\Filter\Compress\Lzf',
        'compress_lzf'                  => 'Zend\Filter\Compress\Lzf',
        'compress\rar'                  => 'Zend\Filter\Compress\Rar',
        'compress_rar'                  => 'Zend\Filter\Compress\Rar',
        'compress\tar'                  => 'Zend\Filter\Compress\Tar',
        'compress_tar'                  => 'Zend\Filter\Compress\Tar',
        'compress\zip'                  => 'Zend\Filter\Compress\Zip',
        'compress_zip'                  => 'Zend\Filter\Compress\Zip',
        'decompress'                    => 'Zend\Filter\Decompress',
        'decrypt'                       => 'Zend\Filter\Decrypt',
        'digits'                        => 'Zend\Filter\Digits',
        'dir'                           => 'Zend\Filter\Dir',
        'encrypt'                       => 'Zend\Filter\Encrypt',
        'encrypt\mcrypt'                => 'Zend\Filter\Encrypt\Mcrypt',
        'encrypt_mcrypt'                => 'Zend\Filter\Encrypt\Mcrypt',
        'encrypt\openssl'               => 'Zend\Filter\Encrypt\Openssl',
        'encrypt_openssl'               => 'Zend\Filter\Encrypt\Openssl',
        'file\decrypt'                  => 'Zend\Filter\File\Decrypt',
        'file_decrypt'                  => 'Zend\Filter\File\Decrypt',
        'file\encrypt'                  => 'Zend\Filter\File\Encrypt',
        'file_encrypt'                  => 'Zend\Filter\File\Encrypt',
        'file\lowercase'                => 'Zend\Filter\File\LowerCase',
        'file\lower_case'               => 'Zend\Filter\File\LowerCase',
        'file_lowercase'                => 'Zend\Filter\File\LowerCase',
        'file_lower_case'               => 'Zend\Filter\File\LowerCase',
        'file\rename'                   => 'Zend\Filter\File\Rename',
        'file_rename'                   => 'Zend\Filter\File\Rename',
        'file\uppercase'                => 'Zend\Filter\File\UpperCase',
        'file\upper_case'               => 'Zend\Filter\File\UpperCase',
        'file_uppercase'                => 'Zend\Filter\File\UpperCase',
        'file_upper_case'               => 'Zend\Filter\File\UpperCase',
        'htmlentities'                  => 'Zend\Filter\HtmlEntities',
        'html_entities'                 => 'Zend\Filter\HtmlEntities',
        'inflector'                     => 'Zend\Filter\Inflector',
        'int'                           => 'Zend\Filter\Int',
        'localizedtonormalized'         => 'Zend\Filter\LocalizedToNormalized',
        'localized_to_normalized'       => 'Zend\Filter\LocalizedToNormalized',
        'normalizedtolocalized'         => 'Zend\Filter\NormalizedToLocalized',
        'normalized_to_localized'       => 'Zend\Filter\NormalizedToLocalizedTest',
        'null'                          => 'Zend\Filter\Null',
        'pregreplace'                   => 'Zend\Filter\PregReplace',
        'preg_replace'                  => 'Zend\Filter\PregReplace',
        'realpath'                      => 'Zend\Filter\RealPath',
        'real_path'                     => 'Zend\Filter\RealPath',
        'stringtolower'                 => 'Zend\Filter\StringToLower',
        'string_to_lower'               => 'Zend\Filter\StringToLower',
        'stringtoupper'                 => 'Zend\Filter\StringToUpper',
        'string_to_upper'               => 'Zend\Filter\StringToUpper',
        'stringtrim'                    => 'Zend\Filter\StringTrim',
        'string_trim'                   => 'Zend\Filter\StringTrim',
        'stripnewlines'                 => 'Zend\Filter\StripNewlines',
        'strip_newlines'                => 'Zend\Filter\StripNewlines',
        'striptags'                     => 'Zend\Filter\StripTags',
        'strip_tags'                    => 'Zend\Filter\StripTags',
        'word\camelcasetodash'          => 'Zend\Filter\Word\CamelCaseToDash',
        'word\camel_case_to_dash'       => 'Zend\Filter\Word\CamelCaseToDash',
        'word_camelcasetodash'          => 'Zend\Filter\Word\CamelCaseToDash',
        'word_camel_case_to_dash'       => 'Zend\Filter\Word\CamelCaseToDash',
        'word\camelcasetoseparator'     => 'Zend\Filter\Word\CamelCaseToSeparator',
        'word\camel_case_to_separator'  => 'Zend\Filter\Word\CamelCaseToSeparator',
        'word_camelcasetoseparator'     => 'Zend\Filter\Word\CamelCaseToSeparator',
        'word_camel_case_to_separator'  => 'Zend\Filter\Word\CamelCaseToSeparator',
        'word\camelcasetounderscore'    => 'Zend\Filter\Word\CamelCaseToUnderscore',
        'word\camel_case_to_underscore' => 'Zend\Filter\Word\CamelCaseToUnderscore',
        'word_camelcasetounderscore'    => 'Zend\Filter\Word\CamelCaseToUnderscore',
        'word_camel_case_to_underscore' => 'Zend\Filter\Word\CamelCaseToUnderscore',
        'word\dashtocamelcase'          => 'Zend\Filter\Word\DashToCamelCase',
        'word\dash_to_camel_case'       => 'Zend\Filter\Word\DashToCamelCase',
        'word_dashtocamelcase'          => 'Zend\Filter\Word\DashToCamelCase',
        'word_dash_to_camel_case'       => 'Zend\Filter\Word\DashToCamelCase',
        'word\dashtoseparator'          => 'Zend\Filter\Word\DashToSeparator',
        'word\dash_to_separator'        => 'Zend\Filter\Word\DashToSeparator',
        'word_dashtoseparator'          => 'Zend\Filter\Word\DashToSeparator',
        'word_dash_to_separator'        => 'Zend\Filter\Word\DashToSeparator',
        'word\dashtounderscore'         => 'Zend\Filter\Word\DashToUnderscore',
        'word\dash_to_underscore'       => 'Zend\Filter\Word\DashToUnderscore',
        'word_dashtounderscore'         => 'Zend\Filter\Word\DashToUnderscore',
        'word_dash_to_underscore'       => 'Zend\Filter\Word\DashToUnderscore',
        'word\separatortocamelcase'     => 'Zend\Filter\Word\SeparatorToCamelCase',
        'word\separator_to_camel_case'  => 'Zend\Filter\Word\SeparatorToCamelCase',
        'word_separatortocamelcase'     => 'Zend\Filter\Word\SeparatorToCamelCase',
        'word_separator_to_camel_case'  => 'Zend\Filter\Word\SeparatorToCamelCase',
        'word\separatortodash'          => 'Zend\Filter\Word\SeparatorToDash',
        'word\separator_to_dash'        => 'Zend\Filter\Word\SeparatorToDash',
        'word_separatortodash'          => 'Zend\Filter\Word\SeparatorToDash',
        'word_separator_to_dash'        => 'Zend\Filter\Word\SeparatorToDash',
        'word\separatortoseparator'     => 'Zend\Filter\Word\SeparatorToSeparator',
        'word\separator_to_separator'   => 'Zend\Filter\Word\SeparatorToSeparator',
        'word_separatortoseparator'     => 'Zend\Filter\Word\SeparatorToSeparator',
        'word_separator_to_separator'   => 'Zend\Filter\Word\SeparatorToSeparator',
        'word\underscoretocamelcase'    => 'Zend\Filter\Word\UnderscoreToCamelCase',
        'word\underscore_to_camel_case' => 'Zend\Filter\Word\UnderscoreToCamelCase',
        'word_underscoretocamelcase'    => 'Zend\Filter\Word\UnderscoreToCamelCase',
        'word_underscore_to_camel_case' => 'Zend\Filter\Word\UnderscoreToCamelCase',
        'word\underscoretodash'         => 'Zend\Filter\Word\UnderscoreToDash',
        'word\underscore_to_dash'       => 'Zend\Filter\Word\UnderscoreToDash',
        'word_underscoretodash'         => 'Zend\Filter\Word\UnderscoreToDash',
        'word_underscore_to_dash'       => 'Zend\Filter\Word\UnderscoreToDash',
        'word\underscoretoseparator'    => 'Zend\Filter\Word\UnderscoreToSeparator',
        'word\underscore_to_separator'  => 'Zend\Filter\Word\UnderscoreToSeparator',
        'word_underscoretoseparator'    => 'Zend\Filter\Word\UnderscoreToSeparator',
        'word_underscore_to_separator'  => 'Zend\Filter\Word\UnderscoreToSeparator',
    );
}
