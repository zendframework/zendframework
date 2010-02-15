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
 * @package    Zend_Translate
 * @subpackage Ressource
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id:$
 */

/**
 * EN-Revision: 20377
 */
return array(
    // Zend_Validate_Alnum
    "Invalid type given, value should be float, string, or integer" => "Ogilltig typ angiven, värdet måste vara av typen flytal, sträng eller heltal",
    "'%value%' contains characters which are non alphabetic and no digits" => "'%value%' inehåller tecken som inte är inom alfabetet eller siffror",
    "'%value%' is an empty string" => "'%value%' är en tom sträng",

    // Zend_Validate_Alpha
    "Invalid type given, value should be a string" => "Ogilltig typ angiven, värdet måste vara av typen sträng",
    "'%value%' contains non alphabetic characters" => "'%value%' innehåller tecken utanför alfabetet",
    "'%value%' is an empty string" => "'%value%' är en tom sträng",

    // Zend_Validate_Barcode
    "'%value%' failed checksum validation" => "'%value%' klarade inte kontrollen av kontrollsumman",
    "'%value%' contains invalid characters" => "'%value%' inehåller ogilltiga tecken",
    "'%value%' should have a length of %length% characters" => "'%value%' skall ha en längd av %length% tecken",
    "Invalid type given, value should be string" => "Ogilltig typ angiven, värdet måste vara av typen sträng",

    // Zend_Validate_Between
    "'%value%' is not between '%min%' and '%max%', inclusively" => "'%value%' är inte mellan '%min%' och '%max%', inklusivt",
    "'%value%' is not strictly between '%min%' and '%max%'" => "'%value%' är inte strikt mellan '%min%' och '%max%'",

    // Zend_Validate_Callback
    "'%value%' is not valid" => "'%value%' är inte gilltigt",
    "Failure within the callback, exception returned" => "Fel inom callbacken, ett undantag returnerades",

    // Zend_Validate_Ccnum
    "'%value%' must contain between 13 and 19 digits" => "'%value%' måste innehålla mellan 13 och 19 siffror",
    "Luhn algorithm (mod-10 checksum) failed on '%value%'" => "Luhn algoritmen (mod-10 kontrollsiffra) felaktig på '%value%'",

    // Zend_Validate_CreditCard
    "Luhn algorithm (mod-10 checksum) failed on '%value%'" => "Luhn algoritmen (mod-10 kontrollsiffra) felaktig på '%value%'",
    "'%value%' must contain only digits" => "'%value%' måste innehålla endast siffror",
    "Invalid type given, value should be a string" => "Ogilltig typ angiven, värdet måste vara av typen sträng",
    "'%value%' contains an invalid amount of digits" => "'%value%' innehåller ett ogilltigt antal siffror",
    "'%value%' is not from an allowed institute" => "'%value%' är inte från ett godkänt institut",
    "Validation of '%value%' has been failed by the service" => "Valideringen av '%value%' har ej godkännts av tjänsten",
    "The service returned a failure while validating '%value%'" => "Tjänsten returnerade ett fel under valideringen av '%value%'",

    // Zend_Validate_Date
    "Invalid type given, value should be string, integer, array or Zend_Date" => "Ogilltig typ angiven, värdet måste vara av typen sträng, heltal, array eller Zend_Date",
    "'%value%' does not appear to be a valid date" => "'%value%' verkar inte vara ett gilltigt datum",
    "'%value%' does not fit the date format '%format%'" => "'%value%' matchar inte datum formatet '%format%'",

    // Zend_Validate_Db_Abstract
    "No record matching %value% was found" => "Ingen rad matchande %value% hittades",
    "A record matching %value% was found" => "En rad matchande %value% hittades",

    // Zend_Validate_Digits
    "Invalid type given, value should be string, integer or float" => "Ogilltig typ angiven, värdet måste vara av typen sträng, heltal eller flyttal",
    "'%value%' contains not only digit characters" => "'%value%' innehåller inte bara siffror",
    "'%value%' is an empty string" => "'%value%' är en tom sträng",

    // Zend_Validate_EmailAddress
    "Invalid type given, value should be a string" => "Ogilltig typ angiven, värdet måste vara av typen sträng",
    "'%value%' is no valid email address in the basic format local-part@hostname" => "'%value%' är inte en epost adress i bas formatet lokal-del@servernamn",
    "'%hostname%' is no valid hostname for email address '%value%'" => "'%hostname%' är inte ett gilltigt server namn för epost adressen '%value%'",
    "'%hostname%' does not appear to have a valid MX record for the email address '%value%'" => "'%hostname%' ser inte ut att ha en gilltig MX pekare för epost adressen '%value%'",
    "'%hostname%' is not in a routable network segment. The email address '%value%' should not be resolved from public network." => "'%hostname%' är inte i ett nåbart nätverks segment. Epost adressen '%value%' kan inte nås från ett publikt nätverk.",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' kan inte matchas mot ett dot-atom format",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' kan inte matchas mot ett quoted-string format",
    "'%localPart%' is no valid local part for email address '%value%'" => "'%localPart%' är inte en gilltigt lokal del för epost adressen '%value%'",
    "'%value%' exceeds the allowed length" => "'%value%' överskrider den tillåtna längden",

    // Zend_Validate_File_Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "För många filer, maximum '%max%' är tillåtna men '%count%' har angivits",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "För få filer, minimum '%min%' förväntades men '%count%' har angivits",

    // Zend_Validate_File_Crc32
    "File '%value%' does not match the given crc32 hashes" => "Filen '%value%' matchar inte den angivna crc32 hashen",
    "A crc32 hash could not be evaluated for the given file" => "En crc32 hash kunde inte valideras för den angivna filen",
    "File '%value%' could not be found" => "Filen '%value%' kunde inte hittas",

    // Zend_Validate_File_ExcludeExtension
    "File '%value%' has a false extension" => "Filen '%value%' har en felaktig filtyps benämning",
    "File '%value%' could not be found" => "Filen '%value%' kunde inte hittas",

    // Zend_Validate_File_ExcludeMimeType
    "File '%value%' has a false mimetype of '%type%'" => "Filen '%value%' har en felaktig mime typ av typen '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Mime typen av fil '%value%' kunde inte identifieras",
    "File '%value%' can not be read" => "Filen '%value%' kunde inte läsas",

    // Zend_Validate_File_Exists
    "File '%value%' does not exist" => "Filen '%value%' existerar inte",

    // Zend_Validate_File_Extension
    "File '%value%' has a false extension" => "Filen '%value%' har en felaktig filtyps benämning",
    "File '%value%' could not be found" => "Filen '%value%' kunde inte hittas",

    // Zend_Validate_File_FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "Alla filerna sammantagna får ej överskrida '%max%' i storlek, men '%size%' beräknades",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "Alla filerna sammantagna får ej underskrida '%min%' i storlek, men '%size%' beräknades",
    "One or more files can not be read" => "En eller flera filer kunder inte läsas",

    // Zend_Validate_File_Hash
    "File '%value%' does not match the given hashes" => "Filen '%value%' matchar inte de givna hash värdena.",
    "A hash could not be evaluated for the given file" => "Ett hash värde kunde inte valideras för den angivna filen",
    "File '%value%' could not be found" => "Filen '%value%' kunde inte hittas",

    // Zend_Validate_File_ImageSize
    "Maximum allowed width for image '%value%' should be '%maxwidth%' but '%width%' detected" => "Maximum tillåtna bredden för bild '%value%' måste vara '%maxwidth%' men '%width%' beräknades",
    "Minimum expected width for image '%value%' should be '%minwidth%' but '%width%' detected" => "Minimum tillåtna bredden för bild '%value%' måste vara '%minwidth%' men '%width%' beräknades",
    "Maximum allowed height for image '%value%' should be '%maxheight%' but '%height%' detected" => "Maximum tillåtna höjden för bild '%value%' måste vara '%maxheight%' men '%height%' beräknades",
    "Minimum expected height for image '%value%' should be '%minheight%' but '%height%' detected" => "Minimum tillåtna höjden för bild '%value%' måste vara '%minheight%' men '%height%' beräknades",
    "The size of image '%value%' could not be detected" => "Storleken för bild '%value%' kunde inte beräknas",
    "File '%value%' can not be read" => "Filen '%value%' kunde inte läsas",

    // Zend_Validate_File_IsCompressed
    "File '%value%' is not compressed, '%type%' detected" => "Filen '%value%' är inte komprimerad, filtyp '%type%' identifierad",
    "The mimetype of file '%value%' could not be detected" => "Mimetypen för fil '%value%' kunde inte identifieras",
    "File '%value%' can not be read" => "Filen '%value%' kunde inte läsas",

    // Zend_Validate_File_IsImage
    "File '%value%' is no image, '%type%' detected" => "Filen '%value%' är ingen bild, filtyp '%type%' identifierad",
    "The mimetype of file '%value%' could not be detected" => "Mimetypen för fil '%value%' kunde inte identifieras",
    "File '%value%' can not be read" => "Filen '%value%' kunde inte läsas",

    // Zend_Validate_File_Md5
    "File '%value%' does not match the given md5 hashes" => "Filen '%value%' matchar inte dom angivna md5 hashvärderna",
    "A md5 hash could not be evaluated for the given file" => "Ett md5 hashvärde kunder inte beräkans för den angivna filen",
    "File '%value%' could not be found" => "Filen '%value%' kunde inte hittas",

    // Zend_Validate_File_MimeType
    "File '%value%' has a false mimetype of '%type%'" => "Filen '%value%' har en felaktig mimetyp; '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Mimetypen för fil '%value%' kunde inte identifieras",
    "File '%value%' can not be read" => "Filen '%value%' kunde inte läsas",

    // Zend_Validate_File_NotExists
    "File '%value%' exists" => "Filen '%value%' existerar",

    // Zend_Validate_File_Sha1
    "File '%value%' does not match the given sha1 hashes" => "Filen '%value%' matchar inte dom angivna sha1 hashvärderna",
    "A sha1 hash could not be evaluated for the given file" => "Ett sha1 hashvärde kunder inte beräkans för den angivna filen",
    "File '%value%' could not be found" => "Filen '%value%' kunde inte hittas",

    // Zend_Validate_File_Size
    "Maximum allowed size for file '%value%' is '%max%' but '%size%' detected" => "Maximum tillåtna storlek för fil '%value%' är '%max%' men '%size%' beräknades",
    "Minimum expected size for file '%value%' is '%min%' but '%size%' detected" => "Minimum tillåtna storlek för fil '%value%' är '%min%' men '%size%' beräknades",
    "File '%value%' could not be found" => "Filen '%value%' kunde inte hittas",

    // Zend_Validate_File_Upload
    "File '%value%' exceeds the defined ini size" => "Filen '%value%' överskrider den definerade ini storleken",
    "File '%value%' exceeds the defined form size" => "Filen '%value%' överskrider den definerade form storleken",
    "File '%value%' was only partially uploaded" => "Filen '%value%' var endast delviss uppladdad",
    "File '%value%' was not uploaded" => "Filen '%value%' blev inte uppladdad",
    "No temporary directory was found for file '%value%'" => "Ingen temporärfilskatalog hittades för fil '%value%'",
    "File '%value%' can't be written" => "Filen '%value%' kan inte skrivas",
    "A PHP extension returned an error while uploading the file '%value%'" => "En PHP tillägsmodul returnerade ett fel under uppladdningen av filen '%value%'",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "Filen '%value%' var en otillåten uppladning, detta kan vara en möjlig attack",
    "File '%value%' was not found" => "Filen '%value%' kunde inte hittas",
    "Unknown error while uploading file '%value%'" => "Okänt fel under uppladdning av fil '%value%'",

    // Zend_Validate_File_WordCount
    "Too much words, maximum '%max%' are allowed but '%count%' were counted" => "För många ord, ett maximum av '%max%' är tillåtna men '%count%' beräknades",
    "Too less words, minimum '%min%' are expected but '%count%' were counted" => "För få ord, ett minimum av '%min%' är tillåtna men '%count%' meräknades",
    "File '%value%' could not be found" => "Filen '%value%' kunde inte hittas",

    // Zend_Validate_Float
    "Invalid type given, value should be float, string, or integer" => "Ogilltig typ angiven, värdet måste vara av typen flyttal, sträng, eller heltal",
    "'%value%' does not appear to be a float" => "'%value%' verkar inte vara ett flyttal",

    // Zend_Validate_GreaterThan
    "'%value%' is not greater than '%min%'" => "'%value%' är inte större än '%min%'",

    // Zend_Validate_Hex
    "Invalid type given, value should be a string" => "Ogilltig typ angiven, värdet måste vara av typen sträng",
    "'%value%' has not only hexadecimal digit characters" => "'%value%' har inte endast hexadecimala tecken",

    // Zend_Validate_Hostname
    "Invalid type given, value should be a string" => "Ogilltig typ angiven, värdet måste vara av typen sträng",
    "'%value%' appears to be an IP address, but IP addresses are not allowed" => "'%value%' ser ut att vara en IP adress, men IP adresser är inte tillåtna",
    "'%value%' appears to be a DNS hostname but cannot match TLD against known list" => "'%value%' ser ut att vara ett DNS värdnamn men kan inte matcha TLD mot en känd lista",
    "'%value%' appears to be a DNS hostname but contains a dash in an invalid position" => "'%value%' ser ut att vara ett DNS värdnamn men innehåller ett bindestreck på en ogilltig position",
    "'%value%' appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "'%value%' ser ut att vara ett DNS värdnamn men matchar inte mot värdnamnsschemat för TLD '%tld%'",
    "'%value%' appears to be a DNS hostname but cannot extract TLD part" => "'%value%' ser ut att vara ett DNS värdnamn men kan ej extrahera TLD delen",
    "'%value%' does not match the expected structure for a DNS hostname" => "'%value%' matchar inte den förväntade strukturen på ett DNS värdnamn",
    "'%value%' does not appear to be a valid local network name" => "'%value%' ser inte ut att vara ett gilltigt lokalt nätverksnamn",
    "'%value%' appears to be a local network name but local network names are not allowed" => "'%value%' ser ut att vara ett gilltigt lokalt nätverksnamn men lokala nätverksnamn är inte tillåtna",
    "'%value%' appears to be a DNS hostname but the given punycode notation cannot be decoded" => "'%value%' ser ut att vara ett DNS värdnamn men den angivna punycode notationen kan ej avkodas",

    // Zend_Validate_Iban
    "Unknown country within the IBAN '%value%'" => "Ogilltigt land inom IBAN '%value%'",
    "'%value%' has a false IBAN format" => "'%value%' har ett felaktigt IBAN format",
    "'%value%' has failed the IBAN check" => "'%value%' klarade inte IBAN kontrollen",

    // Zend_Validate_Identical
    "The token '%token%' does not match the given token '%value%'" => "Identifieringskoden '%token%' matchar inte den angivna identifieringskoden '%value%'",
    "No token was provided to match against" => "Ingen identifieringskod angavs",

    // Zend_Validate_InArray
    "'%value%' was not found in the haystack" => "'%value%' hittades inte i höstacken",

    // Zend_Validate_Int
    "Invalid type given, value should be string or integer" => "Ogilltig typ angiven, värdet måste vara av typen sträng eller heltal",
    "'%value%' does not appear to be an integer" => "'%value%' tycks inte vara en heltal",

    // Zend_Validate_Ip
    "Invalid type given, value should be a string" => "Ogilltig typ angiven, värdet måste vara av typen sträng",
    "'%value%' does not appear to be a valid IP address" => "'%value%' ser inte ut att vara en gilltig IP adress",

    // Zend_Validate_Isbn
    "'%value%' is no valid ISBN number" => "'%value%' är ett ogilltigt ISBN nummer",

    // Zend_Validate_LessThan
    "'%value%' is not less than '%max%'" => "'%value%' är inte lägre än '%max%'",

    // Zend_Validate_NotEmpty
    "Invalid type given, value should be float, string, array, boolean or integer" => "Ogilltig typ angiven, värdet måste vara av typen flyttal, sträng, array, boolean eller heltal",
    "Value is required and can't be empty" => "Värdet måste fyllas i och kan inte lämnas tomt",

    // Zend_Validate_PostCode
    "Invalid type given, value should be string or integer" => "Ogilltig typ angiven, värdet måste vara av typen sträng eller heltal",
    "'%value%' does not appear to be an postal code" => "'%value%' ser inte ut att vara ett postnummer",

    // Zend_Validate_Regex
    "Invalid type given, value should be string, integer or float" => "Ogilltig typ angiven, värdet måste vara av typen sträng, heltal eller flyttal",
    "'%value%' does not match against pattern '%pattern%'" => "'%value%' matchar inte inom mönstret '%pattern%'",

    // Zend_Validate_Sitemap_Changefreq
    "'%value%' is no valid sitemap changefreq" => "'%value%' är inte en gilltig sitemap changefreq parameter",

    // Zend_Validate_Sitemap_Lastmod
    "'%value%' is no valid sitemap lastmod" => "'%value%' är inte en gilltig sitemap lastmod parameter",

    // Zend_Validate_Sitemap_Loc
    "'%value%' is no valid sitemap location" => "'%value%' är inte en gilltig sitemap location parameter",

    // Zend_Validate_Sitemap_Priority
    "'%value%' is no valid sitemap priority" => "'%value%' är inte en gilltig sitemap priority parameter",

    // Zend_Validate_StringLength
    "Invalid type given, value should be a string" => "Ogilltig typ angiven, värdet måste vara av typen sträng",
    "'%value%' is less than %min% characters long" => "'%value%' är kortare än %min% tecken",
    "'%value%' is more than %max% characters long" => "'%value%' is längre än %max% tecken",
);
