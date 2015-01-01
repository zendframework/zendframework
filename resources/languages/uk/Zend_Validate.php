<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * EN-Revision: 16.Jul.2013
 */
return array(
    '' => array('plural_forms' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);'),
    // Zend\I18n\Validator\Alnum
    "Invalid type given. String, integer or float expected" => "Неправильний тип даних. Значення має бути рядком, цілим числом або числом з плаваючою комою",
    "The input contains characters which are non alphabetic and no digits" => "Значення містить символи, які не є літерами або цифрами",
    "The input is an empty string" => "Значення є порожнім рядком",

    // Zend\I18n\Validator\Alpha
    "Invalid type given. String expected" => "Неправильний тип даних. Значення має бути рядком",
    "The input contains non alphabetic characters" => "Значення містить символи, які не є літерами",
    "The input is an empty string" => "Значення є порожнім рядком",

    // Zend\I18n\Validator\DateTime
    "Invalid type given. String expected" => "Неправильний тип даних. Значення має бути рядком",
    "The input does not appear to be a valid datetime" => "Значення є некоректною датою",

    // Zend\I18n\Validator\Float
    "Invalid type given. String, integer or float expected" => "Неправильний тип даних. Значення має бути рядком, цілим числом або числом з плаваючою комою",
    "The input does not appear to be a float" => "Значення не є числом з плаваючою комою",

    // Zend\I18n\Validator\Int
    "Invalid type given. String or integer expected" => "Неправильний тип даних. Значення має бути рядком або цілим числом",
    "The input does not appear to be an integer" => "Значення не є цілим числом",

    // Zend\I18n\Validator\PhoneNumber
    "The input does not match a phone number format" => "Значення не відповідає формату телефонного номера",
    "The country provided is currently unsupported" => "Обрана країна наразі не підтримується",
    "Invalid type given. String expected" => "Неправильний тип даних. Значення має бути рядком",

    // Zend\I18n\Validator\PostCode
    "Invalid type given. String or integer expected" => "Неправильний тип даних. Значення має бути рядком або цілим числом",
    "The input does not appear to be a postal code" => "Значення не є поштовим індексом",
    "An exception has been raised while validating the input" => "Під час валідації значення згенеровано виняток",

    // Zend\Validator\Barcode
    "The input failed checksum validation" => "Значення не пройшло перевірку контрольної суми",
    "The input contains invalid characters" => "Значення містить неприпустимі символи",
    "The input should have a length of %length% characters" => "Значення має мати довжину в %length% символів",
    "Invalid type given. String expected" => "Неправильний тип даних, значення має бути рядком",

    // Zend\Validator\Between
    "The input is not between '%min%' and '%max%', inclusively" => "Значення лежить за межами діапазону '%min%' - '%max%' (включно)",
    "The input is not strictly between '%min%' and '%max%'" => "Значення лежить за межами діапазону '%min%' - '%max%' (виключно)",

    // Zend\Validator\Callback
    "The input is not valid" => "Значення є неправильним",
    "An exception has been raised within the callback" => "В зворотньому виклику згенеровано виняток",

    // Zend\Validator\CreditCard
    "The input seems to contain an invalid checksum" => "Значення має неправильну контрольну суму",
    "The input must contain only digits" => "Значення має містити тільки цифри",
    "Invalid type given. String expected" => "Неправильний тип даних. Значення має бути рядком",
    "The input contains an invalid amount of digits" => "Значення містить неприпустиму кількість цифр",
    "The input is not from an allowed institute" => "Значення не належить до дозволенних платіжних систем",
    "The input seems to be an invalid credit card number" => "Значення не є правильним номером банківської картки",
    "An exception has been raised while validating the input" => "Під час валідації значення згенеровано виняток",

    // Zend\Validator\Csrf
    "The form submitted did not originate from the expected site" => "Надіслана форма не походить з очікуваного сайту",

    // Zend\Validator\Date
    "Invalid type given. String, integer, array or DateTime expected" => "Неправильний тип даних. Значення має бути рядком, цілим числом, масивом або об'єктом Zend_Date",
    "The input does not appear to be a valid date" => "Значення не є коректною датою",
    "The input does not fit the date format '%format%'" => "Значення не відповідає формату дати '%format%'",

    // Zend\Validator\DateStep
    "The input is not a valid step" => "Значення не є коректним кроком",

    // Zend\Validator\Db\AbstractDb
    "No record matching the input was found" => "Не знайдено записів, що відповідають значенню",
    "A record matching the input was found" => "Знайдено запис, що відповідає значенню",

    // Zend\Validator\Digits
    "The input must contain only digits" => "Значення має містити тільки цифри",
    "The input is an empty string" => "Значення є порожнім рядком",
    "Invalid type given. String, integer or float expected" => "Неправильний тип даних. Значення має бути рядком, цілим числом або числом з плаваючою комою",

    // Zend\Validator\EmailAddress
    "Invalid type given. String expected" => "Неправильний тип даних. Значення має бути рядком",
    "The input is not a valid email address. Use the basic format local-part@hostname" => "Значення не є допустимою адресою електронної пошти. Використовуйте стандартний формат ім'я@домен",
    "'%hostname%' is not a valid hostname for the email address" => "'%hostname%' не є допустимим ім'ям хосту для адреси '%value%'",
    "'%hostname%' does not appear to have any valid MX or A records for the email address" => "'%hostname%' не має коректного MX- або A-запису про адресу електронної пошти",
    "'%hostname%' is not in a routable network segment. The email address should not be resolved from public network." => "'%hostname%' не є маршрутизованим сегментом мережі. Адреса електронної пошти не має бути отримана з публічної мережі.",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' не відповідає формату dot-atom",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' не відповідає формату quoted-string",
    "'%localPart%' is not a valid local part for the email address" => "'%localPart%' не є допустимим ім'ям для адреси електронної пошти",
    "The input exceeds the allowed length" => "Значення перевищує дозволену довжину",

    // Zend\Validator\Explode
    "Invalid type given" => "Неправильний тип даних",

    // Zend\Validator\File\Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "Занадто багато файлів, дозволено максимум '%max%', а отримано - '%count%'",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "Занадто мало файлів, дозволено мінімум '%min%', а отримано - '%count%'",

    // Zend\Validator\File\Crc32
    "File does not match the given crc32 hashes" => "Файл не відповідає заданому crc32-хешу",
    "A crc32 hash could not be evaluated for the given file" => "Неможливо обчислити crc32-хеш для даного файлу",
    "File is not readable or does not exist" => "Файл не вдається прочитати або він не існує",

    // Zend\Validator\File\ExcludeExtension
    "File has an incorrect extension" => "Файл має недопустиме розширення",
    "File is not readable or does not exist" => "Файл не вдається прочитати або він не існує",

    // Zend\Validator\File\Exists
    "File does not exist" => "Файл не існує",

    // Zend\Validator\File\Extension
    "File has an incorrect extension" => "Файл має неправильне розширення",
    "File is not readable or does not exist" => "Файл не вдається прочитати або він не існує",

    // Zend\Validator\File\FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "Загальний розмір файлів не повинен перевищувати '%max%', виявлено '%size%'",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "Загальний розмір файлів має бути меншим за '%min%', виявлено '%size%'",
    "One or more files can not be read" => "Неможливо прочитати один чи декілька файлів",

    // Zend\Validator\File\Hash
    "File does not match the given hashes" => "Файл не відповідає вказаному хешу",
    "A hash could not be evaluated for the given file" => "Неможливо обчислити хеш для вказаного файлу",
    "File is not readable or does not exist" => "Файл не вдається прочитати або він не існує",

    // Zend\Validator\File\ImageSize
    "Maximum allowed width for image should be '%maxwidth%' but '%width%' detected" => "Максимальна допустима ширина для зображення складає '%maxwidth%', виявлено '%width%'",
    "Minimum expected width for image should be '%minwidth%' but '%width%' detected" => "Мінімальна очкувана ширина для зображення складає '%minwidth%', виявлено '%width%'",
    "Maximum allowed height for image should be '%maxheight%' but '%height%' detected" => "Максимальна допустима висота для зображення складає '%maxheight%', виявлено '%height%'",
    "Minimum expected height for image should be '%minheight%' but '%height%' detected" => "Мінімальна очікувана висота для зображення складає '%minheight%', виявлено '%height%'",
    "The size of image could not be detected" => "Неможливо визначити розмір зображення",
    "File is not readable or does not exist" => "Файл не вдається прочитати або він не існує",

    // Zend\Validator\File\IsCompressed
    "File is not compressed, '%type%' detected" => "Файл не є стиснутим, виявлено тип '%type%'",
    "The mimetype could not be detected from the file" => "Неможливо визначити MIME-тип із файлу",
    "File is not readable or does not exist" => "Файл не вдається прочитати або він не існує",

    // Zend\Validator\File\IsImage
    "File is no image, '%type%' detected" => "Файл не є зображенням, виявлено тип '%type%'",
    "The mimetype could not be detected from the file" => "Неможливо визначити MIME-тип із файлу",
    "File is not readable or does not exist" => "Файл не вдається прочитати або він не існує",

    // Zend\Validator\File\Md5
    "File does not match the given md5 hashes" => "Файл не відповідає вказаному md5-хешу",
    "An md5 hash could not be evaluated for the given file" => "Неможливо обчислити md5-хеш для вказаного файлу",
    "File is not readable or does not exist" => "Файл не вдається прочитати або він не існує",

    // Zend\Validator\File\MimeType
    "File has an incorrect mimetype of '%type%'" => "Файл має неправильний MIME-тип '%type%'",
    "The mimetype could not be detected from the file" => "Неможливо визначити MIME-тип із файлу",
    "File is not readable or does not exist" => "Файл не вдається прочитати або він не існує",

    // Zend\Validator\File\NotExists
    "File exists" => "Файл вже існує",

    // Zend\Validator\File\Sha1
    "File does not match the given sha1 hashes" => "Файл не відповідає sha1-хешу",
    "A sha1 hash could not be evaluated for the given file" => "Неможливо обчислити sha1-хеш для вказаного файлу",
    "File is not readable or does not exist" => "Файл не вдається прочитати або він не існує",

    // Zend\Validator\File\Size
    "Maximum allowed size for file is '%max%' but '%size%' detected" => "Максимальний дозволений розмір файлу складає '%max%', виявлено '%size%'",
    "Minimum expected size for file is '%min%' but '%size%' detected" => "Мінімальний очікуваний розмір файлу складає '%min%', виявлено '%size%'",
    "File is not readable or does not exist" => "Файл не вдається прочитати або він не існує",

    // Zend\Validator\File\Upload
    "File '%value%' exceeds the defined ini size" => "Розмір файлу '%value%' перевищує дозволений, вказаний в php.ini",
    "File '%value%' exceeds the defined form size" => "Розмір файлу '%value%' перевищує дозволений, вказаний у формі",
    "File '%value%' was only partially uploaded" => "Файл '%value%' було завантажено тільки частково",
    "File '%value%' was not uploaded" => "Файл '%value%' не було завантажений",
    "No temporary directory was found for file '%value%'" => "Не знайдено тимчасову теку для файлу '%value%'",
    "File '%value%' can't be written" => "Файл '%value%' не може бути записаний",
    "A PHP extension returned an error while uploading the file '%value%'" => "PHP-розширення повернуло помилку під час завантаження фалу '%value%'",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "Файл '%value%' завантажено протиправно. Можлива атака",
    "File '%value%' was not found" => "Файл '%value%' не знайдено",
    "Unknown error while uploading file '%value%'" => "Під час завантаження файлу '%value%' виникла невідома помилка",

    // Zend\Validator\File\UploadFile
    "File exceeds the defined ini size" => "Розмір файлу перевищує дозволений, вказаний в php.ini",
    "File exceeds the defined form size" => "Розмір файлу перевищує дозволений, вказаний у формі",
    "File was only partially uploaded" => "Файл було завантажено тільки частково",
    "File was not uploaded" => "Файл '%value%' не було завантажено",
    "No temporary directory was found for file" => "Не знайдено тимчасову теку для файлу",
    "File can't be written" => "Файл '%value%' не може бути записаний",
    "A PHP extension returned an error while uploading the file" => "PHP-розширення повернуло помилку під час завантаження файлу",
    "File was illegally uploaded. This could be a possible attack" => "Файл завантажено протиправно. Можлива атака",
    "File was not found" => "Файл не знайдено",
    "Unknown error while uploading file" => "Під час завантаження файлу виникла невідома помилка",

    // Zend\Validator\File\WordCount
    "Too many words, maximum '%max%' are allowed but '%count%' were counted" => "Занадто багато слів: дозволено максимум '%max%', виявлено '%count%'",
    "Too few words, minimum '%min%' are expected but '%count%' were counted" => "Занадто мало слів: дозволено мінімум '%min%', виявлено '%count%'",
    "File is not readable or does not exist" => "Файл не вдається прочитати або він не існує",

    // Zend\Validator\GreaterThan
    "The input is not greater than '%min%'" => "Значення не є більшим за '%min%'",
    "The input is not greater or equal than '%min%'" => "Значення не дорівнює і не є більшим за '%min%'",

    // Zend\Validator\Hex
    "Invalid type given. String expected" => "Неправильний тип даних. Значення має бути рядком",
    "The input contains non-hexadecimal characters" => "Значення містить не тільки шістнадцяткові символи",

    // Zend\Validator\Hostname
    "The input appears to be a DNS hostname but the given punycode notation cannot be decoded" => "Значення є DNS-ім’ям хосту, але вказане значення не може бути перетворене в припустимий для DNS набір символів",
    "Invalid type given. String expected" => "Неправильний тип даних. Значення має бути рядком",
    "The input appears to be a DNS hostname but contains a dash in an invalid position" => "Значення є DNS-ім’ям хосту, але знак '-' знаходиться в неправильному місці",
    "The input does not match the expected structure for a DNS hostname" => "Значення не відповідає очікуваній структурі для DNS-імені хосту",
    "The input appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "Значення є DNS-ім’ям хосту, але воно не відповідає шаблону для доменних імен верхнього рівня '%tld%'",
    "The input does not appear to be a valid local network name" => "Значення не є коректним ім'ям локальної мережі",
    "The input does not appear to be a valid URI hostname" => "Значення не є коректним URI-ім'ям хосту",
    "The input appears to be an IP address, but IP addresses are not allowed" => "Значення є IP-адресою, але IP-адреси не дозволені",
    "The input appears to be a local network name but local network names are not allowed" => "Значення є ім’ям локальної мережі, але імена локальних мереж не дозволені",
    "The input appears to be a DNS hostname but cannot extract TLD part" => "Значення є DNS-ім’ям хосту, але не вдається визначити домен верхнього рівня",
    "The input appears to be a DNS hostname but cannot match TLD against known list" => "Значення є DNS-ім’ям хосту, але його не вдається співставити із значенням зі списку відомих доменів верхнього рівня",

    // Zend\Validator\Iban
    "Unknown country within the IBAN" => "Невідома IBAN-країна",
    "Countries outside the Single Euro Payments Area (SEPA) are not supported" => "Країни поза межами Єдиної Зони Платежів у Євро (SEPA) не підтримуються",
    "The input has a false IBAN format" => "Значення має неправильний IBAN-формат",
    "The input has failed the IBAN check" => "Значення не пройшло IBAN-перевірку",

    // Zend\Validator\Identical
    "The two given tokens do not match" => "Два вказаних значення не співпадають",
    "No token was provided to match against" => "Не вказано значення для перевірки на ідентичність",

    // Zend\Validator\InArray
    "The input was not found in the haystack" => "Значення не знайдено в списку допустимих значень",

    // Zend\Validator\Ip
    "Invalid type given. String expected" => "Неправильний тип даних. Значення має бути рядком",
    "The input does not appear to be a valid IP address" => "Значення не є коректною IP-адресою",

    // Zend\Validator\IsInstanceOf
    "The input is not an instance of '%className%'" => "Значення не є екземпляром '%className%'",

    // Zend\Validator\Isbn
    "Invalid type given. String or integer expected" => "Неправильний тип даних. Значення має бути рядком або цілим числом",
    "The input is not a valid ISBN number" => "Значення не є коректним номером ISBN",

    // Zend\Validator\LessThan
    "The input is not less than '%max%'" => "Значення не є меншим за '%max%'",
    "The input is not less or equal than '%max%'" => "Значення не дорівнює і не є меншим за '%max%'",

    // Zend\Validator\NotEmpty
    "Value is required and can't be empty" => "Значення обов'язкове і не може бути порожнім",
    "Invalid type given. String, integer, float, boolean or array expected" => "Неправильний тип даних. Значення має бути рядком, цілим числом, числом з плаваючою комою або масивом",

    // Zend\Validator\Regex
    "Invalid type given. String, integer or float expected" => "Неправильний тип даних. Значення має бути рядком, цілим числом або числом з плаваючою комою",
    "The input does not match against pattern '%pattern%'" => "Значення не відповідає шаблону '%pattern%'",
    "There was an internal error while using the pattern '%pattern%'" => "Під час використання шаблону '%pattern%' трапилася внутрішня помилка",

    // Zend\Validator\Sitemap\Changefreq
    "The input is not a valid sitemap changefreq" => "Значенння не є коректним для sitemap changefreq",
    "Invalid type given. String expected" => "Неправильний тип даних. Значення має бути рядком",

    // Zend\Validator\Sitemap\Lastmod
    "The input is not a valid sitemap lastmod" => "Значення не є коректним для sitemap lastmod",
    "Invalid type given. String expected" => "Неправильний тип даних. Значення має бути рядком",

    // Zend\Validator\Sitemap\Loc
    "The input is not a valid sitemap location" => "Значення не є коректним для sitemap location",
    "Invalid type given. String expected" => "Неправильний тип даних. Значення має бути рядком",

    // Zend\Validator\Sitemap\Priority
    "The input is not a valid sitemap priority" => "Значення не є коректним для sitemap priority",
    "Invalid type given. Numeric string, integer or float expected" => "Неправильний тип даних. Значення має бути числовим рядком, цілим числом або числом з плаваючою комою",

    // Zend\Validator\Step
    "Invalid value given. Scalar expected" => "Значення є некоректним. Очікується скалярна величина",
    "The input is not a valid step" => "Значення не є коректним кроком",

    // Zend\Validator\StringLength
    "Invalid type given. String expected" => "Неправильний тип даних. Значення має бути рядком",
    "The input is less than %min% characters long" => "Значення має довжину, меншу за %min% символів",
    "The input is more than %max% characters long" => "Значення має довжину, більшу за %max% символів",

    // Zend\Validator\Uri
    "Invalid type given. String expected" => "Неправильний тип даних. Значення має бути рядком",
    "The input does not appear to be a valid Uri" => "Значення не є коректним Uri",
);
