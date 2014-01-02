<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * NL-Revision: 09.Sept.2012
 */
return array(
    // Zend\I18n\Validator\Alnum
    "Invalid type given. String, integer or float expected" => "Ongeldig type opgegeven, waarde moet een float, string of integer zijn",
    "The input contains characters which are non alphabetic and no digits" => "De input bevat tekens welke alfabetisch, noch numeriek zijn",
    "The input is an empty string" => "De input is een lege string",

    // Zend\I18n\Validator\Alpha
    "Invalid type given. String expected" => "Ongeldig type opgegeven, waarde moet een string zijn",
    "The input contains non alphabetic characters" => "De input bevat tekens welke niet alfabetisch zijn",
    "The input is an empty string" => "De input is een lege string",

    // Zend\I18n\Validator\DateTime
    "Invalid type given. String expected" => "Ongeldig type opgegeven, waarde moet een string zijn",
    "The input does not appear to be a valid datetime" => "De input lijkt geen geldige datetime te zijn",

    // Zend\I18n\Validator\Float
    "Invalid type given. String, integer or float expected" => "Ongeldig type opgegeven, waarde moet een string, integer of een float zijn",
    "The input does not appear to be a float" => "De input lijkt geen geldige float te zijn",

    // Zend\I18n\Validator\Int
    "Invalid type given. String or integer expected" => "Ongeldig type opgegeven, waarde moet een string of een integer zijn",
    "The input does not appear to be an integer" => "De input lijkt geen geldige integer te zijn",

    // Zend\I18n\Validator\PhoneNumber
    "The input does not match a phone number format" => "De input komt niet overeen met een geldig telefoonnummer",
    "The country provided is currently unsupported" => "De opgegeven landcode is momenteel niet ondersteund",
    "Invalid type given. String expected" => "Ongeldig type opgegeven, waarde moet een string zijn",

    // Zend\I18n\Validator\PostCode
    "Invalid type given. String or integer expected" => "Ongeldig type opgegeven, waarde moet een string of integer zijn",
    "The input does not appear to be a postal code" => "De input lijkt geen geldige postcode te zijn",
    "An exception has been raised while validating the input" => "Er is een interne fout opgetreden tijdens het valideren van de input",

    // Zend\Validator\Barcode
    "The input failed checksum validation" => "De input slaagde niet in de checksum validatie",
    "The input contains invalid characters" => "De input bevat ongeldige tekens",
    "The input should have a length of %length% characters" => "De input moet een lengte hebben van %length% tekens",
    "Invalid type given. String expected" => "Ongeldig type opgegeven, waarde moet een string zijn",

    // Zend\Validator\Between
    "The input is not between '%min%' and '%max%', inclusively" => "De input is niet tussen of gelijk aan '%min%' en '%max%'",
    "The input is not strictly between '%min%' and '%max%'" => "De input is niet tussen '%min%' en '%max%'",

    // Zend\Validator\Callback
    "The input is not valid" => "De input is ongeldig",
    "An exception has been raised within the callback" => "Fout opgetreden in de callback, exceptie teruggegeven",

    // Zend\Validator\CreditCard
    "The input seems to contain an invalid checksum" => "De input bevat een ongeldige checksum",
    "The input must contain only digits" => "De input kan alleen cijfers bevatten",
    "Invalid type given. String expected" => "Ongeldig type opgegeven, waarde moet een string zijn",
    "The input contains an invalid amount of digits" => "De input bevat een ongeldige hoeveelheid cijfers",
    "The input is not from an allowed institute" => "De input is niet afkomstig van een toegestaan instituut",
    "The input seems to be an invalid credit card number" => "De input is een ongeldig credit card nummer",
    "An exception has been raised while validating the input" => "Er is een interne fout opgetreden tijdens het valideren van de input",

    // Zend\Validator\Csrf
    "The form submitted did not originate from the expected site" => "Het verzonden formulier kwam niet van de verwachte website",

    // Zend\Validator\Date
    "Invalid type given. String, integer, array or DateTime expected" => "Ongeldig type opgegeven, waarde moet een string, integer, array of DateTime zijn",
    "The input does not appear to be a valid date" => "De input lijkt geen geldige datum te zijn",
    "The input does not fit the date format '%format%'" => "De input past niet in het datumformaat '%format%'",

    // Zend\Validator\DateStep
    "The input is not a valid step" => "De input is geen geldige stap",

    // Zend\Validator\Db\AbstractDb
    "No record matching the input was found" => "Er kon geen overeenkomstig record gevonden worden",
    "A record matching the input was found" => "Een record wat overeenkomt met de input is gevonden",

    // Zend\Validator\Digits
    "The input must contain only digits" => "De input bevat niet enkel numerieke karakters",
    "The input is an empty string" => "De input is een lege string",
    "Invalid type given. String, integer or float expected" => "Ongeldig type opgegeven, waarde moet een string, integer of float zijn",

    // Zend\Validator\EmailAddress
    "Invalid type given. String expected" => "Ongeldig type opgegeven, waarde moet een string zijn",
    "The input is not a valid email address. Use the basic format local-part@hostname" => "De input is geen geldig e-mail adres in het basis formaat lokaal-gedeelte@hostname",
    "'%hostname%' is not a valid hostname for the email address" => "'%hostname%' is geen geldige hostnaam voor het e-mail adres",
    "'%hostname%' does not appear to have any valid MX or A records for the email address" => "'%hostname%' lijkt geen geldig MX of A record te hebben voor het e-mail adres",
    "'%hostname%' is not in a routable network segment. The email address should not be resolved from public network" => "'%hostname%' bevindt zich niet in een routeerbaar netwerk segment. Het e-mail adres zou niet naar mogen worden verwezen vanaf een publiek netwerk",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' kan niet worden gematched met het dot-atom formaat",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' kan niet worden gematched met het quoted-string formaat",
    "'%localPart%' is not a valid local part for the email address" => "'%localPart%' is geen geldig lokaal gedeelte voor het e-mail adres",
    "The input exceeds the allowed length" => "De input overschrijdt de toegestane lengte",

    // Zend\Validator\Explode
    "Invalid type given" => "Ongeldig type opgegeven",

    // Zend\Validator\File\Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "Te veel bestanden, maximaal '%max%' zijn toegestaan, maar '%count%' werd opgegeven",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "Te weinig bestanden, er worden er minimaal '%min%' verwacht, maar er waren er  '%count%' opgegeven",

    // Zend\Validator\File\Crc32
    "File does not match the given crc32 hashes" => "File matcht niet met de opgegeven crc32 hashes",
    "A crc32 hash could not be evaluated for the given file" => "Fout tijdens het genereren van een crc32 hash van het opgegeven bestand",
    "File is not readable or does not exist" => "Het bestand kon niet worden gevonden",

    // Zend\Validator\File\ExcludeExtension
    "File has an incorrect extension" => "Het bestand heeft een ongeldige extensie",
    "File is not readable or does not exist" => "Het bestand kon niet worden gevonden",

    // Zend\Validator\File\Exists
    "File does not exist" => "Bestand bestaat niet",

    // Zend\Validator\File\Extension
    "File has an incorrect extension" => "Het bestand heeft een ongeldige extensie",
    "File is not readable or does not exist" => "Het bestand kon niet worden gevonden",

    // Zend\Validator\File\FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "Alle bestanden tesamen hebben een maximale grootte van '%max%' maar '%size%' was gedetecteerd",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "Alle bestanden tesamen hebben een minimum grotte van '%min%' maar '%size%' was gedetecteerd",
    "One or more files can not be read" => "Eén of meerdere bestanden kon niet gelezen worden",

    // Zend\Validator\File\Hash
    "File does not match the given hashes" => "Het bestand matcht niet met de opgegeven hashes",
    "A hash could not be evaluated for the given file" => "Een hash kon niet worden gegenereerd voor het opgegeven bestand",
    "File is not readable or does not exist" => "Het bestand kon niet worden gevonden",

    // Zend\Validator\File\ImageSize
    "Maximum allowed width for image should be '%maxwidth%' but '%width%' detected" => "Maximum breedte voor afbeelding is '%maxwidth%' maar '%width%' werd gedetecteerd",
    "Minimum expected width for image should be '%minwidth%' but '%width%' detected" => "Minimum breedte voor afbeelding is '%minwidth%' maar '%width%' werd gedetecteerd",
    "Maximum allowed height for image should be '%maxheight%' but '%height%' detected" => "Maximum hoogte voor afbeelding is '%maxheight%' maar '%height%' werd gedetecteerd",
    "Minimum expected height for image should be '%minheight%' but '%height%' detected" => "Minimum hoogte voor afbeelding is '%minheight%' maar '%height%' werd gedetecteerd",
    "The size of image could not be detected" => "De grootte van afbeelding kon niet worden gedetecteerd",
    "File is not readable or does not exist" => "Het bestand kon niet worden gevonden",

    // Zend\Validator\File\IsCompressed
    "File is not compressed, '%type%' detected" => "Het bestand is niet gecomprimeerd, '%type%' gedetecteerd",
    "The mimetype could not be detected from the file" => "Het mimetype van bestand kon niet worden gedetecteerd",
    "File is not readable or does not exist" => "Het bestand kon niet worden gevonden",

    // Zend\Validator\File\IsImage
    "File is no image, '%type%' detected" => "Het bestand is geen afbeelding, '%type%' gedetecteerd",
    "The mimetype could not be detected from the file" => "Het mimetype van bestand kon niet worden gedetecteerd",
    "File is not readable or does not exist" => "Het bestand kon niet worden gevonden",

    // Zend\Validator\File\Md5
    "File does not match the given md5 hashes" => "Het bestand matcht niet met de opgegeven md5-hashes",
    "An md5 hash could not be evaluated for the given file" => "Een md5-hash kon niet gegenereerd worden voor het opgegeven bestand",
    "File is not readable or does not exist" => "Het bestand kon niet worden gevonden",

    // Zend\Validator\File\MimeType
    "File has an incorrect mimetype of '%type%'" => "Het bestand heeft een ongeldig mimetype: '%type%'",
    "The mimetype could not be detected from the file" => "Het mimetype van bestand kon niet worden gedetecteerd",
    "File is not readable or does not exist" => "Het bestand kon niet worden gevonden",

    // Zend\Validator\File\NotExists
    "File exists" => "Het bestand bestaat",

    // Zend\Validator\File\Sha1
    "File does not match the given sha1 hashes" => "Het bestand matcht niet met de opgegeven sha1-hashes",
    "A sha1 hash could not be evaluated for the given file" => "Een sha1-hash kon niet worden gegenereerd voor het opgegeven bestand",
    "File is not readable or does not exist" => "Het bestand kon niet worden gevonden",

    // Zend\Validator\File\Size
    "Maximum allowed size for file is '%max%' but '%size%' detected" => "Maximum grootte voor bestand is '%max%' maar '%size%' werd gedetecteerd",
    "Minimum expected size for file is '%min%' but '%size%' detected" => "Minimum grootte voor bestand is '%min%' maar '%size%' werd gedetecteerd",
    "File is not readable or does not exist" => "Het bestand kon niet worden gevonden",

    // Zend\Validator\File\Upload
    "File '%value%' exceeds the defined ini size" => "Het bestand '%value%' overschrijdt de ini grootte",
    "File '%value%' exceeds the defined form size" => "Het bestand '%value%' overschrijdt de formulier grootte",
    "File '%value%' was only partially uploaded" => "Het bestand '%value%' was slechts gedeeltelijk geüpload",
    "File '%value%' was not uploaded" => "Het bestand '%value%' was niet geüpload",
    "No temporary directory was found for file '%value%'" => "Geen tijdelijke map was gevonden voor bestand '%value%'",
    "File '%value%' can't be written" => "Het bestand '%value%' kan niet worden geschreven",
    "A PHP extension returned an error while uploading the file '%value%'" => "Een PHP-extensie gaf een foutmelding terug tijdens het uploaden van het bestand '%value%'",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "Het bestand '%value%' was illegaal geüpload. Dit kan een aanval zijn",
    "File '%value%' was not found" => "Het bestand '%value%' kon niet worden gevonden",
    "Unknown error while uploading file '%value%'" => "Er is een onbekende fout opgetreden tijdens het uploaden van '%value%'",

    // Zend\Validator\File\UploadFile
    "File exceeds the defined ini size" => "Het bestand overschrijdt de ini grootte",
    "File exceeds the defined form size" => "Het bestand overschrijdt de formulier grootte",
    "File was only partially uploaded" => "Het bestand was slechts gedeeltelijk geüpload",
    "File was not uploaded" => "Het bestand was niet geüpload",
    "No temporary directory was found for file" => "Geen tijdelijke map was gevonden voor bestand",
    "File can't be written" => "Het bestand kan niet worden geschreven",
    "A PHP extension returned an error while uploading the file" => "Een PHP-extensie gaf een foutmelding terug tijdens het uploaden van het bestand",
    "File was illegally uploaded. This could be a possible attack" => "Het bestand was illegaal geüpload. Dit kan een aanval zijn",
    "File was not found" => "Het bestand kon niet worden gevonden",
    "Unknown error while uploading file" => "Er is een onbekende fout opgetreden tijdens het uploaden van",

    // Zend\Validator\File\WordCount
    "Too many words, maximum '%max%' are allowed but '%count%' were counted" => "Te veel woorden, er is een maximum van '%max%', maar er waren '%count%' geteld",
    "Too few words, minimum '%min%' are expected but '%count%' were counted" => "Te weinig worden, er is een minimum van '%min%' maar er waren '%count%' getelc",
    "File is not readable or does not exist" => "Het bestand kon niet worden gevonden",

    // Zend\Validator\GreaterThan
    "The input is not greater than '%min%'" => "De input is niet groter dan '%min%'",
    "The input is not greater or equal than '%min%'" => "De input is niet groter dan of gelijk aan '%min%'",

    // Zend\Validator\Hex
    "Invalid type given. String expected" => "Ongeldig type gegeven, waarde moet een string zijn",
    "The input contains non-hexadecimal characters" => "De input bestaat niet enkel uit hexadecimale cijfers",

    // Zend\Validator\Hostname
    "The input appears to be a DNS hostname but the given punycode notation cannot be decoded" => "De input lijkt een geldige DNS hostnaam te zijn, maar de opgegeven punnycode notatie kan niet worden gedecodeerd",
    "Invalid type given. String expected" => "Ongeldig type gegeven, waarde moet een string zijn",
    "The input appears to be a DNS hostname but contains a dash in an invalid position" => "De input lijkt een DNS hostnaam te zijn, maar bevat een streep op een ongeldige plek",
    "The input does not match the expected structure for a DNS hostname" => "De input matcht niet met de verwachte structuur voor een DNS hostnaam",
    "The input appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "De input lijkt een DNS hostnaam te zijn, maar past niet in het hostnaam-schema voor TLD '%tld%'",
    "The input does not appear to be a valid local network name" => "De input lijkt geen geldige lokale netwerknaam te zijn",
    "The input does not appear to be a valid URI hostname" => "De input blijkt geen geldige URI hostnaam te bevatten",
    "The input appears to be an IP address, but IP addresses are not allowed" => "De input lijkt een IP adres te zijn, maar IP adressen zijn niet toegestaan",
    "The input appears to be a local network name but local network names are not allowed" => "De input lijkt een lokale netwerknaam te zijn, welke niet zijn toegestaan",
    "The input appears to be a DNS hostname but cannot extract TLD part" => "De input lijkt een DNS hostnaam te zijn, maar kan niet het TLD gedeelte bepalen",
    "The input appears to be a DNS hostname but cannot match TLD against known list" => "De input lijkt een DNS hostnaam te zijn, maar het TLD bestaat niet in de lijst met bekende TLD's",

    // Zend\Validator\Iban
    "Unknown country within the IBAN" => "Onbekend land in de IBAN",
    "Countries outside the Single Euro Payments Area (SEPA) are not supported" => "Landen buiten Single Euro Payments Area (SEPA) worden niet ondersteund",
    "The input has a false IBAN format" => "De input heeft een ongeldig IBAN formaat",
    "The input has failed the IBAN check" => "De input is geen geldige IBAN",

    // Zend\Validator\Identical
    "The two given tokens do not match" => "De twee tokens komen niet overeen",
    "No token was provided to match against" => "Er is geen token opgegeven om mee te matchen",

    // Zend\Validator\InArray
    "The input was not found in the haystack" => "De input kon niet worden gevonden in lijst met beschikbare waardes",

    // Zend\Validator\Ip
    "Invalid type given. String expected" => "Ongeldig type gegeven, waarde moet een string zijn",
    "The input does not appear to be a valid IP address" => "De input lijkt geen geldig IP adres te zijn",

    // Zend\Validator\IsInstanceOf
    "The input is not an instance of '%className%'" => "De input is geen instantie van '%className%'",

    // Zend\Validator\Isbn
    "Invalid type given. String or integer expected" => "Ongeldig type opgegeven, waarde moet een string of integer zijn",
    "The input is not a valid ISBN number" => "De input is geen geldig ISBN nummer",

    // Zend\Validator\LessThan
    "The input is not less than '%max%'" => "De input is niet minder dan '%max%'",
    "The input is not less or equal than '%max%'" => "De input is niet minder dan of gelijk aan '%max%'",

    // Zend\Validator\NotEmpty
    "Value is required and can't be empty" => "Waarde is vereist en kan niet leeg worden gelaten",
    "Invalid type given. String, integer, float, boolean or array expected" => "Ongeldig type opgegeven, waarde dient een float, string, array, boolean of integer te zijn",

    // Zend\Validator\Regex
    "Invalid type given. String, integer or float expected" => "Ongeldig type opgegeven, waarde dient een string, integer of float te zijn",
    "The input does not match against pattern '%pattern%'" => "De input matcht niet met het patroon '%pattern%'",
    "There was an internal error while using the pattern '%pattern%'" => "Er is een interne fout opgetreden tijdens het gebruik van het patroon '%pattern%'",

    // Zend\Validator\Sitemap\Changefreq
    "The input is not a valid sitemap changefreq" => "De input is geen geldige sitemap changefreq",
    "Invalid type given. String expected" => "Ongeldig type opgegeven, waarde dient een string te zijn",

    // Zend\Validator\Sitemap\Lastmod
    "The input is not a valid sitemap lastmod" => "De input is geen geldige sitemap lastmod",
    "Invalid type given. String expected" => "Ongeldig type opgegeven, waarde dient een string te zijn",

    // Zend\Validator\Sitemap\Loc
    "The input is not a valid sitemap location" => "De input is geen geldige sitemap locatie",
    "Invalid type given. String expected" => "Ongeldig type opgegeven, waarde dient een string te zijn",

    // Zend\Validator\Sitemap\Priority
    "The input is not a valid sitemap priority" => "De input is geen geldige sitemap prioriteit",
    "Invalid type given. Numeric string, integer or float expected" => "Ongeldig type opgegeven, waarde dient een numerieke string, integer of float te zijn",

    // Zend\Validator\Step
    "Invalid value given. Scalar expected" => "Ongeldige waarde opgegeven, waarde dient een scalar te zijn",
    "The input is not a valid step" => "De input is geen geldige stap",

    // Zend\Validator\StringLength
    "Invalid type given. String expected" => "Ongeldig type opgegeven, waarde dient een string te zijn",
    "The input is less than %min% characters long" => "De input is minder dan %min% tekens lang",
    "The input is more than %max% characters long" => "De input is meer dan %max% tekens lang",

    // Zend\Validator\Uri
    "Invalid type given. String expected" => "Ongeldig type opgegeven, waarde dient een string te zijn",
    "The input does not appear to be a valid Uri" => "De input blijkt geen geldige Uri te zijn",
);
