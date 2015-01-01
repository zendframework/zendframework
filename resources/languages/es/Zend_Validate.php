<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * EN-Revision: 22075
 */
return array(
    // Zend\I18n\Validator\Alnum
    "Invalid type given. String, integer or float expected" => "El tipo especificado no es válido. El valor debe ser de tipo punto flotante, cadena de texto o entero",
    "The input contains characters which are non alphabetic and no digits" => "El valor especificado contiene caracteres que no son alfabéticos ni dígitos",
    "The input is an empty string" => "El valor especificado es una cadena de texto vacia",

    // Zend\I18n\Validator\Alpha
    "Invalid type given. String expected" => "El tipo especificado no es válido, el valor debe ser una cadena de texto",
    "The input contains non alphabetic characters" => "La entrada contiene caracteres no alfabéticos",
    "The input is an empty string" => "El valor especificado es una cadena de texto vacia",

    // Zend\I18n\Validator\DateTime
    "Invalid type given. String expected" => "El tipo especificado no es válido. El valor debe ser una cadena de texto",
    "The input does not appear to be a valid datetime" => "El valor especificado no parece ser una fecha válida",

    // Zend\I18n\Validator\Float
    "Invalid type given. String, integer or float expected" => "El tipo especificado no es válido. El valor debe ser de tipo punto flotante, cadena de texto o entero",
    "The input does not appear to be a float" => "El valor especificado no parece ser un número de punto flotante",

    // Zend\I18n\Validator\Int
    "Invalid type given. String or integer expected" => "El tipo especificado no es válido. El valor debe ser de tipo cadena de texto o entero",
    "The input does not appear to be an integer" => "El valor especificado no parece ser un número entero",

    // Zend\I18n\Validator\PhoneNumber
    "The input does not match a phone number format" => "La entrada no coincide con el formato de un número de teléfono",
    "The country provided is currently unsupported" => "El país especificado no está soportado actualmente",
    "Invalid type given. String expected" => "El tipo especificado no es válido, el valor debe ser una cadena de texto",

    // Zend\I18n\Validator\PostCode
    "Invalid type given. String or integer expected" => "El tipo especificado no es válido, el valor debe ser una cadena de texto o un número entero",
    "The input does not appear to be a postal code" => "El valor especificado no parece ser un código postal",
    "An exception has been raised while validating the input" => "Una excepción ha sido alzada al validar la entrada",

    // Zend\Validator\Barcode
    "The input failed checksum validation" => "Falló la validación de checksum",
    "The input contains invalid characters" => "El valor especificado contiene caracteres no válidos",
    "The input should have a length of %length% characters" => "El valor especificado debe tener una longitud de  %length% caracteres",
    "Invalid type given. String expected" => "El tipo especificado no es válido, el valor debe ser una cadena de texto",

    // Zend\Validator\Between
    "The input is not between '%min%' and '%max%', inclusively" => "El valor especificado no está incluido entre '%min%' y '%max%' inclusive",
    "The input is not strictly between '%min%' and '%max%'" => "El valor especificado no está exactamente entre '%min%' y '%max%'",

    // Zend\Validator\Callback
    "The input is not valid" => "El valor especificado no es válido",
    "An exception has been raised within the callback" => "Fallo dentro de la llamada de retorno, ha devuelto una excepción",

    // Zend\Validator\CreditCard
    "The input seems to contain an invalid checksum" => "El valor especificado parece contener un error en el checksum",
    "The input must contain only digits" => "El valor especificado debe contener solamente dígitos",
    "Invalid type given. String expected" => "El tipo especificado no es válido, el valor debe ser una cadena de texto",
    "The input contains an invalid amount of digits" => "El valor especificado contiene una cantidad de dígitos no válida",
    "The input is not from an allowed institute" => "El valor especificado no corresponde con una institución permitida",
    "The input seems to be an invalid credit card number" => "El valor especificado parece ser un número de tarjeta de crédito no válido",
    "An exception has been raised while validating the input" => "Se ha devuelto una excepción al validar el valor especificado",

    // Zend\Validator\Csrf
    "The form submitted did not originate from the expected site" => "El formulario enviado no se ha originado desde el sitio esperado",

    // Zend\Validator\Date
    "Invalid type given. String, integer, array or DateTime expected" => "El tipo especificado no es válido. El valor debe ser cadena de texto, número entero, array u objeto DateTime",
    "The input does not appear to be a valid date" => "El valor especificado no parece ser una fecha válida",
    "The input does not fit the date format '%format%'" => "El valor especificado no se ajusta al formato de fecha '%format%'",

    // Zend\Validator\DateStep
    "The input is not a valid step" => "El valor especificado no es un escalón válido",

    // Zend\Validator\Db\AbstractDb
    "No record matching the input was found" => "No fue encontrado ningun registro que coincida con el valor especificado",
    "A record matching the input was found" => "Se encontró un registro coincidente con el valor especificado",

    // Zend\Validator\Digits
    "The input must contain only digits" => "El valor especificado debe contener solamente dígitos",
    "The input is an empty string" => "El valor especificado está vacío",
    "Invalid type given. String, integer or float expected" => "El tipo especificado no es válido. El valor debe ser una cadena de texto, un número entero, o un número de punto flotante.",

    // Zend\Validator\EmailAddress
    "Invalid type given. String expected" => "El tipo especificado no es válido, el valor debe ser una cadena de texto",
    "The input is not a valid email address. Use the basic format local-part@hostname" => "El valor especificado no es una dirección de correo electrónico válido en el formato local-part@hostname",
    "'%hostname%' is not a valid hostname for the email address" => "'%hostname%' no es un nombre de host válido para la dirección de correo electrónico",
    "'%hostname%' does not appear to have any valid MX or A records for the email address" => "'%hostname%' no parece tener registros MX o A válidos para la dirección de correo electrónico especificada",
    "'%hostname%' is not in a routable network segment. The email address should not be resolved from public network" => "'%hostname%' no esta en un segmento de red encaminable. La dirección de correo electrónico especificada no se debería poder resolver desde una red pública",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' no es igual al formato dot-atom",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' no es igual al formato quoted-string",
    "'%localPart%' is not a valid local part for the email address" => "'%localPart%' no es una parte local válida para la dirección de correo electrónico especificado",
    "The input exceeds the allowed length" => "el valor especificado excede la longitud permitida",

    // Zend\Validator\Explode
    "Invalid type given" => "El tipo de dato especificado no es válido",

    // Zend\Validator\File\Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "Demasiados archivos, se permiten un máximo de '%max%' pero se han especificado '%count%'",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "Muy pocos archivos, se esperaba un mínimo de '%min%' pero sólo se han especificado '%count%'",

    // Zend\Validator\File\Crc32
    "File does not match the given crc32 hashes" => "El CRC32 del archivo es incorrectos",
    "A crc32 hash could not be evaluated for the given file" => "No se ha podido calcular el CRC32 del archivo especificado",
    "File is not readable or does not exist" => "No se ha podido encontrar el archivo o no se puede leer",

    // Zend\Validator\File\ExcludeExtension
    "File has an incorrect extension" => "El archivo tiene una extensión incorrecta",
    "File is not readable or does not exist" => "No se ha podido encontrar el archivo o no se puede leer",

    // Zend\Validator\File\Exists
    "File does not exist" => "El archivo no existe",

    // Zend\Validator\File\Extension
    "File has an incorrect extension" => "El archivo especificado tiene una extensión incorrecta",
    "File is not readable or does not exist" => "No se ha podido encontrar el archivo especificado'",

    // Zend\Validator\File\FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "El conjunto de archivos debería tener un tamaño máximo de '%max%' pero tiene un tamaño de '%size%'",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "El conjunto de archivos debería tener un tamaño mínimo de '%min%' pero tiene un tamaño de '%size%'",
    "One or more files can not be read" => "Uno o más archivos no se pueden leer",

    // Zend\Validator\File\Hash
    "File does not match the given hashes" => "El archivo no se corresponde con los códigos hash especificados",
    "A hash could not be evaluated for the given file" => "No se ha podido evaluar ningún código hash para el archivo especificado",
    "File is not readable or does not exist" => "El archivo no se puede leer o no existe",

    // Zend\Validator\File\ImageSize
    "Maximum allowed width for image should be '%maxwidth%' but '%width%' detected" => "El ancho máximo para la imagen debería ser '%maxwidth%' pero es '%width%'",
    "Minimum expected width for image should be '%minwidth%' but '%width%' detected" => "El ancho mínimo para la imagen debería ser '%minwidth%' pero es '%width%'",
    "Maximum allowed height for image should be '%maxheight%' but '%height%' detected" => "La altura máxima para la imagen debería ser '%maxheight%' pero es '%height%'",
    "Minimum expected height for image should be '%minheight%' but '%height%' detected" => "La altura mínima para la imagen debería ser '%minheight%' pero es '%height%'",
    "The size of image could not be detected" => "No se ha podido determinar el tamaño de la imagen",
    "File is not readable or does not exist" => "El archivo no se puede leer o no existe",

    // Zend\Validator\File\IsCompressed
    "File is not compressed, '%type%' detected" => "El archivo no está comprimido, '%type%' detectado",
    "The mimetype could not be detected from the file" => "No se ha podido determinar el tipo MIME del archivo",
    "File is not readable or does not exist" => "El archivo no se puede leer o no existe",

    // Zend\Validator\File\IsImage
    "File is no image, '%type%' detected" => "El archivo no es una imagen, '%type%' detectado",
    "The mimetype could not be detected from the file" => "No se ha podido determinar el tipo MIME del archivo",
    "File is not readable or does not exist" => "El archivo no se puede leer o no existe",

    // Zend\Validator\File\Md5
    "File does not match the given md5 hashes" => "El archivo no se corresponde con el MD5 especificado",
    "An md5 hash could not be evaluated for the given file" => "No se ha podido calcular el MD5 del archivo especificado",
    "File is not readable or does not exist" => "El archivo no se puede leer o no existe",

    // Zend\Validator\File\MimeType
    "File has an incorrect mimetype of '%type%'" => "El archivo tiene un tipo MIME '%type%' incorrecto",
    "The mimetype could not be detected from the file" => "No se ha podido determinar el tipo MIME del archivo",
    "File is not readable or does not exist" => "El archivo no se puede leer o no existe",

    // Zend\Validator\File\NotExists
    "File exists" => "El archivo existe",

    // Zend\Validator\File\Sha1
    "File does not match the given sha1 hashes" => "El archivo no coincide los hashes sha1 dados",
    "A sha1 hash could not be evaluated for the given file" => "Un hash sha1 no pudo ser evaluado para el archivo dado",
    "File is not readable or does not exist" => "El archivo no se puede leer o no existe",

    // Zend\Validator\File\Size
    "Maximum allowed size for file is '%max%' but '%size%' detected" => "El tamaño máximo permitido para el archivo es '%max%' pero se ha detectado un tamaño de '%size%'",
    "Minimum expected size for file is '%min%' but '%size%' detected" => "El tamaño mínimo permitido para el archivo es '%min%' pero se ha detectado un tamaño de '%size%'",
    "File is not readable or does not exist" => "No se ha podido encontrar el archivo o no se puede leer",

    // Zend\Validator\File\Upload
    "File '%value%' exceeds the defined ini size" => "El tamaño del archivo '%value%' excede el valor definido en el ini",
    "File '%value%' exceeds the defined form size" => "El archivo '%value%' excede el tamaño definido en el formulario",
    "File '%value%' was only partially uploaded" => "El archivo '%value%' ha sido sólo parcialmente subido",
    "File '%value%' was not uploaded" => "El archivo '%value%' no ha sido subido",
    "No temporary directory was found for file '%value%'" => "No se ha encontrado el directorio temporal para el archivo '%value%'",
    "File '%value%' can't be written" => "No se puede escribir en el archivo '%value%'",
    "A PHP extension returned an error while uploading the file '%value%'" => "Una extensión PHP devolvió un error mientras se subía el archivo '%value%'",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "El archivo '%value%' ha sido subido ilegalmente. Esto podría indicar un ataque",
    "File '%value%' was not found" => "Archivo '%value%' no encontrado",
    "Unknown error while uploading file '%value%'" => "Error desconocido al intentar subir el archivo '%value%'",

    // Zend\Validator\File\UploadFile
    "File exceeds the defined ini size" => "El tamaño del archivo excede el valor definido en el ini",
    "File exceeds the defined form size" => "El archivo excede el tamaño definido en el formulario",
    "File was only partially uploaded" => "El archivo fue sólo parcialmente subido",
    "File was not uploaded" => "El archivo no ha sido subido",
    "No temporary directory was found for file" => "No se ha encontrado el directorio temporal para el archivo",
    "File can't be written" => "No se puede escribir en el archivo",
    "A PHP extension returned an error while uploading the file" => "Una extensión PHP devolvió un error mientras se subía el archivo",
    "File was illegally uploaded. This could be a possible attack" => "El archivo ha sido subido ilegalmente. Esto podría indicar un ataque",
    "File was not found" => "Archivo no encontrado",
    "Unknown error while uploading file" => "Error desconocido al intentar subir el archivo",

    // Zend\Validator\File\WordCount
    "Too many words, maximum '%max%' are allowed but '%count%' were counted" => "Demasiadas palabras, sólo se permiten '%max%' pero se han contado '%count%'",
    "Too few words, minimum '%min%' are expected but '%count%' were counted" => "Muy pocas palabras, se esperaban al menos '%min%' pero se han contado '%count%'",
    "File is not readable or does not exist" => "No se ha podido encontrar o leer el archivo",

    // Zend\Validator\GreaterThan
    "The input is not greater than '%min%'" => "El valor especificado no es más grande que '%min%'",
    "The input is not greater or equal than '%min%'" => "El valor especificado no es más grande o igual que '%min%'",

    // Zend\Validator\Hex
    "Invalid type given. String expected" => "El tipo especificado es incorrecto, el valor debería ser una cadena de texto",
    "The input contains non-hexadecimal characters" => "El valor especificado no consta únicamente de dígitos y caracteres hexadecimales",

    // Zend\Validator\Hostname
    "The input appears to be a DNS hostname but the given punycode notation cannot be decoded" => "La entrada parece ser un nombre de host de DNS pero la notación de punycode no puede ser decodificada",
    "Invalid type given. String expected" => "El tipo especificado es incorrecto, el valor debería ser una cadena de texto",
    "The input appears to be a DNS hostname but contains a dash in an invalid position" => "El valor especificado parece ser un nombre de dominio DNS pero contiene un guión en una posición inválida",
    "The input does not match the expected structure for a DNS hostname" => "El valor especificado no se corresponde con la estructura esperada para un nombre de dominio DNS",
    "The input appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "El valor especificado parece ser un nombre de dominio DNS pero no se puede extraer la parte del TLD",
    "The input does not appear to be a valid local network name" => "El valor especificado no parece ser un nombre de área local válido",
    "The input does not appear to be a valid URI hostname" => "El valor especificado no parece ser un nombre de host URI válido",
    "The input appears to be an IP address, but IP addresses are not allowed" => "El valor especificado parece ser una dirección IP, pero direcciones IP no están permitidas",
    "The input appears to be a local network name but local network names are not allowed" => "El valor especificado parece ser un nombre de red local, pero nombres de red local no están permitidos",
    "The input appears to be a DNS hostname but cannot extract TLD part" => "El valor especificado parece ser un nombre de dominio DNS pero no se puede extraer la parte del TLD",
    "The input appears to be a DNS hostname but cannot match TLD against known list" => "El valor especificado parece ser un nombre de dominio válido pero no se puede encontrar el TLD en una lista conocida",

    // Zend\Validator\Iban
    "Unknown country within the IBAN" => "País desconocido dentro del IBAN'",
    "Countries outside the Single Euro Payments Area (SEPA) are not supported" => "Los países fuera de la Zona Única de Pagos en Euros (SEPA) no están permitidos",
    "The input has a false IBAN format" => "El valor especificado tiene un formato falso de IBAN",
    "The input has failed the IBAN check" => "La prueba de validación de IBAN ha fallado",

    // Zend\Validator\Identical
    "The two given tokens do not match" => "Las dos muestras especificados no concuerdan",
    "No token was provided to match against" => "No se ha especificado ninguna muestra a comprobar",

    // Zend\Validator\InArray
    "The input was not found in the haystack" => "El valor no se encuentra dentro de los valores permitidos",

    // Zend\Validator\Ip
    "Invalid type given. String expected" => "El tipo especificado es incorrecto. El valor debería ser una cadena de texto",
    "The input does not appear to be a valid IP address" => "El valor especificado no parece ser una dirección IP válida",

    // Zend\Validator\IsInstanceOf
    "The input is not an instance of '%className%'" => "El valor especificado no es una instancia de '%className%'",

    // Zend\Validator\Isbn
    "Invalid type given. String or integer expected" => "El tipo especificado es inválido. El valor debería ser una cadena de texto o un entero",
    "The input is not a valid ISBN number" => "El número ISBN especificado no es válido",

    // Zend\Validator\LessThan
    "The input is not less than '%max%'" => "El valor especificado no es menor que '%max%'",
    "The input is not less or equal than '%max%'" => "El valor especificado no es menor o igual que '%max%'",

    // Zend\Validator\NotEmpty
    "Value is required and can't be empty" => "Se requiere un valor y éste no puede estar vacío",
    "Invalid type given. String, integer, float, boolean or array expected" => "El tipo especificado es inválido, el valor debería ser punto floatante, cadena de texto, array, booleano o entero",

    // Zend\Validator\Regex
    "Invalid type given. String, integer or float expected" => "El tipo especificado es incorrecto, el valor debería ser de punto floatante, cadena de texto o entero",
    "The input does not match against pattern '%pattern%'" => "El valor especificado no concuerda con el patrón '%pattern%' especificado",
    "There was an internal error while using the pattern '%pattern%'" => "Se ha producido un error interno al usar el patrón '%pattern%' especificado",

    // Zend\Validator\Sitemap\Changefreq
    "The input is not a valid sitemap changefreq" => "El valor especificado no es una especificación válida de frecuencia de cambio",
    "Invalid type given. String expected" => "El tipo especificado es inválido, el valor debería ser una cadena de texto",

    // Zend\Validator\Sitemap\Lastmod
    "The input is not a valid sitemap lastmod" => "El valor especificado no es un lastmod de mapa web válido",
    "Invalid type given. String expected" => "El tipo especificado es inválido, el valor debería ser una cadena de texto",

    // Zend\Validator\Sitemap\Loc
    "The input is not a valid sitemap location" => "El valor especificado no es una ubicación de mapa web válida",
    "Invalid type given. String expected" => "El tipo especificado es inválido, el valor debería ser una cadena de texto",

    // Zend\Validator\Sitemap\Priority
    "The input is not a valid sitemap priority" => "El valor especificado no es una prioridad de mapa web válida",
    "Invalid type given. Numeric string, integer or float expected" => "El tipo especificado es inválido, el valor debería ser una cadena de texto, un número entero, o un número de punto flotante",

    // Zend\Validator\Step
    "Invalid value given. Scalar expected" => "El valor especificado no es válido, debería ser escalar",
    "The input is not a valid step" => "El valor especificado no es un escalon válido",

    // Zend\Validator\StringLength
    "Invalid type given. String expected" => "El tipo especificado es incorrecto, el valor debería ser una cadena de texto",
    "The input is less than %min% characters long" => "El valor especificado tiene menos de '%min%' caracteres",
    "The input is more than %max% characters long" => "El valor especificado tiene más de '%max%' caracteres",

    // Zend\Validator\Uri
    "Invalid type given. String expected" => "El tipo especificado es incorrecto, el valor debería ser una cadena de texto",
    "The input does not appear to be a valid Uri" => "El valor especificado no parece ser un Uri válido",
);
