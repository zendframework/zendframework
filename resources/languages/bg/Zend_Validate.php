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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * EN-Revision: 25.Jul.2011
 */
return array(
    // Zend_Validate_Alnum
    "Invalid type given. String, integer or float expected" => "Зададен е невалиден тип данни. Очаква се стринг, цяло или реално число",
    "'%value%' contains characters which are non alphabetic and no digits" => "'%value%' съдържа символи, които не са букви или числа",
    "'%value%' is an empty string" => "'%value%' е празен стринг",

    // Zend_Validate_Alpha
    "Invalid type given. String expected" => "Зададен е невалиден тип данни. Очаква се стринг",
    "'%value%' contains non alphabetic characters" => "'%value%' съдържа символи, които не са букви",
    "'%value%' is an empty string" => "'%value%' е празен стринг",

    // Zend_Validate_Barcode
    "'%value%' failed checksum validation" => "'%value%' не успя на премине валидацията на контролната сума",
    "'%value%' contains invalid characters" => "'%value%' съдържа невалидни символи",
    "'%value%' should have a length of %length% characters" => "'%value%' трябва да има дължина от %length% символа",
    "Invalid type given. String expected" => "Зададен е невалиден тип данни. Очаква се стринг",

    // Zend_Validate_Between
    "'%value%' is not between '%min%' and '%max%', inclusively" => "'%value%' не е между '%min%' и '%max%' включително",
    "'%value%' is not strictly between '%min%' and '%max%'" => "'%value%' не е точно между '%min%' и '%max%'",

    // Zend_Validate_Callback
    "'%value%' is not valid" => "'%value%' е с невалидна стойност",
    "An exception has been raised within the callback" => "По време на заявката беше върнато ново изключение",

    // Zend_Validate_Ccnum
    "'%value%' must contain between 13 and 19 digits" => "'%value%' трябва да съдържа между 13 и 19 числа",
    "Luhn algorithm (mod-10 checksum) failed on '%value%'" => "Алгоритъмът на Лун (контролна сума по модул 10) се провали при '%value%'",

    // Zend_Validate_CreditCard
    "'%value%' seems to contain an invalid checksum" => "'%value%' съдържа невалидна контролна сума",
    "'%value%' must contain only digits" => "'%value%' трябва да съдържа само цифри",
    "Invalid type given. String expected" => "Зададен е навалиден тип данни. Очаква се стринг",
    "'%value%' contains an invalid amount of digits" => "'%value%' съдържа невалиден брой цифри",
    "'%value%' is not from an allowed institute" => "'%value%' не е от разрешена организация",
    "'%value%' seems to be an invalid creditcard number" => "'%value%' не е валиден номер на кредитна карта",
    "An exception has been raised while validating '%value%'" => "Беше върнато ново изключение по време на валидацията на '%value%'",

    // Zend_Validate_Date
    "Invalid type given. String, integer, array or Zend_Date expected" => "Зададен е невалиден тип данни. Очаква се стринг, цяло число или Zend_Date",
    "'%value%' does not appear to be a valid date" => "'%value%' не е валидна дата",
    "'%value%' does not fit the date format '%format%'" => "'%value%' не е дата във формата '%format%'",

    // Zend_Validate_Db_Abstract
    "No record matching '%value%' was found" => "Запис съвпадащ с '%value%' не беше открит",
    "A record matching '%value%' was found" => "Запис съвпадащ с '%value%' беше открит",

    // Zend_Validate_Digits
    "Invalid type given. String, integer or float expected" => "Зададен е невалиден тип данни. Очаква се стринг, цяло или реално число",
    "'%value%' must contain only digits" => "'%value%' трябва да съдържа само цифри",
    "'%value%' is an empty string" => "'%value%' е празен стринг",

    // Zend_Validate_EmailAddress
    "Invalid type given. String expected" => "Зададен е навалиден тип данни. Очаква се стринг",
    "'%value%' is not a valid email address in the basic format local-part@hostname" => "'%value%' не е валиден email адрес в базовия формат local-part@hostname",
    "'%hostname%' is not a valid hostname for email address '%value%'" => "'%hostname%' не е валидно име на хост за email адрес '%value%'",
    "'%hostname%' does not appear to have a valid MX record for the email address '%value%'" => "'%hostname%' няма валиден MX запис за email адрес '%value%'",
    "'%hostname%' is not in a routable network segment. The email address '%value%' should not be resolved from public network" => "'%hostname%' не е рутируем мрежов сегмент. E-mail адреса '%value%' не трябва да бъде достъпен от публични мрежи",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' не може да бъде сравнен с dot-atom формат",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' не може да бъде сравнен с quoted-string формат",
    "'%localPart%' is not a valid local part for email address '%value%'" => "'%localPart%' не е валидна локална част от email адрес '%value%'",
    "'%value%' exceeds the allowed length" => "'%value%' надвишава разрешение размер",

    // Zend_Validate_File_Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "Твърде много файлове, максимум '%max%' са разрешени, но '%count%' са зададени",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "Твърде малко файлове, минимум '%min%' са очаквани, но '%count%' са зададени",

    // Zend_Validate_File_Crc32
    "File '%value%' does not match the given crc32 hashes" => "Файлът '%value%' не съвпада с дадения crc32 хаш",
    "A crc32 hash could not be evaluated for the given file" => "Този crc32 хаш не може да оцени зададения файл",
    "File '%value%' is not readable or does not exist" => "Файлът '%value%' не може да бъде прочетен или не съществува",

    // Zend_Validate_File_ExcludeExtension
    "File '%value%' has a false extension" => "Файлът '%value%' има грешно разширение",
    "File '%value%' is not readable or does not exist" => "Файлът '%value%' не може да бъде прочетен или не съществува",

    // Zend_Validate_File_ExcludeMimeType
    "File '%value%' has a false mimetype of '%type%'" => "Файлът '%value%' има грешен маймтайп - '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Не може да бъде открит маймтайп формата на '%value%'",
    "File '%value%' is not readable or does not exist" => "Файлът '%value%' не може да бъде прочетен или не съществува",

    // Zend_Validate_File_Exists
    "File '%value%' does not exist" => "Файлът '%value%' не съществува",

    // Zend_Validate_File_Extension
    "File '%value%' has a false extension" => "Файлът '%value%' е с грешно разширение",
    "File '%value%' is not readable or does not exist" => "Файлът '%value%' е нечетим или не съществува",

    // Zend_Validate_File_FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "Файлове трябва да имат общ размер от максимум '%max%', но в момента той е '%size%'",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "Файлове трябва да имат общ размер от минимум '%min%', но в момента той е '%size%'",
    "One or more files can not be read" => "Един или повече файлове не могат да бъдат прочетени",

    // Zend_Validate_File_Hash
    "File '%value%' does not match the given hashes" => "Файлът '%value%' не съвпада с зададения хаш",
    "A hash could not be evaluated for the given file" => "Този хаш не може да оцени дадения файл",
    "File '%value%' is not readable or does not exist" => "Файлът '%value%' не може да бъде прочетен или не съществува",

    // Zend_Validate_File_ImageSize
    "Maximum allowed width for image '%value%' should be '%maxwidth%' but '%width%' detected" => "Максималната ширина на изображението '%value%' трябва да бъде '%maxwidth%', но в момента е '%width%'",
    "Minimum expected width for image '%value%' should be '%minwidth%' but '%width%' detected" => "Минималната ширина на изображението '%value%' трябва да бъде '%minwidth%', но в момента е '%width%'",
    "Maximum allowed height for image '%value%' should be '%maxheight%' but '%height%' detected" => "Максималната височина на изображението '%value%' трябва да бъде '%maxheight%', но в момента е '%height%'",
    "Minimum expected height for image '%value%' should be '%minheight%' but '%height%' detected" => "Минималната височина на изображението '%value%' трябва да бъде '%minheight%', но в момента е '%height%'",
    "The size of image '%value%' could not be detected" => "Размера на изображението '%value%' не може да бъде открит",
    "File '%value%' is not readable or does not exist" => "Файлът '%value%' не може да бъде прочетен или не съществува",

    // Zend_Validate_File_IsCompressed
    "File '%value%' is not compressed, '%type%' detected" => "Файлът '%value%' не е компресиран, неговия формат е '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Не може да бъде открит маймтайп формата на '%value%'",
    "File '%value%' is not readable or does not exist" => "Файлът '%value%' не може да бъде прочетен или не съществува",

    // Zend_Validate_File_IsImage
    "File '%value%' is no image, '%type%' detected" => "Файлът '%value%' не е изображение, неговия формат е '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Не може да бъде открит маймтайп формата на '%value%'",
    "File '%value%' is not readable or does not exist" => "Файлът '%value%' не може да бъде прочетен или не съществува",

    // Zend_Validate_File_Md5
    "File '%value%' does not match the given md5 hashes" => "Файлът '%value%' не съвпада с дадения md5 хаш",
    "A md5 hash could not be evaluated for the given file" => "Този md5 хаш не може да оцени дадения файл",
    "File '%value%' is not readable or does not exist" => "Файлът '%value%' не може да бъде прочетен или не съществува",

    // Zend_Validate_File_MimeType
    "File '%value%' has a false mimetype of '%type%'" => "Файлът '%value%' има грешен маймтайп - '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Не може да бъде открит маймтайп формата на '%value%'",
    "File '%value%' is not readable or does not exist" => "Файлът '%value%' не може да бъде прочетен или не съществува",

    // Zend_Validate_File_NotExists
    "File '%value%' exists" => "Файлът '%value%' съществува",

    // Zend_Validate_File_Sha1
    "File '%value%' does not match the given sha1 hashes" => "Файлът '%value%' не съвпада с зададения sha1 хаш",
    "A sha1 hash could not be evaluated for the given file" => "Този sha1 хаш не може да оцени дадения файл",
    "File '%value%' is not readable or does not exist" => "Файлът '%value%' не може да бъде прочетен или не съществува",

    // Zend_Validate_File_Size
    "Maximum allowed size for file '%value%' is '%max%' but '%size%' detected" => "Максималния разрешен размер на файла '%value%' е '%max%', но в момента той е '%size%'",
    "Minimum expected size for file '%value%' is '%min%' but '%size%' detected" => "Минималния разрешен размер на файла '%value%' е '%min%', но в момента той е '%size%'",
    "File '%value%' is not readable or does not exist" => "Файлът '%value%' не може да бъде прочетен или не съществува",

    // Zend_Validate_File_Upload
    "File '%value%' exceeds the defined ini size" => "Файлът '%value%' надвишава зададения размер в ini файла",
    "File '%value%' exceeds the defined form size" => "Файлът '%value%' надвишава зададения във формата размер",
    "File '%value%' was only partially uploaded" => "Файлът '%value%' беше качен само частично",
    "File '%value%' was not uploaded" => "Файлът '%value%' не беше качен",
    "No temporary directory was found for file '%value%'" => "Не беше открита временна директория за файла '%value%'",
    "File '%value%' can't be written" => "Файлът '%value%' не може да бъде записан",
    "A PHP extension returned an error while uploading the file '%value%'" => "PHP изключение беше върнато по време на качването на файла '%value%'",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "Файлът '%value%' беше качен без позволение. Това може да бъде потенциална атака",
    "File '%value%' was not found" => "Файлът '%value%' не беше открит",
    "Unknown error while uploading file '%value%'" => "Възникна грешка при качването на файла '%value%'",

    // Zend_Validate_File_WordCount
    "Too much words, maximum '%max%' are allowed but '%count%' were counted" => "Твърде много думи, очакват се максимум '%max%', но '%count%' бяха открити",
    "Too less words, minimum '%min%' are expected but '%count%' were counted" => "Твърде малко думи, очакват се минимум '%min%' но само '%count%' бяха открити",
    "File '%value%' is not readable or does not exist" => "Файлът '%value%' не може да бъде прочетен или не съществува",

    // Zend_Validate_Float
    "Invalid type given. String, integer or float expected" => "Зададен е невалиден тип данни. Очаква се стринг, цяло или реално число",
    "'%value%' does not appear to be a float" => "'%value%' не е реално число",

    // Zend_Validate_GreaterThan
    "'%value%' is not greater than '%min%'" => "'%value%' не е с по-голяма стойност от '%min%'",

    // Zend_Validate_Hex
    "Invalid type given. String expected" => "Зададен е невалиден тип данни. Очаква се стринг",
    "'%value%' has not only hexadecimal digit characters" => "'%value%' не съдържа само шестнадесетични символи",

    // Zend_Validate_Hostname
    "Invalid type given. String expected" => "Зададен е невалиден тип данни. Очаква се стринг",
    "'%value%' appears to be an IP address, but IP addresses are not allowed" => "'%value%' е IP адрес, но IP адреси не са разрешени",
    "'%value%' appears to be a DNS hostname but cannot match TLD against known list" => "'%value%' е DNS хост име, но не присъства в листа с известни TLD",
    "'%value%' appears to be a DNS hostname but contains a dash in an invalid position" => "'%value%' е DNS хост име, но съдържа тире на непозволено място",
    "'%value%' appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "'%value%' е DNS хост име, но не съвпада със схемата на TLD '%tld%'",
    "'%value%' appears to be a DNS hostname but cannot extract TLD part" => "'%value%' е DNS хост име, но не може да се определи TLD часта",
    "'%value%' does not match the expected structure for a DNS hostname" => "'%value%' не съвпада с очакваната структура за DNS хост име",
    "'%value%' does not appear to be a valid local network name" => "'%value%' не е валидно локално мрежово име",
    "'%value%' appears to be a local network name but local network names are not allowed" => "'%value%' е валидно локално мрежово име, но локалните мрежови имена не са позволени",
    "'%value%' appears to be a DNS hostname but the given punycode notation cannot be decoded" => "'%value%' е DNS хост име, но зададения пюникод не може да бъде декодиран",
    "'%value%' does not appear to be a valid URI hostname" => "'%value%' не е валидно URI хост име",

    // Zend_Validate_Iban
    "Unknown country within the IBAN '%value%'" => "Непозната държава с IBAN '%value%'",
    "'%value%' has a false IBAN format" => "'%value%' е грешен IBAN формат",
    "'%value%' has failed the IBAN check" => "'%value%' е невалиден IBAN",

    // Zend_Validate_Identical
    "The two given tokens do not match" => "Двете зададени стойности не съвпадат",
    "No token was provided to match against" => "Не е зададена стойност за сравнение",

    // Zend_Validate_InArray
    "'%value%' was not found in the haystack" => "'%value%' не беше открито",

    // Zend_Validate_Int
    "Invalid type given. String or integer expected" => "Зададен е невалиден тип данни. Очаква се стринг или цяло число",
    "'%value%' does not appear to be an integer" => "'%value%' не е цяло число",

    // Zend_Validate_Ip
    "Invalid type given. String expected" => "Зададен е невалиден тип данни. Очаква се стринг",
    "'%value%' does not appear to be a valid IP address" => "'%value%' не е валиден IP адрес",

    // Zend_Validate_Isbn
    "Invalid type given. String or integer expected" => "Зададен е невалиден тип данни. Очаква се стринг или цяло число",
    "'%value%' is not a valid ISBN number" => "'%value%' не е валиден ISBN номер",

    // Zend_Validate_LessThan
    "'%value%' is not less than '%max%'" => "'%value%' не е с по-малка стойност от '%max%'",

    // Zend_Validate_NotEmpty
    "Invalid type given. String, integer, float, boolean or array expected" => "Зададен е невалиден тип данни. Очаква се стринг, цяло или реално число, булева стойност или масив",
    "Value is required and can't be empty" => "Очакваната стойност не може да бъде празна",

    // Zend_Validate_PostCode
    "Invalid type given. String or integer expected" => "Зададен е невалиден тип данни. Очаква се стринг или цяло число",
    "'%value%' does not appear to be a postal code" => "'%value%' е невалиден пощенски код",

    // Zend_Validate_Regex
    "Invalid type given. String, integer or float expected" => "Зададен е невалиден тип данни. Очаква се стринг, цяло или реално число",
    "'%value%' does not match against pattern '%pattern%'" => "'%value%' не съвпада с шаблона '%pattern%'",
    "There was an internal error while using the pattern '%pattern%'" => "Получена е системна грешка докато се ползва шаблона '%pattern%'",

    // Zend_Validate_Sitemap_Changefreq
    "'%value%' is not a valid sitemap changefreq" => "'%value%' е невалидна стойност за changefreq",
    "Invalid type given. String expected" => "Зададен е невалиден тип данни. Очаква се стринг",

    // Zend_Validate_Sitemap_Lastmod
    "'%value%' is not a valid sitemap lastmod" => "'%value%' е невалидна стойност за lastmod",
    "Invalid type given. String expected" => "Зададен е невалиден тип данни. Очаква се стринг",

    // Zend_Validate_Sitemap_Loc
    "'%value%' is not a valid sitemap location" => "'%value%' е невалидна стойност за location",
    "Invalid type given. String expected" => "Зададен е невалиден тип данни. Очаква се стринг",

    // Zend_Validate_Sitemap_Priority
    "'%value%' is not a valid sitemap priority" => "'%value%' е невалидна стойност за priority",
    "Invalid type given. Numeric string, integer or float expected" => "Зададен е невалиден тип данни. Очаква се стринг, цяло или реално число",

    // Zend_Validate_StringLength
    "Invalid type given. String expected" => "Зададен е невалиден тип данни. Очаква се стринг",
    "'%value%' is less than %min% characters long" => "'%value%' е по-малко от %min% символа",
    "'%value%' is more than %max% characters long" => "'%value%' е повече от %max% символа",
);