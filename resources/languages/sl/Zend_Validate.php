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
 * EN-Revision: 25.Jul.2011
 */
return array(
    // Zend_Validate_Alnum
    "Invalid type given. String, integer or float expected" => "Podan neveljaven tip. Predviden je niz, celo število ali plavajoče število",
    "'%value%' contains characters which are non alphabetic and no digits" => "'%value%' vsebuje ne abecedne znake in nima številk",
    "'%value%' is an empty string" => "'%value%' je prazen niz",

    // Zend_Validate_Alpha
    "Invalid type given. String expected" => "Podan neveljaven tip. Predviden je niz",
    "'%value%' contains non alphabetic characters" => "'%value%' vsebuje ne abecedne znake",
    "'%value%' is an empty string" => "'%value%' je prazen niz",

    // Zend_Validate_Barcode
    "'%value%' failed checksum validation" => "'%value%' neuspešno preverjena preizkusna vsota (checksum)",
    "'%value%' contains invalid characters" => "'%value%' vsebuje nedovoljene znake",
    "'%value%' should have a length of %length% characters" => "'%value%' mora biti dolžine %length% znakov",
    "Invalid type given. String expected" => "Podan neveljaven tip. Predviden je niz",

    // Zend_Validate_Between
    "'%value%' is not between '%min%' and '%max%', inclusively" => "'%value%' ni med '%min%' in vključno '%max%'",
    "'%value%' is not strictly between '%min%' and '%max%'" => "'%value%' ni točno med '%min%' in '%max%'",

    // Zend_Validate_Callback
    "'%value%' is not valid" => "'%value%' vrednost ni veljavna",
    "An exception has been raised within the callback" => "Prišlo je do napake v povratnem klicu",

    // Zend_Validate_Ccnum
    "'%value%' must contain between 13 and 19 digits" => "'%value%' mora biti med 13 in 19 številkami",
    "Luhn algorithm (mod-10 checksum) failed on '%value%'" => "Algoritem 'Luhn' (mod-10 checksum) neuspešen pri '%value%'",

    // Zend_Validate_CreditCard
    "'%value%' seems to contain an invalid checksum" => "'%value%' verjetno vključuje neveljavno preizkusno vsoto (checksum)",
    "'%value%' must contain only digits" => "'%value%' mora vsebovati samo številke",
    "Invalid type given. String expected" => "Neveljaven tip. Predviden je niz",
    "'%value%' contains an invalid amount of digits" => "'%value%' vsebuje neveljavno število številk",
    "'%value%' is not from an allowed institute" => "'%value%' ni iz dovoljenega inštituta",
    "'%value%' seems to be an invalid creditcard number" => "'%value%' se zdi, da je napačna številka kreditne kartice",
    "An exception has been raised while validating '%value%'" => "Prišlo je do napake pri preverjanju vrednosti '%value%'",

    // Zend_Validate_Date
    "Invalid type given. String, integer, array or Zend_Date expected" => "Neveljaven tip. Predviden je niz, celo število, polje ali Zend_Date",
    "'%value%' does not appear to be a valid date" => "'%value%' se zdi, da ni veljaven datum",
    "'%value%' does not fit the date format '%format%'" => "'%value%' se ne ujema s formatom datuma '%format%'",

    // Zend_Validate_Db_Abstract
    "No record matching '%value%' was found" => "Zapis, ki bi ustrezal '%value%' ni bil najden",
    "A record matching '%value%' was found" => "Zapis, ki se ujema '%value%' je bil najden",

    // Zend_Validate_Digits
    "Invalid type given. String, integer or float expected" => "Neveljaven tip. Predviden je niz, celo število ali plavajoče število",
    "'%value%' must contain only digits" => "'%value%' mora vsebovati samo številke",
    "'%value%' is an empty string" => "'%value%' vrednost je prazna",

    // Zend_Validate_EmailAddress
    "Invalid type given. String expected" => "Neveljaven tip. Predviden je niz",
    "'%value%' is not a valid email address in the basic format local-part@hostname" => "'%value%' ni veljavna e-pošta formata lokalni-del@hostname",
    "'%hostname%' is not a valid hostname for email address '%value%'" => "'%hostname%' ni veljavno ime gostitelja za e-poštni naslov '%value%'",
    "'%hostname%' does not appear to have a valid MX record for the email address '%value%'" => "'%hostname%' nima veljavnega MX zapisa za e-pošto '%value%'",
    "'%hostname%' is not in a routable network segment. The email address '%value%' should not be resolved from public network" => "'%hostname%' ni v routable segmentu omrežja. E-pošta '%value%' ne bi smela biti določena iz javnega omrežja",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' se ne ujema s formatom dot-atom",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' se ne ujema s formatom 'quoted-string'",
    "'%localPart%' is not a valid local part for email address '%value%'" => "'%localPart%' ni veljaven lokalni del e-pošte '%value%'",
    "'%value%' exceeds the allowed length" => "'%value%' je večje od dovoljene dolžine",

    // Zend_Validate_File_Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "Preveliko število datotek, dovoljenih je največ '%max%', poslanih je pa '%count%'",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "Premajhno število datotek, najmanj predvidenih je '%min%', poslanih je pa '%count%'",

    // Zend_Validate_File_Crc32
    "File '%value%' does not match the given crc32 hashes" => "Datoteka '%value%' se ne ujema z dano kodo crc32",
    "A crc32 hash could not be evaluated for the given file" => "crc32 kode ni bila moč preveriti za dano datoteko",
    "File '%value%' is not readable or does not exist" => "Datoteke '%value%' ni moč brati ali pa ne obstaja",

    // Zend_Validate_File_ExcludeExtension
    "File '%value%' has a false extension" => "Datoteka '%value%' ima napačno končnico",
    "File '%value%' is not readable or does not exist" => "Datoteke '%value%' ni moč brati ali pa ne obstaja",

    // Zend_Validate_File_ExcludeMimeType
    "File '%value%' has a false mimetype of '%type%'" => "Datoteka '%value%' ima napačen 'mimetype' vrste '%type%'",
    "The mimetype of file '%value%' could not be detected" => "'Mimetype' datoteke '%value%' ni bilo moč zaznati",
    "File '%value%' is not readable or does not exist" => "Datoteka '%value%' ni bralna ali pa ne obstaja",

    // Zend_Validate_File_Exists
    "File '%value%' does not exist" => "Datoteka '%value%' ne obstaja",

    // Zend_Validate_File_Extension
    "File '%value%' has a false extension" => "Datoteka '%value%' ima napačno končnico",
    "File '%value%' is not readable or does not exist" => "Datoteka '%value%' ni bralna ali pa ne obstaja",

    // Zend_Validate_File_FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "Vse datoteke skupaj bi morale imeti največjo velikost '%max%' vendar zaznano je bilo '%size%'",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "Vse datoteke skupaj bi morale imeti najmanjšo velikost '%min%' vendar zaznano je bilo '%size%'",
    "One or more files can not be read" => "Ene ali več datotek ni mogoče prebrati",

    // Zend_Validate_File_Hash
    "File '%value%' does not match the given hashes" => "Datoteka '%value%' se ne ujema z dano kodo",
    "A hash could not be evaluated for the given file" => "Kode ni bilo moč oceniti",
    "File '%value%' is not readable or does not exist" => "Datoteke '%value%' ni mogoče brati ali pa ne obstaja",

    // Zend_Validate_File_ImageSize
    "Maximum allowed width for image '%value%' should be '%maxwidth%' but '%width%' detected" => "Največja dovoljena širina slike '%value%' bi morala biti '%maxwidth%' vendar zaznano je '%width%'",
    "Minimum expected width for image '%value%' should be '%minwidth%' but '%width%' detected" => "Najmanjša predvidena širina slike '%value%' bi morala biti '%minwidth%' vendar zaznano je '%width%'",
    "Maximum allowed height for image '%value%' should be '%maxheight%' but '%height%' detected" => "Največja dovoljena višina slike '%value%' bi morala biti '%maxheight%' vendar zaznano je '%height%'",
    "Minimum expected height for image '%value%' should be '%minheight%' but '%height%' detected" => "Najmanjša predvidena višina '%value%' bi morala biti '%minheight%' vendar zaznano je '%height%'",
    "The size of image '%value%' could not be detected" => "Velikost slike '%value%' ni bilo moč zaznati",
    "File '%value%' is not readable or does not exist" => "Datoteke '%value%' ni mogoče brati ali pa ne obstaja",

    // Zend_Validate_File_IsCompressed
    "File '%value%' is not compressed, '%type%' detected" => "Datoteka '%value%' ni skrčena, '%type%' vrsta zaznana",
    "The mimetype of file '%value%' could not be detected" => "'Mimetype' datoteke '%value%' ni bilo mogoče zaznati",
    "File '%value%' is not readable or does not exist" => "Datoteke '%value%' ni mogoče brati ali pa ne obstaja",

    // Zend_Validate_File_IsImage
    "File '%value%' is no image, '%type%' detected" => "Datoteka '%value%' ni slika, '%type%' vrsta zaznana",
    "The mimetype of file '%value%' could not be detected" => "'Mimetype' datoteke '%value%' ni bilo mogoče zaznati",
    "File '%value%' is not readable or does not exist" => "Datoteke '%value%' ni mogoče brati ali pa ne obstaja",

    // Zend_Validate_File_Md5
    "File '%value%' does not match the given md5 hashes" => "Datoteka '%value%' se ne ujema z dano md5 kodo",
    "A md5 hash could not be evaluated for the given file" => "Kode md5 ni bilo mogoče preveriti za dano datoteko",
    "File '%value%' is not readable or does not exist" => "Datoteke '%value%' ni mogoče brati ali pa ne obstaja",

    // Zend_Validate_File_MimeType
    "File '%value%' has a false mimetype of '%type%'" => "Datoteka '%value%' ima nepravilen 'mimetype' vrste '%type%'",
    "The mimetype of file '%value%' could not be detected" => "'Mimetype' datoteke '%value%' ni bilo mogoče zaznati",
    "File '%value%' is not readable or does not exist" => "Datoteke '%value%' ni mogoče brati ali pa ne obstaja",

    // Zend_Validate_File_NotExists
    "File '%value%' exists" => "Datoteka '%value%' obstaja",

    // Zend_Validate_File_Sha1
    "File '%value%' does not match the given sha1 hashes" => "Datoteka '%value%' se ne ujema s kodo sha1",
    "A sha1 hash could not be evaluated for the given file" => "Za dano datoteko ni bilo mogoče preveriti kode sha1",
    "File '%value%' is not readable or does not exist" => "Datoteke '%value%' ni mogoče prebrati ali pa ne obstaja",

    // Zend_Validate_File_Size
    "Maximum allowed size for file '%value%' is '%max%' but '%size%' detected" => "Največja dovoljena velikost datoteke '%value%' je '%max%' vendar zaznano je '%size%'",
    "Minimum expected size for file '%value%' is '%min%' but '%size%' detected" => "Najmanjša predvidena velikost datoteke '%value%' je '%min%' vendar zaznano je '%size%'",
    "File '%value%' is not readable or does not exist" => "Datoteke '%value%' ni mogoče brati ali pa ne obstaja",

    // Zend_Validate_File_Upload
    "File '%value%' exceeds the defined ini size" => "Datoteka '%value%' presega definirano ini velikost",
    "File '%value%' exceeds the defined form size" => "Datoteka '%value%' presega definirano velikost v formi",
    "File '%value%' was only partially uploaded" => "Datoteka '%value%' je bila dodana samo naložena",
    "File '%value%' was not uploaded" => "Datoteka '%value%' ni bila naložena",
    "No temporary directory was found for file '%value%'" => "Začasna mapa ni bila najdena za datoteko '%value%'",
    "File '%value%' can't be written" => "Datoteke '%value%' ni mogoče zapisati",
    "A PHP extension returned an error while uploading the file '%value%'" => "PHP razširitev je vrnila napako med nalaganjem datoteke '%value%'",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "Datoteka '%value%' ni bila nedovoljeno naložena. Gre za potencialni napad",
    "File '%value%' was not found" => "Datoteka '%value%' ni bila najdena",
    "Unknown error while uploading file '%value%'" => "Neznana napaka pri nalaganju datoteke '%value%'",

    // Zend_Validate_File_WordCount
    "Too much words, maximum '%max%' are allowed but '%count%' were counted" => "Preveč besed, največ '%max%' je dovoljenih vendar preštetih je bilo '%count%'",
    "Too less words, minimum '%min%' are expected but '%count%' were counted" => "Premalo besed, vsaj '%min%' je predvidenih vendar preštetih je bilo '%count%'",
    "File '%value%' is not readable or does not exist" => "Datoteke '%value%' ni mogoče brati ali pa ne obstaja",

    // Zend_Validate_Float
    "Invalid type given. String, integer or float expected" => "Podan nedovoljen tip. Predviden je niz, celo število ali plavajoče število",
    "'%value%' does not appear to be a float" => "'%value%' se zdi, da ni plavajoče število",

    // Zend_Validate_GreaterThan
    "'%value%' is not greater than '%min%'" => "'%value%' ni večje od '%min%'",

    // Zend_Validate_Hex
    "Invalid type given. String expected" => "Podan nedovoljen tip. Predviden je niz",
    "'%value%' has not only hexadecimal digit characters" => "'%value%' nima samo znake šestnajstiških števil",

    // Zend_Validate_Hostname
    "Invalid type given. String expected" => "Podan nedovoljen tip. Predviden je niz",
    "'%value%' appears to be an IP address, but IP addresses are not allowed" => "'%value%' se zdi, da je IP naslov, vendar IP naslovi niso dovoljeni",
    "'%value%' appears to be a DNS hostname but cannot match TLD against known list" => "'%value%' se zdi, da je DNS ime gostitelja vendar se ne ujema s seznamom znanih TLD",
    "'%value%' appears to be a DNS hostname but contains a dash in an invalid position" => "'%value%' se zdi, da je DNS ime gostitelja vendar vključuje pomišljaj na nedovoljenem mestu",
    "'%value%' appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "'%value%' se zdi, da je DNS ime gostitelja vendar se ne ujema s shemo imena gostitelja za TLD '%tld%'",
    "'%value%' appears to be a DNS hostname but cannot extract TLD part" => "'%value%' se zdi, da je DNS ime gostitelja vendar ni mogoče pa izločiti TLD dela",
    "'%value%' does not match the expected structure for a DNS hostname" => "'%value%' se ne ujema s predvideno strukturo za DNS ime gostitelja",
    "'%value%' does not appear to be a valid local network name" => "'%value%' se zdi, da ni veljavno ime lokalnega omrežja",
    "'%value%' appears to be a local network name but local network names are not allowed" => "'%value%' se zdi, da je ime lokalnega omrežja vendar imena lokalnih omrežij niso dovoljena",
    "'%value%' appears to be a DNS hostname but the given punycode notation cannot be decoded" => "'%value%' se zdi, da je ime DNS ime gostitelja vendar danega 'punycode' označevanja ni mogoče dekodirati",
    "'%value%' does not appear to be a valid URI hostname" => "'%value%' se zdi, da ni veljavno ime URI ime gostitelja",

    // Zend_Validate_Iban
    "Unknown country within the IBAN '%value%'" => "Neznana država v vrednosti IBAN '%value%'",
    "'%value%' has a false IBAN format" => "'%value%' ima napačen IBAN format",
    "'%value%' has failed the IBAN check" => "'%value%' ni uspelo IBAN preverjanja",

    // Zend_Validate_Identical
    "The two given tokens do not match" => "Dana žetona se ne ujemata",
    "No token was provided to match against" => "Žeton ni bil dan za ujemanje",

    // Zend_Validate_InArray
    "'%value%' was not found in the haystack" => "'Haystack' ne vsebuje vrednosti '%value%'",

    // Zend_Validate_Int
    "Invalid type given. String or integer expected" => "Podan nedovoljen tip. Predviden je niz ali celo število",
    "'%value%' does not appear to be an integer" => "'%value%' se zdi, da ni celo število",

    // Zend_Validate_Ip
    "Invalid type given. String expected" => "Podan neveljaven tip. Predviden je niz",
    "'%value%' does not appear to be a valid IP address" => "'%value%' se zdi, da ni veljaven IP naslov",

    // Zend_Validate_Isbn
    "Invalid type given. String or integer expected" => "Podan neveljaven tip. Predviden je niz ali celo število",
    "'%value%' is not a valid ISBN number" => "'%value%' ni veljavna ISBN številka",

    // Zend_Validate_LessThan
    "'%value%' is not less than '%max%'" => "'%value%' ni manjša kot '%max%'",

    // Zend_Validate_NotEmpty
    "Invalid type given. String, integer, float, boolean or array expected" => "Podan neveljaven tip. Predviden je niz, celo število, plavajoče število, logična vrednost ali polje",
    "Value is required and can't be empty" => "Vrednost je obvezna in ne sme biti prazna",

    // Zend_Validate_PostCode
    "Invalid type given. String or integer expected" => "Podan neveljaven tip. Predviden je niz ali celo število",
    "'%value%' does not appear to be a postal code" => "'%value%' se zdi, da ni poštna številka",

    // Zend_Validate_Regex
    "Invalid type given. String, integer or float expected" => "Podan neveljaven tip. Predviden je niz, celo število ali plavajoče število",
    "'%value%' does not match against pattern '%pattern%'" => "'%value%' se ne ujema z vzorcem '%pattern%'",
    "There was an internal error while using the pattern '%pattern%'" => "Prišlo je do notranje napake med uporabo vzorca '%pattern%'",

    // Zend_Validate_Sitemap_Changefreq
    "'%value%' is not a valid sitemap changefreq" => "'%value%' ni veljavna 'sitemap changefreq' vrednost",
    "Invalid type given. String expected" => "Podan neveljaven tip. Predviden je niz",

    // Zend_Validate_Sitemap_Lastmod
    "'%value%' is not a valid sitemap lastmod" => "'%value%' ni veljavna 'sitemap lastmod' vrednost",
    "Invalid type given. String expected" => "Podan neveljaven tip. Predviden je niz",

    // Zend_Validate_Sitemap_Loc
    "'%value%' is not a valid sitemap location" => "'%value%' ni veljavna 'sitemap location' vrednost",
    "Invalid type given. String expected" => "Podan neveljaven tip. Predviden je niz",

    // Zend_Validate_Sitemap_Priority
    "'%value%' is not a valid sitemap priority" => "'%value%' ni veljavna 'sitemap priority' vrednost",
    "Invalid type given. Numeric string, integer or float expected" => "Podan neveljaven tip. Predviden je numerični niz znakov, celo število ali plavajoče število",

    // Zend_Validate_StringLength
    "Invalid type given. String expected" => "Podan neveljaven tip. Predviden je niz",
    "'%value%' is less than %min% characters long" => "'%value%' je manjše od dolžine znakov %min%",
    "'%value%' is more than %max% characters long" => "'%value%' je več od dolžine znakov %max%",
);
