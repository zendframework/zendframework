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
    // Zend\I18n\Validator\Alnum
    "Invalid type given. String, integer or float expected" => "Podan neveljaven tip. Predviden je niz, celo število ali število s premično vejico",
    "The input contains characters which are non alphabetic and no digits" => "Vnos vsebuje znake, ki niso abecedni ali številni",
    "The input is an empty string" => "Vnos je prazen niz",

    // Zend\I18n\Validator\Alpha
    "Invalid type given. String expected" => "Podan neveljaven tip. Predviden je niz",
    "The input contains non alphabetic characters" => "Vnos vsebuje ne abecedne znake",
    "The input is an empty string" => "Vnos je prazen niz",

    // Zend\I18n\Validator\DateTime
    "Invalid type given. String expected" => "Podan je neveljaven tip. Pričakovan je niz",
    "The input does not appear to be a valid datetime" => "Vnos ni veljaven datum in čas",

    // Zend\I18n\Validator\Float
    "Invalid type given. String, integer or float expected" => "Podan je neveljaven tip. Niz, celo število ali število s premično vejico",
    "The input does not appear to be a float" => "Vnos ni število s premično vejico",

    // Zend\I18n\Validator\Int
    "Invalid type given. String or integer expected" => "Podan je neveljaven tip. Pričakuje se niz ali celo število",
    "The input does not appear to be an integer" => "Vnos ni celo število",

    // Zend\I18n\Validator\PhoneNumber
    "The input does not match a phone number format" => "Vnos ni telefonska številka ali oblika telefonske številke",
    "The country provided is currently unsupported" => "Ponujena država trenutno ni na voljo",
    "Invalid type given. String expected" => "Podan je neveljaven tip. Pričakuje se niz",

    // Zend\I18n\Validator\PostCode
    "Invalid type given. String or integer expected" => "Podan je neveljaven tip. Pričakuje se niz ali celo število",
    "The input does not appear to be a postal code" => "Vnos ni poštna številka",
    "An exception has been raised while validating the input" => "Med preverjanjem vnosa je prišlo do izjeme",

    // Zend\Validator\Barcode
    "The input failed checksum validation" => "Vnos ni uspel preverjanja preizkusne vsote",
    "The input contains invalid characters" => "Vnos vsebuje napačne znake",
    "The input should have a length of %length% characters" => "Vnos bi moral vsebovati dolžino znakov %length%",
    "Invalid type given. String expected" => "Podan je neveljaven tip. Pričakovan je niz",

    // Zend\Validator\Between
    "The input is not between '%min%' and '%max%', inclusively" => "Vnos ni med '%min%' in '%max%', izključujoče",
    "The input is not strictly between '%min%' and '%max%'" => "Vnos ni točno med '%min%' in '%max%'",

    // Zend\Validator\Callback
    "The input is not valid" => "Vnos ni veljaven",
    "An exception has been raised within the callback" => "Med povratnim klicem je prišlo do izjeme",

    // Zend\Validator\CreditCard
    "The input seems to contain an invalid checksum" => "Vnos vsebuje napačno preizkusno vsoto",
    "The input must contain only digits" => "Vnos lahko vsebuje samo številke",
    "Invalid type given. String expected" => "Podan je neveljaven tip. Pričakuje se niz",
    "The input contains an invalid amount of digits" => "Vnos vsebuje neveljavno število številk",
    "The input is not from an allowed institute" => "Vnos ne spada med dovoljene inštitute",
    "The input seems to be an invalid credit card number" => "Vnos ni veljavna številka kreditne kartice",
    "An exception has been raised while validating the input" => "Med preverjanjem vnosa je prišlo do izjeme",

    // Zend\Validator\Csrf
    "The form submitted did not originate from the expected site" => "Oddani obrazec ne izvira iz pričakovane strani",

    // Zend\Validator\Date
    "Invalid type given. String, integer, array or DateTime expected" => "Podan je neveljaven tip. Pričakuje se niz, celo število, polje ali datum in čas",
    "The input does not appear to be a valid date" => "Vnos ni veljaven datum",
    "The input does not fit the date format '%format%'" => "Vnos ne ustreza obliki datuma '%format%'",

    // Zend\Validator\DateStep
    "The input is not a valid step" => "Vnos ni veljaven korak",

    // Zend\Validator\Db\AbstractDb
    "No record matching the input was found" => "Noben zapis, ki se ujema z vnosom, ni bil najden",
    "A record matching the input was found" => "Zapis, ki se ujema z vnosom, je bil najden",

    // Zend\Validator\Digits
    "The input must contain only digits" => "Vnos mora vsebovati samo številke",
    "The input is an empty string" => "Vnos je prazen niz",
    "Invalid type given. String, integer or float expected" => "Podan je neveljaven tip. Pričakuje se niz, celo število ali število s plavajočo vejico",

    // Zend\Validator\EmailAddress
    "Invalid type given. String expected" => "Podan je neveljaven tip. Pričakuje se niz",
    "The input is not a valid email address. Use the basic format local-part@hostname" => "Vnos ni veljavna e-pošta. Uporabite osnovno obliko local-part@hostname",
    "'%hostname%' is not a valid hostname for the email address" => "'%hostname%' ni veljaven hostname za naslov e-pošte",
    "'%hostname%' does not appear to have any valid MX or A records for the email address" => "'%hostname%' ne vsebuje veljavnih MX ali A zapisov za naslov e-pošte",
    "'%hostname%' is not in a routable network segment. The email address should not be resolved from public network" => "'%hostname%' ni segment usmerjenega omrežja. Naslov e-pošte ne bi smel biti razreševan z javnega omrežja",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' se ne sme ujemati s t.i. dot-atom obliko",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' se ne sme ujemati s quoted-string obliko",
    "'%localPart%' is not a valid local part for the email address" => "'%localPart%' ni veljaven lokalni del za naslov e-pošte",
    "The input exceeds the allowed length" => "Vnos presega dovoljeno dolžino",

    // Zend\Validator\Explode
    "Invalid type given" => "Podan je neveljaven tip",

    // Zend\Validator\File\Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "Preveč datotek, dovoljenih je največ '%max%', vendar podanih je '%count%'",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "Premalo datotek, pričakuje se najmanj '%min%', vendar podanih je '%count%'",

    // Zend\Validator\File\Crc32
    "File does not match the given crc32 hashes" => "Datoteka se ne ujema z zgoščevanjem (hash) crc32",
    "A crc32 hash could not be evaluated for the given file" => "Za podano datoteko zgoščevanja (hash) crc32 ni bilo mogoče določiti",
    "File is not readable or does not exist" => "Datoteka ni na voljo za branje ali pa ne obstaja",

    // Zend\Validator\File\ExcludeExtension
    "File has an incorrect extension" => "Datoteka ima napačno končnico",
    "File is not readable or does not exist" => "Datoteka ni na voljo za branje ali pa ne obstaja",

    // Zend\Validator\File\Exists
    "File does not exist" => "Datoteka ne obstaja",

    // Zend\Validator\File\Extension
    "File has an incorrect extension" => "Datoteka ima napačno končnico",
    "File is not readable or does not exist" => "Datoteka ni na voljo za branje ali pa ne obstaja",

    // Zend\Validator\File\FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "Vse datoteke skupaj imajo lahko največjo velikost '%max%', vendar zaznanih je bilo '%size%'",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "Vse datoteke skupaj imajo lahko najmanjšo velikost '%min%', vendar zaznanih je bilo '%size%'",
    "One or more files can not be read" => "Ena ali več datotek niso na voljo za branje",

    // Zend\Validator\File\Hash
    "File does not match the given hashes" => "Datoteka se ne ujema z danimi zgoščenimi vrednostmi",
    "A hash could not be evaluated for the given file" => "Zgoščene vrednosti ni bilo mogoče preveriti za dano datoteko",
    "File is not readable or does not exist" => "Datoteka ni na voljo za branje ali pa ne obstaja",

    // Zend\Validator\File\ImageSize
    "Maximum allowed width for image should be '%maxwidth%' but '%width%' detected" => "Največja dovoljena širina za sliko je '%maxwidth%', vendar zaznana je '%width%'",
    "Minimum expected width for image should be '%minwidth%' but '%width%' detected" => "Najmanjša pričakovana širina za sliko je '%minwidth%', vendar zaznana je '%width%'",
    "Maximum allowed height for image should be '%maxheight%' but '%height%' detected" => "Največja dovoljena višina za sliko je '%maxheight%', vendar zaznana je '%height%'",
    "Minimum expected height for image should be '%minheight%' but '%height%' detected" => "Najmanjša pričakovana višina za sliko je '%minheight%', vendar zaznana je '%height%'",
    "The size of image could not be detected" => "Velikost slike ni bilo mogoče zaznati",
    "File is not readable or does not exist" => "Datoteka ni na voljo za branje ali pa ne obstaja",

    // Zend\Validator\File\IsCompressed
    "File is not compressed, '%type%' detected" => "Datoteka ni stisnjena, zaznan tip '%type%'",
    "The mimetype could not be detected from the file" => "Vrste datoteke (mimetype) ni mogoče zaznati iz datoteke",
    "File is not readable or does not exist" => "Datoteka ni na voljo za branje ali pa ne obstaja",

    // Zend\Validator\File\IsImage
    "File is no image, '%type%' detected" => "Datoteka ni slika, zaznan tip '%type%'",
    "The mimetype could not be detected from the file" => "Vrste datoteke (mimetype) ni mogoče zaznati iz datoteke",
    "File is not readable or does not exist" => "Datoteka ni na voljo za branje ali pa ne obstaja",

    // Zend\Validator\File\Md5
    "File does not match the given md5 hashes" => "Datoteka se ne ujema z md5 zgošeno vrednostjo",
    "An md5 hash could not be evaluated for the given file" => "Zgoščene vrednosti md5 ni bilo mogoče preveriti za dano datoteko",
    "File is not readable or does not exist" => "Datoteka ni na voljo za branje ali pa ne obstaja",

    // Zend\Validator\File\MimeType
    "File has an incorrect mimetype of '%type%'" => "Datoteka ima napačno vrsto (mimetype) tipa '%type%'",
    "The mimetype could not be detected from the file" => "Vrste datoteke (mimetype) ni bilo mogoče zaznati iz datoteke",
    "File is not readable or does not exist" => "Datoteka ni na voljo za branje ali pa ne obstaja",

    // Zend\Validator\File\NotExists
    "File exists" => "Datoteka obstaja",

    // Zend\Validator\File\Sha1
    "File does not match the given sha1 hashes" => "Datoteka se ne ujema z dano zgoščeno vrednostjo sha1",
    "A sha1 hash could not be evaluated for the given file" => "Zgoščene vrednosti sha1 ni bilo mogoče zaznati za dano datoteko",
    "File is not readable or does not exist" => "Datoteka ni na voljo za branje ali pa ne obstaja",

    // Zend\Validator\File\Size
    "Maximum allowed size for file is '%max%' but '%size%' detected" => "Največja dovoljena velikost datoteke je '%max%', vendar zaznana je '%size%'",
    "Minimum expected size for file is '%min%' but '%size%' detected" => "Najmanjša pričakovana velikost datoteke je '%min%', vendar zaznana je '%size%'",
    "File is not readable or does not exist" => "Datoteka ni na voljo za branje ali pa ne obstaja",

    // Zend\Validator\File\Upload
    "File '%value%' exceeds the defined ini size" => "Datoteka '%value%' presega definirano ini vrednost",
    "File '%value%' exceeds the defined form size" => "Datoteka '%value%' presega velikost definirano v obrazcu",
    "File '%value%' was only partially uploaded" => "Datoteka '%value%' je bila samo delno naložena",
    "File '%value%' was not uploaded" => "Datoteka '%value%' ni bila naložena",
    "No temporary directory was found for file '%value%'" => "Začasnega direktorija ni bilo mogoče najti za datoteko '%value%'",
    "File '%value%' can't be written" => "Datoteke '%value%' ni mogoče zapisati",
    "A PHP extension returned an error while uploading the file '%value%'" => "PHP razširitev je vrnila napako med nalaganjem datoteke '%value%'",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "Datoteka '%value%' je bila nelegalno naložena. To je lahko potencialen napad",
    "File '%value%' was not found" => "Datoteke '%value%' ni bilo mogoče najti",
    "Unknown error while uploading file '%value%'" => "Neznana napaka med nalaganjem datoteke '%value%'",

    // Zend\Validator\File\UploadFile
    "File exceeds the defined ini size" => "Datoteka presega definirano ini vrednost",
    "File exceeds the defined form size" => "Datoteka presega velikost definirano v obrazcu",
    "File was only partially uploaded" => "Datoteka je bila samo delno naložena",
    "File was not uploaded" => "Datoteka ni bila naložena",
    "No temporary directory was found for file" => "Začasnega direktorija ni bilo mogoče najti za datoteko",
    "File can't be written" => "Datoteke ni bilo mogoče zapisati",
    "A PHP extension returned an error while uploading the file" => "PHP razširitev je vrnila napako med nalaganjem datoteke",
    "File was illegally uploaded. This could be a possible attack" => "Datoteka je bila nelegalno naložena. Gre lahko za potencialni napad",
    "File was not found" => "Datoteke ni bilo mogoče najti",
    "Unknown error while uploading file" => "Neznana napaka med nalaganjem datoteke",

    // Zend\Validator\File\WordCount
    "Too many words, maximum '%max%' are allowed but '%count%' were counted" => "Preveč besed, največje dovoljeno število je '%max%', vendar prešteto je bilo '%count%'",
    "Too few words, minimum '%min%' are expected but '%count%' were counted" => "Premalo besed, najmanjše pričakovano število je '%min%', vendar prešteto je bilo '%count%'",
    "File is not readable or does not exist" => "Datoteka ni na voljo za branje ali pa ne obstaja",

    // Zend\Validator\GreaterThan
    "The input is not greater than '%min%'" => "Vnos ni večji od '%min%'",
    "The input is not greater or equal than '%min%'" => "Vnos ni večji ali enak '%min%'",

    // Zend\Validator\Hex
    "Invalid type given. String expected" => "Podan je neveljaven tip. Pričakuje se niz",
    "The input contains non-hexadecimal characters" => "Vnos vsebuje ne heksadecimalne znake",

    // Zend\Validator\Hostname
    "The input appears to be a DNS hostname but the given punycode notation cannot be decoded" => "Vnos je DNS hostname vendar punycode notacije ni mogoče zaznati",
    "Invalid type given. String expected" => "Podan je neveljaven tip. Pričakuje se niz",
    "The input appears to be a DNS hostname but contains a dash in an invalid position" => "Vnos je DNS hostname vendar vsebuje pomišljaj na neveljavnem mestu",
    "The input does not match the expected structure for a DNS hostname" => "Vnos se ne ujema s pričakovano strukturo za DNS hostname",
    "The input appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "Vnos je DNS hostname vendar se ne more ujemati s shemo hostname-a za TLD '%tld%'",
    "The input does not appear to be a valid local network name" => "Vnos ni veljavno ime lokalnega omrežja",
    "The input does not appear to be a valid URI hostname" => "Vnos ni veljaven URI hostname",
    "The input appears to be an IP address, but IP addresses are not allowed" => "Vnost je IP naslov, vendar IP naslovi niso dovoljeni",
    "The input appears to be a local network name but local network names are not allowed" => "Vnost je ime lokalnega omrežja vendar imena lokalnih omrežij niso dovoljena",
    "The input appears to be a DNS hostname but cannot extract TLD part" => "Vnos je DNS hostname vendar ne more izločiti TLD dela",
    "The input appears to be a DNS hostname but cannot match TLD against known list" => "Vnos je DNS hostname vendar se ne more ujemati s TLD-ji znanega seznama",

    // Zend\Validator\Iban
    "Unknown country within the IBAN" => "Neznana država znotraj IBAN-a",
    "Countries outside the Single Euro Payments Area (SEPA) are not supported" => "Države izven SEPA niso podprte",
    "The input has a false IBAN format" => "Vnos vsebuje napačno obliko IBAN",
    "The input has failed the IBAN check" => "Vnos ni uspel IBAN preverjanja",

    // Zend\Validator\Identical
    "The two given tokens do not match" => "Dana žetona se ne ujemata",
    "No token was provided to match against" => "Ni bilo podanega žetona za preverjanje",

    // Zend\Validator\InArray
    "The input was not found in the haystack" => "Vnos ni bil najden v seneni kopici (haystack)",

    // Zend\Validator\Ip
    "Invalid type given. String expected" => "Podan je neveljaven tip. Pričakuje se niz",
    "The input does not appear to be a valid IP address" => "Vnos ni veljaven IP naslov",

    // Zend\Validator\IsInstanceOf
    "The input is not an instance of '%className%'" => "Vnos ni instanca '%className%'",

    // Zend\Validator\Isbn
    "Invalid type given. String or integer expected" => "Podan je neveljaven tip. Pričakuje se niz ali celo število",
    "The input is not a valid ISBN number" => "Vnos ni veljavna ISBN številka",

    // Zend\Validator\LessThan
    "The input is not less than '%max%'" => "Vnos ni manjši od '%max%'",
    "The input is not less or equal than '%max%'" => "Vnos ni manjši ali enak '%max%'",

    // Zend\Validator\NotEmpty
    "Value is required and can't be empty" => "Vrednost je obvezna in ne sme biti prazna",
    "Invalid type given. String, integer, float, boolean or array expected" => "Podan je neveljaven tip. Pričakuje se niz, celo število, število s plavajočo vejico, logična vrednost ali polje",

    // Zend\Validator\Regex
    "Invalid type given. String, integer or float expected" => "Podan je neveljaven tip. Pričakuje se niz, celo število ali število s plavajočo vejico",
    "The input does not match against pattern '%pattern%'" => "Vnos se ne ujema z vzorcem '%pattern%'",
    "There was an internal error while using the pattern '%pattern%'" => "Prišlo je do notranje napake med uporabo vzorca '%pattern%'",

    // Zend\Validator\Sitemap\Changefreq
    "The input is not a valid sitemap changefreq" => "Vnos ni veljavna pogostost spreminjanja kazala (changefreq)",
    "Invalid type given. String expected" => "Podan je neveljaven tip. Priačkuje se niz",

    // Zend\Validator\Sitemap\Lastmod
    "The input is not a valid sitemap lastmod" => "Vnos ni veljavna zadnja sprememba kazala (lastmod)",
    "Invalid type given. String expected" => "Podan je neveljaven tip. Pričakuje se niz",

    // Zend\Validator\Sitemap\Loc
    "The input is not a valid sitemap location" => "Vnos ni veljavna lokacija kazala",
    "Invalid type given. String expected" => "Podan je neveljaven tip. Pričakuje se niz",

    // Zend\Validator\Sitemap\Priority
    "The input is not a valid sitemap priority" => "Vnos ni veljavna prioriteta kazala",
    "Invalid type given. Numeric string, integer or float expected" => "Podan je neveljaven tip. Pričakuje se numerični niz, celo število ali število s plavajočo vejico",

    // Zend\Validator\Step
    "Invalid value given. Scalar expected" => "Podana je neveljavna vrednost. Pričakuje se skalar",
    "The input is not a valid step" => "Vnos ni veljaven korak",

    // Zend\Validator\StringLength
    "Invalid type given. String expected" => "Podan je neveljaven tip. Pričakuje se niz",
    "The input is less than %min% characters long" => "Vnos je manjši od števila znakov %min%",
    "The input is more than %max% characters long" => "Vnos je daljši od števila znakov %max%",

    // Zend\Validator\Uri
    "Invalid type given. String expected" => "Podan je neveljaven tip. Pričakuje se niz",
    "The input does not appear to be a valid Uri" => "Vnos ni veljaven enotni identifikator vira (Uri)",
);
