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
 * EN-Revision: 21760
 */
return array(
    // Zend_Validate_Alnum
    "Invalid type given, value should be float, string, or integer" => "Type de donnée non valide : entier, flottant ou chaîne attendu",
    "'%value%' contains characters which are non alphabetic and no digits" => "'%value%' contient des caractères non alphabétiques et non numériques",
    "'%value%' is an empty string" => "'%value%' est une chaîne vide",

    // Zend_Validate_Alpha
    "Invalid type given, value should be a string" => "Type de donnée non valide : chaîne attendue",
    "'%value%' contains non alphabetic characters" => "'%value%' contient des caractères non alphabétiques",
    "'%value%' is an empty string" => "'%value%' est une chaîne vide",

    // Zend_Validate_Barcode
    "'%value%' failed checksum validation" => "'%value%' ne passe pas la validation de somme de contrôle",
    "'%value%' contains invalid characters" => "'%value%' contient des caractères invalides",
    "'%value%' should have a length of %length% characters" => "'%value%' devrait avoir une taille de %length% caractères",
    "Invalid type given, value should be string" => "Type de donnée non valide : chaîne attendue",

    // Zend_Validate_Between
    "'%value%' is not between '%min%' and '%max%', inclusively" => "'%value%' n'est pas comprise entre '%min%' et '%max%', inclusivement",
    "'%value%' is not strictly between '%min%' and '%max%'" => "'%value%' n'est pas strictement comprise entre '%min%' et '%max%'",

    // Zend_Validate_Callback
    "'%value%' is not valid" => "'%value%' n'est pas valide",
    "Failure within the callback, exception returned" => "Echec de la fonction de rappel, exception retournée",

    // Zend_Validate_Ccnum
    "'%value%' must contain between 13 and 19 digits" => "'%value%' doit contenir entre 13 et 19 chiffres",
    "Luhn algorithm (mod-10 checksum) failed on '%value%'" => "L'algorithme Luhn (somme de contrôle mod-10) a échoué pour '%value%'",

    // Zend_Validate_CreditCard
    "Luhn algorithm (mod-10 checksum) failed on '%value%'" => "L'algorithme Luhn (somme de contrôle mod-10) a échoué pour '%value%'",
    "'%value%' must contain only digits" => "'%value%' ne doit contenir que des chiffres",
    "Invalid type given, value should be a string" => "Type de donnée non valide : chaîne attendue",
    "'%value%' contains an invalid amount of digits" => "'%value%' contient un nombre incorrect de chiffres",
    "'%value%' is not from an allowed institute" => "'%value%' ne provient pas d'une institution autorisée",
    "Validation of '%value%' has been failed by the service" => "La validation de '%value%' a échoué via le service externe",
    "The service returned a failure while validating '%value%'" => "Le service externe a retourné un echec lors de la validation de '%value%'",

    // Zend_Validate_Date
    "Invalid type given, value should be string, integer, array or Zend_Date" => "Type invalide : chaîne, entier, tableau ou Zend_Date requis",
    "'%value%' does not appear to be a valid date" => "'%value%' ne semble pas être une date valide",
    "'%value%' does not fit the date format '%format%'" => "'%value%' ne correspond pas au format de date '%format%'",

    // Zend_Validate_Db_Abstract
    "No record matching %value% was found" => "Aucun enregistrement trouvé pour %value%",
    "A record matching %value% was found" => "Un enregistrement a été trouvé pour %value%",

    // Zend_Validate_Digits
    "Invalid type given, value should be string, integer or float" => "Type invalide : chaîne, entier ou flottant attendu",
    "'%value%' contains characters which are not digits; but only digits are allowed" => "'%value%' contient des caractères qui ne sont pas numériques ; seuls les caractères numériques sont autorisés",
    "'%value%' contains not only digit characters" => "'%value%' ne contient pas que des chiffres",
    "'%value%' is an empty string" => "'%value%' est une chaîne vide",

    // Zend_Validate_EmailAddress
    "Invalid type given, value should be a string" => "Type invalide, chaîne attendue",
    "'%value%' is no valid email address in the basic format local-part@hostname" => "'%value%' n'est pas un email valide dans le format local-part@hostname",
    "'%hostname%' is no valid hostname for email address '%value%'" => "'%hostname%' n'est pas un nom d'hôte valide pour l'adresse email '%value%'",
    "'%hostname%' does not appear to have a valid MX record for the email address '%value%'" => "'%hostname%' ne semble pas avoir d'enregistrement MX valide pour l'adresse email '%value%'",
    "'%hostname%' is not in a routable network segment. The email address '%value%' should not be resolved from public network." => "'%hostname%' n'est pas dans un segment réseau routable. L'adresse email '%value%' ne devrait pas être résolue depuis un réseau public.",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' ne correspond pas au format dot-atom",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' ne correspond pas au format quoted-string",
    "'%localPart%' is no valid local part for email address '%value%'" => "'%localPart%' n'est pas une partie locale valide pour l'adresse email '%value%'",
    "'%value%' exceeds the allowed length" => "'%value%' excède la taille autorisée",

    // Zend_Validate_File_Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "Trop de fichiers : un maximum de'%max%' est autorisé mais '%count%' ont été fournis",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "Trop peu de fichiers : un minimum de '%min%' est autorisé mais '%count%' ont été fournis",

    // Zend_Validate_File_Crc32
    "File '%value%' does not match the given crc32 hashes" => "Le fichier '%value%' ne correspond pas à la somme de contrôle crc32",
    "A crc32 hash could not be evaluated for the given file" => "La somme de contrôle crc32 n'a pas pu être évaluée pour le fichier",
    "File '%value%' could not be found" => "Fichier '%value%' introuvable",

    // Zend_Validate_File_ExcludeExtension
    "File '%value%' has a false extension" => "Le fichier '%value%' n'a pas la bonne extension",
    "File '%value%' could not be found" => "Fichier '%value%' introuvable",

    // Zend_Validate_File_ExcludeMimeType
    "File '%value%' has a false mimetype of '%type%'" => "Le fichier '%value%' n'a pas le bon type MIME : '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Le type MIME de '%value%' n'a pas pu être détecté",
    "File '%value%' can not be read" => "Le fichier '%value%' ne peut être lu",

    // Zend_Validate_File_Exists
    "File '%value%' does not exist" => "Le fichier '%value%' n'existe pas",

    // Zend_Validate_File_Extension
    "File '%value%' has a false extension" => "Le fichier '%value%' n'a pas la bonne extension",
    "File '%value%' could not be found" => "Fichier '%value%' introuvable",

    // Zend_Validate_File_FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "Tous les fichiers devraient avoir une taille maximale de '%max%' mais une taille de '%size%' a été détectée",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "Tous les fichiers devraient avoir une taille minimale de '%min%' mais une taille de '%size%' a été détectée",
    "One or more files can not be read" => "Un ou plusieurs fichiers n'est pas lisible",

    // Zend_Validate_File_Hash
    "File '%value%' does not match the given hashes" => "Le fichier '%value%' ne correspond pas à la somme de contrôle",
    "A hash could not be evaluated for the given file" => "Une somme de contrôle n'a pas pu être calculée pour le fichier",
    "File '%value%' could not be found" => "Fichier '%value%' introuvable",

    // Zend_Validate_File_ImageSize
    "Maximum allowed width for image '%value%' should be '%maxwidth%' but '%width%' detected" => "La largeur maximale de l'image '%value%' devrait être '%maxwidth%' mais '%width%' a été détectée",
    "Minimum expected width for image '%value%' should be '%minwidth%' but '%width%' detected" => "La largeur minimale de l'image '%value%' devrait être '%minwidth%' mais '%width%' a été détectée",
    "Maximum allowed height for image '%value%' should be '%maxheight%' but '%height%' detected" => "La hauteur maximale de l'image '%value%' devrait être '%maxheight%' mais '%height%' a été détectée",
    "Minimum expected height for image '%value%' should be '%minheight%' but '%height%' detected" => "La hauteur minimale de l'image '%value%' devrait être '%minheight%' mais '%height%' a été détectée",
    "The size of image '%value%' could not be detected" => "La taille de l'image '%value%' n'a pas pu être détectée",
    "File '%value%' can not be read" => "Le fichier '%value%' ne peut être lu",

    // Zend_Validate_File_IsCompressed
    "File '%value%' is not compressed, '%type%' detected" => "Le fichier '%value%' n'est pas compressé : '%type%' détecté",
    "The mimetype of file '%value%' could not be detected" => "Le type MIME du fichier '%value%' n'a pu être détecté",
    "File '%value%' can not be read" => "Le fichier '%value%' ne peut être lu",

    // Zend_Validate_File_IsImage
    "File '%value%' is no image, '%type%' detected" => "Le fichier '%value%' n'est pas une image : '%type%' détecté",
    "The mimetype of file '%value%' could not be detected" => "Le type MIME du fichier '%value%' n'a pu être détecté",
    "File '%value%' can not be read" => "Le fichier '%value%' ne peut être lu",

    // Zend_Validate_File_Md5
    "File '%value%' does not match the given md5 hashes" => "Le fichier '%value%' ne correspond pas à la somme de contrôle MD5",
    "A md5 hash could not be evaluated for the given file" => "Une somme de contrôle MD5 n'a pas pu être calculée pour le fichier",
    "File '%value%' could not be found" => "Fichier '%value%' introuvable",

    // Zend_Validate_File_MimeType
    "File '%value%' has a false mimetype of '%type%'" => "Le fichier '%value%' a un mauvais type MIME : '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Le type MIME du fichier '%value%' n'a pu être détecté",
    "File '%value%' can not be read" => "Le fichier '%value%' ne peut être lu",

    // Zend_Validate_File_NotExists
    "File '%value%' exists" => "Le fichier '%value%' existe déja",

    // Zend_Validate_File_Sha1
    "File '%value%' does not match the given sha1 hashes" => "Le fichier '%value%' ne correspond pas à la somme de contrôle SHA-1",
    "A sha1 hash could not be evaluated for the given file" => "La valeur de somme de contrôle SHA-1 n'a pas pu être calculée pour le fichier",
    "File '%value%' could not be found" => "Fichier '%value%' introuvable",

    // Zend_Validate_File_Size
    "Maximum allowed size for file '%value%' is '%max%' but '%size%' detected" => "La taille maximale requise pour le fichier '%value%' est de '%max%' mais '%size%' a été détecté",
    "Minimum expected size for file '%value%' is '%min%' but '%size%' detected" => "La taille minimale requise pour le fichier '%value%' est de '%min%' mais '%size%' a été détecté",
    "File '%value%' could not be found" => "Fichier '%value%' introuvable",

    // Zend_Validate_File_Upload
    "File '%value%' exceeds the defined ini size" => "Le fichier '%value%' excède la taille requise par le fichier ini",
    "File '%value%' exceeds the defined form size" => "Le fichier '%value%' excède la taille requise par le formulaire",
    "File '%value%' was only partially uploaded" => "Le fichier '%value%' n'a été que partiellement envoyé",
    "File '%value%' was not uploaded" => "Le fichier '%value%' n'a pas été envoyé",
    "No temporary directory was found for file '%value%'" => "Pas de dossier temporaire trouvé pour le fichier '%value%'",
    "File '%value%' can't be written" => "Le fichier '%value%' ne peut être écrit",
    "A PHP extension returned an error while uploading the file '%value%'" => "Une extension PHP a retourné une erreur lors de l'envoi du fichier '%value%'",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "Fichier '%value%' mal envoyé. Ceci peut être possiblement une attaque",
    "File '%value%' was not found" => "Fichier '%value%' introuvable",
    "Unknown error while uploading file '%value%'" => "Erreur inconnue lors de l'envoi du fichier '%value%'",

    // Zend_Validate_File_WordCount
    "Too much words, maximum '%max%' are allowed but '%count%' were counted" => "Trop de mots, un maximum de '%max%' est requis, '%count%' ont été fournis",
    "Too less words, minimum '%min%' are expected but '%count%' were counted" => "Trop peu de mots, un minimum de '%min%' est requis, '%count%' ont été fournis",
    "File '%value%' could not be found" => "Le fichier '%value%' est introuvable",

    // Zend_Validate_Float
    "Invalid type given, value should be float, string, or integer" => "Type invalide : chaîne, entier ou flottant attendu",
    "'%value%' does not appear to be a float" => "'%value%' ne semble pas être de type flottant",

    // Zend_Validate_GreaterThan
    "'%value%' is not greater than '%min%'" => "'%value%' n'est pas plus grand que '%min%'",

    // Zend_Validate_Hex
    "Invalid type given, value should be a string" => "Type de donnée non valide : chaîne attendue",
    "'%value%' has not only hexadecimal digit characters" => "'%value%' ne contient pas uniquement des caractères héxadécimaux",

    // Zend_Validate_Hostname
    "Invalid type given, value should be a string" => "Type de donnée non valide : chaîne attendue",
    "'%value%' appears to be an IP address, but IP addresses are not allowed" => "'%value%' semble être une IP valide mais celles-ci ne sont pas autorisées",
    "'%value%' appears to be a DNS hostname but cannot match TLD against known list" => "'%value%' semble être un nom d'hôte DNS mais son extension TLD semble inconnue",
    "'%value%' appears to be a DNS hostname but contains a dash in an invalid position" => "'%value%' semble être un nom d'hôte DNS mais il contient un tiret à une position invalide",
    "'%value%' appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "'%value%' semble être un nom d'hôte DNS valide mais ne correspond pas au schéma de l'extension TLD '%tld%'",
    "'%value%' appears to be a DNS hostname but cannot extract TLD part" => "'%value%' semble être un nom d'hôte DNS mais l'extension TLD ne peut être extraite",
    "'%value%' does not match the expected structure for a DNS hostname" => "'%value%' ne correspond pas à la structure d'un nom d'hôte DNS valide",
    "'%value%' does not appear to be a valid local network name" => "'%value%' ne semble pas être une adresse réseau local valide",
    "'%value%' appears to be a local network name but local network names are not allowed" => "'%value%' semble être un nom réseau local mais les noms locaux sont interdits",
    "'%value%' appears to be a DNS hostname but the given punycode notation cannot be decoded" => "'%value%' semble être un DNS valide mais le code n'a pu être décodé",

    // Zend_Validate_Iban
    "Unknown country within the IBAN '%value%'" => "Pays inconnu pour l'IBAN '%value%'",
    "'%value%' has a false IBAN format" => "'%value%' n'a pas un format IBAN valide",
    "'%value%' has failed the IBAN check" => "'%value%' n'a pas passé la validation IBAN",

    // Zend_Validate_Identical
    "The token '%token%' does not match the given token '%value%'" => "Le jeton '%token%' n'a pas de correspondance avec '%value%'",
    "No token was provided to match against" => "Aucun jeton de correspondance n'a été donné",

    // Zend_Validate_InArray
    "'%value%' was not found in the haystack" => "'%value%' ne fait pas partie des valeurs attendues",

    // Zend_Validate_Int
    "Invalid type given, value should be string or integer" => "Type invalide : chaîne ou entier attendu",
    "'%value%' does not appear to be an integer" => "'%value%' n'est pas un entier",

    // Zend_Validate_Ip
    "Invalid type given, value should be a string" => "Type invalide : chaîne attendue",
    "'%value%' does not appear to be a valid IP address" => "'%value%' n'est pas une IP valide",

    // Zend_Validate_Isbn
    "Invalid type given, value should be string or integer" => "Type invalide : chaîne ou entier attendu",
    "'%value%' is no valid ISBN number" => "'%value%' n'est pas un ISBN valide",

    // Zend_Validate_LessThan
    "'%value%' is not less than '%max%'" => "'%value%' n'est pas plus petit que '%max%'",

    // Zend_Validate_NotEmpty
    "Invalid type given, value should be float, string, array, boolean or integer" => "Type invalide : chaîne, entier, tableau, booléen ou flottant attendu",
    "Value is required and can't be empty" => "Cette valeur est obligatoire et ne peut être vide",

    // Zend_Validate_PostCode
    "Invalid type given. The value should be a string or a integer" => "Type invalide : chaîne ou entier attendu",
    "'%value%' does not appear to be a postal code" => "'%value%' ne semble pas être un code postal valide",

    // Zend_Validate_Regex
    "Invalid type given, value should be string, integer or float" => "Type invalide : chaîne entier ou flottant attendu",
    "'%value%' does not match against pattern '%pattern%'" => "'%value%' n'a pas de correspondance avec le motif '%pattern%'",
    "There was an internal error while using the pattern '%pattern%'" => "Il y a eu une erreur interne lors de l'utilisation du motif '%pattern%'",

    // Zend_Validate_Sitemap_Changefreq
    "'%value%' is no valid sitemap changefreq" => "'%value%' n'est pas une valeur de fréquence de sitemap valide",
    "Invalid type given, value should be a string" => "Type de donnée non valide : chaîne attendue",

    // Zend_Validate_Sitemap_Lastmod
    "'%value%' is no valid sitemap lastmod" => "'%value%' n'est pas une date de modification de sitemap valide",
    "Invalid type given, value should be a string" => "Type de donnée non valide : chaîne attendue",

    // Zend_Validate_Sitemap_Loc
    "'%value%' is no valid sitemap location" => "'%value%' n'est pas un emplacement valide pour une sitemap",
    "Invalid type given, value should be a string" => "Type de donnée non valide : chaîne attendue",

    // Zend_Validate_Sitemap_Priority
    "'%value%' is no valid sitemap priority" => "'%value%' n'est pas une priorité sitemap valide",
    "Invalid type given, the value should be a integer, a float or a numeric string" => "Type invalide : chaîne numérique, entier ou flottant attendu",

    // Zend_Validate_StringLength
    "Invalid type given, value should be a string" => "Type de donnée non valide : chaîne de caractères attendue",
    "'%value%' is less than %min% characters long" => "La taille de '%value%' est inférieur à %min% caractères",
    "'%value%' is more than %max% characters long" => "La taille de '%value%' est supérieur à %max% caractères",
);
