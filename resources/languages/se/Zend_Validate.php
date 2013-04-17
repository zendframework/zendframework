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
 * EN-Revision: 09.Sept.2012
 */
return array(
    // Zend_I18n_Validator_Alnum
    "Invalid type given. String, integer or float expected" => "Ogiltig typ given. Sträng, heltal eller flyttal förväntat",
    "The input contains characters which are non alphabetic and no digits" => "Indatan innehåller tecken som är icke-alfabetiska och inga siffror",
    "The input is an empty string" => "Indatan är en tom sträng",

    // Zend_I18n_Validator_Alpha
    "Invalid type given. String expected" => "Ogiltig typ given. Sträng förväntad",
    "The input contains non alphabetic characters" => "Indatan innehåller icke-alfabetiska tecken",
    "The input is an empty string" => "Indatan är en tom sträng",

    // Zend_I18n_Validator_Float
    "Invalid type given. String, integer or float expected" => "Ogiltig typ given. Sträng, heltal eller flyttal förväntat",
    "The input does not appear to be a float" => "Indatan tycks inte vara ett flyttal",

    // Zend_I18n_Validator_Int
    "Invalid type given. String or integer expected" => "Ogilitig typ given. Sträng eller heltal förväntat",
    "The input does not appear to be an integer" => "Indatan tycks inte vara ett heltal",

    // Zend_I18n_Validator_PostCode
    "Invalid type given. String or integer expected" => "Ogiltig typ given. Sträng eller heltal förväntat",
    "The input does not appear to be a postal code" => "Indatan tycks inte vara ett postnummer",
    "An exception has been raised while validating the input" => "Ett undantag har rests under valideringen av indatan",

    // Zend_Validator_Barcode
    "The input failed checksum validation" => "Valideringen av indatans kontrollsumma misslyckades",
    "The input contains invalid characters" => "Indatan innehåller ogiltiga tecken",
    "The input should have a length of %length% characters" => "Indatan bör vara %length% tecken lång",
    "Invalid type given. String expected" => "Ogiltig typ given. Sträng förväntad",

    // Zend_Validator_Between
    "The input is not between '%min%' and '%max%', inclusively" => "Indatan är inte mellan '%min%' och '%max%', inklusive",
    "The input is not strictly between '%min%' and '%max%'" => "Indatan är inte strikt mellan '%min%' och '%max%'",

    // Zend_Validator_Callback
    "The input is not valid" => "Indatan är inte giltig",
    "An exception has been raised within the callback" => "Ett undantag har rests inom callbacken",

    // Zend_Validator_CreditCard
    "The input seems to contain an invalid checksum" => "Indatan tycks innehålla en ogiltig checksumma",
    "The input must contain only digits" => "Indatan måste innehålla enbart siffror",
    "Invalid type given. String expected" => "Ogiltig typ given. Sträng förväntades",
    "The input contains an invalid amount of digits" => "Indatan innehåller ett ogiltigt antal siffror",
    "The input is not from an allowed institute" => "Indatan härstammar inte från ett giltigt institut",
    "The input seems to be an invalid creditcard number" => "Indatan tycks vara ett ogiltigt kortnummer",
    "An exception has been raised while validating the input" => "Ett undantag har rests under valideringen av indatan",

    // Zend_Validator_Csrf
    "The form submitted did not originate from the expected site" => "Det insända formuläret härstammade inte från den förväntade webbplatsen",

    // Zend_Validator_Date
    "Invalid type given. String, integer, array or DateTime expected" => "Ogiltig typ given. Sträng, heltal, array eller DateTime förväntad",
    "The input does not appear to be a valid date" => "Indatan tycks inte vara ett giltigt datum",
    "The input does not fit the date format '%format%'" => "Indatan passar inte datumformatet '%format%'",

    // Zend_Validator_DateStep
    "Invalid type given. String, integer, array or DateTime expected" => "Ogiltig typ given. Sträng, heltal, array eller DateTime förväntad",
    "The input does not appear to be a valid date" => "Indatan tycks inte vara ett giltigt datum",
    "The input is not a valid step" => "Indatan är inte ett giltigt steg",

    // Zend_Validator_Db_AbstractDb
    "No record matching the input was found" => "Ingen post som matchar indatan kunde hittas",
    "A record matching the input was found" => "En post som matchar indatan hittades",

    // Zend_Validator_Digits
    "'%value%' must contain only digits" => "Indatan får enbart innehålla siffror",
    "'%value%' is an empty string" => "Indatan är en tom sträng",
    "Invalid type given. String, integer or float expected" => "Ogiltig typ given. Sträng, heltal eller flyttal förväntat",

    // Zend_Validator_EmailAddress
    "Invalid type given. String expected" => "Ogiltig typ given. Sträng förväntades",
    "The input is not a valid email address. Use the basic format local-part@hostname" => "Indatan är inte en giltig e-postadress. Använd standardformatet lokal-del@värdnamn",
    "'%hostname%' is not a valid hostname for the email address" => "'%hostname%' är inte ett giltigt värdnamn för e-postadressen",
    "'%hostname%' does not appear to have any valid MX or A records for the email address" => "'%hostname%' tycks inte ha några giltiga MX- eller A-poster för e-postadressen",
    "'%hostname%' is not in a routable network segment. The email address should not be resolved from public network" => "'%hostname%' finns inte i ett dirigerbart nätverkssegment. E-postadressen bör inte lösas ut från det publika nätverket",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' kunde inte matchas mot dot-atom-formatet",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' kan inte matchas mot quoted-string-formatet",
    "'%localPart%' is not a valid local part for the email address" => "'%localPart%' är inte en giltig lokal del för e-postadressen",
    "The input exceeds the allowed length" => "Indatan överskrider den tillåtna längden",

    // Zend_Validator_Explode
    "Invalid type given. String expected" => "Ogiltig typ given. Sträng förväntad",

    // Zend_Validator_File_Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "För många filer, maximalt '%max%' är tillåtna men '%count%' är givna",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "För få filer, minst '%min%' förväntas men '%count%' är givna",

    // Zend_Validator_File_Crc32
    "File '%value%' does not match the given crc32 hashes" => "Filen '%value%' matchar inte de givna crc32-hasharna",
    "A crc32 hash could not be evaluated for the given file" => "En crc32-hash kunde inte utvärderas för den angivna filen",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' är inte läsbar eller existerar inte",

    // Zend_Validator_File_ExcludeExtension
    "File '%value%' has a false extension" => "Filen '%value%' har en felaktig filändelse",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' är inte läsbar eller existerar inte",

    // Zend_Validator_File_Exists
    "File '%value%' does not exist" => "Filen '%value%' existerar inte",

    // Zend_Validator_File_Extension
    "File '%value%' has a false extension" => "Filen '%value%' har en felaktig filändelse",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' är inte läsbar eller existerar inte",

    // Zend_Validator_File_FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "Alla filer bör totalt ha en maximal storlek av '%max%' men '%size%' upptäcktes",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "Alla filer bör totalt ha en minimal storlek av '%min%' men '%size%' upptäcktes",
    "One or more files can not be read" => "En eller flera filer kunde inte läsas",

    // Zend_Validator_File_Hash
    "File '%value%' does not match the given hashes" => "Filen '%value%' matchar inte de givna hasharna",
    "A hash could not be evaluated for the given file" => "En hash kunde inte utvärderas för den angivna filen",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' är inte läsbar eller existerar inte",

    // Zend_Validator_File_ImageSize
    "Maximum allowed width for image '%value%' should be '%maxwidth%' but '%width%' detected" => "Maximal tillåten bredd för bilden '%value%' är '%maxwidth%' men '%width%' upptäcktes",
    "Minimum expected width for image '%value%' should be '%minwidth%' but '%width%' detected" => "Minimal förväntad bredd för bilden '%value%' är '%minwidth%' men '%width%' upptäcktes",
    "Maximum allowed height for image '%value%' should be '%maxheight%' but '%height%' detected" => "Maximal tillåten höjd för '%value%' är '%maxheight%' men '%height%' upptäcktes",
    "Minimum expected height for image '%value%' should be '%minheight%' but '%height%' detected" => "Minimal förväntad höjd för bilden '%value%' är '%minheight%' men '%height%' upptäcktes",
    "The size of image '%value%' could not be detected" => "Storleken på bilden '%value%' kunde inte detekteras",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' är inte läsbar eller existerar inte",

    // Zend_Validator_File_IsCompressed
    "File '%value%' is not compressed, '%type%' detected" => "Filen '%value%' är inte komprimerad, '%type%' upptäcktes",
    "The mimetype of file '%value%' could not be detected" => "Mime-typen för filen '%value%' kunde inte detekteras",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' är inte läsbar eller existerar inte",

    // Zend_Validator_File_IsImage
    "File '%value%' is no image, '%type%' detected" => "Filen '%value%' är ingen bild, '%type%' upptäcktes",
    "The mimetype of file '%value%' could not be detected" => "Mime-typen för filen '%value%' kunde inte detekteras",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' är inte läsbar eller existerar inte",

    // Zend_Validator_File_Md5
    "File '%value%' does not match the given md5 hashes" => "Filen '%value%' matchar inte de givna md5-hasharna",
    "A md5 hash could not be evaluated for the given file" => "En md5-hash kunde inte utvärderas för den angivna filen",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' är inte läsbar eller existerar inte",

    // Zend_Validator_File_MimeType
    "File '%value%' has a false mimetype of '%type%'" => "Filen '%value%' har en felaktig mime-typ av '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Mime-typen för filen '%value%' kunde inte detekteras",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' är inte läsbar eller existerar inte",

    // Zend_Validator_File_NotExists
    "File '%value%' exists" => "Filen '%value%' existerar",

    // Zend_Validator_File_Sha1
    "File '%value%' does not match the given sha1 hashes" => "Filen '%value%' matchar inte de givna sha1-hasharna",
    "A sha1 hash could not be evaluated for the given file" => "En sha1-hash kunde inte utvärderas för den angivna filen",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' är inte läsbar eller existerar inte",

    // Zend_Validator_File_Size
    "Maximum allowed size for file '%value%' is '%max%' but '%size%' detected" => "Maximal tillåten storlek för filen '%value%' är '%max%' men '%size%' upptäcktes",
    "Minimum expected size for file '%value%' is '%min%' but '%size%' detected" => "Minimal förväntad storlek för filen '%value%' är '%min%' men '%size%' upptäcktes",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' är inte läsbar eller existerar inte",

    // Zend_Validator_File_Upload
    "File '%value%' exceeds the defined ini size" => "Filen '%value%' överskrider den definerade ini-storleken",
    "File '%value%' exceeds the defined form size" => "Filen '%value%' överskrider den definerade formulär-storleken",
    "File '%value%' was only partially uploaded" => "Filen '%value%' blev enbart delvis uppladdad",
    "File '%value%' was not uploaded" => "Filen '%value%' laddades inte upp",
    "No temporary directory was found for file '%value%'" => "Ingen temporär mapp hittades för filen '%value%'",
    "File '%value%' can't be written" => "Filen '%value%' kan inte skrivas",
    "A PHP extension returned an error while uploading the file '%value%'" => "Ett PHP-tillägg returnerade ett fel när filen '%value%' laddades upp",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "Filen '%value%' laddades upp olagligt. Det här kan vara en möjlig attack",
    "File '%value%' was not found" => "Filen '%value%' hittades inte",
    "Unknown error while uploading file '%value%'" => "Okänt fel när filen '%value%' laddades upp",

    // Zend_Validator_File_UploadFile
    "File exceeds the defined ini size" => "Filen överskrider den definerade ini-storleken",
    "File exceeds the defined form size" => "Filen överskrider den definerade formulär-storleken",
    "File was only partially uploaded" => "Filen blev enbart delvis uppladdad",
    "File was not uploaded" => "Filen laddades inte upp",
    "No temporary directory was found for file" => "Ingen temporär mapp hittades för filen",
    "File can't be written" => "Filen kan inte skrivas",
    "A PHP extension returned an error while uploading the file" => "Ett PHP-tillägg returnerade ett fel när filen laddades upp",
    "File was illegally uploaded. This could be a possible attack" => "Filen laddades upp olagligt. Det här kan vara en möjlig attack",
    "File was not found" => "Filen hittades inte",
    "Unknown error while uploading file" => "Okänt fel när filen laddades upp",

    // Zend_Validator_File_WordCount
    "Too much words, maximum '%max%' are allowed but '%count%' were counted" => "För många ord, maximalt '%max%' är tillåtna men '%count%' räknades",
    "Too few words, minimum '%min%' are expected but '%count%' were counted" => "För få ord, minimalt '%min%' förväntas men '%count%' räknades",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' är inte läsbar eller existerar inte",

    // Zend_Validator_GreaterThan
    "The input is not greater than '%min%'" => "Indatan är inte större än '%min%'",
    "The input is not greater or equal than '%min%'" => "Indatan är inte större eller lika med '%min%'",

    // Zend_Validator_Hex
    "Invalid type given. String expected" => "Ogiltig typ given. Sträng förväntad",
    "The input contains non-hexadecimal characters" => "Indatan innehåller icke-hexadecimala tecken",

    // Zend_Validator_Hostname
    "The input appears to be a DNS hostname but the given punycode notation cannot be decoded" => "Indatan tycks vara ett DNS-värdnamn men den givna punycode-notationen kan inte avkodas",
    "Invalid type given. String expected" => "Ogiltig typ given. Sträng förväntad",
    "The input appears to be a DNS hostname but contains a dash in an invalid position" => "Indatan tycks vara ett DNS-värdnamn men innehåller ett bindestreck på en ogiltig position",
    "The input does not match the expected structure for a DNS hostname" => "Indatan tycks inte matcha den förväntade strukturen för ett DNS-värdnamn",
    "The input appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "Indatan tycks vara ett DNS-värdnamn men kan inte matcha mot värdnamnsschemat för TLDn '%tld%'",
    "The input does not appear to be a valid local network name" => "Indatan tycks inte vara ett giltigt lokalt nätverksnamn",
    "The input does not appear to be a valid URI hostname" => "'%value%' tycks inte vara ett giltigt URI-värdnamn",
    "The input appears to be an IP address, but IP addresses are not allowed" => "Indatan tycks vara en IP-adress, men IP-adresses är inte tillåtna",
    "The input appears to be a local network name but local network names are not allowed" => "Indatan tycks vara ett lokalt nätverksnamn men lokala nätverksnamn är inte tillåtna",
    "'%value%' appears to be a DNS hostname but cannot extract TLD part" => "Indatan tycks vara ett DNS-värdnamn men kan inte extrahera TLD-delen",
    "'%value%' appears to be a DNS hostname but cannot match TLD against known list" => "Indatan tycks vara ett DNS-värdnamn men kan inte matcha TLDn mot listan med kända",

    // Zend_Validator_Iban
    "Unknown country within the IBAN" => "Okänd land i IBAN-numret",
    "Countries outside the Single Euro Payments Area (SEPA) are not supported" => "Länder utanför SEPA-området (Single Euro Payments Area) stöds ej",
    "The input has a false IBAN format" => "Indatan har ett felaktigt IBAN-format",
    "The input has failed the IBAN check" => "Indatan har ej klarat IBAN-kontrollen",

    // Zend_Validator_Identical
    "The two given tokens do not match" => "De två angivna symbolerna matchar inte varandra",
    "No token was provided to match against" => "Ingen symbol angavs att matcha mot",

    // Zend_Validator_InArray
    "The input was not found in the haystack" => "Indatan hittades inte i höstacken",

    // Zend_Validator_Ip
    "Invalid type given. String expected" => "Ogiltig typ given. Sträng förväntad",
    "The input does not appear to be a valid IP address" => "Indatan tycks inte vara en giltig IP-adress",

    // Zend_Validator_Isbn
    "Invalid type given. String or integer expected" => "Ogiltig typ given. Sträng eller heltal förväntat",
    "The input is not a valid ISBN number" => "Indatan är inte ett giltigt ISBN-nummer",

    // Zend_Validator_LessThan
    "The input is not less than '%max%'" => "Indatan är inte lägre än '%max%'",
    "The input is not less or equal than '%max%'" => "indatan är inte lägre eller lika med '%max%'",

    // Zend_Validator_NotEmpty
    "Value is required and can't be empty" => "Värdet krävs och kan inte vara tomt",
    "Invalid type given. String, integer, float, boolean or array expected" => "Ogiltig typ given. Sträng, heltal, flyttal, boolean eller array förväntad",

    // Zend_Validator_Regex
    "Invalid type given. String, integer or float expected" => "Ogiltig typ given. Sträng, heltal eller flyttal förväntat",
    "The input does not match against pattern '%pattern%'" => "Indatan matchar inte mönstret '%pattern%'",
    "There was an internal error while using the pattern '%pattern%'" => "Det uppstod ett internt fel när mönstret '%pattern%' användes",

    // Zend_Validator_Sitemap_Changefreq
    "The input is not a valid sitemap changefreq" => "Indatan är inte en giltig 'changefreq' för sajtkartor",
    "Invalid type given. String expected" => "Ogiltig typ given. Sträng förväntad",

    // Zend_Validator_Sitemap_Lastmod
    "The input is not a valid sitemap lastmod" => "Indatan är inte en giltig 'lastmod' för sajtkartor",
    "Invalid type given. String expected" => "Ogiltig typ given. Sträng förväntad",

    // Zend_Validator_Sitemap_Loc
    "The input is not a valid sitemap location" => "Indatan är inte en giltig 'location' för sajtkartor",
    "Invalid type given. String expected" => "Ogiltig typ given. Sträng förväntad",

    // Zend_Validator_Sitemap_Priority
    "'%value%' is not a valid sitemap priority" => "Indatan är inte en giltig 'priority' för sajtkartor",
    "Invalid type given. Numeric string, integer or float expected" => "Ogiltig typ given. Numerisk sträng, heltal eller flyttal förväntat",

    // Zend_Validator_Step
    "Invalid value given. Scalar expected" => "Ogiltigt värde givet. Skalär förväntad",
    "The input is not a valid step" => "Indatan är inte ett giltigt steg",

    // Zend_Validator_StringLength
    "Invalid type given. String expected" => "Ogiltig typ given. Sträng förväntad",
    "The input is less than %min% characters long" => "Indatan är mindre än %min% tecken lång",
    "The input is more than %max% characters long" => "Indatan är mer än %max% tecken lång",

    // Zend_Validator_Uri
    "Invalid type given. String expected" => "Ogiltig typ given. Sträng förväntad",
    "The input does not appear to be a valid Uri" => "Indatan tycks inte vara en giltig Uri",
);
