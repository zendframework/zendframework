<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * FR-Revision: 06.June.2013
 */
return array(
    // Zend\I18n\Validator\Alnum
    "Invalid type given. String, integer or float expected" => "Type invalide. Chaîne, entier ou flottant attendu",
    "The input contains characters which are non alphabetic and no digits" => "L'entrée contient des caractères non alphabétiques et non numériques",
    "The input is an empty string" => "L'entrée est une chaîne vide",

    // Zend\I18n\Validator\Alpha
    "Invalid type given. String expected" => "Type invalide. Chaîne attendue",
    "The input contains non alphabetic characters" => "L'entrée contient des caractères non alphabétiques",
    "The input is an empty string" => "L'entrée est une chaîne vide",

    // Zend\I18n\Validator\Float
    "Invalid type given. String, integer or float expected" => "Type invalide. Chaîne, entier ou flottant attendu",
    "The input does not appear to be a float" => "L'entrée n'est pas un nombre flottant",

    // Zend\I18n\Validator\Int
    "Invalid type given. String or integer expected" => "Type invalide. Chaîne ou entier attendu",
    "The input does not appear to be an integer" => "L'entrée n'est pas un entier",

    // Zend\I18n\Validator\PostCode
    "Invalid type given. String or integer expected" => "Type invalid. Chaîne ou entier attendu",
    "The input does not appear to be a postal code" => "L'entrée ne semble pas être un code postal valide",
    "An exception has been raised while validating the input" => "Une exception a été levée lors de la validation de l'entrée",

    // Zend\Validator\Barcode
    "The input failed checksum validation" => "L'entrée n'a pas passé la validation de la somme de contrôle",
    "The input contains invalid characters" => "L'entrée contient des caractères invalides",
    "The input should have a length of %length% characters" => "L'entrée devrait contenir %length% caractères",
    "Invalid type given. String expected" => "Type invalide. Chaîne attendue",

    // Zend\Validator\Between
    "The input is not between '%min%' and '%max%', inclusively" => "L'entrée n'est pas comprise entre '%min%' et '%max%', inclusivement",
    "The input is not strictly between '%min%' and '%max%'" => "L'entrée n'est pas strictement comprise entre '%min%' et '%max%'",

    // Zend\Validator\Callback
    "The input is not valid" => "L'entrée n'est pas valide",
    "An exception has been raised within the callback" => "Une exception a été levée dans la fonction de rappel",

    // Zend\Validator\CreditCard
    "The input seems to contain an invalid checksum" => "L'entrée semble contenir une somme de contrôle invalide",
    "The input must contain only digits" => "L'entrée ne doit contenir que des chiffres",
    "Invalid type given. String expected" => "Type invalide. Chaîne attendue",
    "The input contains an invalid amount of digits" => "L'entrée contient un nombre invalide de chiffres",
    "The input is not from an allowed institute" => "L'entrée ne provient pas d'une institution autorisée",
    "The input seems to be an invalid creditcard number" => "L'entrée semble être un numéro de carte bancaire invalide",
    "An exception has been raised while validating the input" => "Une exception a été levée lors de la validation de l'entrée",

    // Zend\Validator\Csrf
    "The form submitted did not originate from the expected site" => "Le formulaire ne provient pas du site attendu",

    // Zend\Validator\Date
    "Invalid type given. String, integer, array or DateTime expected" => "Type invalide. Chaîne, entier, tableau ou DateTime attendu",
    "The input does not appear to be a valid date" => "L'entrée ne semble pas être une date valide",
    "The input does not fit the date format '%format%'" => "L'entrée ne correspond pas au format '%format%'",

    // Zend\Validator\DateStep
    "Invalid type given. String, integer, array or DateTime expected" => "Entrée invalide. Chaîne, entier, tableau ou DateTime attendu",
    "The input does not appear to be a valid date" => "L'entrée ne semble pas être une date valide",
    "The input is not a valid step" => "L'entrée n'est pas une step valide",

    // Zend\Validator\Db\AbstractDb
    "No record matching the input was found" => "Aucun enregistrement trouvé",
    "A record matching the input was found" => "Un enregistrement a été trouvé",

    // Zend\Validator\Digits
    "The input must contain only digits" => "L'entrée ne doit contenir que des chiffres",
    "The input is an empty string" => "L'entrée est une chaîne vide",
    "Invalid type given. String, integer or float expected" => "Type invalide. Chaîne, entier ou flottant attendu",

    // Zend\Validator\EmailAddress
    "Invalid type given. String expected" => "Type invalide. Chaîne attendue",
    "The input is not a valid email address. Use the basic format local-part@hostname" => "L'entrée n'est pas une adresse email valide. Utilisez le format local-part@hostname",
    "'%hostname%' is not a valid hostname for the email address" => "'%hostname%' n'est pas un nom d'hôte valide pour l'adresse email",
    "'%hostname%' does not appear to have any valid MX or A records for the email address" => "'%hostname%' ne semble pas avoir d'enregistrement MX valide pour l'adresse email",
    "'%hostname%' is not in a routable network segment. The email address should not be resolved from public network" => "'%hostname%' n'est pas dans un segment réseau routable. L'adresse email ne devrait pas être résolue depuis un réseau public.",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' ne correspond pas au format dot-atom",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' ne correspond pas au format quoted-string",
    "'%localPart%' is not a valid local part for the email address" => "'%localPart%' n'est pas une partie locale valide pour l'adresse email",
    "The input exceeds the allowed length" => "L'entrée dépasse la taille autorisée",

    // Zend\Validator\Explode
    "Invalid type given. String expected" => "Type invalide. Chaîne attendue",

    // Zend\Validator\File\Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "Trop de fichiers. '%max%' sont autorisés au maximum, mais '%count%' reçu(s)",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "Trop peu de fichiers. '%min%' sont attendus, mais '%count%' reçu(s)",

    // Zend\Validator\File\Crc32
    "File '%value%' does not match the given crc32 hashes" => "Le fichier '%value%' ne correspond pas aux sommes de contrôle CRC32 données",
    "A crc32 hash could not be evaluated for the given file" => "Une somme de contrôle CRC32 n'a pas pu être calculée pour le fichier",
    "File '%value%' is not readable or does not exist" => "Le fichier '%value%' n'est pas lisible ou n'existe pas",

    // Zend\Validator\File\ExcludeExtension
    "File '%value%' has a false extension" => "Le fichier '%value%' a une mauvaise extension",
    "File '%value%' is not readable or does not exist" => "Le fichier '%value%' n'est pas lisible ou n'existe pas",

    // Zend\Validator\File\Exists
    "File '%value%' does not exist" => "Le fichier '%value%' n'existe pas",

    // Zend\Validator\File\Extension
    "File '%value%' has a false extension" => "Le fichier '%value%' a une mauvaise extension",
    "File '%value%' is not readable or does not exist" => "Le fichier '%value%' n'est pas lisible ou n'existe pas",

    // Zend\Validator\File\FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "Tous les fichiers devraient avoir une taille maximale de '%max%' mais une taille de '%size%' a été détectée",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "Tous les fichiers devraient avoir une taille minimale de '%max%' mais une taille de '%size%' a été détectée",
    "One or more files can not be read" => "Un ou plusieurs fichiers ne peut pas être lu",

    // Zend\Validator\File\Hash
    "File '%value%' does not match the given hashes" => "Le fichier '%value%' ne correspond pas aux sommes de contrôle données",
    "A hash could not be evaluated for the given file" => "Une somme de contrôle n'a pas pu être calculée pour le fichier",
    "File '%value%' is not readable or does not exist" => "Le fichier '%value%' n'est pas lisible ou n'existe pas",

    // Zend\Validator\File\ImageSize
    "Maximum allowed width for image '%value%' should be '%maxwidth%' but '%width%' detected" => "La largeur maximale pour l'image '%value%' devrait être '%maxwidth%', mais '%width%' détecté",
    "Minimum expected width for image '%value%' should be '%minwidth%' but '%width%' detected" => "La largeur minimale pour l'image '%value%' devrait être '%minwidth%', mais '%width%' détecté",
    "Maximum allowed height for image '%value%' should be '%maxheight%' but '%height%' detected" => "La hauteur maximale pour l'image '%value%' devrait être '%maxheight%', mais '%height%' détecté",
    "Minimum expected height for image '%value%' should be '%minheight%' but '%height%' detected" => "La hauteur maximale pour l'image '%value%' devrait être '%minheight%', mais '%height%' détecté",
    "The size of image '%value%' could not be detected" => "La taille de l'image '%value%' n'a pas pu être détectée",
    "File '%value%' is not readable or does not exist" => "Le fichier '%value%' n'est pas lisible ou n'existe pas",

    // Zend\Validator\File\IsCompressed
    "File '%value%' is not compressed, '%type%' detected" => "Le fichier '%value%' n'est pas compressé, '%type%' détecté",
    "The mimetype of file '%value%' could not be detected" => "Le type MIME du fichier '%value%' n'a pas pu être détecté",
    "File '%value%' is not readable or does not exist" => "Le fichier '%value%' n'est pas lisible ou n'existe pas",

    // Zend\Validator\File\IsImage
    "File '%value%' is no image, '%type%' detected" => "Le fichier '%value%' n'est pas une image, '%type%' détecté",
    "The mimetype of file '%value%' could not be detected" => "Le type MIME du fichier '%value%' n'a pas pu être détecté",
    "File '%value%' is not readable or does not exist" => "Le fichier '%value%' n'est pas lisible ou n'existe pas",

    // Zend\Validator\File\Md5
    "File '%value%' does not match the given md5 hashes" => "Le fichier '%value%' ne correspond pas aux sommes de contrôle MD5 données",
    "A md5 hash could not be evaluated for the given file" => "Une somme de contrôle MD5 n'a pas pu être calculée pour le fichier",
    "File '%value%' is not readable or does not exist" => "Le fichier '%value%' n'est pas lisible ou n'existe pas",

    // Zend\Validator\File\MimeType
    "File '%value%' has a false mimetype of '%type%'" => "Le fichier '%value%' a un faux type MIME : '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Le type MIME du fichier '%value%' n'a pas pu être détecté",
    "File '%value%' is not readable or does not exist" => "Le fichier '%value%' n'est pas lisible ou n'existe pas",

    // Zend\Validator\File\NotExists
    "File '%value%' exists" => "Le fichier '%value%' existe",

    // Zend\Validator\File\Sha1
    "File '%value%' does not match the given sha1 hashes" => "Le fichier '%value%' ne correspond pas aux sommes de contrôle SHA1 données",
    "A sha1 hash could not be evaluated for the given file" => "Une somme de contrôle SHA1 n'a pas pu être calculée pour le fichier",
    "File '%value%' is not readable or does not exist" => "Le fichier '%value%' n'est pas lisible ou n'existe pas",

    // Zend\Validator\File\Size
    "Maximum allowed size for file '%value%' is '%max%' but '%size%' detected" => "La taille de fichier maximale pour '%value%' est '%max%', mais '%size%' détectée",
    "Minimum expected size for file '%value%' is '%min%' but '%size%' detected" => "La taille de fichier minimale pour '%value%' est '%min%', mais '%size%' détectée",
    "File '%value%' is not readable or does not exist" => "Le fichier '%value%' n'est pas lisible ou n'existe pas",

    // Zend\Validator\File\Upload
    "File '%value%' exceeds the defined ini size" => "Le fichier '%value%' dépasse la taille définie dans le fichier INI",
    "File '%value%' exceeds the defined form size" => "Le fichier '%value%' dépasse la taille définie dans le formulaire",
    "File '%value%' was only partially uploaded" => "Le fichier '%value%' n'a été que partiellement envoyé",
    "File '%value%' was not uploaded" => "Le fichier '%value%' n'a pas été envoyé",
    "No temporary directory was found for file '%value%'" => "Le dossier temporaire n'a pas été trouvé pour le fichier '%value%'",
    "File '%value%' can't be written" => "Impossible d'écrire dans le fichier '%value%'",
    "A PHP extension returned an error while uploading the file '%value%'" => "Une extension PHP a retourné une erreur en envoyant le fichier '%value%'",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "Le fichier '%value%' a été envoyé illégalement. Il peut s'agir d'une attaque",
    "File '%value%' was not found" => "Le fichier '%value%' n'a pas été trouvé",
    "Unknown error while uploading file '%value%'" => "Erreur inconnue lors de l'envoi du fichier '%value%'",

    // Zend\Validator\File\UploadFile
    "File exceeds the defined ini size" => "Le fichier dépasse la taille définie dans le fichier INI",
    "File exceeds the defined form size" => "Le fichier dépasse la taille définie dans le formulaire",
    "File was only partially uploaded" => "Le fichier n'a été que partiellement envoyé",
    "File was not uploaded" => "Le fichier n'a pas été envoyé",
    "No temporary directory was found for file" => "Le dossier temporaire n'a pas été trouvé pour le fichier",
    "File can't be written" => "Impossible d'écrire dans le fichier",
    "A PHP extension returned an error while uploading the file" => "Une extension PHP a retourné une erreur en envoyant le fichier",
    "File was illegally uploaded. This could be a possible attack" => "Le fichier a été envoyé illégalement. Il peut s'agir d'une attaque",
    "File was not found" => "Le fichier n'a pas été trouvé",
    "Unknown error while uploading file" => "Erreur inconnue lors de l'envoi du fichier",

    // Zend\Validator\File\WordCount
    "Too much words, maximum '%max%' are allowed but '%count%' were counted" => "Trop de mots. '%max%' sont autorisés, '%count%' comptés",
    "Too few words, minimum '%min%' are expected but '%count%' were counted" => "Pas assez de mots. '%min%' sont attendus, '%count%' comptés",
    "File '%value%' is not readable or does not exist" => "Le fichier '%value%' n'est pas lisible ou n'existe pas",

    // Zend\Validator\GreaterThan
    "The input is not greater than '%min%'" => "L'entrée n'est pas supérieure à '%min%'",
    "The input is not greater or equal than '%min%'" => "L'entrée n'est pas supérieure ou égale à '%min%'",

    // Zend\Validator\Hex
    "Invalid type given. String expected" => "Type invalide. Chaîne attendue",
    "The input contains non-hexadecimal characters" => "L'entrée contient des caractères non-hexadécimaux",

    // Zend\Validator\Hostname
    "The input appears to be a DNS hostname but the given punycode notation cannot be decoded" => "L'entrée semble être un DNS valide mais le code n'a pu être décodé",
    "Invalid type given. String expected" => "Type invalide. Chaîne attendue",
    "The input appears to be a DNS hostname but contains a dash in an invalid position" => "L'entrée semble être un nom d'hôte DNS mais il contient un tiret à une position invalide",
    "The input does not match the expected structure for a DNS hostname" => "L'entrée ne correspond pas à la structure attendue d'un nom d'hôte DNS",
    "The input appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "L'entrée semble être un nom d'hôte DNS valide mais ne correspond pas au schéma de l'extension TLD '%tld%'",
    "The input does not appear to be a valid local network name" => "L'entrée ne semble pas être un nom de réseau local valide",
    "The input does not appear to be a valid URI hostname" => "L'entrée ne semble pas être une URI de nom d'hôte valide",
    "The input appears to be an IP address, but IP addresses are not allowed" => "L'entrée semble être une adresse IP valide, mais les adresses IP ne sont pas autorisées",
    "The input appears to be a local network name but local network names are not allowed" => "L'entrée semble être un nom de réseau local, mais les réseaux locaux ne sont pas autorisés",
    "The input appears to be a DNS hostname but cannot extract TLD part" => "L'entrée semble être un nom d'hôte DNS mais l'extension TLD ne peut être extraite",
    "The input appears to be a DNS hostname but cannot match TLD against known list" => "L'entrée semble être un nom d'hôte DNS mais son extension TLD semble inconnue",

    // Zend\Validator\Iban
    "Unknown country within the IBAN" => "Pays inconnu pour l'IBAN",
    "Countries outside the Single Euro Payments Area (SEPA) are not supported" => "Les pays en dehors du Single Euro Payments Area (SEPA) ne sont pas supportés",
    "The input has a false IBAN format" => "L'entrée n'a pas un format IBAN valide",
    "The input has failed the IBAN check" => "L'entrée n'a pas passé la validation IBAN",

    // Zend\Validator\Identical
    "The two given tokens do not match" => "Les deux jetons passés ne correspondent pas",
    "No token was provided to match against" => "Aucun jeton de correspondance n'a été donné",

    // Zend\Validator\InArray
    "The input was not found in the haystack" => "L'entrée ne fait pas partie des valeurs attendues",

    // Zend\Validator\Ip
    "Invalid type given. String expected" => "Type invalide. Chaîne attendue",
    "The input does not appear to be a valid IP address" => "L'entrée ne semble pas être une adresse IP valide",

    // Zend\Validator\Isbn
    "Invalid type given. String or integer expected" => "Type invalide. Chaîne ou entier attendu",
    "The input is not a valid ISBN number" => "L'entrée n'est pas un nombre ISBN valide",

    // Zend\Validator\LessThan
    "The input is not less than '%max%'" => "L'entrée n'est pas inférieure à '%max%'",
    "The input is not less or equal than '%max%'" => "L'entrée n'est pas inférieure ou égale à '%max%'",

    // Zend\Validator\NotEmpty
    "Value is required and can't be empty" => "Une valeur est requise et ne peut être vide",
    "Invalid type given. String, integer, float, boolean or array expected" => "Type invalide. Chaîne, entier, flottant, booléen ou tableau attendu",

    // Zend\Validator\Regex
    "Invalid type given. String, integer or float expected" => "Type invalide. Chaîne, entier ou flottant attendu",
    "The input does not match against pattern '%pattern%'" => "L'entrée n'est pas valide avec l'expression '%pattern%'",
    "There was an internal error while using the pattern '%pattern%'" => "Une erreur interne est survenue avec l'expression '%pattern%'",

    // Zend\Validator\Sitemap\Changefreq
    "The input is not a valid sitemap changefreq" => "L'entrée n'est pas une valeur de fréquence de sitemap valide",
    "Invalid type given. String expected" => "Type invalide. Chaîne attendue",

    // Zend\Validator\Sitemap\Lastmod
    "The input is not a valid sitemap lastmod" => "L'entrée n'est pas une date de modification de sitemap valide",
    "Invalid type given. String expected" => "Type invalide. Chaîne attendue",

    // Zend\Validator\Sitemap\Loc
    "The input is not a valid sitemap location" => "L'entrée n'est pas un emplacement de sitemap valide",
    "Invalid type given. String expected" => "Type invalide. Chaîne attendue",

    // Zend\Validator\Sitemap\Priority
    "The input is not a valid sitemap priority" => "L'entrée n'est pas une priorité de sitemap valide",
    "Invalid type given. Numeric string, integer or float expected" => "Type invalide. Chaîne numérique, entier ou flottant attendu",

    // Zend\Validator\Step
    "Invalid value given. Scalar expected" => "Type invalide. Scalaire attendu",
    "The input is not a valid step" => "L'entrée n'est pas un multiple valide",

    // Zend\Validator\StringLength
    "Invalid type given. String expected" => "Type invalide. Chaîne attendue",
    "The input is less than %min% characters long" => "L'entrée contient moins de %min% caractères",
    "The input is more than %max% characters long" => "L'entrée contient plus de %max% caractères",

    // Zend\Validator\Uri
    "Invalid type given. String expected" => "Type invalide. Chaîne attendue",
    "The input does not appear to be a valid Uri" => "L'entrée ne semble pas être une URI valide",
);
