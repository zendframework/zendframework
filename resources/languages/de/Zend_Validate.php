<?php
/**
 * Zend Framework
 * LICENSE
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * EN-Revision: 09.Sept.2012
 */
return array(
    // Zend_I18n_Validator_Alnum
    "Invalid type given. String, integer or float expected"                                                                     => 'Ungültiger Eingabewert eingegeben. String, Integer oder Float erwartet',
    "The input contains characters which are non alphabetic and no digits"                                                      => 'Der Eingabewert enthält nicht alphanumerische Zeichen',
    "The input is an empty string"                                                                                              => 'Der Eingabewert ist leer',

    // Zend_I18n_Validator_Alpha
    "Invalid type given. String expected"                                                                                       => 'Ungültiger Eingabewert eingegeben. String erwartet',
    "The input contains non alphabetic characters"                                                                              => 'Der Eingabewert enthält nichtalphabetische Zeichen',
    "The input is an empty string"                                                                                              => 'Der Eingabewert ist leer',

    // Zend_I18n_Validator_Float
    "Invalid type given. String, integer or float expected"                                                                     => 'Ungültiger Eingabewert eingegeben. String, Integer oder Float erwartet',
    "The input does not appear to be a float"                                                                                   => 'Der Eingabewert scheint keine Gleitkommazahl zu sein',

    // Zend_I18n_Validator_Int
    "Invalid type given. String or integer expected"                                                                            => 'Ungültiger Eingabewert eingegeben. String oder Integer erwartet',
    "The input does not appear to be an integer"                                                                                => 'Der Eingabewert ist keine ganze Zahl',

    // Zend_I18n_Validator_PostCode
    "Invalid type given. String or integer expected"                                                                            => 'Ungültiger Eingabewert eingegeben. String oder Integer erwartet',
    "The input does not appear to be a postal code"                                                                             => 'Der Eingabewert scheint keine gültige Postleitzahl zu sein',
    "An exception has been raised while validating the input"                                                                   => 'Ein Fehler ist während der Prüfung des Eingabewertes ausgetreten',

    // Zend_Validator_Barcode
    "The input failed checksum validation"                                                                                      => 'Der Eingabewert hat die Prüfung der Prüfsumme nicht bestanden',
    "The input contains invalid characters"                                                                                     => 'Der Eingabewert enthält ungültige Zeichen',
    "The input should have a length of %length% characters"                                                                     => 'Der Eingabewert sollte %length% Zeichen lang sein',
    "Invalid type given. String expected"                                                                                       => 'Ungültiger Eingabewert eingegeben. String erwartet',

    // Zend_Validator_Between
    "The input is not between '%min%' and '%max%', inclusively"                                                                 => "Der Eingabewert ist nicht zwischen '%min%' und '%max%', inklusive diesen Werten",
    "The input is not strictly between '%min%' and '%max%'"                                                                     => "Der Eingabewert ist nicht zwischen '%min%' und '%max%'",

    // Zend_Validator_Callback
    "The input is not valid"                                                                                                    => 'Der Eingabewert ist ungültig',
    "An exception has been raised within the callback"                                                                          => 'Ein Fehler ist während des Callbacks ausgetreten',

    // Zend_Validator_CreditCard
    "The input seems to contain an invalid checksum"                                                                            => 'Der Eingabewert enthält eine ungültige Prüfsumme',
    "The input must contain only digits"                                                                                        => 'Der Eingabewert darf nur ganze Zahlen enthalten',
    "Invalid type given. String expected"                                                                                       => 'Ungültiger Eingabewert eingegeben. String erwartet',
    "The input contains an invalid amount of digits"                                                                            => 'Der Eingabewert enthält eine ungültige Anzahl an Zahlen',
    "The input is not from an allowed institute"                                                                                => 'Der Eingabewert ist von keinem erlaubtem Kreditinstitut',
    "The input seems to be an invalid creditcard number"                                                                        => 'Der Eingabewert scheint eine ungültige Kretitkartennummer zu sein',
    "An exception has been raised while validating the input"                                                                   => 'Ein Fehler ist während der Prüfung des Eingabewertes ausgetreten',

    // Zend_Validator_Csrf
    "The form submitted did not originate from the expected site"                                                               => 'Der Ursprung des abgesendeten Formulares konnte nicht bestätigt werden',

    // Zend_Validator_Date
    "Invalid type given. String, integer, array or DateTime expected"                                                           => 'Ungültiger Eingabewert eingegeben. String, Integer, array oder DateTime erwartet',
    "The input does not appear to be a valid date"                                                                              => 'Der Eingabewert scheint kein gültiges Datum zu sein',
    "The input does not fit the date format '%format%'"                                                                         => "Der Eingabewert entspricht nicht dem Format '%format%'",

    // Zend_Validator_DateStep
    //@todo Better translation for "The input is not a valid step"
    "Invalid type given. String, integer, array or DateTime expected"                                                           => 'Ungültiger Eingabewert eingegeben. String, Integer, array oder DateTime erwartet',
    "The input does not appear to be a valid date"                                                                              => 'Der Eingabewert scheint kein gültiges Datum zu sein',
    "The input is not a valid step"                                                                                             => 'Der Eingabewert ist kein gültiger Abschnitt',

    // Zend_Validator_Db_AbstractDb
    "No record matching the input was found"                                                                                    => 'Es existiert kein Eintrag entsprechend des Eingabewertes',
    "A record matching the input was found"                                                                                     => 'Es existiert bereits ein Eintrag entsprechend des Eingabewertes',

    // Zend_Validator_Digits
    "The input must contain only digits"                                                                                        => 'Der Eingabewert darf nur Zahlen enthalten',
    "The input is an empty string"                                                                                              => 'Der Eingabewert ist leer',
    "Invalid type given. String, integer or float expected"                                                                     => 'Ungültiger Eingabewert eingegeben. String, Integer oder Float erwartet',

    // Zend_Validator_EmailAddress
    "Invalid type given. String expected"                                                                                       => 'Ungültiger Eingabewert eingegeben. String erwartet',
    "The input is not a valid email address. Use the basic format local-part@hostname"                                          => 'Der Eingabewert ist keine gültige E-Mail-Adresse. Benutzen Sie folgendes format: your-name@anbieter',
    "'%hostname%' is not a valid hostname for the email address"                                                                => "'%hostname%' ist kein gültiger Hostname für die Emailadresse",
    "'%hostname%' does not appear to have a valid MX record for the email address '%value%'"                                    => "'%hostname%' scheint keinen gültigen MX Eintrag für die Emailadresse '%value%' zu haben",
    "'%hostname%' is not in a routable network segment. The email address  should not be resolved from public network"          => "'%hostname%' ist in keinem routebaren Netzwerksegment. Die Emailadresse sollte nicht vom öffentlichen Netz aus aufgelöst werden",
    "'%localPart%' can not be matched against dot-atom format"                                                                  => "'%localPart%' passt nicht auf das dot-atom Format",
    "'%localPart%' can not be matched against quoted-string format"                                                             => "'%localPart%' passt nicht auf das quoted-string Format",
    "'%localPart%' is not a valid local part for the email address"                                                             => "'%localPart%' ist kein gültiger lokaler Teil für die Emailadresse",
    "The input exceeds the allowed length"                                                                                      => 'Der Eingabewert ist länger als erlaubt',


    // Zend_Validator_Explode
    "Invalid type given. String expected"                                                                                       => 'Ungültiger Eingabewert eingegeben. String erwartet',

    // Zend_Validator_File_Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given"                                                       => "Zu viele Dateien. Maximal '%max%' sind erlaubt aber '%count%' wurden angegeben",
    "Too few files, minimum '%min%' are expected but '%count%' are given"                                                       => "Zu wenige Dateien. Minimal '%min%' wurden erwartet aber nur '%count%' wurden angegeben",

    // Zend_Validator_File_Crc32
    "File '%value%' does not match the given crc32 hashes"                                                                      => "Die Datei '%value%' entspricht nicht den angegebenen Crc32 Hashes",
    "A crc32 hash could not be evaluated for the given file"                                                                    => "Für die angegebene Datei konnte kein Crc32 Hash evaluiert werden",
    "File '%value%' is not readable or does not exist"                                                                          => "Die Datei '%value%' konnte nicht gelesen werden oder existiert nicht",

    // Zend_Validator_File_ExcludeExtension
    "File '%value%' has a false extension"                                                                                      => "Die Datei '%value%' hat einen falschen Dateityp",
    "File '%value%' is not readable or does not exist"                                                                          => "Die Datei '%value%' konnte nicht gelesen werden oder existiert nicht",

    // Zend_Validator_File_Exists
    "File '%value%' does not exist"                                                                                             => "Die Datei '%value%' existiert nicht",

    // Zend_Validator_File_Extension
    "File '%value%' has a false extension"                                                                                      => "Die Datei '%value%' hat einen falschen Dateityp",
    "File '%value%' is not readable or does not exist"                                                                          => "Die Datei '%value%' konnte nicht gelesen werden oder existiert nicht",

    // Zend_Validator_File_FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected"                                         => "Alle Dateien sollten in Summe eine maximale Größe von '%max%' haben, aber es wurde '%size%' erkannt",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected"                                         => "Alle Dateien sollten in Summe eine minimale Größe von '%min%' haben, aber es wurde '%size%' erkannt",
    "One or more files can not be read"                                                                                         => 'Ein oder mehrere Dateien konnten nicht gelesen werden',

    // Zend_Validator_File_Hash
    "File '%value%' does not match the given hashes"                                                                            => "Die Datei '%value%' entspricht nicht den angegebenen Hashes",
    "A hash could not be evaluated for the given file"                                                                          => "Für die angegebene Datei konnte kein Hash evaluiert werden",
    "File '%value%' is not readable or does not exist"                                                                          => "Die Datei '%value%' konnte nicht gelesen werden oder existiert nicht",

    // Zend_Validator_File_ImageSize
    "Maximum allowed width for image '%value%' should be '%maxwidth%' but '%width%' detected"                                   => "Die maximal erlaubte Breite für das Bild '%value%' ist '%maxwidth%', aber es wurde '%width%' erkannt",
    "Minimum expected width for image '%value%' should be '%minwidth%' but '%width%' detected"                                  => "Die minimal erlaubte Breite für das Bild '%value%' ist '%minwidth%', aber es wurde '%width%' erkannt",
    "Maximum allowed height for image '%value%' should be '%maxheight%' but '%height%' detected"                                => "Die maximal erlaubte Höhe für das Bild '%value%' ist '%maxheight%', aber es wurde '%height%' erkannt",
    "Minimum expected height for image '%value%' should be '%minheight%' but '%height%' detected"                               => "Die minimal erlaubte Höhe für das Bild '%value%' ist '%minheight%', aber es wurde '%height%' erkannt",
    "The size of image '%value%' could not be detected"                                                                         => "Die Größe des Bildes '%value%' konnte nicht erkannt werden",
    "File '%value%' is not readable or does not exist"                                                                          => "Die Datei '%value%' konnte nicht gelesen werden oder existiert nicht",

    // Zend_Validator_File_IsCompressed
    "File '%value%' is not compressed, '%type%' detected"                                                                       => "Die Datei '%value%' ist nicht komprimiert. Es wurde '%type%' erkannt",
    "The mimetype of file '%value%' could not be detected"                                                                      => "Der Mimetyp der Datei '%value%' konnte nicht erkannt werden",
    "File '%value%' is not readable or does not exist"                                                                          => "Die Datei '%value%' konnte nicht gelesen werden oder existiert nicht",

    // Zend_Validator_File_IsImage
    "File '%value%' is no image, '%type%' detected"                                                                             => "Die Datei '%value%' ist kein Bild. Es wurde '%type%' erkannt",
    "The mimetype of file '%value%' could not be detected"                                                                      => "Der Mimetyp der Datei '%value%' konnte nicht erkannt werden",
    "File '%value%' is not readable or does not exist"                                                                          => "Die Datei '%value%' konnte nicht gelesen werden oder existiert nicht",

    // Zend_Validator_File_Md5
    "File '%value%' does not match the given md5 hashes"                                                                        => "Die Datei '%value%' entspricht nicht den angegebenen Md5 Hashes",
    "A md5 hash could not be evaluated for the given file"                                                                      => "Für die angegebene Datei konnte kein Md5 Hash evaluiert werden",
    "File '%value%' is not readable or does not exist"                                                                          => "Die Datei '%value%' konnte nicht gelesen werden oder existiert nicht",

    // Zend_Validator_File_MimeType
    "File '%value%' has a false mimetype of '%type%'"                                                                           => "Die Datei '%value%' hat einen falschen Mimetyp von '%type%'",
    "The mimetype of file '%value%' could not be detected"                                                                      => "Der Mimetyp der Datei '%value%' konnte nicht erkannt werden",
    "File '%value%' is not readable or does not exist"                                                                          => "Die Datei '%value%' konnte nicht gelesen werden oder existiert nicht",

    // Zend_Validator_File_NotExists
    "File '%value%' exists"                                                                                                     => "Die Datei '%value%' existiert bereits",

    // Zend_Validator_File_Sha1
    "File '%value%' does not match the given sha1 hashes"                                                                       => "Die Datei '%value%' entspricht nicht den angegebenen Sha1 Hashes",
    "A sha1 hash could not be evaluated for the given file"                                                                     => "Für die angegebene Datei konnte kein Sha1 Hash evaluiert werden",
    "File '%value%' is not readable or does not exist"                                                                          => "Die Datei '%value%' konnte nicht gelesen werden oder existiert nicht",

    // Zend_Validator_File_Size
    "Maximum allowed size for file '%value%' is '%max%' but '%size%' detected"                                                  => "Die maximal erlaubte Größe für die Datei '%value%' ist '%max%', aber es wurde '%size%' entdeckt",
    "Minimum expected size for file '%value%' is '%min%' but '%size%' detected"                                                 => "Die mindestens erwartete Größe für die Datei '%value%' ist '%min%', aber es wurde '%size%' entdeckt",
    "File '%value%' is not readable or does not exist"                                                                          => "Die Datei '%value%' konnte nicht gelesen werden oder existiert nicht",

    // Zend_Validator_File_Upload
    "File '%value%' exceeds the defined ini size"                                                                               => "Die Datei '%value%' übersteigt die definierte Größe in der Konfiguration",
    "File '%value%' exceeds the defined form size"                                                                              => "Die Datei '%value%' übersteigt die definierte Größe des Formulars",
    "File '%value%' was only partially uploaded"                                                                                => "Die Datei '%value%' wurde nur teilweise hochgeladen",
    "File '%value%' was not uploaded"                                                                                           => "Die Datei '%value%' wurde nicht hochgeladen",
    "No temporary directory was found for file '%value%'"                                                                       => "Für die Datei '%value%' wurde kein temporäres Verzeichnis gefunden",
    "File '%value%' can't be written"                                                                                           => "Die Datei '%value%' konnte nicht geschrieben werden",
    "A PHP extension returned an error while uploading the file '%value%'"                                                      => "Eine PHP Erweiterung hat einen Fehler ausgegeben wärend die Datei '%value%' hochgeladen wurde",
    "File '%value%' was illegally uploaded. This could be a possible attack"                                                    => "Die Datei '%value%' wurde illegal hochgeladen. Dies könnte eine mögliche Attacke sein",
    "File '%value%' was not found"                                                                                              => "Die Datei '%value%' wurde nicht gefunden",
    "Unknown error while uploading file '%value%'"                                                                              => "Ein unbekannter Fehler ist aufgetreten wärend die Datei '%value%' hochgeladen wurde",

    // Zend_Validator_File_WordCount
    "Too much words, maximum '%max%' are allowed but '%count%' were counted"                                                    => "Zu viele Wörter. Maximal '%max%' sind erlaubt, aber '%count%' wurden gezählt",
    "Too few words, minimum '%min%' are expected but '%count%' were counted"                                                   => "Zu wenige Wörter. Mindestens '%min%' wurden erwartet, aber '%count%' wurden gezählt",
    "File '%value%' is not readable or does not exist"                                                                          => "Die Datei '%value%' konnte nicht gelesen werden oder existiert nicht",

    // Zend_Validator_GreaterThan
    "The input is not greater than '%min%'"                                                                                     => "Der Eingabewert ist nicht größer als '%min%'",
    "The input is not greater or equal than '%min%'"                                                                            => "Der Eingabewert ist nicht größer oder gleich '%min%'",

    // Zend_Validator_Hex
    "Invalid type given. String expected"                                                                                       => 'Ungültiger Eingabewert eingegeben. String erwartet',
    "The input contains non-hexadecimal characters"                                                                             => 'Der Eingabewert enthält nicht nur hexadezimale Zeichen',

    // Zend_Validator_Hostname
    "The input appears to be a DNS hostname but the given punycode notation cannot be decoded"                                  => "Der Eingabewert scheint ein DNS Hostname zu sein, aber die angegebene Punycode Schreibweise konnte nicht dekodiert werden",
    "Invalid type given. String expected"                                                                                       => 'Ungültiger Eingabewert eingegeben. String erwartet',
    "The input appears to be a DNS hostname but contains a dash in an invalid position"                                         => "Der Eingabewert scheint ein DNS Hostname zu sein, enthält aber einen Bindestrich an einer ungültigen Position",
    "The input does not match the expected structure for a DNS hostname"                                                        => "Der Eingabewert passt nicht in die erwartete Struktur für einen DNS Hostname",
    "The input appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'"                           => "Der Eingabewert scheint ein DNS Hostname zu sein, passt aber nicht in das Hostname Schema für die TLD '%tld%'",
    "The input does not appear to be a valid local network name"                                                                => "Der Eingabewert scheint kein gültiger lokaler Netzerkname zu sein",
    "The input does not appear to be a valid URI hostname"                                                                      => "Der Eingabewert scheint kein gültiger URI Hostname zu sein",
    "The input appears to be an IP address, but IP addresses are not allowed"                                                   => "Der Eingabewert scheint eine IP-Adresse zu sein, aber IP-Adressen sind nicht erlaubt",
    "The input appears to be a local network name but local network names are not allowed"                                      => "Der Eingabewert scheint ein lokaler Netzwerkname zu sein, aber lokale Netzwerknamen sind nicht erlaubt",
    "The input appears to be a DNS hostname but cannot extract TLD part"                                                        => "Der Eingabewert scheint ein DNS Hostname zu sein, aber der TLD Teil konnte nicht extrahiert werden",
    "The input appears to be a DNS hostname but cannot match TLD against known list"                                            => "Der Eingabewert scheint ein DNS Hostname zu sein, aber die TLD wurde in der bekannten Liste nicht gefunden",

    // Zend_Validator_Iban
    "Unknown country within the IBAN"                                                                                           => "Unbekanntes Land in der IBAN '%value%'",
    "Countries outside the Single Euro Payments Area (SEPA) are not supported"                                                  => 'Länder außerhalb des einheitlichen Euro-Zahlungsverkehrsraum (SEPA) werden nicht unterstützt',
    "The input has a false IBAN format"                                                                                         => 'Der Eingabewert hat ein ungültiges IBAN Format',
    "The input has failed the IBAN check"                                                                                       => 'Die IBAN Prüfung ist fehlgeschlagen',

    // Zend_Validator_Identical
    "The two given tokens do not match"                                                                                         => 'Die zwei angegebenen Token stimmen nicht überein',
    "No token was provided to match against"                                                                                    => "Es wurde kein Token angegeben gegen den geprüft werden kann",

    // Zend_Validator_InArray
    //@todo Better translation for "haystack"
    "The input was not found in the haystack"                                                                                   => "Der Eingabewert wurde nicht im Haystack gefunden",

    // Zend_Validator_Ip
    "Invalid type given. String expected"                                                                                       => 'Ungültiger Eingabewert eingegeben. String erwartet',
    "The input does not appear to be a valid IP address"                                                                        => "Der Eingabewert scheint keine gültige IP-Adresse zu sein",

    // Zend_Validator_Isbn
    "Invalid type given. String or integer expected"                                                                            => 'Ungültiger Eingabewert eingegeben. String oder Integer erwartet',
    "The input is not a valid ISBN number"                                                                                      => "Der Eingabewert ist keine gültige ISBN",

    // Zend_Validator_LessThan
    "The input is not less than '%max%'"                                                                                        => "Der Eingabewert ist nicht weniger als '%max%'",
    "The input is not less or equal than '%max%'"                                                                               => "Der Eingabewert ist nicht weniger als oder gleich '%max%'",

    // Zend_Validator_NotEmpty
    "Value is required and can't be empty"                                                                                      => "Es wird ein Eingabewert benötigt. Dieser darf nicht leer sein",
    "Invalid type given. String, integer, float, boolean or array expected"                                                     => "Ungültiger Eingabewert eingegeben. String, Integer, Float, Boolean oder Array erwartet",

    // Zend_Validator_Regex
    "Invalid type given. String, integer or float expected"                                                                     => 'Ungültiger Eingabewert eingegeben. String, Integer oder Float erwartet',
    "The input does not match against pattern '%pattern%'"                                                                      => "Der Eingabewert entspricht nicht folgendem Muster: '%pattern%'",
    "There was an internal error while using the pattern '%pattern%'"                                                           => "Es gab einen internen Fehler bei der Verwendung des Muster: '%pattern%'",

    // Zend_Validator_Sitemap_Changefreq
    "The input is not a valid sitemap changefreq"                                                                               => "Der Eingabewert ist keine gültige 'changefreq' für Sitemap",
    "Invalid type given. String expected"                                                                                       => 'Ungültiger Eingabewert eingegeben. String erwartet',

    // Zend_Validator_Sitemap_Lastmod
    "The input is not a valid sitemap lastmod"                                                                                  => "Der Eingabewert ist keine gültige 'lastmod' für Sitemap",
    "Invalid type given. String expected"                                                                                       => 'Ungültiger Eingabewert eingegeben. String erwartet',

    // Zend_Validator_Sitemap_Loc
    "The input is not a valid sitemap location"                                                                                 => "Der Eingabewert ist keine gültige 'location' für Sitemap",
    "Invalid type given. String expected"                                                                                       => 'Ungültiger Eingabewert eingegeben. String erwartet',

    // Zend_Validator_Sitemap_Priority
    "The input is not a valid sitemap priority"                                                                                 => "Der Eingabewert ist keine gültige 'priority' für Sitemap",
    "Invalid type given. Numeric string, integer or float expected"                                                             => "Ungültiger Eingabewert eingegeben. Nummerischer String, Integer oder Float erwartet",

    // Zend_Validator_Step
    //@todo Better translation for "The input is not a valid step"
    "Invalid value given. Scalar expected"                                                                                      => "Invalid value given. Scalar expected",
    "The input is not a valid step"                                                                                             => "Der Eingabewert ist kein gültiger Abschnitt",

    // Zend_Validator_StringLength
    "Invalid type given. String expected"                                                                                       => 'Ungültiger Eingabewert eingegeben. String erwartet',
    "The input is less than %min% characters long"                                                                              => "Der Eingabewert ist weniger als %min% Zeichen lang",
    "The input is more than %max% characters long"                                                                              => "Der Eingabewert ist mehr als %max% Zeichen lang",

    // Zend_Validator_Uri
    "Invalid type given. String expected"                                                                                       => 'Ungültiger Eingabewert eingegeben. String erwartet',
    "The input does not appear to be a valid Uri"                                                                               => "Der Eingabewert scheint keine gültige Uri zu sein",
);
