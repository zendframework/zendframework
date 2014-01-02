<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * BG-Revision: 09.Sept.2012
 */
return array(
    // Zend_I18n_Validator_Alnum
    "Invalid type given. String, integer or float expected" => "Зададен е невалиден тип данни. Очаква се стринг, цяло или реално число",
    "The input contains characters which are non alphabetic and no digits" => "Въведени са символи, които не са букви или числа",
    "The input is an empty string" => "Въведен е празен стринг",

    // Zend_I18n_Validator_Alpha
    "Invalid type given. String expected" => "Зададен е невалиден тип данни. Очаква се стринг",
    "The input contains non alphabetic characters" => "Въведени са символи, които не са букви",
    "The input is an empty string" => "Въведен е празен стринг",

    // Zend_I18n_Validator_Float
    "Invalid type given. String, integer or float expected" => "Зададен е невалиден тип данни. Очаква се цяло или реално число",
    "The input does not appear to be a float" => "Не е въведено реално число",

    // Zend_I18n_Validator_Int
    "Invalid type given. String or integer expected" => "Зададен е невалиден тип данни. Очаква се цяло число",
    "The input does not appear to be an integer" => "Не е въведено цяло число",

    // Zend_I18n_Validator_PostCode
    "Invalid type given. String or integer expected" => "Зададен е невалиден тип данни. Очаква се стринг или цяло число",
    "The input does not appear to be a postal code" => "Не е въведен валиден пощенски код",
    "An exception has been raised while validating the input" => "По време на валидацията беше върнато изключение",

    // Zend_Validator_Barcode
    "The input failed checksum validation" => "Въведената стойност не успя на премине валидацията на контролната сума",
    "The input contains invalid characters" => "Въведената стойност съдържа невалидни символи",
    "The input should have a length of %length% characters" => "Въведената стойност трябва да има дължина от %length% символа",
    "Invalid type given. String expected" => "Зададен е невалиден тип данни. Очаква се стринг",

    // Zend_Validator_Between
    "The input is not between '%min%' and '%max%', inclusively" => "Въведената стойност не е между '%min%' и '%max%' включително",
    "The input is not strictly between '%min%' and '%max%'" => "Въведената стойност не е точно между '%min%' и '%max%'",

    // Zend_Validator_Callback
    "The input is not valid" => "Въведена е невалидна стойност",
    "An exception has been raised within the callback" => "По време на заявката беше върнато ново изключение",

    // Zend_Validator_CreditCard
    "The input seems to contain an invalid checksum" => "Въведената стойност съдържа невалидна контролна сума",
    "The input must contain only digits" => "Въведената стойност трябва да съдържа само цифри",
    "Invalid type given. String expected" => "Зададен е навалиден тип данни. Очаква се стринг",
    "The input contains an invalid amount of digits" => "Въведената стойност съдържа невалиден брой цифри",
    "The input is not from an allowed institute" => "Въведената стойност не е разрешена организация",
    "The input seems to be an invalid creditcard number" => "Въведената стойност не е валиден номер на кредитна карта",
    "An exception has been raised while validating the input" => "По време на валидацията беше върнато ново изключение",

    // Zend_Validator_Csrf
    "The form submitted did not originate from the expected site" => "Формата не е изпратена от очаквания сайт",

    // Zend_Validator_Date
    "Invalid type given. String, integer, array or DateTime expected" => "Зададен е невалиден тип данни. Очаква се стринг, цяло число или DateTime",
    "The input does not appear to be a valid date" => "Въведена стойност не е валидна дата",
    "The input does not fit the date format '%format%'" => "Въведена стойност не е дата във формат '%format%'",

    // Zend_Validator_DateStep
    "Invalid type given. String, integer, array or DateTime expected" => "Зададен е невалиден тип данни. Очаква се стринг, цяло число или DateTime",
    "The input does not appear to be a valid date" => "Въведена стойност не е валидна дата",
    "The input is not a valid step" => "Въведена стойност не е валидна стъпка",

    // Zend_Validator_Db_AbstractDb
    "No record matching the input was found" => "Не беше открит запис съвпадащ с въведената стойност",
    "A record matching the input was found" => "Беше открит запис съвпадащ с въведената стойност",

    // Zend_Validator_Digits
    "The input must contain only digits" => "Въведената стойност трябва да съдържа само цифри",
    "The input is an empty string" => "Въведената стойност е празен стринг",
    "Invalid type given. String, integer or float expected" => "Зададен е невалиден тип данни. Очаква се стринг, цяло или реално число",

    // Zend_Validator_EmailAddress
    "Invalid type given. String expected" => "Зададен е навалиден тип данни. Очаква се стринг",
    "The input is not a valid email address. Use the basic format local-part@hostname" => "Въведената стойност не е валиден email адрес в базовия формат local-part@hostname",
    "'%hostname%' is not a valid hostname for the email address" => "'%hostname%' не е валидно име на хост за въведения email адрес",
    "'%hostname%' does not appear to have any valid MX or A records for the email address" => "'%hostname%' няма валиден MX запис за въведения email адрес",
    "'%hostname%' is not in a routable network segment. The email address should not be resolved from public network" => "'%hostname%' не е рутируем мрежов сегмент. Въведения email адрес не трябва да бъде достъпен от публични мрежи",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' не може да бъде сравнен с dot-atom формат",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' не може да бъде сравнен с quoted-string формат",
    "'%localPart%' is not a valid local part for the email address" => "'%localPart%' не е валидна локална част от въведения email адрес",
    "The input exceeds the allowed length" => "Въведената стойност надвишава разрешение размер",

    // Zend_Validator_Explode
    "Invalid type given. String expected" => "Зададен е навалиден тип данни. Очаква се стринг",

    // Zend_Validator_File_Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "Твърде много файлове, максимум '%max%' са разрешени, но '%count%' са зададени",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "Твърде малко файлове, минимум '%min%' са очаквани, но '%count%' са зададени",

    // Zend_Validator_File_Crc32
    "File '%value%' does not match the given crc32 hashes" => "Файлът '%value%' не съвпада с дадения crc32 хаш",
    "A crc32 hash could not be evaluated for the given file" => "Този crc32 хаш не може да оцени зададения файл",
    "File '%value%' is not readable or does not exist" => "Файлът '%value%' не може да бъде прочетен или не съществува",

    // Zend_Validator_File_ExcludeExtension
    "File '%value%' has a false extension" => "Файлът '%value%' има грешно разширение",
    "File '%value%' is not readable or does not exist" => "Файлът '%value%' не може да бъде прочетен или не съществува",

    // Zend_Validator_File_Exists
    "File '%value%' does not exist" => "Файлът '%value%' не съществува",

    // Zend_Validator_File_Extension
    "File '%value%' has a false extension" => "Файлът '%value%' е с грешно разширение",
    "File '%value%' is not readable or does not exist" => "Файлът '%value%' е нечетим или не съществува",

    // Zend_Validator_File_FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "Файлове трябва да имат общ размер от максимум '%max%', но в момента той е '%size%'",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "Файлове трябва да имат общ размер от минимум '%min%', но в момента той е '%size%'",
    "One or more files can not be read" => "Един или повече файлове не могат да бъдат прочетени",

    // Zend_Validator_File_Hash
    "File '%value%' does not match the given hashes" => "Файлът '%value%' не съвпада с зададения хаш",
    "A hash could not be evaluated for the given file" => "Този хаш не може да оцени дадения файл",
    "File '%value%' is not readable or does not exist" => "Файлът '%value%' не може да бъде прочетен или не съществува",

    // Zend_Validator_File_ImageSize
    "Maximum allowed width for image '%value%' should be '%maxwidth%' but '%width%' detected" => "Максималната ширина на изображението '%value%' трябва да бъде '%maxwidth%', но в момента е '%width%'",
    "Minimum expected width for image '%value%' should be '%minwidth%' but '%width%' detected" => "Минималната ширина на изображението '%value%' трябва да бъде '%minwidth%', но в момента е '%width%'",
    "Maximum allowed height for image '%value%' should be '%maxheight%' but '%height%' detected" => "Максималната височина на изображението '%value%' трябва да бъде '%maxheight%', но в момента е '%height%'",
    "Minimum expected height for image '%value%' should be '%minheight%' but '%height%' detected" => "Минималната височина на изображението '%value%' трябва да бъде '%minheight%', но в момента е '%height%'",
    "The size of image '%value%' could not be detected" => "Размера на изображението '%value%' не може да бъде открит",
    "File '%value%' is not readable or does not exist" => "Файлът '%value%' не може да бъде прочетен или не съществува",

    // Zend_Validator_File_IsCompressed
    "File '%value%' is not compressed, '%type%' detected" => "Файлът '%value%' не е компресиран, неговия формат е '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Не може да бъде открит маймтайп формата на '%value%'",
    "File '%value%' is not readable or does not exist" => "Файлът '%value%' не може да бъде прочетен или не съществува",

    // Zend_Validator_File_IsImage
    "File '%value%' is no image, '%type%' detected" => "Файлът '%value%' не е изображение, неговия формат е '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Не може да бъде открит маймтайп формата на '%value%'",
    "File '%value%' is not readable or does not exist" => "Файлът '%value%' не може да бъде прочетен или не съществува",

    // Zend_Validator_File_Md5
    "File '%value%' does not match the given md5 hashes" => "Файлът '%value%' не съвпада с дадения md5 хаш",
    "A md5 hash could not be evaluated for the given file" => "Този md5 хаш не може да оцени дадения файл",
    "File '%value%' is not readable or does not exist" => "Файлът '%value%' не може да бъде прочетен или не съществува",

    // Zend_Validator_File_MimeType
    "File '%value%' has a false mimetype of '%type%'" => "Файлът '%value%' има грешен маймтайп - '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Не може да бъде открит маймтайп формата на '%value%'",
    "File '%value%' is not readable or does not exist" => "Файлът '%value%' не може да бъде прочетен или не съществува",

    // Zend_Validator_File_NotExists
    "File '%value%' exists" => "Файлът '%value%' съществува",

    // Zend_Validator_File_Sha1
    "File '%value%' does not match the given sha1 hashes" => "Файлът '%value%' не съвпада с зададения sha1 хаш",
    "A sha1 hash could not be evaluated for the given file" => "Този sha1 хаш не може да оцени дадения файл",
    "File '%value%' is not readable or does not exist" => "Файлът '%value%' не може да бъде прочетен или не съществува",

    // Zend_Validator_File_Size
    "Maximum allowed size for file '%value%' is '%max%' but '%size%' detected" => "Максималния разрешен размер на файла '%value%' е '%max%', но в момента той е '%size%'",
    "Minimum expected size for file '%value%' is '%min%' but '%size%' detected" => "Минималния разрешен размер на файла '%value%' е '%min%', но в момента той е '%size%'",
    "File '%value%' is not readable or does not exist" => "Файлът '%value%' не може да бъде прочетен или не съществува",

    // Zend_Validator_File_Upload
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

    // Zend_Validator_File_WordCount
    "Too much words, maximum '%max%' are allowed but '%count%' were counted" => "Твърде много думи, очакват се максимум '%max%', но '%count%' бяха открити",
    "Too few words, minimum '%min%' are expected but '%count%' were counted" => "Твърде малко думи, очакват се минимум '%min%' но само '%count%' бяха открити",
    "File '%value%' is not readable or does not exist" => "Файлът '%value%' не може да бъде прочетен или не съществува",

    // Zend_Validator_GreaterThan
    "The input is not greater than '%min%'" => "Въведената стойност не е по-голяма от '%min%'",
    "The input is not greater or equal than '%min%'" => "Въведената стойност не е по-голяма или равна на '%min%'",

    // Zend_Validator_Hex
    "Invalid type given. String expected" => "Зададен е невалиден тип данни. Очаква се стринг",
    "The input contains non-hexadecimal characters" => "Въведената стойност не съдържа само шестнадесетични символи",

    // Zend_Validator_Hostname
    "The input appears to be a DNS hostname but the given punycode notation cannot be decoded" => "Въведената стойност е DNS хост име, но зададения пюникод не може да бъде декодиран",
    "Invalid type given. String expected" => "Зададен е невалиден тип данни. Очаква се стринг",
    "The input appears to be a DNS hostname but contains a dash in an invalid position" => "Въведената стойност е DNS хост име, но съдържа тире на непозволено място",
    "The input does not match the expected structure for a DNS hostname" => "Въведената стойност не съвпада с очакваната структура за DNS хост име",
    "The input appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "Въведената стойност е DNS хост име, но не съвпада със схемата на TLD '%tld%'",
    "The input does not appear to be a valid local network name" => "Въведената стойност не е валидно локално мрежово име",
    "The input does not appear to be a valid URI hostname" => "Въведената стойност не е валидно URI хост име",
    "The input appears to be an IP address, but IP addresses are not allowed" => "Въведената стойност е IP адрес, но IP адреси не са разрешени",
    "The input appears to be a local network name but local network names are not allowed" => "Въведената стойност е валидно локално мрежово име, но локалните мрежови имена не са позволени",
    "The input appears to be a DNS hostname but cannot extract TLD part" => "Въведената стойност е DNS хост име, но не може да се определи TLD часта",
    "The input appears to be a DNS hostname but cannot match TLD against known list" => "Въведената стойност е DNS хост име, но не присъства в листа с известни TLD",

    // Zend_Validator_Iban
    "Unknown country within the IBAN" => "IBAN-а съдържа непозната държава",
    "Countries outside the Single Euro Payments Area (SEPA) are not supported" => "Държави извън Single Euro Payments Area (SEPA) не се поддържат",
    "The input has a false IBAN format" => "Въведената стойност е в грешен IBAN формат",
    "The input has failed the IBAN check" => "Въведен е невалиден IBAN",

    // Zend_Validator_Identical
    "The two given tokens do not match" => "Двете зададени стойности не съвпадат",
    "No token was provided to match against" => "Не е зададена стойност за сравнение",

    // Zend_Validator_InArray
    "The input was not found in the haystack" => "Въведената стойност не беше открита",

    // Zend_Validator_Ip
    "Invalid type given. String expected" => "Зададен е невалиден тип данни. Очаква се стринг",
    "The input does not appear to be a valid IP address" => "Въведената стойност не е валиден IP адрес",

    // Zend_Validator_Isbn
    "Invalid type given. String or integer expected" => "Зададен е невалиден тип данни. Очаква се стринг или цяло число",
    "The input is not a valid ISBN number" => "Въведената стойност не е валиден ISBN номер",

    // Zend_Validator_LessThan
    "The input is not less than '%max%'" => "Въведената стойност не е по-малка от '%max%'",
    "The input is not less or equal than '%max%'" => "Въведената стойност не е по-малка или равна на '%max%'",

    // Zend_Validator_NotEmpty
    "Value is required and can't be empty" => "Очакваната стойност не може да бъде празна",
    "Invalid type given. String, integer, float, boolean or array expected" => "Зададен е невалиден тип данни. Очаква се стринг, цяло или реално число, булева стойност или масив",

    // Zend_Validator_Regex
    "Invalid type given. String, integer or float expected" => "Зададен е невалиден тип данни. Очаква се стринг, цяло или реално число",
    "The input does not match against pattern '%pattern%'" => "Въведената стойност не съвпада с шаблона '%pattern%'",
    "There was an internal error while using the pattern '%pattern%'" => "Получена е системна грешка докато се ползва шаблона '%pattern%'",

    // Zend_Validator_Sitemap_Changefreq
    "The input is not a valid sitemap changefreq" => "Въведена е невалидна стойност за changefreq",
    "Invalid type given. String expected" => "Зададен е невалиден тип данни. Очаква се стринг",

    // Zend_Validator_Sitemap_Lastmod
    "The input is not a valid sitemap lastmod" => "Въведена е невалидна стойност за lastmod",
    "Invalid type given. String expected" => "Зададен е невалиден тип данни. Очаква се стринг",

    // Zend_Validator_Sitemap_Loc
    "The input is not a valid sitemap location" => "Въведена е невалидна стойност за location",
    "Invalid type given. String expected" => "Зададен е невалиден тип данни. Очаква се стринг",

    // Zend_Validator_Sitemap_Priority
    "The input is not a valid sitemap priority" => "Въведена е невалидна стойност за priority",
    "Invalid type given. Numeric string, integer or float expected" => "Зададен е невалиден тип данни. Очаква се стринг, цяло или реално число",

    // Zend_Validator_Step
    "Invalid value given. Scalar expected" => "Зададен е невалиден тип данни. Очаква се скаларен тип",
    "The input is not a valid step" => "Въведената стойност не е валидна стъпка",

    // Zend_Validator_StringLength
    "Invalid type given. String expected" => "Зададен е невалиден тип данни. Очаква се стринг",
    "The input is less than %min% characters long" => "Въведената стойност е по-малкa от %min% символа",
    "The input is more than %max% characters long" => "Въведената стойност е по-голяма от %max% символа",

    // Zend_Validator_Uri
    "Invalid type given. String expected" => "Зададен е невалиден тип данни. Очаква се стринг",
    "The input does not appear to be a valid Uri" => "Въведената стойност не е валиден URI",
);
