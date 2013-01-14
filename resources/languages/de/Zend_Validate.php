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
    "Invalid type given. String, integer or float expected" => "Ungültiger Typ angegeben. String, Integer oder Float erwartet",
    "The inputcontains characters which are non alphabetic and no digits" => "Der Wert enthält Zeichen welche keine Buchstaben und keine Ziffern sind",
    "The inputis an empty string" => "Der Wert ist ein leerer String",

    // Zend_Validate_Alpha
    "Invalid type given. String expected" => "Ungültiger Typ angegeben. String erwartet",
    "The inputcontains non alphabetic characters" => "Der Wert enthält Zeichen welche keine Buchstaben sind",
    "The inputis an empty string" => "Der Wert ist ein leerer String",

    // Zend_Validate_Barcode
    "The inputfailed checksum validation" => "Der Wert hat die Prüfung der Checksumme nicht bestanden",
    "The inputcontains invalid characters" => "Der Wert enthält ungültige Zeichen",
    "The inputshould have a length of %length% characters" => "Der Wert sollte eine Länge von %length% Zeichen haben",
    "Invalid type given. String expected" => "Ungültiger Typ angegeben. String erwartet",

    // Zend_Validate_Between
    "The inputis not between '%min%' and '%max%', inclusively" => "Der Wert ist nicht zwischen '%min%' und '%max%', inklusive diesen Werten",
    "The inputis not strictly between '%min%' and '%max%'" => "Der Wert ist nicht strikt zwischen '%min%' und '%max%'",

    // Zend_Validate_Callback
    "The inputis not valid" => "Der Wert ist nicht gültig",
    "An exception has been raised within the callback" => "Eine Exception wurde im Callback geworfen",

    // Zend_Validate_Ccnum
    "The inputmust contain between 13 and 19 digits" => "Der Wert muss zwischen 13 und 19 Ziffern enthalten",
    "Luhn algorithm (mod-10 checksum) failed on '%value%'" => "Der Luhn Algorithmus (Mod-10 Checksumme) ist auf '%value%' fehlgeschlagen",

    // Zend_Validate_CreditCard
    "The inputseems to contain an invalid checksum" => "Der Wert scheint eine ungültige Prüfsumme zu enthalten",
    "The inputmust contain only digits" => "Der Wert darf nur Ziffern enthalten",
    "Invalid type given. String expected" => "Ungültiger Typ angegeben. String erwartet",
    "The inputcontains an invalid amount of digits" => "Der Wert enthält eine ungültige Anzahl an Ziffern",
    "The inputis not from an allowed institute" => "Der Wert ist nicht von einem der erlaubten Institute",
    "The inputseems to be an invalid creditcard number" => "Der Wert scheint eine ungültige Kreditkarten-Nummer zu sein",
    "An exception has been raised while validating '%value%'" => "Eine Exception wurde wärend der Prüfung von '%value%' geworfen",

    // Zend_Validate_Date
    "Invalid type given. String, integer, array or Zend_Date expected" => "Ungültiger Typ angegeben. String, Integer, Array oder Zend_Date erwartet",
    "The input does not appear to be a valid date" => "Der Wert scheint kein gültiges Datum zu sein",
    "The input does not fit the date format '%format%'" => "Der Wert passt nicht in das angegebene Datumsformat '%format%'",

    // Zend_Validate_Db_Abstract
    "No record matching '%value%' was found" => "Es wurde kein Eintrag gefunden der '%value%' entspricht",
    "A record matching '%value%' was found" => "Ein Eintrag der '%value%' entspricht wurde gefunden",

    // Zend_Validate_Digits
    "Invalid type given. String, integer or float expected" => "Ungültiger Typ angegeben. String, Integer oder Float erwartet",
    "The inputmust contain only digits" => "Der Wert darf nur Ziffern enthalten",
    "The inputis an empty string" => "Der Wert ist ein leerer String",

    // Zend_Validate_EmailAddress
    "Invalid type given. String expected" => "Ungültiger Typ angegeben. String erwartet",
    "The inputis not a valid email address in the basic format local-part@hostname" => "Der Wert ist keine gültige Emailadresse im Basisformat local-part@hostname",
    "'%hostname%' is not a valid hostname for email address '%value%'" => "'%hostname%' ist kein gültiger Hostname für die Emailadresse '%value%'",
    "'%hostname%' does not appear to have a valid MX record for the email address '%value%'" => "'%hostname%' scheint keinen gültigen MX Eintrag für die Emailadresse '%value%' zu haben",
    "'%hostname%' is not in a routable network segment. The email address '%value%' should not be resolved from public network" => "'%hostname%' ist in keinem routebaren Netzwerksegment. Die Emailadresse '%value%' sollte nicht vom öffentlichen Netz aus aufgelöst werden",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' passt nicht auf das dot-atom Format",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' passt nicht auf das quoted-string Format",
    "'%localPart%' is not a valid local part for email address '%value%'" => "'%localPart%' ist kein gültiger lokaler Teil für die Emailadresse '%value%'",
    "The inputexceeds the allowed length" => "Der Wert ist länger als erlaubt",

    // Zend_Validate_File_Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "Zu viele Dateien. Maximal '%max%' sind erlaubt aber '%count%' wurden angegeben",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "Zu wenige Dateien. Minimal '%min%' wurden erwartet aber nur '%count%' wurden angegeben",

    // Zend_Validate_File_Crc32
    "File '%value%' does not match the given crc32 hashes" => "Die Datei '%value%' passt nicht auf die angegebenen Crc32 Hashes",
    "A crc32 hash could not be evaluated for the given file" => "Für die angegebene Datei konnte kein Crc32 Hash evaluiert werden",
    "File '%value%' is not readable or does not exist" => "Die Datei '%value%' konnte nicht gelesen werden oder existiert nicht",

    // Zend_Validate_File_ExcludeExtension
    "File '%value%' has a false extension" => "Die Datei '%value%' hat eine falsche Erweiterung",
    "File '%value%' is not readable or does not exist" => "Die Datei '%value%' konnte nicht gelesen werden oder existiert nicht",

    // Zend_Validate_File_ExcludeMimeType
    "File '%value%' has a false mimetype of '%type%'" => "Die Datei '%value%' hat einen falschen Mimetyp von '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Der Mimetyp der Datei '%value%' konnte nicht erkannt werden",
    "File '%value%' is not readable or does not exist" => "Die Datei '%value%' konnte nicht gelesen werden oder existiert nicht",

    // Zend_Validate_File_Exists
    "File '%value%' does not exist" => "Die Datei '%value%' existiert nicht",

    // Zend_Validate_File_Extension
    "File '%value%' has a false extension" => "Die Datei '%value%' hat eine falsche Erweiterung",
    "File '%value%' is not readable or does not exist" => "Die Datei '%value%' konnte nicht gelesen werden oder existiert nicht",

    // Zend_Validate_File_FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "Alle Dateien sollten in Summe eine maximale Größe von '%max%' haben, aber es wurde '%size%' erkannt",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "Alle Dateien sollten in Summe eine minimale Größe von '%min%' haben, aber es wurde '%size%' erkannt",
    "One or more files can not be read" => "Ein oder mehrere Dateien konnten nicht gelesen werden",

    // Zend_Validate_File_Hash
    "File '%value%' does not match the given hashes" => "Die Datei '%value%' passt nicht auf die angegebenen Hashes",
    "A hash could not be evaluated for the given file" => "Für die angegebene Datei konnte kein Hash evaluiert werden",
    "File '%value%' is not readable or does not exist" => "Die Datei '%value%' konnte nicht gelesen werden oder existiert nicht",

    // Zend_Validate_File_ImageSize
    "Maximum allowed width for image '%value%' should be '%maxwidth%' but '%width%' detected" => "Die maximal erlaubte Breite für das Bild '%value%' ist '%maxwidth%', aber es wurde '%width%' erkannt",
    "Minimum expected width for image '%value%' should be '%minwidth%' but '%width%' detected" => "Die minimal erlaubte Breite für das Bild '%value%' ist '%minwidth%', aber es wurde '%width%' erkannt",
    "Maximum allowed height for image '%value%' should be '%maxheight%' but '%height%' detected" => "Die maximal erlaubte Höhe für das Bild '%value%' ist '%maxheight%', aber es wurde '%height%' erkannt",
    "Minimum expected height for image '%value%' should be '%minheight%' but '%height%' detected" => "Die minimal erlaubte Höhe für das Bild '%value%' ist '%minheight%', aber es wurde '%height%' erkannt",
    "The size of image '%value%' could not be detected" => "Die Größe des Bildes '%value%' konnte nicht erkannt werden",
    "File '%value%' is not readable or does not exist" => "Die Datei '%value%' konnte nicht gelesen werden oder existiert nicht",

    // Zend_Validate_File_IsCompressed
    "File '%value%' is not compressed, '%type%' detected" => "Die Datei '%value%' ist nicht komprimiert. Es wurde '%type%' erkannt",
    "The mimetype of file '%value%' could not be detected" => "Der Mimetyp der Datei '%value%' konnte nicht erkannt werden",
    "File '%value%' is not readable or does not exist" => "Die Datei '%value%' konnte nicht gelesen werden oder existiert nicht",

    // Zend_Validate_File_IsImage
    "File '%value%' is no image, '%type%' detected" => "Die Datei '%value%' ist kein Bild. Es wurde '%type%' erkannt",
    "The mimetype of file '%value%' could not be detected" => "Der Mimetyp der Datei '%value%' konnte nicht erkannt werden",
    "File '%value%' is not readable or does not exist" => "Die Datei '%value%' konnte nicht gelesen werden oder existiert nicht",

    // Zend_Validate_File_Md5
    "File '%value%' does not match the given md5 hashes" => "Die Datei '%value%' passt nicht auf die angegebenen Md5 Hashes",
    "A md5 hash could not be evaluated for the given file" => "Für die angegebene Datei konnte kein Md5 Hash evaluiert werden",
    "File '%value%' is not readable or does not exist" => "Die Datei '%value%' konnte nicht gelesen werden oder existiert nicht",

    // Zend_Validate_File_MimeType
    "File '%value%' has a false mimetype of '%type%'" => "Die Datei '%value%' hat einen falschen Mimetyp von '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Der Mimetyp der Datei '%value%' konnte nicht erkannt werden",
    "File '%value%' is not readable or does not exist" => "Die Datei '%value%' konnte nicht gelesen werden oder existiert nicht",

    // Zend_Validate_File_NotExists
    "File '%value%' exists" => "Die Datei '%value%' existiert bereits",

    // Zend_Validate_File_Sha1
    "File '%value%' does not match the given sha1 hashes" => "Die Datei '%value%' passt nicht auf die angegebenen Sha1 Hashes",
    "A sha1 hash could not be evaluated for the given file" => "Für die angegebene Datei konnte kein Sha1 Hash evaluiert werden",
    "File '%value%' is not readable or does not exist" => "Die Datei '%value%' konnte nicht gelesen werden oder existiert nicht",

    // Zend_Validate_File_Size
    "Maximum allowed size for file '%value%' is '%max%' but '%size%' detected" => "Die maximal erlaubte Größe für die Datei '%value%' ist '%max%', aber es wurde '%size%' entdeckt",
    "Minimum expected size for file '%value%' is '%min%' but '%size%' detected" => "Die mindestens erwartete Größe für die Datei '%value%' ist '%min%', aber es wurde '%size%' entdeckt",
    "File '%value%' is not readable or does not exist" => "Die Datei '%value%' konnte nicht gelesen werden oder existiert nicht",

    // Zend_Validate_File_Upload
    "File '%value%' exceeds the defined ini size" => "Die Datei '%value%' übersteigt die definierte Größe in der Konfiguration",
    "File '%value%' exceeds the defined form size" => "Die Datei '%value%' übersteigt die definierte Größe des Formulars",
    "File '%value%' was only partially uploaded" => "Die Datei '%value%' wurde nur teilweise hochgeladen",
    "File '%value%' was not uploaded" => "Die Datei '%value%' wurde nicht hochgeladen",
    "No temporary directory was found for file '%value%'" => "Für die Datei '%value%' wurde kein temporäres Verzeichnis gefunden",
    "File '%value%' can't be written" => "Die Datei '%value%' konnte nicht geschrieben werden",
    "A PHP extension returned an error while uploading the file '%value%'" => "Eine PHP Erweiterung retournierte einen Fehler wärend die Datei '%value%' hochgeladen wurde",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "Die Datei '%value%' wurde illegal hochgeladen. Dies könnte eine mögliche Attacke sein",
    "File '%value%' was not found" => "Die Datei '%value%' wurde nicht gefunden",
    "Unknown error while uploading file '%value%'" => "Ein unbekannter Fehler ist aufgetreten wärend die Datei '%value%' hochgeladen wurde",

    // Zend_Validate_File_WordCount
    "Too much words, maximum '%max%' are allowed but '%count%' were counted" => "Zu viele Wörter. Maximal '%max%' sind erlaubt, aber '%count%' wurden gezählt",
    "Too less words, minimum '%min%' are expected but '%count%' were counted" => "Zu wenige Wörter. Mindestens '%min%' wurden erwartet, aber '%count%' wurden gezählt",
    "File '%value%' is not readable or does not exist" => "Die Datei '%value%' konnte nicht gelesen werden oder existiert nicht",

    // Zend_Validate_Float
    "Invalid type given. String, integer or float expected" => "Ungültiger Typ angegeben. String, Integer oder Float erwartet",
    "The input does not appear to be a float" => "Der Wert scheint kein Float zu sein",

    // Zend_Validate_GreaterThan
    "The inputis not greater than '%min%'" => "Der Wert ist nicht größer als '%min%'",

    // Zend_Validate_Hex
    "Invalid type given. String expected" => "Ungültiger Typ angegeben. String erwartet",
    "The inputhas not only hexadecimal digit characters" => "Der Wert enthält nicht nur hexadezimale Ziffern",

    // Zend_Validate_Hostname
    "Invalid type given. String expected" => "Ungültiger Typ angegeben. String erwartet",
    "The inputappears to be an IP address, but IP addresses are not allowed" => "Der Wert scheint eine IP Adresse zu sein, aber IP Adressen sind nicht erlaubt",
    "The inputappears to be a DNS hostname but cannot match TLD against known list" => "Der Wert scheint ein DNS Hostname zu sein, aber die TLD wurde in der bekannten Liste nicht gefunden",
    "The inputappears to be a DNS hostname but contains a dash in an invalid position" => "Der Wert scheint ein DNS Hostname zu sein, enthält aber einen Bindestrich an einer ungültigen Position",
    "The inputappears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "Der Wert scheint ein DNS Hostname zu sein, passt aber nicht in das Hostname Schema für die TLD '%tld%'",
    "The inputappears to be a DNS hostname but cannot extract TLD part" => "Der Wert scheint ein DNS Hostname zu sein, aber der TLD Teil konnte nicht extrahiert werden",
    "The input does not match the expected structure for a DNS hostname" => "Der Wert passt nicht in die erwartete Struktur für einen DNS Hostname",
    "The input does not appear to be a valid local network name" => "Der Wert scheint kein gültiger lokaler Netzerkname zu sein",
    "The inputappears to be a local network name but local network names are not allowed" => "Der Wert scheint ein lokaler Netzwerkname zu sein, aber lokale Netzwerknamen sind nicht erlaubt",
    "The inputappears to be a DNS hostname but the given punycode notation cannot be decoded" => "Der Wert scheint ein DNS Hostname zu sein, aber die angegebene Punycode Schreibweise konnte nicht dekodiert werden",
    "The input does not appear to be a valid URI hostname" => "Der Wert scheint kein gültiger URI Hostname zu sein",

    // Zend_Validate_Iban
    "Unknown country within the IBAN '%value%'" => "Unbekanntes Land in der IBAN '%value%'",
    "The inputhas a false IBAN format" => "Der Wert enthält ein falsches IBAN Format",
    "The inputhas failed the IBAN check" => "Die IBAN Prüfung ist für '%value%' fehlgeschlagen",

    // Zend_Validate_Identical
    "The two given tokens do not match" => "Die zwei angegebenen Token stimmen nicht überein",
    "No token was provided to match against" => "Es wurde kein Token angegeben gegen den geprüft werden kann",

    // Zend_Validate_InArray
    "The inputwas not found in the haystack" => "Der Wert wurde im Haystack nicht gefunden",

    // Zend_Validate_Int
    "Invalid type given. String or integer expected" => "Ungültiger Typ angegeben. String oder Integer erwartet",
    "The input does not appear to be an integer" => "Der Wert scheint kein Integer zu sein",

    // Zend_Validate_Ip
    "Invalid type given. String expected" => "Ungültiger Typ angegeben. String erwartet",
    "The input does not appear to be a valid IP address" => "Der Wert scheint keine gültige IP Adresse zu sein",

    // Zend_Validate_Isbn
    "Invalid type given. String or integer expected" => "Ungültiger Typ angegeben. String oder Integer erwartet",
    "The inputis not a valid ISBN number" => "Der Wert ist keine gültige ISBN Nummer",

    // Zend_Validate_LessThan
    "The inputis not less than '%max%'" => "Der Wert ist nicht weniger als '%max%'",

    // Zend_Validate_NotEmpty
    "Invalid type given. String, integer, float, boolean or array expected" => "Ungültiger Typ angegeben. String, Integer, Float, Boolean oder Array erwartet",
    "Value is required and can't be empty" => "Es wird ein Wert benötigt. Dieser darf nicht leer sein",

    // Zend_Validate_PostCode
    "Invalid type given. String or integer expected" => "Ungültiger Typ angegeben. String oder Integer erwartet",
    "The input does not appear to be a postal code" => "Der Wert scheint keine gültige Postleitzahl zu sein",

    // Zend_Validate_Regex
    "Invalid type given. String, integer or float expected" => "Ungültiger Typ angegeben. String, Integer oder Float erwartet",
    "The input does not match against pattern '%pattern%'" => "Der Wert scheint nicht auf das Pattern '%pattern%' zu passen",
    "There was an internal error while using the pattern '%pattern%'" => "Es gab einen internen Fehler bei der Verwendung des Patterns '%pattern%'",

    // Zend_Validate_Sitemap_Changefreq
    "The inputis not a valid sitemap changefreq" => "Der Wert ist keine gültige Changefreq für Sitemap",
    "Invalid type given. String expected" => "Ungültiger Typ angegeben. String erwartet",

    // Zend_Validate_Sitemap_Lastmod
    "The inputis not a valid sitemap lastmod" => "Der Wert ist keine gültige Lastmod für Sitemap",
    "Invalid type given. String expected" => "Ungültiger Typ angegeben. String erwartet",

    // Zend_Validate_Sitemap_Loc
    "The inputis not a valid sitemap location" => "Der Wert ist keine gültige Location für Sitemap",
    "Invalid type given. String expected" => "Ungültiger Typ angegeben. String erwartet",

    // Zend_Validate_Sitemap_Priority
    "The inputis not a valid sitemap priority" => "Der Wert ist keine gültige Priority für Sitemap",
    "Invalid type given. Numeric string, integer or float expected" => "Ungültiger Typ angegeben. Nummerischer String, Integer oder Float erwartet",

    // Zend_Validate_StringLength
    "Invalid type given. String expected" => "Ungültiger Typ angegeben. String erwartet",
    "The inputis less than %min% characters long" => "Der Wert ist weniger als %min% Zeichen lang",
    "The inputis more than %max% characters long" => "Der Wert ist mehr als %max% Zeichen lang",
);
