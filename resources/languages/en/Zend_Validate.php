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
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * EN-Revision: 09.Sept.2012
 */
return array(
    // Zend_I18n_Validator_Alnum
    "Invalid type given. String, integer or float expected" => "Invalid type given. String, integer or float expected",
    "The input contains characters which are non alphabetic and no digits" => "The input contains characters which are non alphabetic and no digits",
    "The input is an empty string" => "The input is an empty string",

    // Zend_I18n_Validator_Alpha
    "Invalid type given. String expected" => "Invalid type given. String expected",
    "The input contains non alphabetic characters" => "The input contains non alphabetic characters",
    "The input is an empty string" => "The input is an empty string",

    // Zend_I18n_Validator_Float
    "Invalid type given. String, integer or float expected" => "Invalid type given. String, integer or float expected",
    "The input does not appear to be a float" => "The input does not appear to be a float",

    // Zend_I18n_Validator_Int
    "Invalid type given. String or integer expected" => "Invalid type given. String or integer expected",
    "The input does not appear to be an integer" => "The input does not appear to be an integer",

    // Zend_I18n_Validator_PostCode
    "Invalid type given. String or integer expected" => "Invalid type given. String or integer expected",
    "The input does not appear to be a postal code" => "The input does not appear to be a postal code",
    "An exception has been raised while validating the input" => "An exception has been raised while validating the input",

    // Zend_Validator_Barcode
    "The input failed checksum validation" => "The input failed checksum validation",
    "The input contains invalid characters" => "The input contains invalid characters",
    "The input should have a length of %length% characters" => "The input should have a length of %length% characters",
    "Invalid type given. String expected" => "Invalid type given. String expected",

    // Zend_Validator_Between
    "The input is not between '%min%' and '%max%', inclusively" => "The input is not between '%min%' and '%max%', inclusively",
    "The input is not strictly between '%min%' and '%max%'" => "The input is not strictly between '%min%' and '%max%'",

    // Zend_Validator_Callback
    "The input is not valid" => "The input is not valid",
    "An exception has been raised within the callback" => "An exception has been raised within the callback",

    // Zend_Validator_CreditCard
    "The input seems to contain an invalid checksum" => "The input seems to contain an invalid checksum",
    "The input must contain only digits" => "The input must contain only digits",
    "Invalid type given. String expected" => "Invalid type given. String expected",
    "The input contains an invalid amount of digits" => "The input contains an invalid amount of digits",
    "The input is not from an allowed institute" => "The input is not from an allowed institute",
    "The input seems to be an invalid creditcard number" => "The input seems to be an invalid creditcard number",
    "An exception has been raised while validating the input" => "An exception has been raised while validating the input",

    // Zend_Validator_Csrf
    "The form submitted did not originate from the expected site" => "The form submitted did not originate from the expected site",

    // Zend_Validator_Date
    "Invalid type given. String, integer, array or DateTime expected" => "Invalid type given. String, integer, array or DateTime expected",
    "The input does not appear to be a valid date" => "The input does not appear to be a valid date",
    "The input does not fit the date format '%format%'" => "The input does not fit the date format '%format%'",

    // Zend_Validator_DateStep
    "Invalid type given. String, integer, array or DateTime expected" => "Invalid type given. String, integer, array or DateTime expected",
    "The input does not appear to be a valid date" => "The input does not appear to be a valid date",
    "The input is not a valid step" => "The input is not a valid step",

    // Zend_Validator_Db_AbstractDb
    "No record matching the input was found" => "No record matching the input was found",
    "A record matching the input was found" => "A record matching the input was found",

    // Zend_Validator_Digits
    "The input must contain only digits" => "The input must contain only digits",
    "The input is an empty string" => "The input is an empty string",
    "Invalid type given. String, integer or float expected" => "Invalid type given. String, integer or float expected",

    // Zend_Validator_EmailAddress
    "Invalid type given. String expected" => "Invalid type given. String expected",
    "The input is not a valid email address. Use the basic format local-part@hostname" => "The input is not a valid email address. Use the basic format local-part@hostname",
    "'%hostname%' is not a valid hostname for the email address" => "'%hostname%' is not a valid hostname for the email address",
    "'%hostname%' does not appear to have any valid MX or A records for the email address" => "'%hostname%' does not appear to have any valid MX or A records for the email address",
    "'%hostname%' is not in a routable network segment. The email address should not be resolved from public network" => "'%hostname%' is not in a routable network segment. The email address should not be resolved from public network",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' can not be matched against dot-atom format",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' can not be matched against quoted-string format",
    "'%localPart%' is not a valid local part for the email address" => "'%localPart%' is not a valid local part for the email address",
    "The input exceeds the allowed length" => "The input exceeds the allowed length",

    // Zend_Validator_Explode
    "Invalid type given. String expected" => "Invalid type given. String expected",

    // Zend_Validator_File_Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "Too many files, maximum '%max%' are allowed but '%count%' are given",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "Too few files, minimum '%min%' are expected but '%count%' are given",

    // Zend_Validator_File_Crc32
    "File '%value%' does not match the given crc32 hashes" => "File '%value%' does not match the given crc32 hashes",
    "A crc32 hash could not be evaluated for the given file" => "A crc32 hash could not be evaluated for the given file",
    "File '%value%' is not readable or does not exist" => "File '%value%' is not readable or does not exist",

    // Zend_Validator_File_ExcludeExtension
    "File '%value%' has a false extension" => "File '%value%' has a false extension",
    "File '%value%' is not readable or does not exist" => "File '%value%' is not readable or does not exist",

    // Zend_Validator_File_Exists
    "File '%value%' does not exist" => "File '%value%' does not exist",

    // Zend_Validator_File_Extension
    "File '%value%' has a false extension" => "File '%value%' has a false extension",
    "File '%value%' is not readable or does not exist" => "File '%value%' is not readable or does not exist",

    // Zend_Validator_File_FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "All files in sum should have a maximum size of '%max%' but '%size%' were detected",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "All files in sum should have a minimum size of '%min%' but '%size%' were detected",
    "One or more files can not be read" => "One or more files can not be read",

    // Zend_Validator_File_Hash
    "File '%value%' does not match the given hashes" => "File '%value%' does not match the given hashes",
    "A hash could not be evaluated for the given file" => "A hash could not be evaluated for the given file",
    "File '%value%' is not readable or does not exist" => "File '%value%' is not readable or does not exist",

    // Zend_Validator_File_ImageSize
    "Maximum allowed width for image '%value%' should be '%maxwidth%' but '%width%' detected" => "Maximum allowed width for image '%value%' should be '%maxwidth%' but '%width%' detected",
    "Minimum expected width for image '%value%' should be '%minwidth%' but '%width%' detected" => "Minimum expected width for image '%value%' should be '%minwidth%' but '%width%' detected",
    "Maximum allowed height for image '%value%' should be '%maxheight%' but '%height%' detected" => "Maximum allowed height for image '%value%' should be '%maxheight%' but '%height%' detected",
    "Minimum expected height for image '%value%' should be '%minheight%' but '%height%' detected" => "Minimum expected height for image '%value%' should be '%minheight%' but '%height%' detected",
    "The size of image '%value%' could not be detected" => "The size of image '%value%' could not be detected",
    "File '%value%' is not readable or does not exist" => "File '%value%' is not readable or does not exist",

    // Zend_Validator_File_IsCompressed
    "File '%value%' is not compressed, '%type%' detected" => "File '%value%' is not compressed, '%type%' detected",
    "The mimetype of file '%value%' could not be detected" => "The mimetype of file '%value%' could not be detected",
    "File '%value%' is not readable or does not exist" => "File '%value%' is not readable or does not exist",

    // Zend_Validator_File_IsImage
    "File '%value%' is no image, '%type%' detected" => "File '%value%' is no image, '%type%' detected",
    "The mimetype of file '%value%' could not be detected" => "The mimetype of file '%value%' could not be detected",
    "File '%value%' is not readable or does not exist" => "File '%value%' is not readable or does not exist",

    // Zend_Validator_File_Md5
    "File '%value%' does not match the given md5 hashes" => "File '%value%' does not match the given md5 hashes",
    "A md5 hash could not be evaluated for the given file" => "A md5 hash could not be evaluated for the given file",
    "File '%value%' is not readable or does not exist" => "File '%value%' is not readable or does not exist",

    // Zend_Validator_File_MimeType
    "File '%value%' has a false mimetype of '%type%'" => "File '%value%' has a false mimetype of '%type%'",
    "The mimetype of file '%value%' could not be detected" => "The mimetype of file '%value%' could not be detected",
    "File '%value%' is not readable or does not exist" => "File '%value%' is not readable or does not exist",

    // Zend_Validator_File_NotExists
    "File '%value%' exists" => "File '%value%' exists",

    // Zend_Validator_File_Sha1
    "File '%value%' does not match the given sha1 hashes" => "File '%value%' does not match the given sha1 hashes",
    "A sha1 hash could not be evaluated for the given file" => "A sha1 hash could not be evaluated for the given file",
    "File '%value%' is not readable or does not exist" => "File '%value%' is not readable or does not exist",

    // Zend_Validator_File_Size
    "Maximum allowed size for file '%value%' is '%max%' but '%size%' detected" => "Maximum allowed size for file '%value%' is '%max%' but '%size%' detected",
    "Minimum expected size for file '%value%' is '%min%' but '%size%' detected" => "Minimum expected size for file '%value%' is '%min%' but '%size%' detected",
    "File '%value%' is not readable or does not exist" => "File '%value%' is not readable or does not exist",

    // Zend_Validator_File_Upload
    "File '%value%' exceeds the defined ini size" => "File '%value%' exceeds the defined ini size",
    "File '%value%' exceeds the defined form size" => "File '%value%' exceeds the defined form size",
    "File '%value%' was only partially uploaded" => "File '%value%' was only partially uploaded",
    "File '%value%' was not uploaded" => "File '%value%' was not uploaded",
    "No temporary directory was found for file '%value%'" => "No temporary directory was found for file '%value%'",
    "File '%value%' can't be written" => "File '%value%' can't be written",
    "A PHP extension returned an error while uploading the file '%value%'" => "A PHP extension returned an error while uploading the file '%value%'",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "File '%value%' was illegally uploaded. This could be a possible attack",
    "File '%value%' was not found" => "File '%value%' was not found",
    "Unknown error while uploading file '%value%'" => "Unknown error while uploading file '%value%'",

    // Zend_Validator_File_WordCount
    "Too much words, maximum '%max%' are allowed but '%count%' were counted" => "Too much words, maximum '%max%' are allowed but '%count%' were counted",
    "Too less words, minimum '%min%' are expected but '%count%' were counted" => "Too less words, minimum '%min%' are expected but '%count%' were counted",
    "File '%value%' is not readable or does not exist" => "File '%value%' is not readable or does not exist",

    // Zend_Validator_GreaterThan
    "The input is not greater than '%min%'" => "The input is not greater than '%min%'",
    "The input is not greater or equal than '%min%'" => "The input is not greater or equal than '%min%'",

    // Zend_Validator_Hex
    "Invalid type given. String expected" => "Invalid type given. String expected",
    "The input contains non-hexadecimal characters" => "The input contains non-hexadecimal characters",

    // Zend_Validator_Hostname
    "The input appears to be a DNS hostname but the given punycode notation cannot be decoded" => "The input appears to be a DNS hostname but the given punycode notation cannot be decoded",
    "Invalid type given. String expected" => "Invalid type given. String expected",
    "The input appears to be a DNS hostname but contains a dash in an invalid position" => "The input appears to be a DNS hostname but contains a dash in an invalid position",
    "The input does not match the expected structure for a DNS hostname" => "The input does not match the expected structure for a DNS hostname",
    "The input appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "The input appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'",
    "The input does not appear to be a valid local network name" => "The input does not appear to be a valid local network name",
    "The input does not appear to be a valid URI hostname" => "The input does not appear to be a valid URI hostname",
    "The input appears to be an IP address, but IP addresses are not allowed" => "The input appears to be an IP address, but IP addresses are not allowed",
    "The input appears to be a local network name but local network names are not allowed" => "The input appears to be a local network name but local network names are not allowed",
    "The input appears to be a DNS hostname but cannot extract TLD part" => "The input appears to be a DNS hostname but cannot extract TLD part",
    "The input appears to be a DNS hostname but cannot match TLD against known list" => "The input appears to be a DNS hostname but cannot match TLD against known list",

    // Zend_Validator_Iban
    "Unknown country within the IBAN" => "Unknown country within the IBAN",
    "Countries outside the Single Euro Payments Area (SEPA) are not supported" => "Countries outside the Single Euro Payments Area (SEPA) are not supported",
    "The input has a false IBAN format" => "The input has a false IBAN format",
    "The input has failed the IBAN check" => "The input has failed the IBAN check",

    // Zend_Validator_Identical
    "The two given tokens do not match" => "The two given tokens do not match",
    "No token was provided to match against" => "No token was provided to match against",

    // Zend_Validator_InArray
    "The input was not found in the haystack" => "The input was not found in the haystack",

    // Zend_Validator_Ip
    "Invalid type given. String expected" => "Invalid type given. String expected",
    "The input does not appear to be a valid IP address" => "The input does not appear to be a valid IP address",

    // Zend_Validator_Isbn
    "Invalid type given. String or integer expected" => "Invalid type given. String or integer expected",
    "The input is not a valid ISBN number" => "The input is not a valid ISBN number",

    // Zend_Validator_LessThan
    "The input is not less than '%max%'" => "The input is not less than '%max%'",
    "The input is not less or equal than '%max%'" => "The input is not less or equal than '%max%'",

    // Zend_Validator_NotEmpty
    "Value is required and can't be empty" => "Value is required and can't be empty",
    "Invalid type given. String, integer, float, boolean or array expected" => "Invalid type given. String, integer, float, boolean or array expected",

    // Zend_Validator_Regex
    "Invalid type given. String, integer or float expected" => "Invalid type given. String, integer or float expected",
    "The input does not match against pattern '%pattern%'" => "The input does not match against pattern '%pattern%'",
    "There was an internal error while using the pattern '%pattern%'" => "There was an internal error while using the pattern '%pattern%'",

    // Zend_Validator_Sitemap_Changefreq
    "The input is not a valid sitemap changefreq" => "The input is not a valid sitemap changefreq",
    "Invalid type given. String expected" => "Invalid type given. String expected",

    // Zend_Validator_Sitemap_Lastmod
    "The input is not a valid sitemap lastmod" => "The input is not a valid sitemap lastmod",
    "Invalid type given. String expected" => "Invalid type given. String expected",

    // Zend_Validator_Sitemap_Loc
    "The input is not a valid sitemap location" => "The input is not a valid sitemap location",
    "Invalid type given. String expected" => "Invalid type given. String expected",

    // Zend_Validator_Sitemap_Priority
    "The input is not a valid sitemap priority" => "The input is not a valid sitemap priority",
    "Invalid type given. Numeric string, integer or float expected" => "Invalid type given. Numeric string, integer or float expected",

    // Zend_Validator_Step
    "Invalid value given. Scalar expected" => "Invalid value given. Scalar expected",
    "The input is not a valid step" => "The input is not a valid step",

    // Zend_Validator_StringLength
    "Invalid type given. String expected" => "Invalid type given. String expected",
    "The input is less than %min% characters long" => "The input is less than %min% characters long",
    "The input is more than %max% characters long" => "The input is more than %max% characters long",

    // Zend_Validator_Uri
    "Invalid type given. String expected" => "Invalid type given. String expected",
    "The input does not appear to be a valid Uri" => "The input does not appear to be a valid Uri",
);
