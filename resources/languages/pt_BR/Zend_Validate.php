<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * EN-Revision: 06.Feb.2013
 */
return array(
    // Zend_I18n_Validator_Alnum
    "Invalid type given. String, integer or float expected" => "O tipo especificado é inválido, o valor deve ser float, string, ou inteiro",
    "The input contains characters which are non alphabetic and no digits" => "O valor de entrada contém caracteres que não são alfabéticos e nem dígitos",
    "The input is an empty string" => "O valor de entrada é uma string vazia",

    // Zend_I18n_Validator_Alpha
    "Invalid type given. String expected" => "O tipo especificado é inválido, o valor deve ser uma string",
    "The input contains non alphabetic characters" => "O valor de entrada contém caracteres não alfabéticos",
    "The input is an empty string" => "O valor de entrada é uma string vazia",

    // Zend_I18n_Validator_Float
    "Invalid type given. String, integer or float expected" => "O tipo especificado é inválido, o valor deve ser float, string, ou inteiro",
    "The input does not appear to be a float" => "O valor de entrada não parece ser to tipo float",

    // Zend_I18n_Validator_Int
    "Invalid type given. String or integer expected" => "O tipo especificado é inválido, o valor deve ser string ou inteiro",
    "The input does not appear to be an integer" => "O valor de entrada não parece ser do tipo inteiro",

    // Zend_I18n_Validator_PostCode
    "Invalid type given. String or integer expected" => "O tipo especificado é inválido, o valor deve ser string ou inteiro",
    "The input does not appear to be a postal code" => "O valor de entrada não parece ser um código postal",
    "An exception has been raised while validating the input" => "Uma exceção foi lançada durante a validação do valor de entrada",

    // Zend_Validator_Barcode
    "The input failed checksum validation" => "O valor de entrada falhou na validação do checksum",
    "The input contains invalid characters" => "O valor de entrada contém caracteres inválidos",
    "The input should have a length of %length% characters" => "O valor de entrada deveria ter %length% caracteres de comprimento",
    "Invalid type given. String expected" => "O tipo especificado é inválido, o valor deve ser string",

    // Zend_Validator_Between
    "The input is not between '%min%' and '%max%', inclusively" => "O valor de entrada não está entre '%min%' e '%max%', inclusivamente",
    "The input is not strictly between '%min%' and '%max%'" => "O valor de entrada não está exatamente entre '%min%' e '%max%'",

    // Zend_Validator_Callback
    "The input is not valid" => "O valor de entrada não é válido",
    "An exception has been raised within the callback" => "Uma exceçao foi lançada na chamada de retorno",

    // Zend_Validator_CreditCard
    "The input seems to contain an invalid checksum" => "O valor de entrada parece conter um checksum inválido",
    "The input must contain only digits" => "O valor de entrada deve conter apenas dígitos",
    "Invalid type given. String expected" => "O tipo especificado é inválido, o valor deve ser string",
    "The input contains an invalid amount of digits" => "O valor de entrada contém uma quantidade inválida de dígitos",
    "The input is not from an allowed institute" => "O valor de entrada não vem de uma instituição autorizada",
    "The input seems to be an invalid creditcard number" => "O valor de entrada parece ser um número de cartão de crédito inválido",
    "An exception has been raised while validating the input" => "Uma exceçao foi lançada durante a validação do valor de entrada",

    // Zend_Validator_Csrf
    "The form submitted did not originate from the expected site" => "O formulário apresentado não se originou a partir do site esperado",

    // Zend_Validator_Date
    "Invalid type given. String, integer, array or DateTime expected" => "O tipo especificado é inválido, o valor deve ser string, inteiro, matriz ou DateTime",
    "The input does not appear to be a valid date" => "O valor de entrada não parece ser uma data válida",
    "The input does not fit the date format '%format%'" => "O valor de entrada não se encaixa no formato de data '%format%'",

    // Zend_Validator_DateStep
    "Invalid type given. String, integer, array or DateTime expected" => "O tipo especificado é inválido, o valor deve ser string, inteiro, matriz ou DateTime",
    "The input does not appear to be a valid date" => "O valor de entrada não parece ser uma data válida",
    "The input is not a valid step" => "O valor de entrada não é um passo válido",

    // Zend_Validator_Db_AbstractDb
    "No record matching the input was found" => "Não foi encontrado nenhum registro para o valor de entrada",
    "A record matching the input was found" => "Um registro foi encontrado para o valor de entrada",

    // Zend_Validator_Digits
    "The input must contain only digits" => "O valor de entrada deve conter apenas dígitos",
    "The input is an empty string" => "O valor de entrada é uma string vazia",
    "Invalid type given. String, integer or float expected" => "O tipo especificado é inválido, o valor deve ser string, inteiro ou float",

    // Zend_Validator_EmailAddress
    "Invalid type given. String expected" => "O tipo especificado é inválido, o valor deve ser do tipo string",
    "The input is not a valid email address. Use the basic format local-part@hostname" => "O valor de entrada não é um endereço de e-mail válido. Use o formato local-part@hostname",
    "'%hostname%' is not a valid hostname for the email address" => "'%hostname%' não é um nome de host válido para o endereço de e-mail",
    "'%hostname%' does not appear to have any valid MX or A records for the email address" => "'%hostname%' não parece ter um registro MX ou A válido para o endereço de e-mail",
    "'%hostname%' is not in a routable network segment. The email address should not be resolved from public network" => "'%hostname%' não é um segmento de rede roteável. O endereço de e-mail não deve ser resolvido a partir de uma rede pública.",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' não corresponde com o formato dot-atom",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' não corresponde com o formato quoted-string",
    "'%localPart%' is not a valid local part for the email address" => "'%localPart%' não é uma parte local válida para o endereço de e-mail",
    "The input exceeds the allowed length" => "O valor de entrada excede o comprimento permitido",

    // Zend_Validator_Explode
    "Invalid type given. String expected" => "O tipo especificado é inválido, o valor deve ser do tipo string",

    // Zend_Validator_File_Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "Há muitos arquivos, são permitidos no máximo '%max%', mas '%count%' foram fornecidos",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "Há poucos arquivos, são esperados no mínimo '%min%', mas '%count%' foram fornecidos",

    // Zend_Validator_File_Crc32
    "File '%value%' does not match the given crc32 hashes" => "O arquivo '%value%' não corresponde a hash crc32 fornecido",
    "A crc32 hash could not be evaluated for the given file" => "Não foi possível avaliar uma hash crc32 para o arquivo fornecido",
    "File '%value%' is not readable or does not exist" => "O arquivo '%value%' não pode ser lido ou não existe",

    // Zend_Validator_File_ExcludeExtension
    "File '%value%' has a false extension" => "O arquivo '%value%' possui uma extensão incorreta",
    "File '%value%' is not readable or does not exist" => "O arquivo '%value%' não pode ser lido ou não existe",

    // Zend_Validator_File_Exists
    "File '%value%' does not exist" => "O arquivo '%value%' não existe",

    // Zend_Validator_File_Extension
    "File has an incorrect extension" => "O arquivo possui uma extensão incorreta",
    "File is not readable or does not exist" => "O arquivo não pode ser lido ou não existe",

    // Zend_Validator_File_FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "Todos os arquivos devem ter um tamanho máximo de '%max%', mas um tamanho de '%size%' foi detectado",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "Todos os arquivos devem ter um tamanho mínimo de '%min%', mas um tamanho de '%size%' foi detectado",
    "One or more files can not be read" => "Um ou mais arquivos não podem ser lidos",

    // Zend_Validator_File_Hash
    "File '%value%' does not match the given hashes" => "O arquivo '%value%' não corresponde as hashes fornecidas",
    "A hash could not be evaluated for the given file" => "Não foi possível avaliar uma hash para o arquivo fornecido",
    "File '%value%' is not readable or does not exist"  => "O arquivo '%value%' não pode ser lido ou não existe",

    // Zend_Validator_File_ImageSize
    "Maximum allowed width for image '%value%' should be '%maxwidth%' but '%width%' detected" => "A largura máxima permitida para a imagem '%value%' deveria ser '%maxwidth%', mas '%width%' foi detectada",
    "Minimum expected width for image '%value%' should be '%minwidth%' but '%width%' detected" => "A largura mínima esperada para a imagem '%value%' deveria ser '%minwidth%', mas '%width%' foi detectada",
    "Maximum allowed height for image '%value%' should be '%maxheight%' but '%height%' detected" => "A altura máxima permitida para a imagem '%value%' deveria ser '%maxheight%', mas '%height%' foi detectada",
    "Minimum expected height for image '%value%' should be '%minheight%' but '%height%' detected" => "A altura mínima esperada para a imagem '%value%' deveria ser '%minheight%', mas '%height%' foi detectada",
    "The size of image '%value%' could not be detected" => "O tamanho da imagem '%value%' não pôde ser detectado",
    "File '%value%' is not readable or does not exist" => "O arquivo '%value%' não pode ser lido ou não existe",

    // Zend_Validator_File_IsCompressed
    "File '%value%' is not compressed, '%type%' detected" => "O arquivo '%value%' não está compactado: '%type%' detectado",
    "The mimetype of file '%value%' could not be detected" => "O mimetype do arquivo '%value%' não pôde ser detectado",
    "File '%value%' is not readable or does not exist" => "O arquivo '%value%' não pode ser lido ou não existe",

    // Zend_Validator_File_IsImage
    "File '%value%' is no image, '%type%' detected" => "O arquivo '%value%' não é uma imagem: '%type%' detectado",
    "The mimetype of file '%value%' could not be detected" => "O mimetype do arquivo '%value%' não pôde ser detectado",
    "File '%value%' is not readable or does not exist" => "O arquivo '%value%' não pode ser lido ou não existe",

    // Zend_Validator_File_Md5
    "File '%value%' does not match the given md5 hashes" => "O arquivo '%value%' não corresponde as hashes md5 fornecidas",
    "A md5 hash could not be evaluated for the given file" => "Não foi possível avaliar uma hash md5 para o arquivo fornecido",
    "File '%value%' is not readable or does not exist" => "O arquivo '%value%' não pode ser lido ou não existe",

    // Zend_Validator_File_MimeType
    "File '%value%' has a false mimetype of '%type%'" => "O arquivo '%value%' tem o mimetype incorreto: '%type%'",
    "The mimetype of file '%value%' could not be detected" => "O mimetype do arquivo '%value%' não pôde ser detectado",
    "File '%value%' is not readable or does not exist" => "O arquivo '%value%' não pode ser lido ou não existe",

    // Zend_Validator_File_NotExists
    "File '%value%' exists" => "O arquivo '%value%' existe",

    // Zend_Validator_File_Sha1
    "File '%value%' does not match the given sha1 hashes" => "O arquivo '%value%' não corresponde as hashes sha1 fornecidas",
    "A sha1 hash could not be evaluated for the given file" => "Não foi possível avaliar uma hash sha1 para o arquivo fornecido",
    "File '%value%' is not readable or does not exist" => "O arquivo '%value%' não pode ser lido ou não existe",

   // Zend_Validator_File_Size
    "Maximum allowed size for file is '%max%' but '%size%' detected" => "O tamanho máximo permitido para o arquivo é '%max%', mas '%size%' foi detectado",
    "Minimum expected size for file is '%min%' but '%size%' detected" => "O tamanho mínimo esperado para o arquivo é '%min%', mas '%size%' foi detectado",
    "File is not readable or does not exist" => "O arquivo não pode ser lido ou não existe",

    // Zend_Validator_File_Upload
    "File '%value%' exceeds the defined ini size" => "O arquivo '%value%' excede o tamanho definido na configuração",
    "File '%value%' exceeds the defined form size" => "O arquivo '%value%' excede o tamanho definido do formulário",
    "File '%value%' was only partially uploaded" => "O arquivo '%value%' foi apenas parcialmente enviado",
    "File '%value%' was not uploaded" => "O arquivo '%value%' não foi enviado",
    "No temporary directory was found for file '%value%'" => "Nenhum diretório temporário foi encontrado para o arquivo '%value%'",
    "File '%value%' can't be written" => "O arquivo '%value%' não pôde ser escrito",
    "A PHP extension returned an error while uploading the file '%value%'" => "Uma extensão do PHP retornou um erro enquanto o arquivo '%value%' era enviado",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "O arquivo '%value%' foi enviado ilegalmente. Isto poderia ser um possível ataque",
    "File '%value%' was not found" => "O arquivo '%value%' não foi encontrado",
    "Unknown error while uploading file '%value%'" => "Erro desconhecido ao enviar o arquivo '%value%'",

    // Zend_Validator_File_UploadFile
    "File exceeds the defined ini size" => "O arquivo excede o tamanho definido na configuração",
    "File exceeds the defined form size" => "O arquivo excede o tamanho definido do formulário",
    "File was only partially uploaded" => "O arquivo foi apenas parcialmente enviado",
    "File was not uploaded" => "O arquivo não foi enviado",
    "No temporary directory was found for file" => "Nenhum diretório temporário foi encontrado para o arquivo",
    "File can't be written" => "O arquivo não pôde ser escrito",
    "A PHP extension returned an error while uploading the file" => "Uma extensão do PHP retornou um erro enquanto o arquivo era enviado",
    "File was illegally uploaded. This could be a possible attack" => "O arquivo foi enviado ilegalmente. Isto poderia ser um possível ataque",
    "File was not found" => "O arquivo não foi encontrado",
    "Unknown error while uploading file" => "Erro desconhecido ao enviar o arquivo",

    // Zend_Validator_File_WordCount
    "Too much words, maximum '%max%' are allowed but '%count%' were counted" => "Há muitas palavras, são permitidas no máximo '%max%', mas '%count%' foram contadas",
    "Too few words, minimum '%min%' are expected but '%count%' were counted" => "Há poucas palavras, são esperadas no mínimo '%min%', mas '%count%' foram contadas",
    "File '%value%' is not readable or does not exist" => "O arquivo '%value%' não pode ser lido ou não existe",

    // Zend_Validator_GreaterThan
    "The input is not greater than '%min%'" => "O valor de entrada não é maior do que '%min%'",
    "The input is not greater or equal than '%min%'" => "O valor de entrada não é maior ou igual a '%min%'",

    // Zend_Validator_Hex
    "Invalid type given. String expected" => "O tipo especificado é inválido, o valor deve ser to tipo string",
    "The input contains non-hexadecimal characters" => "O valor de entrada contém caracteres não-hexadecimais",

    // Zend_Validator_Hostname
    "The input appears to be a DNS hostname but the given punycode notation cannot be decoded" => "O valor de entrada parece ser um hostname de DNS, mas a notação punycode fornecida não pode ser decodificada",
    "Invalid type given. String expected" => "O tipo especificado é inválido, o valor deve ser do tipo string",
    "The input appears to be a DNS hostname but contains a dash in an invalid position" => "O valor de entrada parece ser um hostname de DNS, mas contém um traço em uma posição inválida",
    "The input does not match the expected structure for a DNS hostname" => "O valor de entrada não corresponde com a estrutura esperada para um hostname de DNS",
    "The input appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "O valor de entrada parece ser um hostname de DNS, mas não corresponde ao esquema de hostname para o TLD '%tld%'",
    "The input does not appear to be a valid local network name" => "O valor de entrada não parece ser um nome de rede local válido",
    "The input does not appear to be a valid URI hostname" => "O valor de entrada não parece ser um URI hostname válido",
    "The input appears to be an IP address, but IP addresses are not allowed" => "O valor de entrada parece ser um endereço de IP, mas endereços de IP não são permitidos",
    "The input appears to be a local network name but local network names are not allowed" => "O valor de entrada parece ser um nome de rede local, mas os nomes de rede local não são permitidos",
    "The input appears to be a DNS hostname but cannot extract TLD part" => "O valor de entrada parece ser um hostname de DNS, mas o TLD não pôde ser extraído",
    "The input appears to be a DNS hostname but cannot match TLD against known list" => "O valor de entrada parece ser um hostname de DNS, mas o TLD não corresponde a nenhum TLD conhecido",

    // Zend_Validator_Iban
    "Unknown country within the IBAN" => "País desconhecido para o IBAN",
    "Countries outside the Single Euro Payments Area (SEPA) are not supported" => "Países fora da Área Única de Pagamentos em Euros (SEPA) não são suportados",
    "The input has a false IBAN format" => "O valor de entrada não é um formato IBAN válido",
    "The input has failed the IBAN check" => "O valor de entrada falhou na verificação do IBAN",

    // Zend_Validator_Identical
    "The two given tokens do not match" => "Os dois tokens fornecidos não combinam",
    "No token was provided to match against" => "Nenhum token foi fornecido para a comparação",

    // Zend_Validator_InArray
    "The input was not found in the haystack" => "O valor de entrada não faz parte dos valores esperados",

    // Zend_Validator_Ip
    "Invalid type given. String expected" => "O tipo especificado é inválido, o valor deve ser uma string",
    "The input does not appear to be a valid IP address" => "O valor de entrada não parece ser um endereço de IP válido",

    // Zend_Validator_Isbn
    "Invalid type given. String or integer expected" => "O tipo especificado é inválido, o valor deve ser string ou inteiro",
    "The input is not a valid ISBN number" => "O valor de entrada não é um número ISBN válido",

    // Zend_Validator_LessThan
    "The input is not less than '%max%'" => "O valor de entrada não é menor do que '%max%'",
    "The input is not less or equal than '%max%'" => "O valor de entrada não é menor ou igual a '%max%'",

    // Zend_Validator_NotEmpty
    "Value is required and can't be empty" => "O valor é obrigatório e não pode estar vazio",
    "Invalid type given. String, integer, float, boolean or array expected" => "O tipo especificado é inválido, o valor deve ser float, string, matriz, booleano ou inteiro",

    // Zend_Validator_Regex
    "Invalid type given. String, integer or float expected" => "O tipo especificado é inválido, o valor deve ser string, inteiro ou float",
    "The input does not match against pattern '%pattern%'" => "O valor de entrada não corresponde ao padrão '%pattern%'",
    "There was an internal error while using the pattern '%pattern%'" => "Houve um erro interno durante o uso do padrão '%pattern%'",

    // Zend_Validator_Sitemap_Changefreq
    "The input is not a valid sitemap changefreq" => "O valor de entrada não é um changefreq (frequência de alterações) de sitemap válido",
    "Invalid type given. String expected" => "O tipo especificado é inválido, o valor deve ser uma string",

    // Zend_Validator_Sitemap_Lastmod
    "The input is not a valid sitemap lastmod" => "O valor de entrada não é um lastmod (última modificação) de sitemap válido",
    "Invalid type given. String expected" => "O tipo especificado é inválido, o valor deve ser uma string",

    // Zend_Validator_Sitemap_Loc
    "The input is not a valid sitemap location" => "O valor de entrada não é uma localização de sitemap válida",
    "Invalid type given. String expected" => "O tipo especificado é inválido, o valor deve ser uma string",

    // Zend_Validator_Sitemap_Priority
    "The input is not a valid sitemap priority" => "O valor de entrada não é uma prioridade de sitemap válida",
    "Invalid type given. Numeric string, integer or float expected" => "O tipo especificado é inválido, o valor deve ser um inteiro, um float ou uma string numérica",

    // Zend_Validator_Step
    "Invalid value given. Scalar expected" => "O valor especificado é inválido, o valor deve ser escalar",
    "The input is not a valid step" => "O valor de entrada não é um passo válido",

    // Zend_Validator_StringLength
    "Invalid type given. String expected" => "O tipo especificado é inválido, o valor deve ser uma string",
    "The input is less than %min% characters long" => "O tamanho do valor de entrada é inferior a %min% caracteres",
    "The input is more than %max% characters long" => "O tamanho do valor de entrada é superior a %max% caracteres",

    // Zend_Validator_Uri
    "Invalid type given. String expected" => "O tipo especificado é inválido, o valor deve ser uma string",
    "The input does not appear to be a valid Uri" => "O valor de entrada não parece ser uma Uri válida",
);
