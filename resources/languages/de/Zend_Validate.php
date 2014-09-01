<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
/**
 * EN-Revision: 09.Sept.2012
 */
return array(
    // Zend\I18n\Validator\Alnum
    "Invalid type given. String, integer or float expected" => "Ungültiger Eingabewert. String, Integer oder Float erwartet",
    "The input contains characters which are non alphabetic and no digits" => "Der Eingabewert enthält nicht alphanumerische Zeichen",
    "The input is an empty string" => "Der Eingabewert ist leer",
    
    // Zend\I18n\Validator\Alpha
    "Invalid type given. String expected" => "Ungültiger Eingabewert. String erwartet",
    "The input contains non alphabetic characters" => "Der Eingabewert enthält nichtalphabetische Zeichen",
    "The input is an empty string" => "Der Eingabewert ist leer",
    
    // Zend\I18n\Validator\DateTime
    "Invalid type given. String expected" => "Ungültiger Eingabewert. String erwartet",
    "The input does not appear to be a valid datetime" => "Der Eingabewert scheint keine gültige Datums- und Zeitangabe zu sein",
    
    // Zend\I18n\Validator\Float
    "Invalid type given. String, integer or float expected" => "Ungültiger Eingabewert. String, Integer oder Float erwartet",
    "The input does not appear to be a float" => "Der Eingabewert scheint keine Gleitkommazahl zu sein",
    
    // Zend\I18n\Validator\Int
    "Invalid type given. String or integer expected" => "Ungültiger Eingabewert. String oder Integer erwartet",
    "The input does not appear to be an integer" => "Der Eingabewert ist keine ganze Zahl",
    
    // Zend\I18n\Validator\PhoneNumber
    "The input does not match a phone number format" => "Der Eingabewert ist keine gültige Telefonnummer",
    "The country provided is currently unsupported" => "Das gegebene Land wird zurzeit nicht unterstützt",
    "Invalid type given. String expected" => "Ungültiger Eingabewert. String erwartet",
    
    // Zend\I18n\Validator\PostCode
    "Invalid type given. String or integer expected" => "Ungültiger Eingabewert. String oder Integer erwartet",
    "The input does not appear to be a postal code" => "Der Eingabewert scheint keine gültige Postleitzahl zu sein",
    "An exception has been raised while validating the input" => "Ein Fehler ist während der Prüfung des Eingabewertes aufgetreten",
    
    // Zend\Validator\Barcode
    "The input failed checksum validation" => "Der Eingabewert hat die Prüfsumme nicht bestanden",
    "The input contains invalid characters" => "Der Eingabewert enthält ungültige Zeichen",
    "The input should have a length of %length% characters" => "Der Eingabewert sollte %length% Zeichen lang sein",
    "Invalid type given. String expected" => "Ungültiger Eingabewert. String erwartet",
    
    // Zend\Validator\Between
    "The input is not between '%min%' and '%max%', inclusively" => "Der Eingabewert ist nicht zwischen '%min%' und '%max%', inklusive",
    "The input is not strictly between '%min%' and '%max%'" => "Der Eingabewert ist nicht genau zwischen '%min%' und '%max%'",
    
    // Zend\Validator\Callback
    "The input is not valid" => "Der Eingabewert ist ungültig",
    "An exception has been raised within the callback" => "Ein Fehler ist während des Callbacks aufgetreten",
    
    // Zend\Validator\CreditCard
    "The input seems to contain an invalid checksum" => "Der Eingabewert enthält eine ungültige Prüfsumme",
    "The input must contain only digits" => "Der Eingabewert darf nur ganze Zahlen enthalten",
    "Invalid type given. String expected" => "Ungültiger Eingabewert. String erwartet",
    "The input contains an invalid amount of digits" => "Der Eingabewert enthält eine ungültige Anzahl an Zahlen",
    "The input is not from an allowed institute" => "Der Eingabewert ist von keinem erlaubtem Kreditinstitut",
    "The input seems to be an invalid creditcard number" => "Der Eingabewert scheint eine ungültige Kreditkartennummer zu sein",
    "An exception has been raised while validating the input" => "Ein Fehler ist während der Prüfung des Eingabewertes aufgetreten",
    
    // Zend\Validator\Csrf
    "The form submitted did not originate from the expected site" => "Der Ursprung des abgesendeten Formulars konnte nicht bestätigt werden",
    
    // Zend\Validator\Date
    "Invalid type given. String, integer, array or DateTime expected" => "Ungültiger Eingabewert. String, Integer, Array oder Datums- und Zeitangabe erwartet",
    "The input does not appear to be a valid date" => "Der Eingabewert scheint kein gültiges Datum zu sein",
    "The input does not fit the date format '%format%'" => "Der Eingabewert entspricht nicht dem Format '%format%'",
    
    // Zend\Validator\DateStep
    "Invalid type given. String, integer, array or DateTime expected" => "Ungültiger Eingabewert. String, Integer, Array oder Datums- und Zeitangabe erwartet",
    "The input does not appear to be a valid date" => "Der Eingabewert scheint kein gültiges Datum zu sein",
    "The input does not fit the date format '%format%'" => "Der Eingabewert pass nicht zum Datumsformat '%format%'",
    "The input is not a valid step" => "Der Eingabewert ist kein gültiger Abschnitt",
    
    // Zend\Validator\Db\AbstractDb
    "No record matching the input was found" => "Es existiert kein Eintrag entsprechend des Eingabewertes",
    "A record matching the input was found" => "Es existiert bereits ein Eintrag entsprechend des Eingabewertes",
    
    // Zend\Validator\Digits
    "The input must contain only digits" => "Der Eingabewert darf nur Zahlen enthalten",
    "The input is an empty string" => "Der Eingabewert ist leer",
    "Invalid type given. String, integer or float expected" => "Ungültiger Eingabewert. String, Integer oder Float erwartet",
    
    // Zend\Validator\EmailAddress
    "Invalid type given. String expected" => "Ungültiger Eingabewert. String erwartet",
    "The input is not a valid email address. Use the basic format local-part@hostname" => "Der Eingabewert ist keine gültige E-Mail-Adresse. Benutzen Sie folgendes Format: dein-name@anbieter",
    "'%hostname%' is not a valid hostname for the email address" => "'%hostname%' ist kein gültiger Hostname für die E-Mail-Adresse",
    "'%hostname%' does not appear to have a valid MX record for the email address '%value%'" => "'%hostname%' scheint keinen gültigen MX Eintrag für die E-Mail-Adresse '%value%' zu haben",
    "'%hostname%' is not in a routable network segment. The email address  should not be resolved from public network" => "'%hostname%' ist in keinem routebaren Netzwerksegment. Die E-Mail-Adresse sollte nicht vom öffentlichen Netz aus aufgelöst werden",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' passt nicht auf das dot-atom Format",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' passt nicht auf das quoted-string Format",
    "'%localPart%' is not a valid local part for the email address" => "'%localPart%' ist kein gültiger lokaler Teil für die E-Mail-Adresse",
    "The input exceeds the allowed length" => "Der Eingabewert ist länger als erlaubt",
    
    // Zend\Validator\Explode
    "Invalid type given" => "Ungültiger Eingabewert.",
    
    // Zend\Validator\File\Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "Zu viele Dateien. Maximal '%max%' sind erlaubt aber '%count%' wurden angegeben",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "Zu wenige Dateien. Minimal '%min%' wurden erwartet aber nur '%count%' wurden angegeben",
    
    // Zend\Validator\File\Crc32
    "File does not match the given crc32 hashes" => "Die Datei entspricht nicht den angegebenen CRC32 Hashes",
    "A crc32 hash could not be evaluated for the given file" => "Für die angegebene Datei konnte kein CRC32 Hash evaluiert werden",
    "File is not readable or does not exist" => "Die Datei ist nicht lesbar oder existiert nicht",
    
    // Zend\Validator\File\ExcludeExtension
    "File has an incorrect extension" => "Die Datei hat einen falschen Dateityp",
    "File is not readable or does not exist" => "Die Datei ist nicht lesbar oder existiert nicht",
    
    // Zend\Validator\File\Exists
    "File does not exist" => "Die Datei existiert nicht",
    
    // Zend\Validator\File\Extension
    "File has an incorrect extension" => "Die Datei hat einen falschen Dateityp",
    "File is not readable or does not exist" => "Die Datei ist nicht lesbar oder existiert nicht",
    
    // Zend\Validator\File\FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "Alle Dateien sollten in Summe eine maximale Größe von '%max%' haben, aber es wurde '%size%' erkannt",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "Alle Dateien sollten in Summe eine minimale Größe von '%min%' haben, aber es wurde '%size%' erkannt",
    "One or more files can not be read" => "Ein oder mehrere Dateien konnten nicht gelesen werden",
    
    // Zend\Validator\File\Hash
    "File does not match the given hashes" => "Die Datei entspricht nicht den angegebenen Hashes",
    "A hash could not be evaluated for the given file" => "Für die angegebene Datei konnte kein Hash evaluiert werden",
    "File is not readable or does not exist" => "Die Datei ist nicht lesbar oder existiert nicht",
    
    // Zend\Validator\File\ImageSize
    "Maximum allowed width for image should be '%maxwidth%' but '%width%' detected" => "Die maximal erlaubte Breite für das Bild ist '%maxwidth%', aber es wurde '%width%' erkannt",
    "Minimum expected width for image should be '%minwidth%' but '%width%' detected" => "Die minimal erlaubte Breite für das Bild ist '%minwidth%', aber es wurde '%width%' erkannt",
    "Maximum allowed height for image should be '%maxheight%' but '%height%' detected" => "Die maximal erlaubte Höhe für das Bild ist '%maxheight%', aber es wurde '%height%' erkannt",
    "Minimum expected height for image should be '%minheight%' but '%height%' detected" => "Die minimal erlaubte Höhe für das Bild ist '%minheight%', aber es wurde '%height%' erkannt",
    "The size of image could not be detected" => "Die Größe des Bildes konnte nicht erkannt werden",
    "File is not readable or does not exist" => "Die Datei ist nicht lesbar oder existiert nicht",
    
    // Zend\Validator\File\IsCompressed
    "File is not compressed, '%type%' detected" => "Die Datei ist nicht komprimiert. Es wurde '%type%' erkannt",
    "The mimetype of file could not be detected" => "Der Mimetyp der Datei konnte nicht erkannt werden",
    "File is not readable or does not exist" => "Die Datei ist nicht lesbar oder existiert nicht",
    
    // Zend\Validator\File\IsImage
    "File is no image, '%type%' detected" => "Die Datei ist kein Bild. Es wurde '%type%' erkannt",
    "The mimetype of file could not be detected" => "Der Mimetyp der Datei konnte nicht erkannt werden",
    "File is not readable or does not exist" => "Die Datei ist nicht lesbar oder existiert nicht",
    
    // Zend\Validator\File\Md5
    "File does not match the given md5 hashes" => "Die Datei entspricht nicht den angegebenen MD5 Hashes",
    "A md5 hash could not be evaluated for the given file" => "Für die angegebene Datei konnte kein MD5 Hash evaluiert werden",
    "File is not readable or does not exist" => "Die Datei ist nicht lesbar oder existiert nicht",
    
    // Zend\Validator\File\MimeType
    "File has a false mimetype of '%type%'" => "Die Datei hat einen falschen Mimetyp von '%type%'",
    "The mimetype could not be detected from the file" => "Der Mimetyp der Datei konnte nicht erkannt werden",
    "File is not readable or does not exist" => "Die Datei ist nicht lesbar oder existiert nicht",
    
    // Zend\Validator\File\NotExists
    "File exists" => "Die Datei existiert bereits",
    
    // Zend\Validator\File\Sha1
    "File does not match the given sha1 hashes" => "Die Datei entspricht nicht den angegebenen SHA1 Hashes",
    "A sha1 hash could not be evaluated for the given file" => "Für die angegebene Datei konnte kein SHA1 Hash evaluiert werden",
    "File is not readable or does not exist" => "Die Datei ist nicht lesbar oder existiert nicht",
    
    // Zend\Validator\File\Size
    "Maximum allowed size for file is '%max%' but '%size%' detected" => "Die maximal erlaubte Größe für die Datei ist '%max%', aber es wurde '%size%' entdeckt",
    "Minimum expected size for file is '%min%' but '%size%' detected" => "Die mindestens erwartete Größe für die Datei ist '%min%', aber es wurde '%size%' entdeckt",
    "File is not readable or does not exist" => "Die Datei ist nicht lesbar oder existiert nicht",
    
    // Zend\Validator\File\Upload
    "File '%value%' exceeds the defined ini size" => "Die Datei '%value%' übersteigt die definierte Größe in der Konfiguration",
    "File '%value%' exceeds the defined form size" => "Die Datei '%value%' übersteigt die definierte Größe des Formulars",
    "File '%value%' was only partially uploaded" => "Die Datei '%value%' wurde nur teilweise hochgeladen",
    "File '%value%' was not uploaded" => "Die Datei '%value%' wurde nicht hochgeladen",
    "No temporary directory was found for file '%value%'" => "Für die Datei '%value%' wurde kein temporäres Verzeichnis gefunden",
    "File '%value%' can't be written" => "Die Datei '%value%' konnte nicht geschrieben werden",
    "A PHP extension returned an error while uploading the file '%value%'" => "Eine PHP Erweiterung hat einen Fehler ausgegeben wärend die Datei '%value%' hochgeladen wurde",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "Die Datei '%value%' wurde illegal hochgeladen. Dies könnte eine mögliche Attacke sein",
    "File '%value%' was not found" => "Die Datei '%value%' wurde nicht gefunden",
    "Unknown error while uploading file '%value%'" => "Ein unbekannter Fehler ist aufgetreten wärend die Datei '%value%' hochgeladen wurde",
    
    // Zend\Validator\File\UploadFile
    "File exceeds the defined ini size" => "Die Datei übersteigt die definierte Größe in der Konfiguration",
    "File exceeds the defined form size" => "Die Datei übersteigt die definierte Größe des Formulars",
    "File was only partially uploaded" => "Die Datei wurde nur teilweise hochgeladen",
    "File was not uploaded" => "Die Datei wurde nicht hochgeladen",
    "No temporary directory was found for file" => "Für die Datei wurde kein temporäres Verzeichnis gefunden",
    "File can't be written" => "Die Datei konnte nicht geschrieben werden",
    "A PHP extension returned an error while uploading the file" => "Eine PHP Erweiterung hat einen Fehler ausgegeben wärend die Datei hochgeladen wurde",
    "File was illegally uploaded. This could be a possible attack" => "Die Datei wurde illegal hochgeladen. Dies könnte eine mögliche Attacke sein",
    "File was not found" => "Die Datei wurde nicht gefunden",
    "Unknown error while uploading file" => "Ein unbekannter Fehler ist aufgetreten wärend die Datei hochgeladen wurde",
    
    // Zend\Validator\File\WordCount
    "Too much words, maximum '%max%' are allowed but '%count%' were counted" => "Zu viele Wörter. Maximal '%max%' sind erlaubt, aber '%count%' wurden gezählt",
    "Too few words, minimum '%min%' are expected but '%count%' were counted" => "Zu wenige Wörter. Mindestens '%min%' wurden erwartet, aber '%count%' wurden gezählt",
    "File is not readable or does not exist" => "Die Datei ist nicht lesbar oder existiert nicht",
    
    // Zend\Validator\GreaterThan
    "The input is not greater than '%min%'" => "Der Eingabewert ist nicht größer als '%min%'",
    "The input is not greater or equal than '%min%'" => "Der Eingabewert ist nicht größer oder gleich '%min%'",
    
    // Zend\Validator\Hex
    "Invalid type given. String expected" => "Ungültiger Eingabewert. String erwartet",
    "The input contains non-hexadecimal characters" => "Der Eingabewert enthält nicht nur hexadezimale Zeichen",
    
    // Zend\Validator\Hostname
    "The input appears to be a DNS hostname but the given punycode notation cannot be decoded" => "Der Eingabewert scheint ein DNS Hostname zu sein, aber die angegebene Punycode Schreibweise konnte nicht dekodiert werden",
    "Invalid type given. String expected" => "Ungültiger Eingabewert. String erwartet",
    "The input appears to be a DNS hostname but contains a dash in an invalid position" => "Der Eingabewert scheint ein DNS Hostname zu sein, enthält aber einen Bindestrich an einer ungültigen Position",
    "The input does not match the expected structure for a DNS hostname" => "Der Eingabewert passt nicht in die erwartete Struktur für einen DNS Hostname",
    "The input appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "Der Eingabewert scheint ein DNS Hostname zu sein, passt aber nicht in das Hostname Schema für die TLD '%tld%'",
    "The input does not appear to be a valid local network name" => "Der Eingabewert scheint kein gültiger lokaler Netzerkname zu sein",
    "The input does not appear to be a valid URI hostname" => "Der Eingabewert scheint kein gültiger URI Hostname zu sein",
    "The input appears to be an IP address, but IP addresses are not allowed" => "Der Eingabewert scheint eine IP-Adresse zu sein, aber IP-Adressen sind nicht erlaubt",
    "The input appears to be a local network name but local network names are not allowed" => "Der Eingabewert scheint ein lokaler Netzwerkname zu sein, aber lokale Netzwerknamen sind nicht erlaubt",
    "The input appears to be a DNS hostname but cannot extract TLD part" => "Der Eingabewert scheint ein DNS Hostname zu sein, aber der TLD-Teil konnte nicht extrahiert werden",
    "The input appears to be a DNS hostname but cannot match TLD against known list" => "Der Eingabewert scheint ein DNS Hostname zu sein, aber die TLD wurde in der bekannten Liste nicht gefunden",
    
    // Zend\Validator\Iban
    "Unknown country within the IBAN" => "Unbekanntes Land in der IBAN '%value%'",
    "Countries outside the Single Euro Payments Area (SEPA) are not supported" => "Länder außerhalb des einheitlichen Euro-Zahlungsverkehrsraum (SEPA) werden nicht unterstützt",
    "The input has a false IBAN format" => "Der Eingabewert hat ein ungültiges IBAN Format",
    "The input has failed the IBAN check" => "Die IBAN Prüfung ist fehlgeschlagen",
    
    // Zend\Validator\Identical
    "The two given tokens do not match" => "Die zwei angegebenen Token stimmen nicht überein",
    "No token was provided to match against" => "Es wurde kein Token angegeben gegen den geprüft werden kann",
    
    // Zend\Validator\InArray
    "The input was not found in the haystack" => "Der Eingabewert wurde nicht im Array gefunden",
    
    // Zend\Validator\Ip
    "Invalid type given. String expected" => "Ungültiger Eingabewert. String erwartet",
    "The input does not appear to be a valid IP address" => "Der Eingabewert scheint keine gültige IP-Adresse zu sein",
    
    // Zend\Validator\IsInstanceOf
    "The input is not an instance of '%className%'" => "Der Eingabewert ist keine Instanz von '%className%'",
    
    // Zend\Validator\Isbn
    "Invalid type given. String or integer expected" => "Ungültiger Eingabewert. String oder Integer erwartet",
    "The input is not a valid ISBN number" => "Der Eingabewert ist keine gültige ISBN",
    
    // Zend\Validator\LessThan
    "The input is not less than '%max%'" => "Der Eingabewert ist nicht weniger als '%max%'",
    "The input is not less or equal than '%max%'" => "Der Eingabewert ist nicht weniger als oder gleich '%max%'",
    
    // Zend\Validator\NotEmpty
    "Value is required and can't be empty" => "Es wird ein Eingabewert benötigt. Dieser darf nicht leer sein",
    "Invalid type given. String, integer, float, boolean or array expected" => "Ungültiger Eingabewert. String, Integer, Float, Boolean oder Array erwartet",
    
    // Zend\Validator\Regex
    "Invalid type given. String, integer or float expected" => "Ungültiger Eingabewert. String, Integer oder Float erwartet",
    "The input does not match against pattern '%pattern%'" => "Der Eingabewert entspricht nicht folgendem Muster: '%pattern%'",
    "There was an internal error while using the pattern '%pattern%'" => "Es gab einen internen Fehler bei der Verwendung des Musters: '%pattern%'",
    
    // Zend\Validator\Sitemap\Changefreq
    "The input is not a valid sitemap changefreq" => "Der Eingabewert ist keine gültige 'changefreq' für Sitemap",
    "Invalid type given. String expected" => "Ungültiger Eingabewert. String erwartet",
    
    // Zend\Validator\Sitemap\Lastmod
    "The input is not a valid sitemap lastmod" => "Der Eingabewert ist keine gültige 'lastmod' für Sitemap",
    "Invalid type given. String expected" => "Ungültiger Eingabewert. String erwartet",
    
    // Zend\Validator\Sitemap\Loc
    "The input is not a valid sitemap location" => "Der Eingabewert ist keine gültige 'location' für Sitemap",
    "Invalid type given. String expected" => "Ungültiger Eingabewert. String erwartet",
    
    // Zend\Validator\Sitemap\Priority
    "The input is not a valid sitemap priority" => "Der Eingabewert ist keine gültige 'priority' für Sitemap",
    "Invalid type given. Numeric string, integer or float expected" => "Ungültiger Eingabewert. Nummerischer String, Integer oder Float erwartet",
    
    // Zend\Validator\Step
    "Invalid value given. Scalar expected" => "Ungültiger Eingabewert. Skalar erwartet",
    "The input is not a valid step" => "Der Eingabewert ist kein gültiger Abschnitt",
    
    // Zend\Validator\StringLength
    "Invalid type given. String expected" => "Ungültiger Eingabewert. String erwartet",
    "The input is less than %min% characters long" => "Der Eingabewert ist weniger als %min% Zeichen lang",
    "The input is more than %max% characters long" => "Der Eingabewert ist mehr als %max% Zeichen lang",
    
    // Zend\Validator\Uri
    "Invalid type given. String expected" => "Ungültiger Eingabewert. String erwartet",
    "The input does not appear to be a valid Uri" => "Der Eingabewert scheint keine gültige Uri zu sein",
);
