<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * EN-Revision: 04.Apr.2013
 */
return array(
    // Zend\I18n\Validator\Alnum
    "Invalid type given. String, integer or float expected" => "Tipus no vàlid donat. S'espera una cadena de text, un enter o un nombre de precisió simple",
    "The input contains characters which are non alphabetic and no digits" => "L'entrada conté caràcters que no són alfabètics ni dígits",
    "The input is an empty string" => "L'entrada és una cadena buida",

    // Zend\I18n\Validator\Alpha
    "Invalid type given. String expected" => "Tipus no vàlid donat. S'espera una cadena de text",
    "The input contains non alphabetic characters" => "L'entrada conté caràcters no alfabètics",
    "The input is an empty string" => "L'entrada és una cadena buida",

    // Zend\I18n\Validator\Float
    "Invalid type given. String, integer or float expected" => "Tipus no vàlid donat. S'espera una cadena de text, un enter o un nombre de precisió simple",
    "The input does not appear to be a float" => "L'entrada no sembla ser un nombre de precisió simple",

    // Zend\I18n\Validator\Int
    "Invalid type given. String or integer expected" => "Tipus no vàlid donat. S'espera una cadena de text o un enter",
    "The input does not appear to be an integer" => "L'entrada no sembla ser un nombre enter",

    // Zend\I18n\Validator\PostCode
    "Invalid type given. String or integer expected" => "Tipus no vàlid donat. S'espera una cadena de text o un enter",
    "The input does not appear to be a postal code" => "L'entrada no sembla ser un codi postal",
    "An exception has been raised while validating the input" => "S'ha llançat una excepció en validar l'entrada",

    // Zend\Validator\Barcode
    "The input failed checksum validation" => "L'entrada ha fallat la validació de la suma de comprovació",
    "The input contains invalid characters" => "L'entrada conté caràcters no vàlids",
    "The input should have a length of %length% characters" => "L'entrada ha de tenir una longitud de %length% caràcters",
    "Invalid type given. String expected" => "Tipus no vàlid donat. S'espera una cadena de text",

    // Zend\Validator\Between
    "The input is not between '%min%' and '%max%', inclusively" => "L'entrada no és entre '% min%' i '% max%', inclusivament",
    "The input is not strictly between '%min%' and '%max%'" => "L'entrada no és estrictament entre '% min%' i '%% max'",

    // Zend\Validator\Callback
    "The input is not valid" => "L'entrada no és vàlida",
    "An exception has been raised within the callback" => "S'ha llançat una excepció en el callback",

    // Zend\Validator\CreditCard
    "The input seems to contain an invalid checksum" => "L'entrada sembla contenir una suma de comprovació no vàlida",
    "The input must contain only digits" => "L'entrada ha de contenir només dígits",
    "Invalid type given. String expected" => "Tipus no vàlid donat. S'espera una cadena de text",
    "The input contains an invalid amount of digits" => "L'entrada conté una quantitat no vàlida de dígits",
    "The input is not from an allowed institute" => "L'entrada no és d'una institució permesa",
    "The input seems to be an invalid credit card number" => "L'entrada sembla ser un número de targeta de crèdit no vàlid",
    "An exception has been raised while validating the input" => "S'ha llançat una excepció validant l'entrada",

    // Zend\Validator\Csrf
    "The form submitted did not originate from the expected site" => "El formulari presentat no es va originar en el lloc esperat",

    // Zend\Validator\Date
    "Invalid type given. String, integer, array or DateTime expected" => "Tipus no vàlid donat. S'espera una cadena de text, un enter, un array o DateTime",
    "The input does not appear to be a valid date" => "L'entrada no sembla ser una data vàlida",
    "The input does not fit the date format '%format%'" => "L'entrada no s'ajusta al format de la data '%format%'",

    // Zend\Validator\DateStep
    "The input is not a valid step" => "L'entrada no és un pas vàlid",

    // Zend\Validator\Db\AbstractDb
    "No record matching the input was found" => "No hi ha cap registre que coincideixi amb l'entrada",
    "A record matching the input was found" => "Es va trobar un registre coincident l'entrada",

    // Zend\Validator\Digits
    "The input must contain only digits" => "L'entrada només ha de contenir dígits",
    "The input is an empty string" => "L'entrada és una cadena buida",
    "Invalid type given. String, integer or float expected" => "Tipus no vàlid donat. S'espera una cadena de text, un enter o un nombre de precisió simple",

    // Zend\Validator\EmailAddress
    "Invalid type given. String expected" => "Tipus no vàlid donat. S'espera una cadena de text",
    "The input is not a valid email address. Use the basic format local-part@hostname" => "L'entrada no és una adreça vàlida de correu electrònic. Utilitzeu el format bàsic local-part@hostname",
    "'%hostname%' is not a valid hostname for the email address" => "'%hostname%' no és un nom de host vàlid per a la direcció de correu electrònic",
    "'%hostname%' does not appear to have any valid MX or A records for the email address" => "'%hostname%' no sembla tenir cap registres MX o A vàlids per l'adreça de correu electrònic",
    "'%hostname%' is not in a routable network segment. The email address should not be resolved from public network" => "'%hostname%' no està en un segment de xarxa encaminador. La direcció de correu electrònic no ha de ser resolts des d'una xarxa pública",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' no pot ser comparada amb el format dot-atom",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' no pot ser comparada amb el format quoted-string",
    "'%localPart%' is not a valid local part for the email address" => "'%localPart%' no és una part local vàlida per a la direcció de correu electrònic",
    "The input exceeds the allowed length" => "L'entrada supera la longitud permesa",

    // Zend\Validator\Explode
    "Invalid type given" => "Tipus no vàlid donat",

    // Zend\Validator\File\Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "Massa arxius, estan permesos màxim '%max%' però s'han donat '%count%'",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "Falten arxius, s'espera mínim '%min%' però s'han donat '%count%'",

    // Zend\Validator\File\Crc32
    "File does not match the given crc32 hashes" => "L'arxiu no coindideix amb el hash crc32 donat",
    "A crc32 hash could not be evaluated for the given file" => "El hash crc32 no es va poder avaluar per l'arxiu donat",
    "File is not readable or does not exist" => "L'arxiu no és pot llegir o no existeix",

    // Zend\Validator\File\ExcludeExtension
    "File has an incorrect extension" => "L'arxiu té una extensió falsa",
    "File is not readable or does not exist" => "L'arxiu no és pot llegir o no existeix",

    // Zend\Validator\File\Exists
    "File does not exist" => "L'arxiu no existeix",

    // Zend\Validator\File\Extension
    "File has an incorrect extension" => "L'arxiu té una extensió falsa",
    "File is not readable or does not exist" => "L'arxiu no és pot llegir o no existeix",

    // Zend\Validator\File\FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "Tots els arxius en la suma haurien de tenir una mida màxima de '%max%' però s'ha detectat la mida '%size%'",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "Tots els arxius en la suma haurien de tenir una mida màxima de '%max%' però s'ha detectat la mida '%size%'",
    "One or more files can not be read" => "Un o més fitxers no es poden llegir",

    // Zend\Validator\File\Hash
    "File does not match the given hashes" => "L'arxiu no coincideix amb els valors hash donats",
    "A hash could not be evaluated for the given file" => "El hash no es va poder avaluar per l'arxiu donat",
    "File is not readable or does not exist" => "L'arxiu no és pot llegir o no existeix",

    // Zend\Validator\File\ImageSize
    "Maximum allowed width for image should be '%maxwidth%' but '%width%' detected" => "L'amplada màxima permesa per a la imatge hauria de ser '%maxwidth%' però s'ha detectat '%width%'",
    "Minimum expected width for image should be '%minwidth%' but '%width%' detected" => "L'amplada mínima permesa per a la imatge hauria de ser '%maxwidth%' però s'ha detectat '%width%'",
    "Maximum allowed height for image should be '%maxheight%' but '%height%' detected" => "L'alçada màxima permesa per a la imatge hauria de ser '%maxwidth%' però s'ha detectat '%width%'",
    "Minimum expected height for image should be '%minheight%' but '%height%' detected" => "L'amplada mínima permesa per a la imatge hauria de ser '%maxwidth%' però s'ha detectat '%width%'",
    "The size of image could not be detected" => "La mida de la imatge no s'ha pogut detectar",
    "File is not readable or does not exist" => "L'arxiu no és pot llegir o no existeix",

    // Zend\Validator\File\IsCompressed
    "File is not compressed, '%type%' detected" => "L'arxiu no està comprimit, s'ha detectat '%type%' ",
    "The mimetype could not be detected from the file" => "El mimetype de l'arxiu no s'ha pogut detectar",
    "File is not readable or does not exist" => "L'arxiu no és pot llegir o no existeix",

    // Zend\Validator\File\IsImage
    "File is no image, '%type%' detected" => "L'arxiu no és una imatge, s'ha detectat '%type%'",
    "The mimetype could not be detected from the file" => "El mimetype de l'arxiu no s'ha pogut detectar",
    "File is not readable or does not exist" => "L'arxiu no és pot llegir o no existeix",

    // Zend\Validator\File\Md5
    "File does not match the given md5 hashes" => "L'arxiu no coindideix amb el hash md5 donat",
    "An md5 hash could not be evaluated for the given file" => "El hash md5 no es va poder avaluar per l'arxiu donat",
    "File is not readable or does not exist" => "L'arxiu no és pot llegir o no existeix",

    // Zend\Validator\File\MimeType
    "File has an incorrect mimetype of '%type%'" => "L'arxiu té un mimetype incorrecte del tipus '%type%'",
    "The mimetype could not be detected from the file" => "El mimetype de l'arxiu no s'ha pogut detectar",
    "File is not readable or does not exist" => "L'arxiu no és pot llegir o no existeix",

    // Zend\Validator\File\NotExists
    "File exists" => "L'arxiu existeix",

    // Zend\Validator\File\Sha1
    "File does not match the given sha1 hashes" => "L'arxiu no coindideix amb el hash sha1 donat",
    "A sha1 hash could not be evaluated for the given file" => "El hash sha1 no es va poder avaluar per l'arxiu donat",
    "File is not readable or does not exist" => "L'arxiu no és pot llegir o no existeix",

    // Zend\Validator\File\Size
    "Maximum allowed size for file is '%max%' but '%size%' detected" => "La mida màxima permesa per a l'arxiu és '%max%' però s'ha detectat '%size%'",
    "Minimum expected size for file is '%min%' but '%size%' detected" => "La mida mínima permesa per a l'arxiu és '%max%' però s'ha detectat '%size%'",
    "File is not readable or does not exist" => "L'arxiu no és pot llegir o no existeix",

    // Zend\Validator\File\Upload
    "File '%value%' exceeds the defined ini size" => "L'arxiu '%value%' supera la mida definida inicialment",
    "File '%value%' exceeds the defined form size" => "L'arxiu '%value%' supera la mida definida en el formulari",
    "File '%value%' was only partially uploaded" => "L'arxiu '%value%' s'ha carregat parcialment",
    "File '%value%' was not uploaded" => "L'arxiu '%value%' no s'ha carregat",
    "No temporary directory was found for file '%value%'" => "No s'ha trobat cap directory temporal per al fitxer '%value%'",
    "File '%value%' can't be written" => "L'arxiu '%value%' no és pot escriure",
    "A PHP extension returned an error while uploading the file '%value%'" => "Una extensió PHP ha retornat un error al pujar l'arxiu '%value%'",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "L'arxiu '%value%' s'ha carregat il·legalment. Això podria ser un possible atac",
    "File '%value%' was not found" => "L'arxiu '%value%' no s'ha trobat",
    "Unknown error while uploading file '%value%'" => "Error desconegut en pujar l'arxiu '%value%'",

    // Zend\Validator\File\UploadFile
    "File exceeds the defined ini size" => "L'arxiu supera la mida definida inicialment",
    "File exceeds the defined form size" => "L'arxiu supera la mida definida en el formulari",
    "File was only partially uploaded" => "L'arxiu s'ha carregat parcialment",
    "File was not uploaded" => "L'arxiu no s'ha carregat",
    "No temporary directory was found for file" => "No s'ha trobat cap directory temporal per al fitxer",
    "File can't be written" => "L'arxiu no és pot escriure",
    "A PHP extension returned an error while uploading the file" => "Una extensió PHP ha retornat un error al pujar l'arxiu ",
    "File was illegally uploaded. This could be a possible attack" => "L'arxiu s'ha carregat il·legalment. Això podria ser un possible atac",
    "File was not found" => "L'arxiu no s'ha trobat",
    "Unknown error while uploading file" => "Error desconegut en pujar l'arxiu",

    // Zend\Validator\File\WordCount
    "Too many words, maximum '%max%' are allowed but '%count%' were counted" => "Excés de paraules, màxim '%max%' es permeten però s'han comptat '%count%'",
    "Too few words, minimum '%min%' are expected but '%count%' were counted" => "Falten paraules, mínim '%min%' es permeten però s'han comptat '%count%'",
    "File is not readable or does not exist" => "L'arxiu no és pot llegir o no existeix",

    // Zend\Validator\GreaterThan
    "The input is not greater than '%min%'" => "L'entrada no és més gran que '%min%'",
    "The input is not greater or equal than '%min%'" => "L'entrada no és més gran o igual que '%min%'",

    // Zend\Validator\Hex
    "Invalid type given. String expected" => "Tipus no vàlid donat. S'espera una cadena de text",
    "The input contains non-hexadecimal characters" => "L'entrada conté caràcters no hexadecimals",

    // Zend\Validator\Hostname
    "The input appears to be a DNS hostname but the given punycode notation cannot be decoded" => "L'entrada sembla ser un nom d'amfitrió DNS però la notació punycode donada no pot ser descodificada",
    "Invalid type given. String expected" => "Tipus no vàlid donat. S'espera una cadena de text",
    "The input appears to be a DNS hostname but contains a dash in an invalid position" => "L'entrada sembla ser un nom de host DNS, però conté un guió en una posició no vàlida",
    "The input does not match the expected structure for a DNS hostname" => "L'entrada no conicideix amb l'estructura esperada per a un nom de host DNS",
    "The input appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "L'entrada sembla ser un nom de host DNS però no coincideix amb l'esquema de nom de host pel TLD '%tld%'",
    "The input does not appear to be a valid local network name" => "L'entrada no sembla ser un nom de xarxa local vàlid",
    "The input does not appear to be a valid URI hostname" => "L'entrada no sembla ser un nom de host URI vàlid",
    "The input appears to be an IP address, but IP addresses are not allowed" => "L'entrada sembla ser una adreça IP, però les adreçes OP no estàn permeses",
    "The input appears to be a local network name but local network names are not allowed" => "L'entrada sembla un nom de xarxa local, però els noms de xarxa local no es permeten",
    "The input appears to be a DNS hostname but cannot extract TLD part" => "L'entrada sembla ser un nom de host DNS però no pot extreure la part TLD",
    "The input appears to be a DNS hostname but cannot match TLD against known list" => "L'entrada sembla ser un nom de host DNS però no s'ha trobat una coincidència del TLD amb la llista coneguda",

    // Zend\Validator\Iban
    "Unknown country within the IBAN" => "País desconegut dins l'IBAN",
    "Countries outside the Single Euro Payments Area (SEPA) are not supported" => "Els països no pertanyents a la zona única de pagaments (SEPA) no són compatibles",
    "The input has a false IBAN format" => "L'entrada té un fals format IBAN",
    "The input has failed the IBAN check" => "L'entrada no ha passat la verificació IBAN",

    // Zend\Validator\Identical
    "The two given tokens do not match" => "Els dos tokens donats no coincideixen",
    "No token was provided to match against" => "No s'ha proporcionat cap token per fer la comprovació",

    // Zend\Validator\InArray
    "The input was not found in the haystack" => "L'entrada no s'ha trobat",

    // Zend\Validator\Ip
    "Invalid type given. String expected" => "Tipus no vàlid donat. S'espera una cadena de text",
    "The input does not appear to be a valid IP address" => "L'entrada no sembla ser una adreça IP vàlida",

    // Zend\Validator\IsInstanceOf
    "The input is not an instance of '%className%'" => "L'entrada no és una instància de '%className%'",

    // Zend\Validator\Isbn
    "Invalid type given. String or integer expected" => "Tipus no vàlid donat. S'espera una cadena de text o un enter",
    "The input is not a valid ISBN number" => "L'entrada no és un ISBN vàlid",

    // Zend\Validator\LessThan
    "The input is not less than '%max%'" => "L'entrada no és inferior a '%max%'",
    "The input is not less or equal than '%max%'" => "L'entrada no és menor o igual que '%max%'",

    // Zend\Validator\NotEmpty
    "Value is required and can't be empty" => "El valor és obligatori i no pot estar buit",
    "Invalid type given. String, integer, float, boolean or array expected" => "Tipus no vàlid donat. S'espera una cadena de text, un enter, un nombre de precisió simple, un booleà o un array",

    // Zend\Validator\Regex
    "Invalid type given. String, integer or float expected" => "Tipus no vàlid donat. S'espera una cadena de text, un enter o un nombre de precisió simple",
    "The input does not match against pattern '%pattern%'" => "L'entrada no coincideix amb el patró '%pattern%'",
    "There was an internal error while using the pattern '%pattern%'" => "S'ha produït un error intern al utilitzar el patró '%pattern%'",

    // Zend\Validator\Sitemap\Changefreq
    "The input is not a valid sitemap changefreq" => "L'entrada no és un mapa de lloc changefreq vàlid",
    "Invalid type given. String expected" => "Tipus no vàlid donat. S'espera una cadena de text",

    // Zend\Validator\Sitemap\Lastmod
    "The input is not a valid sitemap lastmod" => "L'entrada no és un mapa de lloc lastmod vàlid",
    "Invalid type given. String expected" => "Tipus no vàlid donat. S'espera una cadena de text",

    // Zend\Validator\Sitemap\Loc
    "The input is not a valid sitemap location" => "L'entrada no és una ubicació del mapa de lloc vàlida",
    "Invalid type given. String expected" => "Tipus no vàlid donat. S'espera una cadena de text",

    // Zend\Validator\Sitemap\Priority
    "The input is not a valid sitemap priority" => "L'entrada no és una prioritat del mapa de lloc vàlida",
    "Invalid type given. Numeric string, integer or float expected" => "Tipus no vàlid donat. S'espera una cadena de text numèrica, un enter o nombre de precisió simple",

    // Zend\Validator\Step
    "Invalid value given. Scalar expected" => "Valor incorrecte donat. S'espera un escalar",
    "The input is not a valid step" => "L'entrada no és un pas vàlid",

    // Zend\Validator\StringLength
    "Invalid type given. String expected" => "Tipus no vàlid donat. S'espera una cadena de text",
    "The input is less than %min% characters long" => "L'entrada és menor que %min% caràcters",
    "The input is more than %max% characters long" => "L'entrada és més que %max% caràcters",

    // Zend\Validator\Uri
    "Invalid type given. String expected" => "Tipus no vàlid donat. S'espera una cadena de text",
    "The input does not appear to be a valid Uri" => "L'entrada no sembla ser un URI vàlid",
);
