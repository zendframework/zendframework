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
    "Invalid type given. String, integer or float expected" => "Ogiltig typ given. Sträng, heltal eller flyttal förväntat",
    "'%value%' contains characters which are non alphabetic and no digits" => "'%value%' innehåller tecken som är icke-alfabetiska och inga siffror",
    "'%value%' is an empty string" => "'%value%' är en tom sträng",

    // Zend_Validate_Alpha
    "Invalid type given. String expected" => "Ogiltig typ given. Sträng förväntad",
    "'%value%' contains non alphabetic characters" => "'%value%' innehåller icke-alfabetiska tecken",
    "'%value%' is an empty string" => "'%value%' är en tom sträng",

    // Zend_Validate_Barcode
    "'%value%' failed checksum validation" => "Kontrollsummans validering för '%value%' misslyckades",
    "'%value%' contains invalid characters" => "'%value%' innehåller ogiltiga tecken",
    "'%value%' should have a length of %length% characters" => "'%value%' bör vara %length% tecken långt",
    "Invalid type given. String expected" => "Ogiltig typ given. Sträng förväntad",

    // Zend_Validate_Between
    "'%value%' is not between '%min%' and '%max%', inclusively" => "'%value%' är inte mellan '%min%' och '%max%', inklusive",
    "'%value%' is not strictly between '%min%' and '%max%'" => "'%value%' är inte strikt mellan '%min%' och '%max%'",

    // Zend_Validate_Callback
    "'%value%' is not valid" => "'%value%' är inte giltigt",
    "An exception has been raised within the callback" => "Ett undantag har utlösts inom callbacken",

    // Zend_Validate_Ccnum
    "'%value%' must contain between 13 and 19 digits" => "'%value%' måste innehålla mellan 13 och 19 siffror",
    "Luhn algorithm (mod-10 checksum) failed on '%value%'" => "Luhn-algorithmen (mod-10 kontrollsumma) misslyckades för '%value%'",

    // Zend_Validate_CreditCard
    "'%value%' seems to contain an invalid checksum" => "'%value%' tycks innehålla en ogiltig kontrollsumma",
    "'%value%' must contain only digits" => "'%value%' får endast innehålla siffror",
    "Invalid type given. String expected" => "Ogiltig typ given. Sträng förväntades",
    "'%value%' contains an invalid amount of digits" => "'%value%' innehåller ett ogiltigt antal siffror",
    "'%value%' is not from an allowed institute" => "'%value%' är inte från ett tillåtet institut",
    "'%value%' seems to be an invalid creditcard number" => "'%value%' tycks vara ett ogiltigt kreditkortsnummer",
    "An exception has been raised while validating '%value%'" => "Ett undantag har utlösts under valideringen av '%value%'",

    // Zend_Validate_Date
    "Invalid type given. String, integer, array or Zend_Date expected" => "Ogiltig typ given. Sträng, heltal, array eller Zend_Date förväntat",
    "'%value%' does not appear to be a valid date" => "'%value%' tycks inte vara ett giltigt datum",
    "'%value%' does not fit the date format '%format%'" => "'%value%' passar inte datumformatet '%format%'",

    // Zend_Validate_Db_Abstract
    "No record matching '%value%' was found" => "Ingen post som matchar '%value%' kunde hittas",
    "A record matching '%value%' was found" => "En post som matchar '%value%' hittades",

    // Zend_Validate_Digits
    "Invalid type given. String, integer or float expected" => "Ogiltig typ given. Sträng, heltal eller flyttal förväntat",
    "'%value%' must contain only digits" => "'%value%' får enbart innehålla siffror",
    "'%value%' is an empty string" => "'%value%' är en tom sträng",

    // Zend_Validate_EmailAddress
    "Invalid type given. String expected" => "Ogiltig typ given. Sträng förväntades",
    "'%value%' is not a valid email address in the basic format local-part@hostname" => "'%value%' är inte en giltig e-postadress på standardformatet lokal-del@värdnamn",
    "'%hostname%' is not a valid hostname for email address '%value%'" => "'%hostname%' är inte ett giltigt värdnamn för en e-postadress '%value%'",
    "'%hostname%' does not appear to have a valid MX record for the email address '%value%'" => "'%hostname%' tycks inte ha en giltig MX-post för e-postadressen '%value%'",
    "'%hostname%' is not in a routable network segment. The email address '%value%' should not be resolved from public network" => "'%hostname%' är inte ett dirigerbart nätverkssegment. E-postadressen '%value%' bör inte lösas ut från det publika nätverket",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' kunde inte matchas mot dot-atom formatet",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' kan inte matchas mot quoted-string formatet",
    "'%localPart%' is not a valid local part for email address '%value%'" => "'%localPart%' är inte en giltig lokal del för e-postadressen '%value%'",
    "'%value%' exceeds the allowed length" => "'%value%' överskrider den tillåtna längden",

    // Zend_Validate_File_Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "För många filer, maximalt '%max%' är tillåtna men '%count%' är angivna",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "För få filer, minst '%min%' förväntas men '%count%' är angivna",

    // Zend_Validate_File_Crc32
    "File '%value%' does not match the given crc32 hashes" => "Filen '%value%' matchar inte de givna crc32-hasharna",
    "A crc32 hash could not be evaluated for the given file" => "En crc32-hash kunde inte utvärderas för den angivna filen",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' är inte läsbar eller existerar inte",

    // Zend_Validate_File_ExcludeExtension
    "File '%value%' has a false extension" => "Filen '%value%' har en felaktig filändelse",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' är inte läsbar eller existerar inte",

    // Zend_Validate_File_ExcludeMimeType
    "File '%value%' has a false mimetype of '%type%'" => "Filen '%value%' har mime-typen '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Mime-typen för filen '%value%' kunde inte detekteras",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' är inte läsbar eller existerar inte",

    // Zend_Validate_File_Exists
    "File '%value%' does not exist" => "Filen '%value%' existerar inte",

    // Zend_Validate_File_Extension
    "File '%value%' has a false extension" => "Filen '%value%' har en felaktig filändelse",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' är inte läsbar eller existerar inte",

    // Zend_Validate_File_FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "Alla filer bör totalt ha en maximal storlek av '%max%' men '%size%' upptäcktes",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "Alla filer bör totalt ha en minimal storlek av '%min%' men '%size%' upptäcktes",
    "One or more files can not be read" => "En eller flera filer kunde inte läsas",

    // Zend_Validate_File_Hash
    "File '%value%' does not match the given hashes" => "Filen '%value%' matchar inte de givna hasharna",
    "A hash could not be evaluated for the given file" => "En hash kunde inte utvärderas för den angivna filen",
    "File '%value%' is not readable or does not exist" => "Filebn '%value%' är inte läsbar eller existerar inte",

    // Zend_Validate_File_ImageSize
    "Maximum allowed width for image '%value%' should be '%maxwidth%' but '%width%' detected" => "Maximal tillåten bredd för bilden '%value%' är '%maxwidth%' men '%width%' upptäcktes",
    "Minimum expected width for image '%value%' should be '%minwidth%' but '%width%' detected" => "Minimal förväntad bredd för bilden '%value%' är '%minwidth%' men '%width%' upptäcktes",
    "Maximum allowed height for image '%value%' should be '%maxheight%' but '%height%' detected" => "Maximal tillåten höjd för '%value%' är '%maxheight%' men '%height%' upptäcktes",
    "Minimum expected height for image '%value%' should be '%minheight%' but '%height%' detected" => "Minimal förväntad höjd för bilden '%value%' är '%minheight%' men '%height%' upptäcktes",
    "The size of image '%value%' could not be detected" => "Storleken på bilden '%value%' kunde inte detekteras",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' är inte läsbar eller existerar inte",

    // Zend_Validate_File_IsCompressed
    "File '%value%' is not compressed, '%type%' detected" => "Filen '%value%' är inte komprimerad, '%type%' upptäcktes",
    "The mimetype of file '%value%' could not be detected" => "Mime-typen för filen '%value%' kunde inte detekteras",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' är inte läsbar eller existerar inte",

    // Zend_Validate_File_IsImage
    "File '%value%' is no image, '%type%' detected" => "Filen '%value%' är ingen bild, '%type%' upptäcktes",
    "The mimetype of file '%value%' could not be detected" => "Mime-typen för filen '%value%' kunde inte detekteras",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' är inte läsbar eller existerar inte",

    // Zend_Validate_File_Md5
    "File '%value%' does not match the given md5 hashes" => "Filen '%value%' matchar inte de givna md5-hasharna",
    "A md5 hash could not be evaluated for the given file" => "En md5-hash kunde inte utvärderas för den angivna filen",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' är inte läsbar eller existerar inte",

    // Zend_Validate_File_MimeType
    "File '%value%' has a false mimetype of '%type%'" => "Filen '%value%' har en felaktig mime-typ av '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Mime-typen för filen '%value%' kunde inte detekteras",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' är inte läsbar eller existerar inte",

    // Zend_Validate_File_NotExists
    "File '%value%' exists" => "Filen '%value%' existerar",

    // Zend_Validate_File_Sha1
    "File '%value%' does not match the given sha1 hashes" => "Filen '%value%' matchar inte de givna sha1-hasharna",
    "A sha1 hash could not be evaluated for the given file" => "En sha1-hash kunde inte utvärderas för den angivna filen",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' är inte läsbar eller existerar inte",

    // Zend_Validate_File_Size
    "Maximum allowed size for file '%value%' is '%max%' but '%size%' detected" => "Maximal tillåten storlek för filen '%value%' är '%max%' men '%size%' upptäcktes",
    "Minimum expected size for file '%value%' is '%min%' but '%size%' detected" => "Minimal förväntad storlek för filen '%value%' är '%min%' men '%size%' upptäcktes",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' är inte läsbar eller existerar inte",

    // Zend_Validate_File_Upload
    "File '%value%' exceeds the defined ini size" => "Filen '%value%' överskrider den definerade ini-storleken",
    "File '%value%' exceeds the defined form size" => "Filen '%value%' överskrider den definerade formulär-storleken",
    "File '%value%' was only partially uploaded" => "Filen '%value%' blev enbart delvis uppladdad",
    "File '%value%' was not uploaded" => "Filen '%value%' laddades inte upp",
    "No temporary directory was found for file '%value%'" => "Ingen temporär folder hittades för filen '%value%'",
    "File '%value%' can't be written" => "Filen '%value%' kan inte skrivas",
    "A PHP extension returned an error while uploading the file '%value%'" => "Ett PHP-tillägg returnerade ett fel när filen '%value%' laddades upp",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "Filen '%value%' laddades upp olagligt. Det här kan vara en möjlig attack",
    "File '%value%' was not found" => "Filen '%value%' hittades inte",
    "Unknown error while uploading file '%value%'" => "Okänt fel när filen '%value%' laddades upp",

    // Zend_Validate_File_WordCount
    "Too much words, maximum '%max%' are allowed but '%count%' were counted" => "För många ord, maximalt '%max%' är tillåtna men '%count%' räknades",
    "Too less words, minimum '%min%' are expected but '%count%' were counted" => "För få ord, minimalt '%min%' förväntas men '%count%' räknades",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' är inte läsbar eller existerar inte",

    // Zend_Validate_Float
    "Invalid type given. String, integer or float expected" => "Ogiltig typ given. Sträng, heltal eller flyttal förväntat",
    "'%value%' does not appear to be a float" => "'%value%' tycks inte vara ett flyttal",

    // Zend_Validate_GreaterThan
    "'%value%' is not greater than '%min%'" => "'%value%' är inte större än '%min%'",

    // Zend_Validate_Hex
    "Invalid type given. String expected" => "Ogiltig typ given. Sträng förväntad",
    "'%value%' has not only hexadecimal digit characters" => "'%value%' har inte enbart hexadecimala siffertecken",

    // Zend_Validate_Hostname
    "Invalid type given. String expected" => "Ogiltig typ given. Sträng förväntad",
    "'%value%' appears to be an IP address, but IP addresses are not allowed" => "'%value%' tycks vara en IP-adress, men IP-adresses är inte tillåtna",
    "'%value%' appears to be a DNS hostname but cannot match TLD against known list" => "'%value%' tycks vara ett DNS-värdnamn men kan inte matcha TLDn mot listan med kända",
    "'%value%' appears to be a DNS hostname but contains a dash in an invalid position" => "'%value%' tycks vara ett DNS-värdnamn men innehåller ett bindestreck på en ogiltig position",
    "'%value%' appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "'%value%' tycks vara ett DNS-värdnamn men kan inte matcha mot värdnamnsschemat för TLDn '%tld%'",
    "'%value%' appears to be a DNS hostname but cannot extract TLD part" => "'%value%' tycks vara ett DNS-värdnamn men kan inte extrahera TLD-delen",
    "'%value%' does not match the expected structure for a DNS hostname" => "'%value%' tycks inte matcha den förväntade strukturen för ett DNS-värdnamn",
    "'%value%' does not appear to be a valid local network name" => "'%value%' tycks inte vara ett giltigt lokalt nätverksnamn",
    "'%value%' appears to be a local network name but local network names are not allowed" => "'%value%' tycks vara ett lokalt nätverksnamn men lokala nätverksnamn är inte tillåtna",
    "'%value%' appears to be a DNS hostname but the given punycode notation cannot be decoded" => "'%value%' tycks vara ett DNS-värdnamn men den angivna punycode-notationen kan inte avkodas",
    "'%value%' does not appear to be a valid URI hostname" => "'%value%' tycks inte vara ett giltigt URI-värdnamn",

    // Zend_Validate_Iban
    "Unknown country within the IBAN '%value%'" => "Okänd land i IBAN-numret '%value%'",
    "'%value%' has a false IBAN format" => "'%value%' har ett felaktigt IBAN-format",
    "'%value%' has failed the IBAN check" => "IBAN-kontrollen har misslyckats för '%value%'",

    // Zend_Validate_Identical
    "The two given tokens do not match" => "De två angivna symbolerna matchar inte varandra",
    "No token was provided to match against" => "Ingen symbol angavs att matcha mot",

    // Zend_Validate_InArray
    "'%value%' was not found in the haystack" => "'%value%' hittades inte i höstacken",

    // Zend_Validate_Int
    "Invalid type given. String or integer expected" => "Ogiltig typ given. Sträng eller heltal förväntat",
    "'%value%' does not appear to be an integer" => "'%value%' tycks inte vara ett heltal",

    // Zend_Validate_Ip
    "Invalid type given. String expected" => "Ogiltig typ given. Sträng förväntad",
    "'%value%' does not appear to be a valid IP address" => "'%value%' tycks inte vara en giltig IP-adress",

    // Zend_Validate_Isbn
    "Invalid type given. String or integer expected" => "Ogiltig typ given. Sträng eller heltal förväntat",
    "'%value%' is not a valid ISBN number" => "'%value%' är inte ett giltigt ISBN-nummer",

    // Zend_Validate_LessThan
    "'%value%' is not less than '%max%'" => "'%value%' är inte lägre än '%max%'",

    // Zend_Validate_NotEmpty
    "Invalid type given. String, integer, float, boolean or array expected" => "Ogiltig typ given. Sträng, heltal, flyttal, boolean eller array förväntad",
    "Value is required and can't be empty" => "Värdet krävs och kan inte vara tomt",

    // Zend_Validate_PostCode
    "Invalid type given. String or integer expected" => "Ogiltig typ given. Sträng eller heltal förväntat",
    "'%value%' does not appear to be a postal code" => "'%value%' tycks inte vara ett postnummer",

    // Zend_Validate_Regex
    "Invalid type given. String, integer or float expected" => "Ogiltig typ given. Sträng, heltal eller flyttal förväntat",
    "'%value%' does not match against pattern '%pattern%'" => "'%value%' matchar inte mönstret '%pattern%'",
    "There was an internal error while using the pattern '%pattern%'" => "Det uppstod ett internt serverfel när mönstret '%pattern%' användes",

    // Zend_Validate_Sitemap_Changefreq
    "'%value%' is not a valid sitemap changefreq" => "'%value%' är inte en giltig 'changefreq' för sajtkartor",
    "Invalid type given. String expected" => "Ogiltig typ given. Sträng förväntad",

    // Zend_Validate_Sitemap_Lastmod
    "'%value%' is not a valid sitemap lastmod" => "'%value%' är inte en giltig 'lastmod' för sajtkartor",
    "Invalid type given. String expected" => "Ogiltig typ given. Sträng förväntad",

    // Zend_Validate_Sitemap_Loc
    "'%value%' is not a valid sitemap location" => "'%value%' är inte en giltig 'location' för sajtkartor",
    "Invalid type given. String expected" => "Ogiltig typ given. Sträng förväntad",

    // Zend_Validate_Sitemap_Priority
    "'%value%' is not a valid sitemap priority" => "'%value%' är inte en giltig 'priority' för sajtkartor",
    "Invalid type given. Numeric string, integer or float expected" => "Ogiltig typ given. Sträng, heltal eller flyttal förväntat",

    // Zend_Validate_StringLength
    "Invalid type given. String expected" => "Ogiltig typ given. Sträng förväntad",
    "'%value%' is less than %min% characters long" => "'%value%' är mindre än %min% tecken lång",
    "'%value%' is more than %max% characters long" => "'%value%' är mer än %max% tecken lång",
);
