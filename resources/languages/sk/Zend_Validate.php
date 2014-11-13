<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * SK-Revision: 30.Sep.2013
 */
return array(
    // Zend\I18n\Validate\Alnum
    "Invalid type given. String, integer or float expected" => "Chybný typ. Bol očakávaný reťazec, celé alebo desatinné číslo",
    "The input contains characters which are non alphabetic and no digits" => "Hodnota obsahuje aj iné znaky ako písmená a číslice",
    "The input is an empty string" => "Hodnota je prázdny reťazec",

    // Zend\I18n\Validator\Alpha
    "Invalid type given. String expected" => "Chybný typ. Bol očakávaný reťazec",
    "The input contains non alphabetic characters" => "Hodnota obsahuje aj iné znaky ako písmená",
    "The input is an empty string" => "Hodnota je prázdny reťazec",

    // Zend\I18n\Validator\DateTime
    "Invalid type given. String expected" => "Chybný typ. Bol očakávaný reťazec",
    "The input does not appear to be a valid datetime" => "Hodnota nie je platný časový údaj",
    
    // Zend\I18n\Validator\Float
    "Invalid type given. String, integer or float expected" => "Chybný typ. Bol očakávaný reťazec, celé alebo desatinné číslo",
    "The input does not appear to be a float" => "Hodnota nie je desatinné číslo",
    
    // Zend\I18n\Validator\Int
    "Invalid type given. String or integer expected" => "Chybný typ. Bol očakávaný reťazec, alebo celé číslo",
    "The input does not appear to be an integer" => "Hodnota nie je celé číslo",
    
    // Zend\I18n\Validator\PhoneNumber
    "The input does not match a phone number format" => "Zadaná hodnota nezodpovedá formatu telefonného čísla",
    "The country provided is currently unsupported" => "The country provided is currently unsupported",
    "Invalid type given. String expected" => "Chybný typ. Bol očakávaný reťazec.",
    
    // Zend\I18n\Validator\PostCode
    "Invalid type given. String or integer expected" => "Chybný typ. Bol očakávaný reťazec, alebo celé číslo",
    "The input does not appear to be a postal code" => "Hodnota nevyzerá ako PSČ",
    "An exception has been raised while validating the input" => "Počas validácie bola vyvolaná výnimka",
    
    // Zend\Validator\Barcode
    "The input failed checksum validation" => "Hodnota má chybný kontrolný súčet",
    "The input contains invalid characters" => "Hodnota obsahuje neplatné znaky",
    "The input should have a length of %length% characters" => "Hodnota by mal mať dĺžku %length% znakov",
    "Invalid type given. String expected" => "Chybný typ. Bol očakávaný reťazec.",

    // Zend\Validator\Between
    "The input is not between '%min%' and '%max%', inclusively" => "Hodnota nie je medzi '%min%' a '%max%', vrátane",
    "The input is not strictly between '%min%' and '%max%'" => "Hodnota nie je presne medzi '%min%' a '%max%'",

    // Zend\Validator\Callback
    "The input is not valid" => "Hodnota nie je platná",
    "An exception has been raised within the callback" => "Počas volania bola vyvolaná výnimka",

    // Zend\Validator\CreditCard
    "The input seems to contain an invalid checksum" => "Hodnota obsahuje neplatný kontrolný súčet",
    "The input must contain only digits" => "Hodnota musí obsahovať iba čísla",
    "Invalid type given. String expected" => "Chybný typ. Bol očakávaný reťazec",
    "The input contains an invalid amount of digits" => "Hodnota obsahuje neplatný počet číslic",
    "The input is not from an allowed institute" => "Hodnota nie je od povolenej spoločnosti",
    "The input seems to be an invalid credit card number" => "Hodnota nie je platné číslo kreditnej karty",
    "An exception has been raised while validating the input" => "Počas validácie bola vyvoláná výnimka",

    // Zend\Validator\Csrf
    "The form submitted did not originate from the expected site" => "Odoslaný formulár nepochádza z predpokladanej stránky",
    
    // Zend\Validator\Date
    "Invalid type given. String, integer, array or DateTime expected" => "Chybný typ. Bol očakávaný reťazec, číslo, pole, alebo DateTime",
    "The input does not appear to be a valid date" => "Hodnota nie je platný dátum",
    "The input does not fit the date format '%format%'" => "Hodnota nezodpovedá formátu dátumu '%format%'",

    // Zend\Validator\DateStep
    "The input is not a valid step" => "Hodnota nie je platný krok",
    
    // Zend\Validator\Db\AbstractDb
    "No record matching the input was found" => "Nebol nájdený žiadny záznam zodpovedajúci zadanej hodnote",
    "A record matching the input was found" => "Bol nájdený záznam zodpovedajúci zadanej hodnote",

    // Zend\Validator\Digits
    "The input must contain only digits" => "Hodnota musí obsahovať len číslice",
    "The input is an empty string" => "Hodnota je prázdny reťazec",
    "Invalid type given. String, integer or float expected" => "Chybný typ. Bol očakávaný reťazec, celé alebo desatinné číslo",
    
    // Zend\Validator\EmailAddress
    "Invalid type given. String expected" => "Chybný typ. Bol očakávaný reťazec",
    "The input is not a valid email address. Use the basic format local-part@hostname" => "'%value%' nie je platná e-mailová adresa. Použite formát local-part@hostname",
    "'%hostname%' is not a valid hostname for the email address" => "'%hostname%' nie je platný hostname pre emailovú adresu",
    "'%hostname%' does not appear to have any valid MX or A records for the email address" => "'%hostname%' neobsahuje platný MX záznam pre e-mailovú adresu",
    "'%hostname%' is not in a routable network segment. The email address should not be resolved from public network" => "'%hostname%' nie je v smerovateľnom úseku sieťe. E-mailová adresa by nemala byť požadovaná z verejnej siete",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' nemôže byť porovnaný voči dot-atom formátu",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' nemôže byť porovnaný voči quoted-string formátu",
    "'%localPart%' is not a valid local part for the email address" => "'%localPart%' nie je platná 'local part' pre e-mailovú adresu",
    "The input exceeds the allowed length" => "Hodnota prekročila povolenú dĺžku",

    // Zend\Validator\Explode
    "Invalid type given" => "Chybný typ",
    
    // Zend\Validator\File\Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "Príliš veľa súborov. Maximum je '%max%', ale bolo zadaných '%count%'",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "Príliš málo súborov. Minimum je '%min%', ale bol zadaný len '%count%'",

    // Zend\Validator\File\Crc32
    "File does not match the given crc32 hashes" => "Súbor nezodpovedá zadanému crc32 hashu",
    "A crc32 hash could not be evaluated for the given file" => "Pre zadaný súbor nemohol byť vypočítaný crc32 hash",
    "File is not readable or does not exist" => "Súbor buď nie je čitateľný, alebo neexistuje",

    // Zend\Validator\File\ExcludeExtension
    "File has an incorrect extension" => "Súbor má nesprávnu príponu",
    "File is not readable or does not exist" => "Súbor buď nie je čitateľný, alebo neexistuje",

    // Zend\Validator\File\Exists
    "File does not exist" => "Súbor neexistuje",

    // Zend\Validator\File\Extension
    "File has an incorrect extension" => "Súbor má nesprávnu príponu",
    "File is not readable or does not exist" => "Súbor buď nie je čitateľný, alebo neexistuje",

    // Zend\Validator\File\FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "Súčet veľkostí všetkých súborov by mal byť maximálne '%max%', ale je '%size%'",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "Súčet veľkostí všetkých súborov by mal byť najmenej '%min%', ale je '%size%'",
    "One or more files can not be read" => "Jeden, alebo viac súborov nie je možné načítať",

    // Zend\Validator\File\Hash
    "File does not match the given hashes" => "Súbor nezodpovedá danému hashu",
    "A hash could not be evaluated for the given file" => "Hash nemohol byť pre daný súbor vypočítaný",
    "File is not readable or does not exist" => "Súbor buď nie je čitateľný, alebo neexistuje",

    // Zend\Validator\File\ImageSize
    "Maximum allowed width for image should be '%maxwidth%' but '%width%' detected" => "Maximálna šírka obrázku by mala byť '%maxwidth%', ale je '%width%'",
    "Minimum expected width for image should be '%minwidth%' but '%width%' detected" => "Minimálna šírka obrázku by mala byť '%minwidth%', ale je '%width%'",
    "Maximum allowed height for image should be '%maxheight%' but '%height%' detected" => "Maximálna výška obrázku by mala byť '%maxheight%', ale je '%height%'",
    "Minimum expected height for image should be '%minheight%' but '%height%' detected" => "Minimálna výška obrázku by mala byť '%minheight%', ale je '%height%'",
    "The size of image could not be detected"=> "Nebolo možné zistiť rozmery obrázka",
    "File is not readable or does not exist" => "Súbor buď nie je čitateľný, alebo neexistuje",

    // Zend\Validator\File\IsCompressed
    "File is not compressed, '%type%' detected" => "Súbor nie je komprimovaný, ale '%type%'",
    "The mimetype could not be detected from the file" => "Mimetyp súboru nebolo možné zisťit",
    "File is not readable or does not exist" => "Súbor buď nie je čitateľný, alebo neexistuje",

    // Zend\Validator\File\IsImage
    "File is no image, '%type%' detected" => "Súbor '%value%' nie je obrázok, ale '%type%'",
    "The mimetype could not be detected from the file" => "Mimetyp súboru nebolo možné zistiť",
    "File is not readable or does not exist" => "Súbor buď nie je čitateľný, alebo neexistuje",

    // Zend\Validator\File\Md5
    "File does not match the given md5 hashes" => "Súbor nezodpovedá danému md5 hashu",
    "An md5 hash could not be evaluated for the given file" => "Md5 hash nemohol byť pre daný súbor vypočítaný",
    "File is not readable or does not exist" => "Súbor buď nie je čitateľný, alebo neexistuje",

    // Zend\Validator\File\MimeType
    "File has an incorrect mimetype of '%type%'" => "Súbor má neplatný mimetyp '%type%'",
    "The mimetype could not be detected from the file" => "Mimetyp súboru nebolo možné zistiť",
    "File is not readable or does not exist" => "Súbor buď nie je čitateľný, alebo neexistuje",

    // Zend\Validator\File\NotExists
    "File exists" => "Súbor existuje",

    // Zend\Validator\File\Sha1
    "File does not match the given sha1 hashes" => "Súbor nezodpovedá danému sha1 hashu",
    "A sha1 hash could not be evaluated for the given file" => "Sha1 hash nemohol byť pre daný súbor vypočítaný",
    "File is not readable or does not exist" => "Súbor buď nie je čitateľný, alebo neexistuje",

    // Zend\Validator\File\Size
    "Maximum allowed size for file is '%max%' but '%size%' detected" => "Maximálna povolená veľkosť súboru je '%max%', ale súbor má '%size%'",
    "Minimum expected size for file is '%min%' but '%size%' detected" => "Minimálna veľkosť súboru je '%min%', ale súbor má '%size%'",
    "File is not readable or does not exist" => "Súbor buď nie je čitateľný, alebo neexistuje",

    // Zend\Validator\File\Upload
    "File '%value%' exceeds the defined ini size" => "Súbor '%value%' prekročil veľkosť definovanú v ini súbore",
    "File '%value%' exceeds the defined form size" => "Súbor '%value%' prekročil veľkosť definovanú vo formulári",
    "File '%value%' was only partially uploaded" => "Súbor '%value%' bol odoslaný len čiastočne",
    "File '%value%' was not uploaded" => "Súbor '%value%' nebol odoslaný",
    "No temporary directory was found for file '%value%'" => "Pre súbor '%value%' nebol nájdený žiadny dočasný adresár",
    "File '%value%' can't be written" => "Súbor '%value%' nemôže byť zapísaný",
    "A PHP extension returned an error while uploading the file '%value%'" => "PHP rozšírenie vrátilo chybu počas nahrávania súboru '%value%'",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "Súbor '%value%' bol neoprávnene nahraný. Môže se jednať o útok",
    "File '%value%' was not found" => "Súbor '%value%' nebol nájdený",
    "Unknown error while uploading file '%value%'" => "Počas odosielania súboru '%value%' došlo k chybe",

    // Zend\Validator\File\UploadFile
    "File exceeds the defined ini size" => "Súbor prekročil veľkosť definovanú v ini súbore",
    "File exceeds the defined form size" => "Súbor prekročil veľkosť definovanú vo formulári",
    "File was only partially uploaded" => "Súbor bol odoslaný len čiastočne",
    "File was not uploaded" => "Súbor nebol odoslaný",
    "No temporary directory was found for file" => "Pre súbor nebol najdený žiadny dočasný adresár",
    "File can't be written" => "Súbor nemože byť zapisaný",
    "A PHP extension returned an error while uploading the file" => "PHP rozšírenie vrátilo chybu počas nahrávania súboru",
    "File was illegally uploaded. This could be a possible attack" => "Súbor bol neoprávnene nahraný. Môže se jednať o útok",
    "File was not found" => "Súbor nebol nájdený",
    "Unknown error while uploading file" => "Počas odosielania súboru došlo k chybe",
    
    // Zend\Validator\File\WordCount
    "Too many words, maximum '%max%' are allowed but '%count%' were counted" => "Príliš veľa slov. Maximálne je ich dovolených '%max%', ale bolo zadaných '%count%'",
    "Too few words, minimum '%min%' are expected but '%count%' were counted" => "Príliš málo slov. Musí ich byť aspoň '%min%', ale bolo zadaných len '%count%'",
    "File is not readable or does not exist" => "Súbor buď nie je čitateľný, alebo neexistuje",

    // Zend\Validator\GreaterThan
    "The input is not greater than '%min%'" => "Hodnota nie je väčšia ako '%min%'",
    "The input is not greater or equal than '%min%'" => "Hodnota nie je väčšia alebo rovná '%min%'",

    // Zend\Validator\Hex
    "Invalid type given. String expected" => "Chybný typ. Bol očakávaný reťazec",
    "The input contains non-hexadecimal characters" => "Hodnota neobsahuje len znaky hexadecimálnych čísel",

    // Zend\Validator\Hostname
    "The input appears to be a DNS hostname but the given punycode notation cannot be decoded" => "Zadaná hodnota vyzerá ako DNS hostname ale zadanú punycode notáciu nie je možné dekódovať",
    "Invalid type given. String expected" => "Chybný typ. Bol očakávaný reťazec",
    "The input appears to be a DNS hostname but contains a dash in an invalid position" => "Zadaná hodnota vyzerá ako hostname, ale obsahuje pomlčku na nedovolenom mieste",
    "The input does not match the expected structure for a DNS hostname" => "Zadaná hodnota nezodpovedá očakávanej štruktúre hostname",
    "The input appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "Zadaná hodnota vyzerá ako hostname, ale nezodpovedá formátu hostname pre '%tld%'",
    "The input does not appear to be a valid local network name" => "Zadaná hodnota nevyzerá ako platné sieťové meno",
    "The input does not appear to be a valid URI hostname" => "Zadaná hodnota nevyzerá ako platné URI hostname",
    "The input appears to be an IP address, but IP addresses are not allowed" => "Zadaná hodnota vyzerá ako IP adresa, ale tie nie sú dovolené",
    "The input appears to be a local network name but local network names are not allowed" => "Zadaná hodnota vyzerá ako hostname lokálnej siete, tie ale nie sú povolené",    
    "The input appears to be a DNS hostname but cannot extract TLD part" => "Zadaná hodnota síce vyzerá ako hostname, ale nemožno určiť TLD",
    "The input appears to be a DNS hostname but cannot match TLD against known list" => "Zadaná hodnota vyzerá ako hostname, ale nemohol byť overený voči známym TLD",
    
    // Zend\Validator\Iban
    "Unknown country within the IBAN" => "Neznámý štát v IBAN",
    "Countries outside the Single Euro Payments Area (SEPA) are not supported" => "Štáty mimo jednotný europský platobný priestor (SEPA) nie su podporované",
    "The input has a false IBAN format" => "Hodnota nie je platný formát IBAN",
    "The input has failed the IBAN check" => "Hodnota neprešla kontrolou IBAN",

    // Zend\Validator\Identical
    "The two given tokens do not match" => "Zadané položky nie su zhodné",
    "No token was provided to match against" => "Nebola zadáná položka pre porovnanie",

    // Zend\Validator\InArray
    "The input was not found in the haystack" => "Hodnota nebola nájdená v zozname",

    // Zend\Validator\Ip
    "Invalid type given. String expected" => "Chybný typ. Bol očakávaný reťazec",
    "The input does not appear to be a valid IP address" => "Hodnota nie je platná IP adresa",

    // Zend\Validator\IsInstanceOf
    "The input is not an instance of '%className%'" => "Hodnota nie je  inštanciou triedy '%className%'",
    
    // Zend\Validator\Isbn
    "Invalid type given. String or integer expected" => "Chybný typ. Bol očakávaný reťazec, alebo celé číslo",
    "The input is not a valid ISBN number" => "Hodnota nie je platné ISBN",

    // Zend\Validator\LessThan
    "The input is not less than '%max%'" => "Hodnota nie je menej ako '%max%'",
    "The input is not less or equal than '%max%'" => "Hodnota nie je menej alebo presne než '%max%'",

    // Zend\Validator\NotEmpty
    "Value is required and can't be empty" => "Položka je povinná a nesmie byť prázdna",
    "Invalid type given. String, integer, float, boolean or array expected" => "Chybný typ. Bol očakávaný reťazec, celé alebo desatinné číslo, boolean alebo pole",
        
    // Zend\Validator\Regex
    "Invalid type given. String, integer or float expected" => "Chybný typ. Bol očakávaný reťazec, celé alebo desatinné číslo",
    "The input does not match against pattern '%pattern%'" => "Hodnota nezodpovedá šablóne '%pattern%'",
    "There was an internal error while using the pattern '%pattern%'" => "Počas spracovania šablóny '%pattern%' došlo k internej chybe",

    // Zend\Validator\Sitemap\Changefreq
    "The input is not a valid sitemap changefreq" => "Hodnota nie je platný 'changefreq' pre sitemapu",
    "Invalid type given. String expected" => "Chybný typ. Bol očakávaný reťazec",

    // Zend\Validator\Sitemap\Lastmod
    "The input is not a valid sitemap lastmod" => "Hodnota nie je platný 'lastmod' pre sitemapu",
    "Invalid type given. String expected" => "Chybný typ. Bol očakávaný reťazec",

    // Zend\Validator\Sitemap\Loc
    "The input is not a valid sitemap location" => "Hodnota nie je platná 'location' pre sitemapu",
    "Invalid type given. String expected" => "Chybný typ. Bol očakávaný reťazec",

    // Zend\Validator\Sitemap\Priority
    "The input is not a valid sitemap priority" => "Hodnota nie je platná 'priority' pre sitemapu",
    "Invalid type given. Numeric string, integer or float expected" => "Chybný typ. Bol očakávaný číselný reťazec, celé alebo desatinné číslo",

    // Zend\Validator\Step
    "Invalid value given. Scalar expected" => "Chybný hodnota. Bola očakávana skalarna hodnota",
    "The input is not a valid step" => "Hodnota nie je platný krok",
    
    // Zend\Validator\StringLength
    "Invalid type given. String expected" => "Chybný typ. Bol očakávaný reťazec",
    "The input is less than %min% characters long" => "Hodnota je kratšia ako %min% znakov",
    "The input is more than %max% characters long" => "Hodnota je dlhšia ako %max% znakov",
    
    // Zend\Validator\Uri
    "Invalid type given. String expected" => "Chybný typ. Bol očakávaný reťazec",
    "The input does not appear to be a valid Uri" => "Hodnota nevyzerá ako platná Uri",
);
