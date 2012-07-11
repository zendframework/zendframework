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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * EN-Revision: 22668
 */
return array(
    // Zend_Validate_Alnum
    "Invalid type given. String, integer or float expected" => "Tipo di dato non valido. Era atteso un dato di tipo string, float o integer",
    "'%value%' contains characters which are non alphabetic and no digits" => "'%value%' contiene caratteri che non sono alfanumerici",
    "'%value%' is an empty string" => "'%value%' è una stringa vuota",

    // Zend_Validate_Alpha
    "Invalid type given. String expected" => "Tipo di dato non valido. Era attesto un dato di tipo string",
    "'%value%' contains non alphabetic characters" => "'%value%' contiene caratteri non alfabetici",
    "'%value%' is an empty string" => "'%value%' è una stringa vuota",

    // Zend_Validate_Barcode
    "'%value%' failed checksum validation" => "'%value%' non ha un checksum valido",
    "'%value%' contains invalid characters" => "'%value%' contiene caratteri non permessi",
    "'%value%' should have a length of %length% characters" => "'%value%' non ha la lunghezza corretta di %length% caratteri",
    "Invalid type given. String expected" => "Tipo di dato non valido. Era attesto un dato di tipo string",

    // Zend_Validate_Between
    "'%value%' is not between '%min%' and '%max%', inclusively" => "'%value%' non è compreso tra '%min%' e '%max%', inclusi",
    "'%value%' is not strictly between '%min%' and '%max%'" => "'%value%' non è strettamente compreso tra '%min%' e '%max%'",

    // Zend_Validate_Callback
    "'%value%' is not valid" => "'%value%' non è valido",
    "An exception has been raised within the callback" => "Un'eccezione è stata sollevata all'interno della callback",

    // Zend_Validate_Ccnum
    "'%value%' must contain between 13 and 19 digits" => "'%value%' deve contenere tra 13 e 19 cifre",
    "Luhn algorithm (mod-10 checksum) failed on '%value%'" => "L'algoritmo di Luhn (checksum mod-10) è fallito su '%value%'",

    // Zend_Validate_CreditCard
    "'%value%' seems to contain an invalid checksum" => "'%value%' sembra avere un checksum non valido",
    "'%value%' must contain only digits" => "'%value%' deve contenere solo cifre",
    "Invalid type given. String expected" => "Tipo di dato non valido. Era attesto un dato di tipo string",
    "'%value%' contains an invalid amount of digits" => "'%value%' contiene un numero non valido di cifre",
    "'%value%' is not from an allowed institute" => "'%value%' proviene da un istituto non supportato",
    "'%value%' seems to be an invalid creditcard number" => "'%value%' sembra essere un numero di carta di credito non valido",
    "An exception has been raised while validating '%value%'" => "Un'eccezione è stata sollevada durante la validazione di '%value%'",

    // Zend_Validate_Date
    "Invalid type given. String, integer, array or Zend_Date expected" => "Tipo di dato non valido. Era atteso un dato di tipo string, integer, array o Zend_Date",
    "'%value%' does not appear to be a valid date" => "'%value%' non sembra essere una data valida",
    "'%value%' does not fit the date format '%format%'" => "'%value%' non corrisponde al formato data '%format%'",

    // Zend_Validate_Db_Abstract
    "No record matching '%value%' was found" => "Non è stato trovata nessuna riga con valore '%value%'",
    "A record matching '%value%' was found" => "E' già stata trovata una riga con valore '%value%'",

    // Zend_Validate_Digits
    "Invalid type given. String, integer or float expected" => "Tipo di dato non valido. Era atteso un dato di tipo string, float o integer",
    "'%value%' must contain only digits" => "'%value%' deve contenere solo cifre",
    "'%value%' is an empty string" => "'%value%' è una stringa vuota",

    // Zend_Validate_EmailAddress
    "Invalid type given. String expected" => "Tipo di dato non valido. Era attesto un dato di tipo string",
    "'%value%' is not a valid email address in the basic format local-part@hostname" => "'%value%' non è un indirizzo email valido nel formato base local-part@hostname",
    "'%hostname%' is not a valid hostname for email address '%value%'" => "'%hostname%' non è un hostname valido nell'indirizzo email '%value%'",
    "'%hostname%' does not appear to have a valid MX record for the email address '%value%'" => "'%hostname%' non sembra avere un record MX DNS valido nell'indirizzo email %value%'",
    "'%hostname%' is not in a routable network segment. The email address '%value%' should not be resolved from public network." => "'%hostname%' non è in un segmento di rete routabile. L'indirizzo email '%value%' non può essere risolto nella rete pubblica.",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' non può essere validato nel formato dot-atom",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' non può essere validato nel formato quoted-string",
    "'%localPart%' is not a valid local part for email address '%value%'" => "'%localPart%' non è una local part valida nell'indirizzo email '%value%'",
    "'%value%' exceeds the allowed length" => "'%value%' supera la lunghezza consentita",

    // Zend_Validate_File_Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "Troppi file, sono consentiti massimo '%max%' file ma ne sono stati passati '%count%'",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "Troppi pochi file, sono attesi minimo '%min%' file ma ne sono stato passati solo '%count%'",

    // Zend_Validate_File_Crc32
    "File '%value%' does not match the given crc32 hashes" => "Il file '%value%' non ha un hash crc32 tra quelli consentiti",
    "A crc32 hash could not be evaluated for the given file" => "L'hash crc32 non può essere calcolato per il file dato",
    "File '%value%' is not readable or does not exist" => "Il file '%value%' non è leggibile o non esiste",

    // Zend_Validate_File_ExcludeExtension
    "File '%value%' has a false extension" => "Il file '%value%' ha un'estensione invalida",
    "File '%value%' is not readable or does not exist" => "Il file '%value%' non è leggibile o non esiste",

    // Zend_Validate_File_ExcludeMimeType
    "File '%value%' has a false mimetype of '%type%'" => " Il file '%value%' ha un mimetype non consentito: '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Il mimetype del file '%value%' non può essere individuato",
    "File '%value%' is not readable or does not exist" => "Il file '%value%' non è leggibile o non esiste",

    // Zend_Validate_File_Exists
    "File '%value%' does not exist" => "Il file '%value%' non esiste",

    // Zend_Validate_File_Extension
    "File '%value%' has a false extension" => "Il file '%value%' ha un'estensione invalida",
    "File '%value%' is not readable or does not exist" => "Il file '%value%' non è leggibile o non esiste",

    // Zend_Validate_File_FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "I file devono avere in totale una dimensione massima di '%max%' ma è stata rilevata una dimensione di '%size%'",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "I file devono avere in totale una dimensione minima di '%min%' ma è stata rilevata una dimensione di '%size%'",
    "One or more files can not be read" => "Uno o più file non possono essere letti",

    // Zend_Validate_File_Hash
    "File '%value%' does not match the given hashes" => "I file '%value%' non corrisponde agli hash dati",
    "A hash could not be evaluated for the given file" => "Un hash non può essere valutato per il file dato",
    "File '%value%' is not readable or does not exist" => "Il file '%value%' non è leggibile o non esiste",

    // Zend_Validate_File_ImageSize
    "Maximum allowed width for image '%value%' should be '%maxwidth%' but '%width%' detected" => "La larghezza massima consentita per l'immagine '%value%' è '%maxwidth%' ma è stata rilevata una larghezza di '%width%'",
    "Minimum expected width for image '%value%' should be '%minwidth%' but '%width%' detected" => "La larghezza minima consentita per l'immagine '%value%' è '%minwidth%' ma è stata rilevata una larghezza di '%width%'",
    "Maximum allowed height for image '%value%' should be '%maxheight%' but '%height%' detected" => "L'altezza massima consentita per l'immagine '%value%' è '%maxheight%' ma è stata rilevata un'altezza di '%height%'",
    "Minimum expected height for image '%value%' should be '%minheight%' but '%height%' detected" => "L'altezza minima consentita per l'immagine '%value%' è '%minheight%' ma è stata rilevata un'altezza di '%height%'",
    "The size of image '%value%' could not be detected" => "Le dimensioni dell'immagine '%value%' non possono essere rilevate",
    "File '%value%' is not readable or does not exist" => "Il file '%value%' non è leggibile o non esiste",

    // Zend_Validate_File_IsCompressed
    "File '%value%' is not compressed, '%type%' detected" => "Il file '%value%' non è un file compresso, ma un file di tipo '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Il mimetype del file '%value%' non può essere rilevato",
    "File '%value%' is not readable or does not exist" => "Il file '%value%' non è leggibile o non esiste",

    // Zend_Validate_File_IsImage
    "File '%value%' is no image, '%type%' detected" => "Il file '%value%' non è un'immagine, ma un file di tipo '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Il mimetype del file '%value%' non può essere rilevato",
    "File '%value%' is not readable or does not exist" => "Il file '%value%' non è leggibile o non esiste",

    // Zend_Validate_File_Md5
    "File '%value%' does not match the given md5 hashes" => "Il file '%value%' non corrisponde agli hash md5 dati",
    "A md5 hash could not be evaluated for the given file" => "Un hash md5 non può essere valutato per il file dato",
    "File '%value%' is not readable or does not exist" => "Il file '%value%' non è leggibile o non esiste",

    // Zend_Validate_File_MimeType
    "File '%value%' has a false mimetype of '%type%'" => "Il file '%value%' ha un mimetype invalido: '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Il mimetype del file '%value%' non può essere rilevato",
    "File '%value%' is not readable or does not exist" => "Il file '%value%' non è leggibile o non esiste",

    // Zend_Validate_File_NotExists
    "File '%value%' exists" => "Il file '%value%' esiste già",

    // Zend_Validate_File_Sha1
    "File '%value%' does not match the given sha1 hashes" => "Il file '%value%' non corrisponde agli hash sha1 dati",
    "A sha1 hash could not be evaluated for the given file" => "Un hash sha1 non può essere valutato per il file dato",
    "File '%value%' is not readable or does not exist" => "Il file '%value%' non è leggibile o non esiste",

    // Zend_Validate_File_Size
    "Maximum allowed size for file '%value%' is '%max%' but '%size%' detected" => "La dimensione massima consentita per il file '%value%' è '%max%' ma è stata rilevata una dimensione di '%size%'",
    "Minimum expected size for file '%value%' is '%min%' but '%size%' detected" => "La dimensione minima consentita per il file '%value%' è '%min%' ma è stata rilevata una dimensione di '%size%'",
    "File '%value%' is not readable or does not exist" => "Il file '%value%' non è leggibile o non esiste",

    // Zend_Validate_File_Upload
    "File '%value%' exceeds the defined ini size" => "Il file '%value%' eccede la dimensione definita nell'ini",
    "File '%value%' exceeds the defined form size" => "Il file '%value%' eccede la dimensione definita nella form",
    "File '%value%' was only partially uploaded" => "Il file '%value%' è stato caricato solo parzialmente",
    "File '%value%' was not uploaded" => "Il file '%value%' non è stato caricato",
    "No temporary directory was found for file '%value%'" => "Non è stata trovata una directory temporanea per il file '%value%'",
    "File '%value%' can't be written" => "Il file '%value%' non può essere scritto",
    "A PHP extension returned an error while uploading the file '%value%'" => "Un'estensione di PHP ha generato un errore durante il caricamento del file '%value%'",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "Il file '%value%' è stato caricato irregolarmente. Potrebbe trattarsi di un attacco",
    "File '%value%' was not found" => "Il file '%value%' non è stato trovato",
    "Unknown error while uploading file '%value%'" => "Errore sconosciuto durante il caricamento del file '%value%'",

    // Zend_Validate_File_WordCount
    "Too much words, maximum '%max%' are allowed but '%count%' were counted" => "Il file contiene troppe parole, ne sono consentite massimo '%max%' ma ne sono state contate '%count%'",
    "Too less words, minimum '%min%' are expected but '%count%' were counted" => "Il file contiene troppe poche parole, ne sono consentite minimo '%min%' ma ne sono state contate '%count%'",
    "File '%value%' is not readable or does not exist" => "Il file '%value%' non è leggibile o non esiste",

    // Zend_Validate_Float
    "Invalid type given. String, integer or float expected" => "Tipo di dato non valido. Era atteso un dato di tipo string, float o integer",
    "'%value%' does not appear to be a float" => "'%value%' non sembra essere un dato di tipo float",

    // Zend_Validate_GreaterThan
    "'%value%' is not greater than '%min%'" => "'%value%' non è maggiore di '%min%'",

    // Zend_Validate_Hex
    "Invalid type given. String expected" => "Tipo di dato non valido. Era attesto un dato di tipo string",
    "'%value%' has not only hexadecimal digit characters" => "'%value%' non è composto solo da caratteri esadecimali",

    // Zend_Validate_Hostname
    "Invalid type given. String expected" => "Tipo di dato non valido. Era attesto un dato di tipo string",
    "'%value%' appears to be an IP address, but IP addresses are not allowed" => "'%value%' sembra essere un indirizzo IP, ma gli indirizzi IP non sono consentiti",
    "'%value%' appears to be a DNS hostname but cannot match TLD against known list" => "'%value%' sembra essere un hostname DNS ma il suo TLD è sconosciuto",
    "'%value%' appears to be a DNS hostname but contains a dash in an invalid position" => "'%value%' sembra essere un hostname DNS ma contiene un trattino in una posizione non valida",
    "'%value%' appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "'%value%' sembra essere un hostname DNS ma non rispetta lo schema per il TLD '%tld%'",
    "'%value%' appears to be a DNS hostname but cannot extract TLD part" => "'%value%' sembra essere un hostname DNS ma non è possibile estrarne il TLD",
    "'%value%' does not match the expected structure for a DNS hostname" => "'%value%' non sembra rispettare la struttura attesa per un hostname DNS",
    "'%value%' does not appear to be a valid local network name" => "'%value%' non sembra essere un nome valido per una rete locale",
    "'%value%' appears to be a local network name but local network names are not allowed" => "'%value%' sembra essere un nome di una rete locale e queste non sono consentite",
    "'%value%' appears to be a DNS hostname but the given punycode notation cannot be decoded" => "'%value%' sembra essere un hostname DNS ma la notazione punycode data non può essere decodificata",
    "'%value%' does not appear to be a valid URI hostname" => "'%value%' non sembra essere un hostname URI valido",
    
    // Zend_Validate_Iban
    "Unknown country within the IBAN '%value%'" => "Codice paese sconosciuto con l'IBAN '%value%'",
    "'%value%' has a false IBAN format" => "'%value%' ha un formato IBAN non valido",
    "'%value%' has failed the IBAN check" => "'%value%' ha fallito il controllo IBAN",

    // Zend_Validate_Identical
    "The two given tokens do not match" => "I due token dati non corrispondono",
    "No token was provided to match against" => "Non è stato dato nessun token per il confronto",

    // Zend_Validate_InArray
    "'%value%' was not found in the haystack" => "'%value%' non è stato trovato nell'array",

    // Zend_Validate_Int
    "Invalid type given. String or integer expected" => "Tipo di dato non valido. Era atteso un dato di tipo string o integer",
    "'%value%' does not appear to be an integer" => "'%value%' non sembra essere un intero",

    // Zend_Validate_Ip
    "Invalid type given. String expected" => "Tipo di dato non valido. Era attesto un dato di tipo string",
    "'%value%' does not appear to be a valid IP address" => "'%value%' non sembra essere un indirizzo IP valido",

    // Zend_Validate_Isbn
    "Invalid type given. String or integer expected" => "Tipo di dato non valido. Era atteso un dato di tipo string o integer",
    "'%value%' is not a valid ISBN number" => "'%value%' non è un numero ISBN valido",

    // Zend_Validate_LessThan
    "'%value%' is not less than '%max%'" => "'%value%' non è minore di '%max%'",

    // Zend_Validate_NotEmpty
    "Invalid type given. String, integer, float, boolean or array expected" => "Tipo di dato non valido. Era atteso un dato di tipo string, integer, float, boolean o array",
    "Value is required and can't be empty" => "Il dato è richiesto e non può essere vuoto",

    // Zend_Validate_PostCode
    "Invalid type given. String or integer expected" => "Tipo di dato non valido. Era atteso un dato di tipo string o integer",
    "'%value%' does not appear to be a postal code" => "'%value%' non sembra essere un codice postale",

    // Zend_Validate_Regex
    "Invalid type given. String, integer or float expected" => "Tipo di dato non valido. Era atteso un dato di tipo string, float o integer",
    "'%value%' does not match against pattern '%pattern%'" => "'%value%' non corrisponde al pattern '%pattern%'",
    "There was an internal error while using the pattern '%pattern%'" => "Si è verificato un errore interno usando il pattern '%pattern%'",

    // Zend_Validate_Sitemap_Changefreq
    "'%value%' is not a valid sitemap changefreq" => "'%value%' non è una sitemap changefreq valida",
    "Invalid type given. String expected" => "Tipo di dato non valido. Era attesto un dato di tipo string",

    // Zend_Validate_Sitemap_Lastmod
    "'%value%' is not a valid sitemap lastmod" => "'%value%' non è un sitemap lastmod valido",
    "Invalid type given. String expected" => "Tipo di dato non valido. Era attesto un dato di tipo string",

    // Zend_Validate_Sitemap_Loc
    "'%value%' is not a valid sitemap location" => "'%value%' non è una sitemap location valida",
    "Invalid type given. String expected" => "Tipo di dato non valido. Era attesto un dato di tipo string",

    // Zend_Validate_Sitemap_Priority
    "'%value%' is not a valid sitemap priority" => "'%value%' non è una sitemap priority valida",
    "Invalid type given. Numeric string, integer or float expected" => "Tipo di dato non valido. Era atteso un dato di tipo stringa numerica, float o integer",

    // Zend_Validate_StringLength
    "Invalid type given. String expected" => "Tipo di dato non valido. Era attesto un dato di tipo string",
    "'%value%' is less than %min% characters long" => "'%value%' è meno lungo di %min% caratteri",
    "'%value%' is more than %max% characters long" => "'%value%' è più lungo di %max% caratteri",
);
