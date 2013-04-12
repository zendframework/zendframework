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
 * CS-Revision: 28.Mar.2013
 */
return array(
    // Zend_I18n_Validator_Alnum
    "Invalid type given. String, integer or float expected" => "Chybný typ. Byl očekáván řetězec, celé nebo desetinné číslo",
    "The input contains characters which are non alphabetic and no digits" => "Hodnota obsahuje i jiné znaky než písmena a číslice",
    "The input is an empty string" => "Hodnota je prázdný řetězec",

    // Zend_I18n_Validator_Alpha
    "Invalid type given. String expected" => "Chybný typ. Byl očekáván řetězec",
    "The input contains non alphabetic characters" => "Hodnota obsahuje i jiné znaky než písmena",
    "The input is an empty string" => "Hodnota je prázdný řetězec",

    // Zend_I18n_Validator_Float
    "Invalid type given. String, integer or float expected" => "Chybný typ. Byl očekáván řetězec, celé nebo desetinné číslo",
    "The input does not appear to be a float" => "Hodnota nevypadá jako desetinné číslo",

    // Zend_I18n_Validator_Int
    "Invalid type given. String or integer expected" => "Chybný typ. Byl očekáván řetězec nebo celé číslo",
    "The input does not appear to be an integer" => "Hodnota nevypadá jako celé číslo",

    // Zend_I18n_Validator_PostCode
    "Invalid type given. String or integer expected" => "Chybný typ. Byl očekáván řetězec nebo celé číslo",
    "The input does not appear to be a postal code" => "Hodnota nevypadá jako PSČ",
    "An exception has been raised while validating the input" => "Během kontroly hodnoty byla vyvolána výjimka",

    // Zend_Validator_Barcode
    "The input failed checksum validation" => "Hodnota má chybný kontrolní součet",
    "The input contains invalid characters" => "Hodnota obsahuje neplatné znaky",
    "The input should have a length of %length% characters" => "Hodnota by měla mít délku %length% znaků",
    "Invalid type given. String expected" => "Chybný typ. Byl očekáván řetězec",

    // Zend_Validator_Between
    "The input is not between '%min%' and '%max%', inclusively" => "Hodnota není mezi '%min%' a '%max%', včetně",
    "The input is not strictly between '%min%' and '%max%'" => "Hodnota není přesně mezi '%min%' a '%max%'",

    // Zend_Validator_Callback
    "The input is not valid" => "Hodnota není platná",
    "An exception has been raised within the callback" => "Během volání byla vyvolána výjimka",

    // Zend_Validator_CreditCard
    "The input seems to contain an invalid checksum" => "Hodnota obsahuje neplatný kontrolní součet",
    "The input must contain only digits" => "Hodnota musí obsahovat pouze číslice",
    "Invalid type given. String expected" => "Chybný typ. Byl očekáván řetězec",
    "The input contains an invalid amount of digits" => "Hodnota obsahuje neplatný počet číslic",
    "The input is not from an allowed institute" => "Hodnota není od povolené společnosti",
    "The input seems to be an invalid creditcard number" => "Hodnota není platné číslo kreditní karty",
    "An exception has been raised while validating the input" => "Během validace byla vyvolána výjimka",

    // Zend_Validator_Csrf
    "The form submitted did not originate from the expected site" => "Odeslaný formulář nepochází z předpokládané internetové stránky",

    // Zend_Validator_Date
    "Invalid type given. String, integer, array or DateTime expected" => "Chybný typ. Byl očekáván řetězec, číslo, pole nebo objekt DateTime",
    "The input does not appear to be a valid date" => "Hodnota nevypadá jako platné datum",
    "The input does not fit the date format '%format%'" => "Hodnota neodpovídá formátu data '%format%'",

    // Zend_Validator_DateStep
    "The input is not a valid step" => "Hodnota není platný krok",

    // Zend_Validator_Db_Abstract
    "No record matching the input was found" => "Nebyl nalezen žádný záznam odpovídající hodnotě",
    "A record matching the input was found" => "Byl nalezen záznam odpovídající hodnotě",

    // Zend_Validator_Digits
    "The input must contain only digits" => "Hodnota musí obsahovat pouze číslice",
    "The input is an empty string" => "Hodnota je prázdný řetězec",
    "Invalid type given. String, integer or float expected" => "Chybný typ. Byl očekáván řetězec, celé nebo desetinné číslo",

    // Zend_Validator_EmailAddress
    "Invalid type given. String expected" => "Chybný typ. Byl očekáván řetězec",
    "The input is not a valid email address. Use the basic format local-part@hostname" => "Hodnota není platná emailová adresa ve formátu local-part@hostname",
    "'%hostname%' is not a valid hostname for the email address" => "'%hostname%' není platné hostname pro emailovou adresu",
    "'%hostname%' does not appear to have any valid MX or A records for the email address" => "'%hostname%' neobsahuje platný MX záznam pro emailovou adresu",
    "'%hostname%' is not in a routable network segment. The email address should not be resolved from public network" => "'%hostname%' není v směrovatelném úseku sítě. Emailová adresa by neměla být požadována z veřejné sítě",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' nemůže být porovnán proti dot-atom formátu",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' nemůže být porovnán proti quoted-string formátu",
    "'%localPart%' is not a valid local part for the email address" => "'%localPart%' není platná 'local part' pro emailovou adresu",
    "The input exceeds the allowed length" => "Hodnota překročila povolenou délku",

    // Zend_Validator_Explode
    "Invalid type given." => "Chybný typ.",

    // Zend_Validator_File_Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "Příliš mnoho souborů. Maximum je '%max%', ale bylo zadáno '%count%'",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "Příliš málo souborů. Minimum je '%min%', ale byl zadáno jen '%count%'",

    // Zend_Validator_File_Crc32
    "File does not match the given crc32 hashes" => "Soubor neodpovídá zadanému crc32 hashi",
    "A crc32 hash could not be evaluated for the given file" => "Pro zadaný soubor nemohl být vypočítán crc32 hash",
    "File is not readable or does not exist" => "Soubor není čitelný nebo neexistuje",

    // Zend_Validator_File_ExcludeExtension
    "File has an incorrect extension" => "Soubor má nesprávnou příponu",
    "File is not readable or does not exist" => "Soubor není čitelný nebo neexistuje",

    // Zend_Validator_File_Exists
    "File does not exist" => "Soubor neexistuje",

    // Zend_Validator_File_Extension
    "File has an incorrect extension" => "Soubor má nesprávnou příponu",
    "File is not readable or does not exist" => "Soubor není čitelný nebo neexistuje",

    // Zend_Validator_File_FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "Součet velikostí všech souborů by měl být maximálně '%max%', ale je '%size%'",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "Součet velikostí všech souborů by měl být nejméně '%min%', ale je '%size%'",
    "One or more files can not be read" => "Jeden nebo více souborů není možné načíst",

    // Zend_Validator_File_Hash
    "File does not match the given hashes" => "Soubor neodpovídané danému hashi",
    "A hash could not be evaluated for the given file" => "Hash nemohl být pro daný soubor vypočítán",
    "File is not readable or does not exist" => "Soubor není čitelný nebo neexistuje",

    // Zend_Validator_File_ImageSize
    "Maximum allowed width for image should be '%maxwidth%' but '%width%' detected" => "Maximální šířka obrázku by měla být '%maxwidth%', ale je '%width%'",
    "Minimum expected width for image should be '%minwidth%' but '%width%' detected" => "Minimální šířka obrázku by měla být '%minwidth%', ale je '%width%'",
    "Maximum allowed height for image should be '%maxheight%' but '%height%' detected" => "Maximální výška obrázku by měla být '%maxheight%', ale je '%height%'",
    "Minimum expected height for image should be '%minheight%' but '%height%' detected" => "Minimální výška obrázku by měla být '%minheight%', ale je '%height%'",
    "The size of image could not be detected" => "Rozměry obrázku nebylo možné zjistit",
    "File is not readable or does not exist" => "Soubor není čitelný nebo neexistuje",

    // Zend_Validator_File_IsCompressed
    "File is not compressed, '%type%' detected" => "Soubor není komprimovaný, ale '%type%'",
    "The mimetype could not be detected from the file" => "Mimetyp souboru nebylo možné zjistit",
    "File is not readable or does not exist" => "Soubor není čitelný nebo neexistuje",

    // Zend_Validator_File_IsImage
    "File is no image, '%type%' detected" => "Soubor není obrázek, ale '%type%'",
    "The mimetype could not be detected from the file" => "Mimetyp souboru nebylo možné zjistit",
    "File is not readable or does not exist" => "Soubor není čitelný nebo neexistuje",

    // Zend_Validator_File_Md5
    "File does not match the given md5 hashes" => "Soubor neodpovídá danému md5 hashi",
    "An md5 hash could not be evaluated for the given file" => "md5 hash nemohl být pro daný soubor vypočítán",
    "File is not readable or does not exist" => "Soubor není čitelný nebo neexistuje",

    // Zend_Validator_File_MimeType
    "File has an incorrect mimetype of '%type%'" => "Soubor má neplatný mimetyp '%type%'",
    "The mimetype could not be detected from the file" => "Mimetyp souboru nebylo možné zjistit",
    "File is not readable or does not exist" => "Soubor není čitelný nebo neexistuje",

    // Zend_Validator_File_NotExists
    "File exists" => "Soubor již existuje",

    // Zend_Validator_File_Sha1
    "File does not match the given sha1 hashes" => "Soubor neodpovídá danému sha1 hashi",
    "A sha1 hash could not be evaluated for the given file" => "sha1 hash nemohl být pro daný soubor vypočítán",
    "File is not readable or does not exist" => "Soubor není čitelný nebo neexistuje",

    // Zend_Validator_File_Size
    "Maximum allowed size for file is '%max%' but '%size%' detected" => "Maximální velikost souboru by měla být '%max%', ale je '%size%'",
    "Minimum expected size for file is '%min%' but '%size%' detected" => "Minimální velikost souboru by měla být '%min%', ale je '%size%'",
    "File is not readable or does not exist" => "Soubor není čitelný nebo neexistuje",

    // Zend_Validator_File_Upload
    "File '%value%' exceeds the defined ini size" => "Soubor '%value%' překročil velikost definovanou v ini souboru",
    "File '%value%' exceeds the defined form size" => "Soubor '%value%' překročil velikost definovanou ve formuláři",
    "File '%value%' was only partially uploaded" => "Soubor '%value%' byl nahrán jen částečně",
    "File '%value%' was not uploaded" => "Soubor '%value%' nebyl nahrán",
    "No temporary directory was found for file '%value%'" => "Pro soubor '%value%' nebyl nalezen žádný dočasný adresář",
    "File '%value%' can't be written" => "Soubor '%value%' nemůže být zapsán",
    "A PHP extension returned an error while uploading the file '%value%'" => "Rozšíření PHP vrátilo chybu během nahrávání souboru '%value%'",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "Soubor '%value%' byl nedovoleně nahrán. Může se jednat o útok",
    "File '%value%' was not found" => "Soubor '%value%' nebyl nalezen",
    "Unknown error while uploading file '%value%'" => "Během nahrávání souboru '%value%' došlo k neznámé chybě",

    // Zend_Validator_File_UploadFile
    "File exceeds the defined ini size" => "Soubor překročil velikost definovanou v ini souboru",
    "File exceeds the defined form size" => "Soubor překročil velikost definovanou ve formuláře",
    "File was only partially uploaded" => "Soubor byl nahrán jen částečně",
    "File was not uploaded" => "Soubor nebyl nahrán",
    "No temporary directory was found for file" => "Pro soubor nebyl nalezen žádný dočasný adresář",
    "File can't be written" => "Soubor nemůže být zapsán",
    "A PHP extension returned an error while uploading the file" => "Rozšíření PHP vrátilo chybu během nahrávání souboru",
    "File was illegally uploaded. This could be a possible attack" => "Soubor byl nedovoleně nahrán. Může se jednat o útok",
    "File was not found" => "Soubor nebyl nalezen",
    "Unknown error while uploading file" => "Během nahrávání souboru došlo k neznámé chybě",

    // Zend_Validator_File_WordCount
    "Too much words, maximum '%max%' are allowed but '%count%' were counted" => "Příliš mnoho slov. Je jich dovoleno maximálně '%max%', ale bylo zadáno '%count%'",
    "Too few words, minimum '%min%' are expected but '%count%' were counted" => "Příliš málo slov. Musí jich být alespoň '%min%', ale bylo zadáno jen '%count%'",
    "File is not readable or does not exist" => "Soubor není čitelný nebo neexistuje",

    // Zend_Validator_GreaterThan
    "The input is not greater than '%min%'" => "Hodnota není větší než '%min%'",
    "The input is not greater or equal than '%min%'" => "Hodnota není větší nebo rovna '%min%'",

    // Zend_Validator_Hex
    "Invalid type given. String expected" => "Chybný typ. Byl očekáván řetězec",
    "The input contains non-hexadecimal characters" => "Hodnota neobsahuje jen znaky hexadecimálních čísel",

    // Zend_Validator_Hostname
    "The input appears to be a DNS hostname but the given punycode notation cannot be decoded" => "Hodnota vypadá jako DNS hostname ale zadanou punycode notaci není možné dekódovat",
    "Invalid type given. String expected" => "Chybný typ. Byl očekáván řetězec",
    "The input appears to be a DNS hostname but contains a dash in an invalid position" => "Hodnota vypadá jako hostname, ale obsahuje pomlčku na nedovoleném místě",
    "The input does not match the expected structure for a DNS hostname" => "Hodnota neodpovídá očekáváné struktuře hostname",
    "The input appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "Hodnota vypadá jako hostname, ale neodpovídá formátu hostname pro '%tld%'",
    "The input does not appear to be a valid local network name" => "Hodnota nevypadá jako platné síťové jméno",
    "The input does not appear to be a valid URI hostname" => "Hodnota nevypadá jako platný hostname URI",
    "The input appears to be an IP address, but IP addresses are not allowed" => "Hodnota vypadá jako IP adresa, ale ty nejsou dovoleny",
    "The input appears to be a local network name but local network names are not allowed" => "Hodnota vypadá jako hostname lokální sítě, ty ale nejsou povoleny",
    "The input appears to be a DNS hostname but cannot extract TLD part" => "Hodnota sice vypadá jako hostname, ale nemohu určit TLD",
    "The input appears to be a DNS hostname but cannot match TLD against known list" => "Hodnota vypadá jako hostname, ale nemohl být ověřen proti známým TLD",

    // Zend_Validator_Iban
    "Unknown country within the IBAN" => "Neznámý stát v IBAN",
    "Countries outside the Single Euro Payments Area (SEPA) are not supported" => "Státy mimo jednotný evropský platební prostor nejsou podporovány",
    "The input has a false IBAN format" => "Hodnota není platný formát IBAN",
    "The input has failed the IBAN check" => "Hodnota neprošlo kontrolou IBAN",

    // Zend_Validator_Identical
    "The two given tokens do not match" => "Zadané položky nejsou shodné",
    "No token was provided to match against" => "Nebyla zadána položka pro porovnání",

    // Zend_Validator_InArray
    "The input was not found in the haystack" => "Hodnota nebyla nalezena v seznamu",

    // Zend_Validator_Ip
    "Invalid type given. String expected" => "Chybný typ. Byl očekáván řetězec",
    "The input does not appear to be a valid IP address" => "Hodnota nevypadá jako platná IP adresa",

    // Zend_Validator_Isbn
    "Invalid type given. String or integer expected" => "Chybný typ. Byl očekáván řetězec nebo celé číslo",
    "The input is not a valid ISBN number" => "Hodnota není platné ISBN",

    // Zend_Validator_IsInstanceOf
    "The input is not an instance of '%className%'" => "Hodnota není instancí třídy '%className%'",

    // Zend_Validator_LessThan
    "The input is not less than '%max%'" => "Hodnota není menší než '%max%'",
    "The input is not less or equal than '%max%'" => "Hodnota není menší nebo rovna '%max%'",

    // Zend_Validator_NotEmpty
    "Value is required and can't be empty" => "Položka je povinná a nesmí být prázdná",
    "Invalid type given. String, integer, float, boolean or array expected" => "Chybný typ. Byl očekáván řetězec, celé nebo desetinné číslo, boolean nebo pole",

    // Zend_Validator_Regex
    "Invalid type given. String, integer or float expected" => "Chybný typ. Byl očekáván řetězec, celé nebo desetinné číslo",
    "The input does not match against pattern '%pattern%'" => "Hodnota neodpovídá šabloně '%pattern%'",
    "There was an internal error while using the pattern '%pattern%'" => "Během zpracování šablony '%pattern%' došlo k interní chybě",

    // Zend_Validator_Sitemap_Changefreq
    "The input is not a valid sitemap changefreq" => "Hodnota není platné 'changefreq' pro sitemapu",
    "Invalid type given. String expected" => "Chybný typ. Byl očekáván řetězec",

    // Zend_Validator_Sitemap_Lastmod
    "The input is not a valid sitemap lastmod" => "Hodnota není platné 'lastmod' pro sitemapu",
    "Invalid type given. String expected" => "Chybný typ. Byl očekáván řetězec",

    // Zend_Validator_Sitemap_Loc
    "The input is not a valid sitemap location" => "Hodnota není platná 'location' pro sitemapu",
    "Invalid type given. String expected" => "Chybný typ. Byl očekáván řetězec",

    // Zend_Validator_Sitemap_Priority
    "The input is not a valid sitemap priority" => "Hodnota není platná 'priority' pro sitemapu",
    "Invalid type given. Numeric string, integer or float expected" => "Chybný typ. Byl očekáván číselný řetězec, celé nebo desetinné číslo",

    // Zend_Validator_Step
    "Invalid value given. Scalar expected" => "Chybná hodnota. Byla očekávána skalární hodnota",
    "The input is not a valid step" => "Hodnota není platný krok",

    // Zend_Validator_StringLength
    "Invalid type given. String expected" => "Chybný typ. Byl očekáván řetězec",
    "The input is less than %min% characters long" => "Hodnota je kratší než %min% znaků",
    "The input is more than %max% characters long" => "Hodnota je delší než %max% znaků",

    // Zend_Validator_Uri
    "Invalid type given. String expected" => "Chybný typ. Byl očekáván řetězec",
    "The input does not appear to be a valid Uri" => "Hodnota nevypadá jako platná URI",
);
