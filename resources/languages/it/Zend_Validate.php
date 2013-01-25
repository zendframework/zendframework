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
 * EN-Revision: 09.Sept.2012
 */
return array(
    // Zend_I18n_Validator_Alnum
    "Invalid type given. String, integer or float expected" => "Tipo di dato non valido. Era atteso un dato di tipo string, float o integer",
    "The input contains characters which are non alphabetic and no digits" => "L'input contiene caratteri che non sono alfanumerici",
    "The input is an empty string" => "L'input è una stringa vuota",

    // Zend_I18n_Validator_Alpha
    "Invalid type given. String expected" => "Tipo di dato non valido. Era attesto un dato di tipo string",
    "The input contains non alphabetic characters" => "L'input contiene caratteri non alfabetici",
    "The input is an empty string" => "L'input è una stringa vuota",

    // Zend_I18n_Validator_Float
    "Invalid type given. String, integer or float expected" => "Tipo di dato non valido. Era atteso un dato di tipo string, float o integer",
    "The input does not appear to be a float" => "L'input non sembra essere un dato di tipo float",

    // Zend_I18n_Validator_Int
    "Invalid type given. String or integer expected" => "Tipo di dato non valido. Era atteso un dato di tipo string o integer",
    "The input does not appear to be an integer" => "L'input non sembra essere un intero",

    // Zend_I18n_Validator_PostCode
    "Invalid type given. String or integer expected" => "Tipo di dato non valido. Era atteso un dato di tipo string o integer",
    "The input does not appear to be a postal code" => "L'input non sembra essere un codice postale",
    "An exception has been raised while validating the input" => "Un'eccezione è stata sollevada durante la validazione dell'input",

    // Zend_Validator_Barcode
    "The input failed checksum validation" => "L'input non ha un checksum valido",
    "The input contains invalid characters" => "L'input contiene caratteri non permessi",
    "The input should have a length of %length% characters" => "L'input non ha la lunghezza corretta di %length% caratteri",
    "Invalid type given. String expected" => "Tipo di dato non valido. Era attesto un dato di tipo string",

    // Zend_Validator_Between
    "The input is not between '%min%' and '%max%', inclusively" => "L'input non è compreso tra '%min%' e '%max%', inclusi",
    "The input is not strictly between '%min%' and '%max%'" => "L'input non è strettamente compreso tra '%min%' e '%max%'",

    // Zend_Validator_Callback
    "The input is not valid" => "L'input non è valido",
    "An exception has been raised within the callback" => "Un'eccezione è stata sollevata all'interno della callback",

    // Zend_Validator_CreditCard
    "The input seems to contain an invalid checksum" => "L'input sembra avere un checksum non valido",
    "The input must contain only digits" => "L'input deve contenere solo cifre",
    "Invalid type given. String expected" => "Tipo di dato non valido. Era attesto un dato di tipo string",
    "The input contains an invalid amount of digits" => "L'input contiene un numero non valido di cifre",
    "The input is not from an allowed institute" => "L'input proviene da un istituto non supportato",
    "The input seems to be an invalid creditcard number" => "L'input sembra essere un numero di carta di credito non valido",
    "An exception has been raised while validating the input" => "Un'eccezione è stata sollevada durante la validazione dell'input",

    // Zend_Validator_Csrf
    "The form submitted did not originate from the expected site" => "La form inviata non ha avuto origine dal luogo previsto",

    // Zend_Validator_Date
    "Invalid type given. String, integer, array or DateTime expected" => "Tipo di dato non valido. Era atteso un dato di tipo string, integer, array o DateTime",
    "The input does not appear to be a valid date" => "L'input non sembra essere una data valida",
    "The input does not fit the date format '%format%'" => "L'input non corrisponde al formato data '%format%'",

    // Zend_Validator_DateStep
    "Invalid type given. String, integer, array or DateTime expected" => "Tipo di dato non valido. Era atteso un dato di tipo string, integer, array o DateTime",
    "The input does not appear to be a valid date" => "L'input non sembra essere una data valida",
    "The input is not a valid step" => "L'input non è uno step valido",

    // Zend_Validator_Db_AbstractDb
    "No record matching the input was found" => "Non è stata trovata nessuna riga corrispondente all'input",
    "A record matching the input was found" => "E' già stata trovata una riga corrispondente all'input",

    // Zend_Validator_Digits
    "The input must contain only digits" => "L'input deve contenere solo cifre",
    "The input is an empty string" => "L'input è una stringa vuota",
    "Invalid type given. String, integer or float expected" => "Tipo di dato non valido. Era atteso un dato di tipo string, float o integer",

    // Zend_Validator_EmailAddress
    "Invalid type given. String expected" => "Tipo di dato non valido. Era attesto un dato di tipo string",
    "The input is not a valid email address. Use the basic format local-part@hostname" => "L'input non è un indirizzo email valido nel formato base local-part@hostname",
    "'%hostname%' is not a valid hostname for the email address" => "'%hostname%' non è un hostname valido nell'indirizzo email",
    "'%hostname%' does not appear to have any valid MX or A records for the email address" => "'%hostname%' non sembra avere un record MX o A valido nell'indirizzo email",
    "'%hostname%' is not in a routable network segment. The email address should not be resolved from public network" => "'%hostname%' non è in un segmento di rete instradabile. L'indirizzo email non può essere risolto nella rete pubblica.",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' non può essere validato nel formato dot-atom",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' non può essere validato nel formato quoted-string",
    "'%localPart%' is not a valid local part for the email address" => "'%localPart%' non è una local part valida nell'indirizzo email",
    "The input exceeds the allowed length" => "L'input supera la lunghezza consentita",

    // Zend_Validator_Explode
    "Invalid type given. String expected" => "Tipo di dato non valido. Era attesto un dato di tipo string",

    // Zend_Validator_File_Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "Troppi file, sono consentiti massimo '%max%' file ma ne sono stati passati '%count%'",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "Troppi pochi file, sono attesi minimo '%min%' file ma ne sono stato passati solo '%count%'",

    // Zend_Validator_File_Crc32
    "File '%value%' does not match the given crc32 hashes" => "Il file '%value%' non ha un hash crc32 tra quelli consentiti",
    "A crc32 hash could not be evaluated for the given file" => "L'hash crc32 non può essere calcolato per il file dato",
    "File '%value%' is not readable or does not exist" => "Il file '%value%' non è leggibile o non esiste",

    // Zend_Validator_File_ExcludeExtension
    "File '%value%' has a false extension" => "Il file '%value%' ha un'estensione invalida",
    "File '%value%' is not readable or does not exist" => "Il file '%value%' non è leggibile o non esiste",

    // Zend_Validator_File_Exists
    "File '%value%' does not exist" => "Il file '%value%' non esiste",

    // Zend_Validator_File_Extension
    "File '%value%' has a false extension" => "Il file '%value%' ha un'estensione invalida",
    "File '%value%' is not readable or does not exist" => "Il file '%value%' non è leggibile o non esiste",

    // Zend_Validator_File_FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "I file devono avere in totale una dimensione massima di '%max%' ma è stata rilevata una dimensione di '%size%'",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "I file devono avere in totale una dimensione minima di '%min%' ma è stata rilevata una dimensione di '%size%'",
    "One or more files can not be read" => "Uno o più file non possono essere letti",

    // Zend_Validator_File_Hash
    "File '%value%' does not match the given hashes" => "I file '%value%' non corrisponde agli hash dati",
    "A hash could not be evaluated for the given file" => "Un hash non può essere valutato per il file dato",
    "File '%value%' is not readable or does not exist" => "Il file '%value%' non è leggibile o non esiste",

    // Zend_Validator_File_ImageSize
    "Maximum allowed width for image '%value%' should be '%maxwidth%' but '%width%' detected" => "La larghezza massima consentita per l'immagine '%value%' è '%maxwidth%' ma è stata rilevata una larghezza di '%width%'",
    "Minimum expected width for image '%value%' should be '%minwidth%' but '%width%' detected" => "La larghezza minima consentita per l'immagine '%value%' è '%minwidth%' ma è stata rilevata una larghezza di '%width%'",
    "Maximum allowed height for image '%value%' should be '%maxheight%' but '%height%' detected" => "L'altezza massima consentita per l'immagine '%value%' è '%maxheight%' ma è stata rilevata un'altezza di '%height%'",
    "Minimum expected height for image '%value%' should be '%minheight%' but '%height%' detected" => "L'altezza minima consentita per l'immagine '%value%' è '%minheight%' ma è stata rilevata un'altezza di '%height%'",
    "The size of image '%value%' could not be detected" => "Le dimensioni dell'immagine '%value%' non possono essere rilevate",
    "File '%value%' is not readable or does not exist" => "Il file '%value%' non è leggibile o non esiste",

    // Zend_Validator_File_IsCompressed
    "File '%value%' is not compressed, '%type%' detected" => "Il file '%value%' non è un file compresso, ma un file di tipo '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Il mimetype del file '%value%' non può essere rilevato",
    "File '%value%' is not readable or does not exist" => "Il file '%value%' non è leggibile o non esiste",

    // Zend_Validator_File_IsImage
    "File '%value%' is no image, '%type%' detected" => "Il file '%value%' non è un'immagine, ma un file di tipo '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Il mimetype del file '%value%' non può essere rilevato",
    "File '%value%' is not readable or does not exist" => "Il file '%value%' non è leggibile o non esiste",

    // Zend_Validator_File_Md5
    "File '%value%' does not match the given md5 hashes" => "Il file '%value%' non corrisponde agli hash md5 dati",
    "A md5 hash could not be evaluated for the given file" => "Un hash md5 non può essere valutato per il file dato",
    "File '%value%' is not readable or does not exist" => "Il file '%value%' non è leggibile o non esiste",

    // Zend_Validator_File_MimeType
    "File '%value%' has a false mimetype of '%type%'" => "Il file '%value%' ha un mimetype invalido: '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Il mimetype del file '%value%' non può essere rilevato",
    "File '%value%' is not readable or does not exist" => "Il file '%value%' non è leggibile o non esiste",

    // Zend_Validator_File_NotExists
    "File '%value%' exists" => "Il file '%value%' esiste già",

    // Zend_Validator_File_Sha1
    "File '%value%' does not match the given sha1 hashes" => "Il file '%value%' non corrisponde agli hash sha1 dati",
    "A sha1 hash could not be evaluated for the given file" => "Un hash sha1 non può essere valutato per il file dato",
    "File '%value%' is not readable or does not exist" => "Il file '%value%' non è leggibile o non esiste",

    // Zend_Validator_File_Size
    "Maximum allowed size for file '%value%' is '%max%' but '%size%' detected" => "La dimensione massima consentita per il file '%value%' è '%max%' ma è stata rilevata una dimensione di '%size%'",
    "Minimum expected size for file '%value%' is '%min%' but '%size%' detected" => "La dimensione minima consentita per il file '%value%' è '%min%' ma è stata rilevata una dimensione di '%size%'",
    "File '%value%' is not readable or does not exist" => "Il file '%value%' non è leggibile o non esiste",

    // Zend_Validator_File_Upload
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

    // Zend_Validator_File_WordCount
    "Too much words, maximum '%max%' are allowed but '%count%' were counted" => "Il file contiene troppe parole, ne sono consentite massimo '%max%' ma ne sono state contate '%count%'",
    "Too less words, minimum '%min%' are expected but '%count%' were counted" => "Il file contiene troppe poche parole, ne sono consentite minimo '%min%' ma ne sono state contate '%count%'",
    "File '%value%' is not readable or does not exist" => "Il file '%value%' non è leggibile o non esiste",

    // Zend_Validator_GreaterThan
    "The input is not greater than '%min%'" => "L'input non è maggiore di '%min%'",
    "The input is not greater or equal than '%min%'" => "L'input non è maggiore o uguale a '%min%'",

    // Zend_Validator_Hex
    "Invalid type given. String expected" => "Tipo di dato non valido. Era attesto un dato di tipo string",
    "The input contains non-hexadecimal characters" => "L'input non è composto solo da caratteri esadecimali",

    // Zend_Validator_Hostname
    "The input appears to be a DNS hostname but the given punycode notation cannot be decoded" => "L'input sembra essere un hostname DNS ma la notazione punycode data non può essere decodificata",
    "Invalid type given. String expected" => "Tipo di dato non valido. Era attesto un dato di tipo string",
    "The input appears to be a DNS hostname but contains a dash in an invalid position" => "L'input sembra essere un hostname DNS ma contiene un trattino in una posizione non valida",
    "The input does not match the expected structure for a DNS hostname" => "L'input non sembra rispettare la struttura attesa per un hostname DNS",
    "The input appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "L'input sembra essere un hostname DNS ma non rispetta lo schema per il TLD '%tld%'",
    "The input does not appear to be a valid local network name" => "L'input non sembra essere un nome valido per una rete locale",
    "The input does not appear to be a valid URI hostname" => "L'input non sembra essere un hostname URI valido",
    "The input appears to be an IP address, but IP addresses are not allowed" => "L'input sembra essere un indirizzo IP, ma gli indirizzi IP non sono consentiti",
    "The input appears to be a local network name but local network names are not allowed" => "L'input sembra essere un nome di una rete locale e queste non sono consentite",
    "The input appears to be a DNS hostname but cannot extract TLD part" => "L'input sembra essere un hostname DNS ma non è possibile estrarne il TLD",
    "The input appears to be a DNS hostname but cannot match TLD against known list" => "L'input sembra essere un hostname DNS ma il suo TLD è sconosciuto",

    // Zend_Validator_Iban
    "Unknown country within the IBAN" => "Codice paese sconosciuto nell'IBAN fornito",
    "Countries outside the Single Euro Payments Area (SEPA) are not supported" => "I paesi fuori dall'Area unica dei pagamenti in euro (SEPA) non sono supportati",
    "The input has a false IBAN format" => "L'input ha un formato IBAN non valido",
    "The input has failed the IBAN check" => "L'input ha fallito il controllo IBAN",

    // Zend_Validator_Identical
    "The two given tokens do not match" => "I due token dati non corrispondono",
    "No token was provided to match against" => "Non è stato dato nessun token per il confronto",

    // Zend_Validator_InArray
    "The input was not found in the haystack" => "L'input non è stato trovato nell'array",

    // Zend_Validator_Ip
    "Invalid type given. String expected" => "Tipo di dato non valido. Era attesto un dato di tipo string",
    "The input does not appear to be a valid IP address" => "L'input non sembra essere un indirizzo IP valido",

    // Zend_Validator_Isbn
    "Invalid type given. String or integer expected" => "Tipo di dato non valido. Era atteso un dato di tipo string o integer",
    "The input is not a valid ISBN number" => "L'input non è un numero ISBN valido",

    // Zend_Validator_LessThan
    "The input is not less than '%max%'" => "L'input non è minore di '%max%'",
    "The input is not less or equal than '%max%'" => "L'input non è minore o uguale a '%max%'",

    // Zend_Validator_NotEmpty
    "Value is required and can't be empty" => "Il dato è richiesto e non può essere vuoto",
    "Invalid type given. String, integer, float, boolean or array expected" => "Tipo di dato non valido. Era atteso un dato di tipo string, integer, float, boolean o array",

    // Zend_Validator_Regex
    "Invalid type given. String, integer or float expected" => "Tipo di dato non valido. Era atteso un dato di tipo string, float o integer",
    "The input does not match against pattern '%pattern%'" => "L'input non corrisponde al pattern '%pattern%'",
    "There was an internal error while using the pattern '%pattern%'" => "Si è verificato un errore interno usando il pattern '%pattern%'",

    // Zend_Validator_Sitemap_Changefreq
    "The input is not a valid sitemap changefreq" => "L'input non è una sitemap changefreq valida",
    "Invalid type given. String expected" => "Tipo di dato non valido. Era attesto un dato di tipo string",

    // Zend_Validator_Sitemap_Lastmod
    "The input is not a valid sitemap lastmod" => "L'input non è un sitemap lastmod valido",
    "Invalid type given. String expected" => "Tipo di dato non valido. Era attesto un dato di tipo string",

    // Zend_Validator_Sitemap_Loc
    "The input is not a valid sitemap location" => "L'input non è una sitemap location valida",
    "Invalid type given. String expected" => "Tipo di dato non valido. Era attesto un dato di tipo string",

    // Zend_Validator_Sitemap_Priority
    "The input is not a valid sitemap priority" => "L'input non è una sitemap priority valida",
    "Invalid type given. Numeric string, integer or float expected" => "Tipo di dato non valido. Era atteso un dato di tipo stringa numerica, float o integer",

    // Zend_Validator_Step
    "Invalid value given. Scalar expected" => "Tipo di dato non valido. Era attesto un dato di tipo scalare",
    "The input is not a valid step" => "L'input non è uno step valido",

    // Zend_Validator_StringLength
    "Invalid type given. String expected" => "Tipo di dato non valido. Era attesto un dato di tipo string",
    "The input is less than %min% characters long" => "L'input è meno lungo di %min% caratteri",
    "The input is more than %max% characters long" => "L'input è più lungo di %max% caratteri",

    // Zend_Validator_Uri
    "Invalid type given. String expected" => "Tipo di dato non valido. Era attesto un dato di tipo string",
    "The input does not appear to be a valid Uri" => "L'input non sembra essere un indirizzo URI valido",
);
