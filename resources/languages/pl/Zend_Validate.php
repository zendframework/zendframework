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
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * EN-Revision: 25.Jul.2011
 */
return array(
    // Zend_Validate_Alnum
    "Invalid type given. String, integer or float expected" => "Podana wartość powinna być ciągiem znaków, liczbą całkowitą lub liczbą zmiennoprzecinkową",
    "'%value%' contains characters which are non alphabetic and no digits" => "Wartość '%value%' powinna zawierać znaki z alfabetu lub cyfry",
    "'%value%' is an empty string" => "'%value%' jest pustym ciągiem znaków",

    // Zend_Validate_Alpha
    "Invalid type given. String expected" => "Podana wartość nie jest ciągiem znaków",
    "'%value%' contains non alphabetic characters" => "'%value%' zawiera znaki spoza alfabetu",
    "'%value%' is an empty string" => "'%value%' jest pustym ciągiem znaków",

    // Zend_Validate_Barcode
    "'%value%' failed checksum validation" => "Błędna suma kontrolna dla wartości '%value%'",
    "'%value%' contains invalid characters" => "'%value%' zawiera niedozwolone znaki",
    "'%value%' should have a length of %length% characters" => "Wartość '%value%' powinna być długości %length% znaków",
    "Invalid type given. String expected" => "Podana wartość nie jest ciągiem znaków",

    // Zend_Validate_Between
    "'%value%' is not between '%min%' and '%max%', inclusively" => "'%value%' nie zawiera się w przedziale od '%min%' do '%max%' włącznie",
    "'%value%' is not strictly between '%min%' and '%max%'" => "'%value%' nie zawiera się w przedziale od '%min%' do '%max%'",

    // Zend_Validate_Callback
    "'%value%' is not valid" => "Wartość '%value%' jest nie poprawna",
    "An exception has been raised within the callback" => "Wystąpił błąd podczas działania funkcji sprawdzającej",

    // Zend_Validate_Ccnum
    "'%value%' must contain between 13 and 19 digits" => "'%value%' musi zawierać od 13 do 19 cyfr",
    "Luhn algorithm (mod-10 checksum) failed on '%value%'" => "Błąd podczas wykonywania algorytmu Luhna (mod-10 checksum) dla wartości '%value%'",

    // Zend_Validate_CreditCard
    "'%value%' seems to contain an invalid checksum" => "'%value%' zawiera niepoprawną sumę kontrolną",
    "'%value%' must contain only digits" => "Numer karty może zawierać tylko cyfry",
    "Invalid type given. String expected" => "Podana wartość nie jest ciągiem znaków",
    "'%value%' contains an invalid amount of digits" => "Numer '%value%' zawiera niepoprawną liczbę cyfr",
    "'%value%' is not from an allowed institute" => "Numer '%value%' nie jest z dozwolonej instytucji",
    "'%value%' seems to be an invalid creditcard number" => "'%value%' jest niepoprawnym numerem karty",
    "An exception has been raised while validating '%value%'" => "Wystąpił błąd podczas sprawdzania numeru karty '%value%'",

    // Zend_Validate_Date
    "Invalid type given. String, integer, array or Zend_Date expected" => "Podana wartość powinna być ciągiem znaków, liczbą, tablicą lub obiektem Zend_Date",
    "'%value%' does not appear to be a valid date" => "'%value%' nie jest poprawną datą",
    "'%value%' does not fit the date format '%format%'" => "Data '%value%' nie jest w formacie '%format%'",

    // Zend_Validate_Db_Abstract
    "No record matching '%value%' was found" => "Nie znaleziono rekordu dla '%value%'",
    "A record matching '%value%' was found" => "Znaleziono rekord dla '%value%'",

    // Zend_Validate_Digits
    "Invalid type given. String, integer or float expected" => "Podana wartość powinna być ciągiem znaków, liczbą całkowitą lub liczbą zmiennoprzecinkową",
    "'%value%' must contain only digits" => "'%value%' może zawierać tylko cyfry",
    "'%value%' is an empty string" => "'%value%' jest pustym ciągiem znaków",

    // Zend_Validate_EmailAddress
    "Invalid type given. String expected" => "Podana wartość nie jest ciągiem znaków",
    "'%value%' is not a valid email address in the basic format local-part@hostname" => "'%value%' nie jest poprawnym adresem email w formacie nazwa@serwer",
    "'%hostname%' is not a valid hostname for email address '%value%'" => "Email '%value%' zawiera niepoprawną nazwę serwera '%hostname%'",
    "'%hostname%' does not appear to have a valid MX record for the email address '%value%'" => "Serwer '%hostname%' nie posiada poprawnie zdefiniowanego rekordu MX dla adresu '%value%'",
    "'%hostname%' is not in a routable network segment. The email address '%value%' should not be resolved from public network" => "'%hostname%' nie rutowalnym segmentem sieci. Email '%value%' nie powinien być wykrywany z sieci publiczej",
    "'%localPart%' can not be matched against dot-atom format" => "Nazwa '%localPart%' nie jest w formacie dot-atom",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' nie jest w formacie quoted-string",
    "'%localPart%' is not a valid local part for email address '%value%'" => "'%localPart%' nie jest poprawną nazwą",
    "'%value%' exceeds the allowed length" => "Wartość '%value%' przekroczyła dozwoloną długość",

    // Zend_Validate_File_Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "Wybrano '%count%' plików. Dopuszczalna liczba plików to '%max%'",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "Wybrano '%count%' plików. Minimalna liczba plików to '%min%'",

    // Zend_Validate_File_Crc32
    "File '%value%' does not match the given crc32 hashes" => "Błędna suma kontrolna pliku '%value%'",
    "A crc32 hash could not be evaluated for the given file" => "Nie można obliczyć sumy kontrolnej dla podanego pliku",
    "File '%value%' is not readable or does not exist" => "Plik '%value%' nie istnieje lub nie można go odczytać",

    // Zend_Validate_File_ExcludeExtension
    "File '%value%' has a false extension" => "Plik '%value%' ma niepoprawne rozszerzenie",
    "File '%value%' is not readable or does not exist" => "Plik '%value%' nie istnieje lub nie można go odczytać",

    // Zend_Validate_File_ExcludeMimeType
    "File '%value%' has a false mimetype of '%type%'" => "Plik '%value%' ma niepoprawny typ MIME '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Nie można wykryć typu MIME dla pliku '%value%'",
    "File '%value%' is not readable or does not exist" => "Plik '%value%' nie istnieje lub nie można go odczytać",

    // Zend_Validate_File_Exists
    "File '%value%' does not exist" => "Plik '%value%' nie istnieje",

    // Zend_Validate_File_Extension
    "File '%value%' has a false extension" => "Plik '%value%' ma niepoprawne rozszerzenie",
    "File '%value%' is not readable or does not exist" => "Plik '%value%' nie istnieje lub nie można go odczytać",

    // Zend_Validate_File_FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "Wybrane pliki łącznie zajmują '%size%'. Maksymalny łączny rozmiar to '%max%'",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "Wybrane pliki łącznie zajmują '%size%'. Minimalny łączny rozmiar to '%min%'",
    "One or more files can not be read" => "Jeden lub więcej plików nie mogą zostać odczytane",

    // Zend_Validate_File_Hash
    "File '%value%' does not match the given hashes" => "Plik '%value%' ma niedopuszczalny hash",
    "A hash could not be evaluated for the given file" => "Nie można obliczyć funkcji haszującej dla podanego pliku",
    "File '%value%' is not readable or does not exist" => "Plik '%value%' nie istnieje lub nie można go odczytać",

    // Zend_Validate_File_ImageSize
    "Maximum allowed width for image '%value%' should be '%maxwidth%' but '%width%' detected" => "Plik '%value%' ma szerokość '%width%'. Maksymalna szerokość to '%maxwidth%'",
    "Minimum expected width for image '%value%' should be '%minwidth%' but '%width%' detected" => "Plik '%value%' ma szerokość '%width%'. Minimalna szerokość to '%maxwidth%'",
    "Maximum allowed height for image '%value%' should be '%maxheight%' but '%height%' detected" => "Plik '%value%' ma wysokość '%height%'. Maksymalna wysokość to '%maxheight%'",
    "Minimum expected height for image '%value%' should be '%minheight%' but '%height%' detected" => "Plik '%value%' ma wysokość '%height%'. Minimalna wysokość to '%minheight%'",
    "The size of image '%value%' could not be detected" => "Nie można określić rozmiaru pliku '%value%'",
    "File '%value%' is not readable or does not exist" => "Plik '%value%' nie istnieje lub nie można go odczytać",

    // Zend_Validate_File_IsCompressed
    "File '%value%' is not compressed, '%type%' detected" => "Plik '%value%' typu '%type%' nie jest skompresowany",
    "The mimetype of file '%value%' could not be detected" => "Nie można wykryć typu MIME dla pliku '%value%'",
    "File '%value%' is not readable or does not exist" => "Plik '%value%' nie istnieje lub nie można go odczytać",

    // Zend_Validate_File_IsImage
    "File '%value%' is no image, '%type%' detected" => "Plik '%value%' typu '%type%' nie jest obrazem",
    "The mimetype of file '%value%' could not be detected" => "Nie można wykryć typu MIME dla pliku '%value%'",
    "File '%value%' is not readable or does not exist" => "Plik '%value%' nie istnieje lub nie można go odczytać",

    // Zend_Validate_File_Md5
    "File '%value%' does not match the given md5 hashes" => "Plik '%value%' ma niedopuszczalny hash md5",
    "A md5 hash could not be evaluated for the given file" => "Nie można obliczyć funkcji haszującej md5 dla podanego pliku",
    "File '%value%' is not readable or does not exist" => "Plik '%value%' nie istnieje lub nie można go odczytać",

    // Zend_Validate_File_MimeType
    "File '%value%' has a false mimetype of '%type%'" => "Plik '%value%' ma niepoprawny typ MIME '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Nie można wykryć typu MIME dla pliku '%value%'",
    "File '%value%' is not readable or does not exist" => "Plik '%value%' nie istnieje lub nie można go odczytać",

    // Zend_Validate_File_NotExists
    "File '%value%' exists" => "Plik '%value%' istnieje",

    // Zend_Validate_File_Sha1
    "File '%value%' does not match the given sha1 hashes" => "Plik '%value%' ma niedopuszczalny hash sha1",
    "A sha1 hash could not be evaluated for the given file" => "Nie można obliczyć funkcji haszującej sha1 dla podanego pliku",
    "File '%value%' is not readable or does not exist" => "Plik '%value%' nie istnieje lub nie można go odczytać",

    // Zend_Validate_File_Size
    "Maximum allowed size for file '%value%' is '%max%' but '%size%' detected" => "Podany plik ma rozmiar '%size%'. Maksymalny rozmiar pliku to '%max%'",
    "Minimum expected size for file '%value%' is '%min%' but '%size%' detected" => "Podany plik ma rozmiar '%size%'. Minimalny rozmiar pliku to '%min%'",
    "File '%value%' is not readable or does not exist" => "Plik '%value%' nie istnieje lub nie można go odczytać",

    // Zend_Validate_File_Upload
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

    // Zend_Validate_File_WordCount
    "Too much words, maximum '%max%' are allowed but '%count%' were counted" => "Podano '%count%' słów. Maksymalna liczba słów to '%max%'",
    "Too less words, minimum '%min%' are expected but '%count%' were counted" => "Podano '%count%' słów. Minimalna liczba słów to '%min%'",
    "File '%value%' is not readable or does not exist" => "Plik '%value%' nie istnieje lub nie można go odczytać",

    // Zend_Validate_Float
    "Invalid type given. String, integer or float expected" => "Podana wartość powinna być ciągiem znaków, liczbą całkowitą lub liczbą zmiennoprzecinkową",
    "'%value%' does not appear to be a float" => "'%value%' nie jest liczbą zmiennoprzecinkową",

    // Zend_Validate_GreaterThan
    "'%value%' is not greater than '%min%'" => "'%value%' nie jest większe niż '%min%'",

    // Zend_Validate_Hex
    "Invalid type given. String expected" => "Podana wartość nie jest ciągiem znaków",
    "'%value%' has not only hexadecimal digit characters" => "'%value%' nie jest wartością heksadecymalną",

    // Zend_Validate_Hostname
    "Invalid type given. String expected" => "Podana wartość nie jest ciągiem znaków",
    "'%value%' appears to be an IP address, but IP addresses are not allowed" => "Wartość '%value%' jest adresem IP a nie nazwą hosta",
    "'%value%' appears to be a DNS hostname but cannot match TLD against known list" => "'%value%' zawiera nieznane TLD",
    "'%value%' appears to be a DNS hostname but contains a dash in an invalid position" => "Nazwa hosta '%value%' zawiera znak '-' w złym miejscu",
    "'%value%' appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "Nazwa hosta '%value%' jest niezgodna ze schematem dla TLD '%tld%'",
    "'%value%' appears to be a DNS hostname but cannot extract TLD part" => "Nie można rozpoznać TLD dla nazwy hosta '%value%'",
    "'%value%' does not match the expected structure for a DNS hostname" => "'%value%' nie jest poprawną nazwą hosta",
    "'%value%' does not appear to be a valid local network name" => "'%value%' nie jest poprawną nazwą sieci lokalnej",
    "'%value%' appears to be a local network name but local network names are not allowed" => "'%value%' prawdopodobnie jest nazwą sieci lokalnej. Nazwy sieci lokalnych są niedozwolone",
    "'%value%' appears to be a DNS hostname but the given punycode notation cannot be decoded" => "Nie można zdekodować punycode dla podanej nazwy hosta '%value%'",

    // Zend_Validate_Iban
    "Unknown country within the IBAN '%value%'" => "Niepoprawny kraj w IBAN '%value%'",
    "'%value%' has a false IBAN format" => "Wartość '%value%' nie jest w formacie IBAN",
    "'%value%' has failed the IBAN check" => "Wystąpił błąd podczas sprawdzania IBAN dla '%value%'",

    // Zend_Validate_Identical
    "The two given tokens do not match" => "Podane wartości nie są takie same",
    "No token was provided to match against" => "Nie podano wartości do porównania",

    // Zend_Validate_InArray
    "'%value%' was not found in the haystack" => "Nie znaleziono wartości '%value%'",

    // Zend_Validate_Int
    "Invalid type given. String or integer expected" => "Podana wartość powinna być ciągiem znaków lub liczbą całkowitą",
    "'%value%' does not appear to be an integer" => "'%value%' nie jest liczbą",

    // Zend_Validate_Ip
    "Invalid type given. String expected" => "Podana wartość nie jest ciągiem znaków",
    "'%value%' does not appear to be a valid IP address" => "'%value%' nie jest poprawnym adresem IP",

    // Zend_Validate_Isbn
    "Invalid type given. String or integer expected" => "Podana wartość powinna być ciągiem znaków lub liczbą całkowitą",
    "'%value%' is not a valid ISBN number"  => "'%value%' nie jest poprawnym ISBN",

    // Zend_Validate_LessThan
    "'%value%' is not less than '%max%'" => "'%value%' nie jest mniejsze niż '%max%'",

    // Zend_Validate_NotEmpty
    "Invalid type given. String, integer, float, boolean or array expected" => "Podana wartość powinna być ciągiem znaków, liczbą całkowitą, liczbą zmiennoprzecinkową, wartością logiczną lub tablicą",
    "Value is required and can't be empty" => "To pole jest wymagane",

    // Zend_Validate_PostCode
    "Invalid type given. String or integer expected" => "Podana wartość powinna być ciągiem znaków lub liczbą całkowitą",
    "'%value%' does not appear to be a postal code" => "Wartość '%value%' nie jest poprawnym kodem pocztowym",

    // Zend_Validate_Regex
    "Invalid type given. String, integer or float expected" => "Podana wartość powinna być ciągiem znaków, liczbą całkowitą lub liczbą zmiennoprzecinkową",
    "'%value%' does not match against pattern '%pattern%'" => "Wartość '%value%' nie pasuje do wzorca '%pattern%'",
    "There was an internal error while using the pattern '%pattern%'" => "Wystąpił błąd podczas dopasowania wyrażenia '%pattern%'",

    // Zend_Validate_Sitemap_Changefreq
    "'%value%' is not a valid sitemap changefreq" => "'%value%' nie jest poprawną wartością changefreq",
    "Invalid type given. String expected" => "Podana wartość nie jest ciągiem znaków",

    // Zend_Validate_Sitemap_Lastmod
    "'%value%' is not a valid sitemap lastmod" => "'%value%' nie jest poprawną wartością lastmod",
    "Invalid type given. String expected" => "Podana wartość nie jest ciągiem znaków",

    // Zend_Validate_Sitemap_Loc
    "'%value%' is not a valid sitemap location" => "'%value%' nie jest poprawną lokalizacją mapy strony",
    "Invalid type given. String expected" => "Podana wartość nie jest ciągiem znaków",

    // Zend_Validate_Sitemap_Priority
    "'%value%' is not a valid sitemap priority" => "'%value%' nie jest poprawną wartością priorytetu",
    "Invalid type given. Numeric string, integer or float expected" => "Podana wartość powinna być ciągiem znaków, liczbą całkowitą lub liczbą zmiennoprzecinkową",

    // Zend_Validate_StringLength
    "Invalid type given. String expected" => "Podana wartość nie jest ciągiem znaków",
    "'%value%' is less than %min% characters long" => "'%value%' zawiera mniej niż %min% znaków",
    "'%value%' is more than %max% characters long" => "'%value%' zawiera więcej niż %max% znaków",
);
