<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * EN-Revision: 16.Oct.2013
 */
return array(
    // Zend\I18n\Validator\Alnum
    "Invalid type given. String, integer or float expected" => "Podana wartość powinna być ciągiem znaków, liczbą całkowitą lub liczbą zmiennoprzecinkową",
    "The input contains characters which are non alphabetic and no digits" => "Podana wartość powinna zawierać znaki z alfabetu lub cyfry",
    "The input is an empty string" => "Podana wartość jest pustym ciągiem znaków",

    // Zend\I18n\Validator\Alpha
    "Invalid type given. String expected" => "Podana wartość nie jest ciągiem znaków",
    "The input contains non alphabetic characters" => "Podana wartość zawiera znaki spoza alfabetu",

    // Zend\I18n\Validator\DateTime
    "The input does not appear to be a valid datetime" => "Podana wartość nie jest poprawną datą",

    // Zend\I18n\Validator\Float
    "The input does not appear to be a float" => "Podana wartość nie jest liczbą zmiennoprzecinkową",

    // Zend\I18n\Validator\Int
    "Invalid type given. String or integer expected" => "Podana wartość powinna być ciągiem znaków lub liczbą całkowitą",
    "The input does not appear to be an integer" => "Podana wartość nie jest liczbą całkowitą",

    // Zend\I18n\Validator\PhoneNumber
    "The input does not match a phone number format" => "Wartość nie pasuje do formatu numeru telefonu",
    "The country provided is currently unsupported" => "Ten kraj nie jest wspierany",

    // Zend\I18n\Validator\PostCode
    "The input does not appear to be a postal code" => "Podana wartość nie jest kodem pocztowym",
    "An exception has been raised while validating the input" => "Wystąpił błąd podczas sprawdzania danych",

    // Zend\Validator\Barcode
    "The input failed checksum validation" => "Błędna suma kontrolna dla wartości",
    "The input contains invalid characters" => "Wartość zawiera niedozwolone znaki",
    "The input should have a length of %length% characters" => "Wartość powinna być długości %length% znaków",

    // Zend\Validator\Between
    "The input is not between '%min%' and '%max%', inclusively" => "Podana wartość nie zawiera się w przedziale od '%min%' do '%max%' włącznie",
    "The input is not strictly between '%min%' and '%max%'" => "Podana wartość nie zawiera się w przedziale od '%min%' do '%max%'",

    // Zend\Validator\Callback
    "The input is not valid" => "Podana wartość jest nie poprawna",
    "An exception has been raised within the callback" => "Wystąpił błąd podczas działania funkcji sprawdzającej",

    // Zend\Validator\CreditCard
    "The input seems to contain an invalid checksum" => "Numer zawiera niepoprawną sumę kontrolną",
    "The input must contain only digits" => "Podana wartość może zawierać tylko cyfry",
    "The input contains an invalid amount of digits" => "Numer zawiera niepoprawną liczbę cyfr",
    "The input is not from an allowed institute" => "Numer nie jest z dozwolonej instytucji",
    "The input seems to be an invalid credit card number" => "Podana wartość jest niepoprawnym numerem karty",

    // Zend\Validator\Csrf
    "The form submitted did not originate from the expected site" => "Przesłany formularz nie pochodzi z oczekiwanej strony",

    // Zend\Validator\Date
    "Invalid type given. String, integer, array or DateTime expected" => "Podana wartość powinna być ciągiem znaków, liczbą, tablicą lub obiektem DateTime",
    "The input does not appear to be a valid date" => "Podana wartość nie jest poprawną datą",
    "The input does not fit the date format '%format%'" => "Data nie jest w formacie '%format%'",

    // Zend\Validator\DateStep
    "The input is not a valid step" => "Wartość nie jest poprawnym krokiem",

    // Zend\Validator\Db\AbstractDb
    "No record matching the input was found" => "Nie znaleziono rekordu dla podanej wartości",
    "A record matching the input was found" => "Znaleziono rekord dla podanej wartośći",

    // Zend\Validator\Digits

    // Zend\Validator\EmailAddress
    "The input is not a valid email address. Use the basic format local-part@hostname" => "Podana wartość nie jest poprawnym adresem email w formacie nazwa@serwer",
    "'%hostname%' is not a valid hostname for the email address" => "Adres email zawiera niepoprawną nazwę serwera '%hostname%'",
    "'%hostname%' does not appear to have any valid MX or A records for the email address" => "Serwer '%hostname%' nie posiada poprawnie zdefiniowanego rekordu MX dla adresu email",
    "'%hostname%' is not in a routable network segment. The email address should not be resolved from public network" => "'%hostname%' nie rutowalnym segmentem sieci. Adres email nie powinien być wykrywany z sieci publiczej",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' nie jest w formacie dot-atom",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' nie jest w formacie quoted-string",
    "'%localPart%' is not a valid local part for the email address" => "'%localPart%' nie jest poprawną nazwą.",
    "The input exceeds the allowed length" => "Podana wartość przekroczyła dozwoloną długość",

    // Zend\Validator\Explode
    "Invalid type given" => "Nieprawiłowy typ",

    // Zend\Validator\File\Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "Wybrano '%count%' plików. Dopuszczalna liczba plików to '%max%'",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "Wybrano '%count%' plików. Minimalna liczba plików to '%min%'",

    // Zend\Validator\File\Crc32
    "File does not match the given crc32 hashes" => "Błędna suma kontrolna pliku",
    "A crc32 hash could not be evaluated for the given file" => "Nie można obliczyć sumy kontrolnej dla podanego pliku",
    "File is not readable or does not exist" => "Plik nie istnieje lub nie można go odczytać",

    // Zend\Validator\File\ExcludeExtension
    "File has an incorrect extension" => "Plik ma niepoprawne rozszerzenie",
    "File is not readable or does not exist" => "Plik  nie istnieje lub nie można go odczytać",

    // Zend\Validate\File\ExcludeMimeType
    "File '%value%' has a false mimetype of '%type%'" => "Plik '%value%' ma niepoprawny typ MIME '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Nie można wykryć typu MIME dla pliku '%value%'",
    "File '%value%' is not readable or does not exist" => "Plik '%value%' nie istnieje lub nie można go odczytać",

    // Zend\Validator\File\Exists
    "File does not exist" => "Plik nie istnieje",

    // Zend\Validator\File\Extension
    "File has an incorrect extension" => "Plik ma niepoprawne rozszerzenie",

    // Zend\Validator\File\FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "Wybrane pliki łącznie zajmują '%size%'. Maksymalny łączny rozmiar to '%max%'",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "Wybrane pliki łącznie zajmują '%size%'. Minimalny łączny rozmiar to '%min%'",
    "One or more files can not be read" => "Jeden lub więcej plików nie mogą zostać odczytane",

    // Zend\Validator\File\Hash
    "File does not match the given hashes" => "Plik ma niedopuszczalny hash",
    "A hash could not be evaluated for the given file" => "Nie można obliczyć funkcji haszującej dla podanego pliku",

    // Zend\Validator\File\ImageSize
    "Maximum allowed width for image should be '%maxwidth%' but '%width%' detected" => "Plik ma szerokość '%width%'. Maksymalna szerokość to '%maxwidth%'",
    "Minimum expected width for image should be '%minwidth%' but '%width%' detected" => "Plik ma szerokość '%width%'. Minimalna szerokość to '%maxwidth%'",
    "Maximum allowed height for image should be '%maxheight%' but '%height%' detected" => "Plik ma wysokość '%height%'. Maksymalna wysokość to '%maxheight%'",
    "Minimum expected height for image should be '%minheight%' but '%height%' detected" => "Plik ma wysokość '%height%'. Minimalna wysokość to '%minheight%'",
    "The size of image could not be detected" => "Nie można określić rozmiaru pliku",

    // Zend\Validator\File\IsCompressed
    "File is not compressed, '%type%' detected" => "Plik typu '%type%' nie jest skompresowany",
    "The mimetype could not be detected from the file" => "Nie można wykryć typu MIME dla pliku",

    // Zend\Validator\File\IsImage
    "File is no image, '%type%' detected" => "Plik typu '%type%' nie jest obrazem",
    "The mimetype could not be detected from the file" => "Nie można wykryć typu MIME dla pliku",

    // Zend\Validator\File\Md5
    "File does not match the given md5 hashes" => "Plik ma niedopuszczalny hash md5",
    "An md5 hash could not be evaluated for the given file" => "Nie można obliczyć funkcji haszującej md5 dla podanego pliku",

    // Zend\Validator\File\MimeType
    "File has an incorrect mimetype of '%type%'" => "Plik ma niepoprawny typ MIME '%type%'",
    "The mimetype could not be detected from the file" => "Nie można wykryć typu MIME dla pliku",

    // Zend\Validator\File\NotExists
    "File exists" => "Plik istnieje",

    // Zend\Validator\File\Sha1
    "File does not match the given sha1 hashes" => "Plik ma niedopuszczalny hash sha1",
    "A sha1 hash could not be evaluated for the given file" => "Nie można obliczyć funkcji haszującej sha1 dla podanego pliku",

    // Zend\Validator\File\Size
    "Maximum allowed size for file is '%max%' but '%size%' detected" => "Podany plik ma rozmiar '%size%'. Maksymalny rozmiar pliku to '%max%'",
    "Minimum expected size for file is '%min%' but '%size%' detected" => "Podany plik ma rozmiar '%size%'. Minimalny rozmiar pliku to '%min%'",

    // Zend\Validator\File\Upload
    "File '%value%' exceeds the defined ini size" => "Rozmiar pliku '%value%' przekroczył zdefiniowaną wartość w ini",
    "File '%value%' exceeds the defined form size" => "Rozmiar pliku '%value%' przekroczył zdefiniowaną wartość w formularzu",
    "File '%value%' was only partially uploaded" => "Plik '%value%' nie został całkowicie wysłany",
    "File '%value%' was not uploaded" => "Plik '%value%' nie został wysłany",
    "No temporary directory was found for file '%value%'" => "Nie zdefiniowano tymczasowego katalogu",
    "File '%value%' can't be written" => "Nie można zapisać pliku '%value%'",
    "A PHP extension returned an error while uploading the file '%value%'" => "Rozszerzenie PHP zgłosiło wyjątek podczas wysyłania pliku '%value%'",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "Plik '%value%' został niepoprawnie wysłany. Istnieje możliwość wystąpienia ataku",
    "File '%value%' was not found" => "Nie znaleziono pliku '%value%'",
    "Unknown error while uploading file '%value%'" => "Nieznany błąd podczas wysyłania pliku '%value%'",

    // Zend\Validator\File\UploadFile
    "File exceeds the defined ini size" => "Rozmiar pliku przekroczył zdefiniowaną wartość w ini",
    "File exceeds the defined form size" => "Rozmiar pliku przekroczył zdefiniowaną wartość w formularzu",
    "File was only partially uploaded" => "Plik nie został całkowicie wysłany",
    "File was not uploaded" => "Plik nie został wysłany",
    "No temporary directory was found for file" => "Nie zdefiniowano tymczasowego katalogu",
    "File can't be written" => "Nie można zapisać pliku",
    "A PHP extension returned an error while uploading the file" => "Rozszerzenie PHP zgłosiło wyjątek podczas wysyłania pliku",
    "File was illegally uploaded. This could be a possible attack" => "Plik został niepoprawnie wysłany. Istnieje możliwość wystąpienia ataku",
    "File was not found" => "Nie znaleziono pliku",
    "Unknown error while uploading file" => "Nieznany błąd podczas wysyłania pliku",

    // Zend\Validator\File\WordCount
    "Too many words, maximum '%max%' are allowed but '%count%' were counted" => "Podano '%count%' słów. Maksymalna liczba słów to '%max%'",
    "Too few words, minimum '%min%' are expected but '%count%' were counted" => "Podano '%count%' słów. Minimalna liczba słów to '%min%'",
    "File is not readable or does not exist" => "Plik nie istnieje lub nie można go odczytać",

    // Zend\Validator\GreaterThan
    "The input is not greater than '%min%'" => "Podana wartość nie jest większe niż '%min%'",
    "The input is not greater or equal than '%min%'" => "Podana wartość nie jest większe lub równa od '%min%'",

    // Zend\Validator\Hex
    "The input contains non-hexadecimal characters" => "Wartość nie jest wartością heksadecymalną",

    // Zend\Validator\Hostname
    "The input appears to be a DNS hostname but contains a dash in an invalid position" => "Nazwa hosta zawiera znak '-' w złym miejscu",
    "The input appears to be a DNS hostname but cannot extract TLD part" => "Nie można rozpoznać TLD dla nazwy hosta",
    "The input does not match the expected structure for a DNS hostname" => "Podana wartość nie jest poprawną nazwą hosta",
    "The input appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "Nazwa hosta jest niezgodna ze schematem dla TLD '%tld%'",
    "The input does not appear to be a valid local network name" => "Podana wartość nie jest poprawną nazwą sieci lokalnej",
    "The input does not appear to be a valid URI hostname" => "Podana wartość nie jest poprawny URI nazwy hosta",
    "The input appears to be an IP address, but IP addresses are not allowed" => "Podana wartość jest adresem IP a nie nazwą hosta",
    "The input appears to be a local network name but local network names are not allowed" => "Wartość prawdopodobnie jest nazwą sieci lokalnej. Nazwy sieci lokalnych są niedozwolone",
    "The input appears to be a DNS hostname but cannot extract TLD part" => "Nie można wyodrębnić TLD",
    "The input appears to be a DNS hostname but cannot match TLD against known list" => "Nazwa hosta zawiera nieznane TLD",

    // Zend\Validator\Iban
    "Unknown country within the IBAN" => "Niepoprawny kraj w IBAN",
    "Countries outside the Single Euro Payments Area (SEPA) are not supported" => "Kraje spoza Jednolitego Obszaru Płatniczego w Euro (SEPA) nie są obsługiwane",
    "The input has a false IBAN format" => "Wartość nie jest w formacie IBAN",
    "The input has failed the IBAN check" => "Wystąpił błąd podczas sprawdzania IBAN",

    // Zend\Validator\Identical
    "The two given tokens do not match" => "Podane wartości nie są takie same",
    "No token was provided to match against" => "Nie podano wartości do porównania",

    // Zend\Validator\InArray
    "The input was not found in the haystack" => "Nie znaleziono wartości",

    // Zend\Validator\Ip
    "The input does not appear to be a valid IP address" => "Podana wartość nie jest poprawnym adresem IP",

    // Zend\Validator\IsInstanceOf
    "The input is not an instance of '%className%'" => "Wartość nie jest instancją '%className%'",

    // Zend\Validator\Isbn
    "The input is not a valid ISBN number" => "Podana wartość nie jest poprawnym ISBN",

    // Zend\Validator\LessThan
    "The input is not less than '%max%'" => "Podana wartość nie jest mniejsze niż '%max%'",
    "The input is not less or equal than '%max%'" => "Podana wartość nie jest mniejsze lub równa '%max%'",

    // Zend\Validator\NotEmpty
    "Value is required and can't be empty" => "To pole jest wymagane",
    "Invalid type given. String, integer, float, boolean or array expected" => "Podana wartość powinna być ciągiem znaków, liczbą całkowitą, liczbą zmiennoprzecinkową, wartością logiczną lub tablicą",

    // Zend\Validator\Regex
    "The input does not match against pattern '%pattern%'" => "Podana wartość  nie pasuje do wzorca '%pattern%'",
    "There was an internal error while using the pattern '%pattern%'" => "Wystąpił błąd podczas dopasowania wyrażenia '%pattern%'",

    // Zend\Validator\Sitemap\Changefreq
    "The input is not a valid sitemap changefreq" => "Podana wartość nie jest poprawną wartością changefreq",

    // Zend\Validator\Sitemap\Lastmod
    "The input is not a valid sitemap lastmod" => "Podana wartość nie jest poprawną wartością lastmod",

    // Zend\Validator\Sitemap\Loc
    "The input is not a valid sitemap location" => "Podana wartość nie jest poprawną lokalizacją mapy strony",

    // Zend\Validator\Sitemap\Priority
    "The input is not a valid sitemap priority" => "Podana wartość nie jest poprawną wartością priorytetu",
    "Invalid type given. Numeric string, integer or float expected" => "Podana wartość powinna być ciągiem znaków, liczbą całkowitą lub liczbą zmiennoprzecinkową",

    // Zend\Validator\Step
    "Invalid value given. Scalar expected" => "Nie poprawna wartość skalarna",
    "The input is not a valid step" => "Wartość nie jest poprawnym krokiem",

    // Zend\Validator\StringLength
    "The input is less than %min% characters long" => "Podana wartość zawiera mniej niż %min% znaków",
    "The input is more than %max% characters long" => "Podana wartość zawiera więcej niż %max% znaków",

    // Zend\Validator\Uri
    "The input does not appear to be a valid Uri" => "Wartość nie jest poprawnym Uri",
);
