<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * EN-Revision: 16.Jul.2013
 */
return array(
    // Zend\I18n\Validator\Alnum
    "Invalid type given. String, integer or float expected" => "Érvénytelen típus. Érvényes típusok: karakterláncok, egészek, tizedes törtek",
    "The input contains characters which are non alphabetic and no digits" => "A megadott érték tartalmaz betűkön és számjegyeken kívüli karaktereket",
    "The input is an empty string" => "A megadott érték üres",

    // Zend\I18n\Validator\Alpha
    "Invalid type given. String expected" => "Érvénytelen típus. Érvényes típusok: karakterláncok",
    "The input contains non alphabetic characters" => "A megadott érték tartalmaz betűkön kívüli karaktereket",
    "The input is an empty string" => "A megadott érték üres",

    // Zend\I18n\Validator\DateTime
    "Invalid type given. String expected" => "Érvénytelen típus. Érvényes típusok: karakterláncok",
    "The input does not appear to be a valid datetime" => "A megadott érték nem tűnik érvényes dátum-idő-nek",

    // Zend\I18n\Validator\Float
    "Invalid type given. String, integer or float expected" => "Érvénytelen típus. Érvényes típusok: karakterláncok, egészek, tizedes törtek",
    "The input does not appear to be a float" => "A megadott érték nem tűnik érvényes számnak",

    // Zend\I18n\Validator\Int
    "Invalid type given. String or integer expected" => "Érvénytelen típus. Érvényes típusok: karakterláncok, egészek",
    "The input does not appear to be an integer" => "A megadott érték nem tűnik érvényes egész számnak",

    // Zend\I18n\Validator\PhoneNumber
    "The input does not match a phone number format" => "A megadott érték nem felel meg a várt telefon szám formátumnak",
    "The country provided is currently unsupported" => "A megadott ország jelenleg nem támogatott",
    "Invalid type given. String expected" => "Érvénytelen típus. Érvényes típusok: karakterláncok",

    // Zend\I18n\Validator\PostCode
    "Invalid type given. String or integer expected" => "Érvénytelen típus. Érvényes típusok: karakterláncok, egészek",
    "The input does not appear to be a postal code" => "A megadott érték nem tűnik irányítószámnak",
    "An exception has been raised while validating the input" => "A megadott érték érvényesítése közben nem várt hiba történt",

    // Zend\Validator\Barcode
    "The input failed checksum validation" => "A megadott érték ellenőrző összege nem megfelelő",
    "The input contains invalid characters" => "A megadott érték érvénytelen karaktereket tartalmaz",
    "The input should have a length of %length% characters" => "A megadott érték hossza %length% karakter kell, hogy legyen",
    "Invalid type given. String expected" => "Érvénytelen típus. Érvényes típusok: karakterláncok",

    // Zend\Validator\Between
    "The input is not between '%min%' and '%max%', inclusively" => "A megadott érték nem esik - megengedően - '%min%' és '%max%' közé",
    "The input is not strictly between '%min%' and '%max%'" => "A megadott érték nem esik - szigorúan - '%min%' és '%max%' közé",

    // Zend\Validator\Callback
    "The input is not valid" => "A megadott érték érvénytelen",
    "An exception has been raised within the callback" => "A visszahívandó függvény nem várt hibát okozott.",

    // Zend\Validator\CreditCard
    "The input seems to contain an invalid checksum" => "A megadott érték érvénytelen ellenőrző összeget tartalmaz",
    "The input must contain only digits" => "A megadott érték csak számjegyeket tartalmazhat",
    "Invalid type given. String expected" => "Érvénytelen típus. Érvényes típusok: karakterláncok",
    "The input contains an invalid amount of digits" => "A megadott érték érvénytelen számú számjegyet tartalmaz",
    "The input is not from an allowed institute" => "A megadott érték nem megengedett intézethez tartozik",
    "The input seems to be an invalid credit card number" => "A megadott érték érvénytelen hitelkártya számnak tűnik",
    "An exception has been raised while validating the input" => "A megadott érték érvényesítése közben nem várt hiba történt",

    // Zend\Validator\Csrf
    "The form submitted did not originate from the expected site" => "Az űrlap nem a várt oldalról érkezett (Elképzelhető, hogy ez egy CSRF támadási kisérlet)",

    // Zend\Validator\Date
    "Invalid type given. String, integer, array or DateTime expected" => "Érvénytelen típus. Érvényes típusok: karakterláncok, egész számok, tömbök, \\DateTime típus",
    "The input does not appear to be a valid date" => "A megadott érték nem tűnik érvényes dátumnak",
    "The input does not fit the date format '%format%'" => "A megadott érték nem felel meg a következő dátum formátumnak: '%format%'",

    // Zend\Validator\DateStep
    "The input is not a valid step" => "A megadott érték nem érvényes lépcső",

    // Zend\Validator\Db\AbstractDb
    "No record matching the input was found" => "Nem található megadott értéknek megfelelő adatbázis rekord",
    "A record matching the input was found" => "A megadott értéknek megfelelő adatbázis rekord létezik",

    // Zend\Validator\Digits
    "The input must contain only digits" => "A megadott érték csak számjegyeket tartalmazhat",
    "The input is an empty string" => "A megadott érték üres",
    "Invalid type given. String, integer or float expected" => "Érvénytelen típus. Érvényes típusok: karakterláncok, egészek, tizedes törtek",

    // Zend\Validator\EmailAddress
    "Invalid type given. String expected" => "Érvénytelen típus. Érvényes típusok: karakterláncok",
    "The input is not a valid email address. Use the basic format local-part@hostname" => "A megadott érték nem érvényes email cím. A helyinev@kiszolgalo formátum használható.",
    "'%hostname%' is not a valid hostname for the email address" => "'%hostname%' nem érvényes kiszolgáló név az email címhez",
    "'%hostname%' does not appear to have any valid MX or A records for the email address" => "'%hostname%' nem tűnik érvényes MX vagy A rekordnak az email címhez",
    "'%hostname%' is not in a routable network segment. The email address should not be resolved from public network" => "'%hostname%' nem visszafejthető hálózati szegmens. Az email cím nem visszafejthető a nyilvános hálózaton",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' nem feleltethető meg a dot-atom formátumnak.",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' nem feleltethető meg az idézőjelezett-karakterlánc (quoted-string) formátumnak",
    "'%localPart%' is not a valid local part for the email address" => "'%localPart%' nem érvényes lokális név az email címhez",
    "The input exceeds the allowed length" => "A megadott érték hossza meghaladja a megengedettet",

    // Zend\Validator\Explode
    "Invalid type given" => "A megadott érték típusa érvénytelen",

    // Zend\Validator\File\Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "Túl sok fájl, maximum '%max%' megengedett, de '%count%' érkezett",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "Túl kevés fájl, minimum '%min%' megengedett,de '%count%' érkezett",

    // Zend\Validator\File\Crc32
    "File does not match the given crc32 hashes" => "A fájl nem felel meg a megadott crc32 hasheknek",
    "A crc32 hash could not be evaluated for the given file" => "A crc32 hash nem számítható ki a megadott fájlhoz",
    "File is not readable or does not exist" => "A fájl nem létezik vagy nem olvasható",

    // Zend\Validator\File\ExcludeExtension
    "File has an incorrect extension" => "A fájl kiterjesztése nem megfelelő",
    "File is not readable or does not exist" => "A fájl nem létezik vagy nem olvasható",

    // Zend\Validator\File\Exists
    "File does not exist" => "A fájl nem létezik",

    // Zend\Validator\File\Extension
    "File has an incorrect extension" => "A fájl kiterjesztése nem megfelelő",
    "File is not readable or does not exist" => "A fájl nem létezik vagy nem olvasható",

    // Zend\Validator\File\FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "Az összes fájl együttes mérete maximum '%max%' lehet, de '%size%' méretű fájl érkezett",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "Az összes fájl együttes mérete minimum '%min%' lehet, de '%size%' méretű fájl érkezett",
    "One or more files can not be read" => "Egy vagy több fájl olvasása nem sikerült",

    // Zend\Validator\File\Hash
    "File does not match the given hashes" => "A fájl nem felel meg az adott hasheknek",
    "A hash could not be evaluated for the given file" => "A hash nem számítható ki az adott fájlhoz",
    "File is not readable or does not exist" => "A fájl nem létezik vagy nem olvasható",

    // Zend\Validator\File\ImageSize
    "Maximum allowed width for image should be '%maxwidth%' but '%width%' detected" => "A kép megengedett legnagyobb szélessége '%maxwidth%' de '%width%' széles kép érkezett",
    "Minimum expected width for image should be '%minwidth%' but '%width%' detected" => "A kép megengedett legnagyobb szélessége '%minwidth%' de '%width%' széles kép érkezett",
    "Maximum allowed height for image should be '%maxheight%' but '%height%' detected" => "A kép megengedett legnagyobb magassága '%maxheight%' de '%height%' magas kép érkezett",
    "Minimum expected height for image should be '%minheight%' but '%height%' detected" => "A kép megengedett legnagyobb magassága '%minheight%' de '%height%' magas kép érkezett",
    "The size of image could not be detected" => "A kép méretét nem sikerült meghatározni",
    "File is not readable or does not exist" => "A fájl nem létezik vagy nem olvasható",

    // Zend\Validator\File\IsCompressed
    "File is not compressed, '%type%' detected" => "A kapott fájl nem tömörített, '%type%' típusúnak tűnik.",
    "The mimetype could not be detected from the file" => "A fájl mime típusát nem sikerült azonosítani",
    "File is not readable or does not exist" => "A fájl nem létezik vagy nem olvasható",

    // Zend\Validator\File\IsImage
    "File is no image, '%type%' detected" => "A fájl nem kép, '%type%' típusúnak tűnik",
    "The mimetype could not be detected from the file" => "A fájl mime típusát nem sikerült azonosítani",
    "File is not readable or does not exist" => "A fájl nem létezik vagy nem olvasható",

    // Zend\Validator\File\Md5
    "File does not match the given md5 hashes" => "A fájl nem felel meg a megadott md5 hasheknek",
    "An md5 hash could not be evaluated for the given file" => "Az md5 hash nem számítható ki a megadott fájlhoz",
    "File is not readable or does not exist" => "A fájl nem létezik vagy nem olvasható",

    // Zend\Validator\File\MimeType
    "File has an incorrect mimetype of '%type%'" => "File has an incorrect mimetype of '%type%'",
    "The mimetype could not be detected from the file" => "A fájl mime típusát nem sikerült azonosítani",
    "File is not readable or does not exist" => "A fájl nem létezik vagy nem olvasható",

    // Zend\Validator\File\NotExists
    "File exists" => "A fájl már létezik",

    // Zend\Validator\File\Sha1
    "File does not match the given sha1 hashes" => "A fájl nem felel meg a megadott sha1 hasheknek",
    "A sha1 hash could not be evaluated for the given file" => "Az sha1 hash nem számítható ki a megadott fájlhoz",
    "File is not readable or does not exist" => "A fájl nem létezik vagy nem olvasható",

    // Zend\Validator\File\Size
    "Maximum allowed size for file is '%max%' but '%size%' detected" => "Maximum megengedett fájlméret '%max%', de '%size%' érkezett",
    "Minimum expected size for file is '%min%' but '%size%' detected" => "Minimum megengedett fájlméret '%min%', de '%size%' érkezett",
    "File is not readable or does not exist" => "A fájl nem létezik vagy nem olvasható",

    // Zend\Validator\File\Upload
    "File '%value%' exceeds the defined ini size" => "A(z) '%value%' fájl mérete meghaladja a PHP beállításokban megengedettet",
    "File '%value%' exceeds the defined form size" => "A(z) '%value%' fájl mérete meghaladja az űrlap által megengedettet",
    "File '%value%' was only partially uploaded" => "A(z) '%value%' fájl csak részlegesen lett feltöltve",
    "File '%value%' was not uploaded" => "A(z) '%value%' fájl nem lett feltöltve",
    "No temporary directory was found for file '%value%'" => "A(z) '%value%' fájl számára nem található átmeneti könyvtár",
    "File '%value%' can't be written" => "A(z) '%value%' fájl nem írható",
    "A PHP extension returned an error while uploading the file '%value%'" => "Egy PHP kiterjesztés nem várt hibát okozott a(z) '%value%' fájl feltöltése közben",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "A(z) '%value%' fájl illegálisan került feltöltésre. Elképzelhető, hogy ez egy támadás következménye",
    "File '%value%' was not found" => "A(z) '%value%' fájl nem található",
    "Unknown error while uploading file '%value%'" => "Ismeretlen hiba történt a(z) '%value%' fájl feltöltése közben",

    // Zend\Validator\File\UploadFile
    "File exceeds the defined ini size" => "A fájl mérete meghaladja a PHP beállításokban megengedettet",
    "File exceeds the defined form size" => "A fájl mérete meghaladja az űrlap által megengedettet",
    "File was only partially uploaded" => "A fájl csak részlegesen lett feltöltve",
    "File was not uploaded" => "A fájl nem lett feltöltve",
    "No temporary directory was found for file" => "A fájl számára nem található átmeneti könyvtár",
    "File can't be written" => "A fájl nem írható",
    "A PHP extension returned an error while uploading the file" => "Egy PHP kiterjesztés nem várt hibát okozott a fájl feltöltése közben",
    "File was illegally uploaded. This could be a possible attack" => "A fájl illegálisan került feltöltésre. Elképzelhető, hogy ez egy támadás következménye",
    "File was not found" => "A fájl nem található",
    "Unknown error while uploading file" => "Ismeretlen hiba történt a fájl feltöltése közben",

    // Zend\Validator\File\WordCount
    "Too many words, maximum '%max%' are allowed but '%count%' were counted" => "Túl sok szó, maximum '%max%' szó megengedett, de '%count%' érkezett",
    "Too few words, minimum '%min%' are expected but '%count%' were counted" => "Túl kevés szó, minimum '%min%' szó megengedett, de '%count%' érkezett",
    "File is not readable or does not exist" => "A fájl nem létezik vagy nem olvasható",

    // Zend\Validator\GreaterThan
    "The input is not greater than '%min%'" => "A megadott érték nem nagyobb, mint '%min%'",
    "The input is not greater or equal than '%min%'" => "A megadott érték nem nagyobb vagy egyenlő, mint '%min%'",

    // Zend\Validator\Hex
    "Invalid type given. String expected" => "Érvénytelen típus. Érvényes típusok: karakterláncok",
    "The input contains non-hexadecimal characters" => "A megadott érték nem-hexadecimális karaktert tartalmaz",

    // Zend\Validator\Hostname
    "The input appears to be a DNS hostname but the given punycode notation cannot be decoded" => "A megadott érték DNS kiszolgáló névnek tűnik, de a megadott punycode jelölés nem visszakódolható",
    "Invalid type given. String expected" => "Érvénytelen típus. Érvényes típusok: karakterláncok",
    "The input appears to be a DNS hostname but contains a dash in an invalid position" => "A megadott érték érvényes DNS kiszolgáló névnek tűnik, de érvénytelen helyen tartalmaz kötőjelet",
    "The input does not match the expected structure for a DNS hostname" => "A megadott érték nem felel meg a várt DNS kiszolgáló név struktúrának",
    "The input appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "A megadott érték DNS kiszolgáló névnek tűnik, de nem sikerült megfeleltetni a '%tld%' kiszolgáló név sémának",
    "The input does not appear to be a valid local network name" => "A megadott érték nem tűnik érvényes helyi hálózati névnek",
    "The input does not appear to be a valid URI hostname" => "A megadott érték nem tűnik érvényes URI kiszolgáló névnek",
    "The input appears to be an IP address, but IP addresses are not allowed" => "A megadott érték IP címnek tűnik, de IP címek nem megengedettek",
    "The input appears to be a local network name but local network names are not allowed" => "A megadott érték helyi hálózati címnek tűnik, de helyi hálózati címek nem megengedettek",
    "The input appears to be a DNS hostname but cannot extract TLD part" => "A megadott érték DNS kiszolgáló névnek tűnik, de a TLD részt nem sikerült visszafejteni",
    "The input appears to be a DNS hostname but cannot match TLD against known list" => "A megadott érték DNS kiszolgáló névnek tűnik, de a megadott TLD ismeretlen",

    // Zend\Validator\Iban
    "Unknown country within the IBAN" => "Ismeretlen ország az IBAN rendszerben",
    "Countries outside the Single Euro Payments Area (SEPA) are not supported" => "A SEPA fizetési övezeten kívüli országok nem támogatottak",
    "The input has a false IBAN format" => "A megadott érték nem felel meg az IBAN formátumnak",
    "The input has failed the IBAN check" => "A megadott érték nem felel meg az IBAN ellenőrzésnek",

    // Zend\Validator\Identical
    "The two given tokens do not match" => "A két megadott érték nem egyezik",
    "No token was provided to match against" => "Az összehasonlítási érték hiányzik",

    // Zend\Validator\InArray
    "The input was not found in the haystack" => "A megadott érték nem található az érvényes halmazban/tömbben.",

    // Zend\Validator\Ip
    "Invalid type given. String expected" => "Érvénytelen típus. Érvényes típusok: karakterláncok",
    "The input does not appear to be a valid IP address" => "A megadott érték nem tűnik érvényes IP címnek",

    // Zend\Validator\IsInstanceOf
    "The input is not an instance of '%className%'" => "A megadott érték nem '%className%'",

    // Zend\Validator\Isbn
    "Invalid type given. String or integer expected" => "Érvénytelen típus. Érvényes típusok: karakterláncok, egészek",
    "The input is not a valid ISBN number" => "A megadott érték nem érvényes ISBN szám",

    // Zend\Validator\LessThan
    "The input is not less than '%max%'" => "A megadott érték nem kisebb, mint '%max%'",
    "The input is not less or equal than '%max%'" => "A megadott érték nem kisebb vagy egyenlő, mint '%max%'",

    // Zend\Validator\NotEmpty
    "Value is required and can't be empty" => "A megadott érték kötelező és nem lehet üres",
    "Invalid type given. String, integer, float, boolean or array expected" => "Invalid type given. String, integer, float, boolean or array expected",

    // Zend\Validator\Regex
    "Invalid type given. String, integer or float expected" => "Érvénytelen típus. Érvényes típusok: karakterláncok, egészek, tizedes törtek",
    "The input does not match against pattern '%pattern%'" => "A megadott érték nem felel meg a '%pattern%' mintának",
    "There was an internal error while using the pattern '%pattern%'" => "A(z) '%pattern%' minta használata közben hiba történt",

    // Zend\Validator\Sitemap\Changefreq
    "The input is not a valid sitemap changefreq" => "A megadott érték nem érvényes `sitemap changefreq`",
    "Invalid type given. String expected" => "Érvénytelen típus. Érvényes típusok: karakterláncok",

    // Zend\Validator\Sitemap\Lastmod
    "The input is not a valid sitemap lastmod" => "A megadott érték nem érvényes `sitemap lastmod`",
    "Invalid type given. String expected" => "Érvénytelen típus. Érvényes típusok: karakterláncok",

    // Zend\Validator\Sitemap\Loc
    "The input is not a valid sitemap location" => "A megadott érték nem érvényes `sitemap location`",
    "Invalid type given. String expected" => "Érvénytelen típus. Érvényes típusok: karakterláncok",

    // Zend\Validator\Sitemap\Priority
    "The input is not a valid sitemap priority" => "The input is not a valid sitemap priority",
    "Invalid type given. Numeric string, integer or float expected" => "Érvénytelen típus. Érvényes típusok: numerikus karakterláncok, egészek vagy tizedes törtek",

    // Zend\Validator\Step
    "Invalid value given. Scalar expected" => "Érvénytelen típus. Érvényes típusok: skalárok (egész számok, tizedes törtek, karakterláncok, stb)",
    "The input is not a valid step" => "A megadott érték nem érvényes lépcső",

    // Zend\Validator\StringLength
    "Invalid type given. String expected" => "Érvénytelen típus. Érvényes típusok: karakterláncok",
    "The input is less than %min% characters long" => "A megadott érték rövidebb, mint %min% hosszúságú",
    "The input is more than %max% characters long" => "A megadott érték hosszabb, mint %max% hosszúságú",

    // Zend\Validator\Uri
    "Invalid type given. String expected" => "Érvénytelen típus. Érvényes típusok: karakterláncok",
    "The input does not appear to be a valid Uri" => "A megadott érték nem tűnik érvényes Uri-nak",
);
