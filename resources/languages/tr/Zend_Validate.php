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
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * EN-Revision: 28.Sept.2012
 */
return array(
    // Zend_I18n_Validator_Alnum
    "Invalid type given. String, integer or float expected" => "Geçersiz tür verildi, Dize, tamsayı ya da ondalık sayı bekleniyor",
    "The input contains characters which are non alphabetic and no digits" => "Girdi, harf ve rakam olmayan karakterler içeriyor",
    "The input is an empty string" => "Girdi boş bir dizedir",

    // Zend_I18n_Validator_Alpha
    "Invalid type given. String expected" => "Geçersiz tür verildi. Dize bekleniyor",
    "The input contains non alphabetic characters" => "Girdi alfabetik olmayan karakterler içeriyor",
    "The input is an empty string" => "Girdi boş bir dizedir",

    // Zend_I18n_Validator_Float
    "Invalid type given. String, integer or float expected" => "Geçersiz tür verildi, Dize, tamsayı ya da ondalık sayı bekleniyor",
    "The input does not appear to be a float" => "Girdi ondalık bir sayı olarak görünmüyor",

    // Zend_I18n_Validator_Int
    "Invalid type given. String or integer expected" => "Geçersiz tür verildi. Dize veya tamsayı bekleniyor",
    "The input does not appear to be an integer" => "Girdi bir tamsayı olarak görünmüyor",

    // Zend_I18n_Validator_PostCode
    "Invalid type given. String or integer expected" => "Geçersiz tür verildi. Dizge veya tamsayı bekleniyor",
    "The input does not appear to be a postal code" => "Girdi bir posta kodu olarak görünmüyor",
    "An exception has been raised while validating the input" => "Girdi doğrulanırken bir istisna meydana geldi",

    // Zend_Validator_Barcode
    "The input failed checksum validation" => "Girişin sağlama doğrulaması başarısız oldu",
    "The input contains invalid characters" => "Girdi geçersiz karakterler içeriyor",
    "The input should have a length of %length% characters" => "Girdi %length% karakter uzunluğunda olmalıdır",
    "Invalid type given. String expected" => "Yanlış tür verildi. Dizge bekleniyor",

    // Zend_Validator_Between
    "The input is not between '%min%' and '%max%', inclusively" => "Girdi '%min%' ve '%max%' karakter arasında değil",
    "The input is not strictly between '%min%' and '%max%'" => "Girdi tamamen '%min%' ve '%max%' karakter arasında değil",

    // Zend_Validator_Callback
    "The input is not valid" => "Girdi geçerli değil",
    "An exception has been raised within the callback" => "Callback içinde bir istisna meydana gelmiştir",

    // Zend_Validator_CreditCard
    "The input seems to contain an invalid checksum" => "Girdi geçersiz bir sağlama toplamı içeriyor",
    "The input must contain only digits" => "Girdi sadece rakam içermelidir",
    "Invalid type given. String expected" => "Yanlış tür verildi. Dizge bekleniyor",
    "The input contains an invalid amount of digits" => "Girdi geçersiz sayıda rakam içeriyor",
    "The input is not from an allowed institute" => "Girdi izin verilen bir kuruluştan değil",
    "The input seems to be an invalid creditcard number" => "Girdi geçersiz bir kredi kartı numarası gibi görünüyor",
    "An exception has been raised while validating the input" => "Girdi doğrulanırken bir hata meydana geldi",

    // Zend_Validator_Csrf
    "The form submitted did not originate from the expected site" => "Gönderilen form beklenen site kaynaklı gibi görünmüyor",

    // Zend_Validator_Date
    "Invalid type given. String, integer, array or DateTime expected" => "Geçersiz tür verildi. Dizge, tamsayı, dizi veya ZamanSaat bekleniyor",
    "The input does not appear to be a valid date" => "Girdi geçerli bir tarih olarak görünmüyor",
    "The input does not fit the date format '%format%'" => "Girdi '%format%' tarih biçimine uymuyor",

    // Zend_Validator_DateStep
    "Invalid type given. String, integer, array or DateTime expected" => "Geçersiz tür verildi. Dizge, tamsayı, dizi veya ZamanSaat bekleniyor",
    "The input does not appear to be a valid date" => "Girdi geçerli bir tarih olarak görünmüyor",
    "The input is not a valid step" => "Girdi geçerli bir dilim değildir",

    // Zend_Validator_Db_AbstractDb
    "No record matching the input was found" => "Girdi ile eşleşen hiçbir kayıt bulunamadı",
    "A record matching the input was found" => "Girdi ile eşleşen bir kayıt bulundu",

    // Zend_Validator_Digits
    "The input must contain only digits" => "Girdi sadece rakam içermelidir",
    "The input is an empty string" => "Girdi boş bir dizedir",
    "Invalid type given. String, integer or float expected" => "Geçersiz tür verildi, Dize, tamsayı ya da ondalık sayı bekleniyor",

    // Zend_Validator_EmailAddress
    "Invalid type given. String expected" => "Yanlış tür verildi. Dize bekleniyor",
    "The input is not a valid email address. Use the basic format local-part@hostname" => "Girdi geçerli bir E-posta adresi değil. local-part@hostname formatını kullanın",
    "'%hostname%' is not a valid hostname for the email address" => "'%hostname%' E-posta adresi için geçerli bir sağlayıcı değildir",
    "'%hostname%' does not appear to have any valid MX or A records for the email address" => "'%hostname%', E-posta adresi için geçerli bir A veya MX kaydına sahip gibi görünmüyor",
    "'%hostname%' is not in a routable network segment. The email address should not be resolved from public network" => "'%hostname%' yönlendirilebilir bir ağ katmanı değil. E-posta adresi açık bir ağdan çözümlenmemeli",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' dot-atom formatıyla uyuşmuyor",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' tırnaklı-dizge formatıyla uyuşmuyor",
    "'%localPart%' is not a valid local part for the email address" => "'%localPart%' e-posta için geçerli bir yerel parça değil",
    "The input exceeds the allowed length" => "Girdi izin verilen uzunluğu aşıyor",

    // Zend_Validator_Explode
    "Invalid type given. String expected" => "Yanlış tür verildi. Dize bekleniyor",

    // Zend_Validator_File_Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "Çok fazla dosya, en fazla '%max%' adete izin veriliyor fakat '%count%' adet verildi",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "Çok faz dosya, en az '%min%' adet bekleniyor fakat '%count%' adet verildi",

    // Zend_Validator_File_Crc32
    "File '%value%' does not match the given crc32 hashes" => "'%value%' dosyası verilen crc32 hash'ları ile eşleşmiyor",
    "A crc32 hash could not be evaluated for the given file" => "Bir crc32 hash'ı verilen dosya için değerlendirilemiyor",
    "File '%value%' is not readable or does not exist" => "'%value%' dosyası okunamıyor ya da yok",

    // Zend_Validator_File_ExcludeExtension
    "File '%value%' has a false extension" => "'%value%' dosyası yanlış bir uzantıya sahip",
    "File '%value%' is not readable or does not exist" => "'%value%' dosyası okunamıyor ya da yok",

    // Zend_Validator_File_Exists
    "File '%value%' does not exist" => "'%value%' dosyası yok",

    // Zend_Validator_File_Extension
    "File '%value%' has a false extension" => "'%value%' dosyası yanlış bir uzantıya sahip",
    "File '%value%' is not readable or does not exist" => "'%value%' dosyası okunamıyor ya da yok",

    // Zend_Validator_File_FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "Tüm dosyaların toplam boyutu en fazla '%max%' olmalıdır fakat '%size%' tespit edildi",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "Tüm dosyaların toplam boyutu en az '%min%' olmalıdır fakat '%size%' tespit edildi",
    "One or more files can not be read" => "Bir ya da daha fazla dosya okunamıyor",

    // Zend_Validator_File_Hash
    "File '%value%' does not match the given hashes" => "'%value%' dosyası verilen hash ile uyuşmuyor",
    "A hash could not be evaluated for the given file" => "Bir hash verilen dosya için değerlendirmeye alınamamıştır",
    "File '%value%' is not readable or does not exist" => "'%value%' dosyası okunamıyor ya da yok",

    // Zend_Validator_File_ImageSize
    "Maximum allowed width for image '%value%' should be '%maxwidth%' but '%width%' detected" => "'%value%' resim dosyasının genişliği en fazla '%maxwidth%' olmalıdır fakat '%width%' algılandı",
    "Minimum expected width for image '%value%' should be '%minwidth%' but '%width%' detected" => "'%value%' resim dosyasının genişliği en az '%minwidth%' olmalıdır fakat '%width%' algılandı",
    "Maximum allowed height for image '%value%' should be '%maxheight%' but '%height%' detected" => "'%value%' resim dosyasının yüksekliği en fazla '%maxheight%' olmalıdır fakat '%height%' algılandı",
    "Minimum expected height for image '%value%' should be '%minheight%' but '%height%' detected" => "'%value%' resim dosyasının yüksekliği en az '%minheight%' olmalıdır fakat '%height%' algılandı",
    "The size of image '%value%' could not be detected" => "'%value%' resminin boyutları algılanamadı",
    "File '%value%' is not readable or does not exist" => "'%value%' dosyası okunamıyor ya da yok",

    // Zend_Validator_File_IsCompressed
    "File '%value%' is not compressed, '%type%' detected" => "'%value%' dosyası sıkıştırılmış değil, '%type%' algılandı",
    "The mimetype of file '%value%' could not be detected" => "'%value%' dosyasının mime-tipi algılanamadı",
    "File '%value%' is not readable or does not exist" => "'%value%' dosyası okunamıyor ya da yok",

    // Zend_Validator_File_IsImage
    "File '%value%' is no image, '%type%' detected" => "'%value%' resim değil, '%type%' algılandı",
    "The mimetype of file '%value%' could not be detected" => "'%value%' dosyasının mime-tipi algılanamadı",
    "File '%value%' is not readable or does not exist" => "'%value%' dosyası okunamıyor ya da yok",

    // Zend_Validator_File_Md5
    "File '%value%' does not match the given md5 hashes" => "'%value%' dosyası verilen md5 hash'ları ile eşleşmiyor",
    "A md5 hash could not be evaluated for the given file" => "Bir md5 hash'ı verilen dosya için eşleştirilemedi",
    "File '%value%' is not readable or does not exist" => "'%value%' dosyası okunamıyor ya da yok",

    // Zend_Validator_File_MimeType
    "File '%value%' has a false mimetype of '%type%'" => "'%value%' dosyası yanlış mime-tipine sahip: '%type%'",
    "The mimetype of file '%value%' could not be detected" => "'%value%' dosyasının mime-tipi algılanamadı",
    "File '%value%' is not readable or does not exist" => "'%value%' dosyası okunamıyor ya da yok",

    // Zend_Validator_File_NotExists
    "File '%value%' exists" => "'%value%' dosyası var",

    // Zend_Validator_File_Sha1
    "File '%value%' does not match the given sha1 hashes" => "'%value%' dosyası verilen sha1 hash'ları ile eşleşmiyor",
    "A sha1 hash could not be evaluated for the given file" => "Bir sha1 hash'ı verilen dosya için eşleştirilemedi",
    "File '%value%' is not readable or does not exist" => "'%value%' dosyası okunamıyor ya da yok",

    // Zend_Validator_File_Size
    "Maximum allowed size for file '%value%' is '%max%' but '%size%' detected" => "'%value%' dosyası için izin verilen en büyük boyut '%max%' fakat '%size%' algılandı",
    "Minimum expected size for file '%value%' is '%min%' but '%size%' detected" => "'%value%' dosyası için izin verilen en az boyut '%min%' fakat '%size%' algılandı",
    "File '%value%' is not readable or does not exist" => "'%value%' dosyası okunamıyor ya da yok",

    // Zend_Validator_File_Upload
    "File '%value%' exceeds the defined ini size" => "'%value%' dosyası tanımlanan ini boyutunu aşıyor",
    "File '%value%' exceeds the defined form size" => "'%value%' dosyası tanımlanan form boyutunu aşıyor",
    "File '%value%' was only partially uploaded" => "'%value%' dosyası kısmen yüklenmiştir",
    "File '%value%' was not uploaded" => "'%value%' dosyası yüklenemedi",
    "No temporary directory was found for file '%value%'" => "Geçici dizin '%value%' dosyası için bulunamamıştır",
    "File '%value%' can't be written" => "'%value%' dosyasına yazılamıyor",
    "A PHP extension returned an error while uploading the file '%value%'" => "Bir PHP uzantısı '%value%' dosyasını yüklerken bir hata verdi",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "'%value%' dosyası yasadışı bir biçimde yüklendi. Bu olası bir saldırı olabilir",
    "File '%value%' was not found" => "'%value%' dosyası bulunamadı",
    "Unknown error while uploading file '%value%'" => "'%value%' dosyasını yüklerken bilinmeyen bir hata ile karşılaşıldı",

    // Zend_Validator_File_WordCount
    "Too much words, maximum '%max%' are allowed but '%count%' were counted" => "Çok fazla kelime, en fazla '%max%' kelimeye izin veriliyor fakat '%count%' sayıldı",
    "Too few words, minimum '%min%' are expected but '%count%' were counted" => "Çok az kelime, en az '%min%' kelimeye izin veriliyor fakat '%count%' sayıldı",
    "File '%value%' is not readable or does not exist" => "'%value%' dosyası okunamıyor ya da yok",

    // Zend_Validator_GreaterThan
    "The input is not greater than '%min%'" => "Girdi '%min%' değerinden büyük değil",
    "The input is not greater or equal than '%min%'" => "Girdi '%min%' değerinden büyük ya da eşit değil",

    // Zend_Validator_Hex
    "Invalid type given. String expected" => "Yanlış tür verildi. Dizge bekleniyor",
    "The input contains non-hexadecimal characters" => "Giriş onaltılık olmayan karakterler içeriyor",

    // Zend_Validator_Hostname
    "The input appears to be a DNS hostname but the given punycode notation cannot be decoded" => "Girdi bir DNS host adı gibi görünüyor fakat verilen punycode gösteriminde deşifre edilemiyor",
    "Invalid type given. String expected" => "Yanlış tür verildi. Dizge bekleniyor",
    "The input appears to be a DNS hostname but contains a dash in an invalid position" => "Girdi bir DNS host adı gibi görünüyor fakat yanlış pozisyonda tire içeriyor",
    "The input does not match the expected structure for a DNS hostname" => "Girdi bir DNS host adının beklenen yapısıyla eşleşmiyor",
    "The input appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "Girdi DNS host adı gibi görünüyor fakat TLD '%tld%' şeması ile eşleştirilemiyor",
    "The input does not appear to be a valid local network name" => "Girdi geçerli bir yerel ağ adı gibi görünmüyor",
    "The input does not appear to be a valid URI hostname" => "Girdi geçerli bir URI host adı olarak görünmüyor",
    "The input appears to be an IP address, but IP addresses are not allowed" => "Girdi bir IP adresi gibi görünüyor fakat IP adreslerine izin verilmiyor",
    "The input appears to be a local network name but local network names are not allowed" => "Girdi bir yerel ağ adı gibi görünüyor fakat yerel ağ adlarına izin verilmiyor",
    "The input appears to be a DNS hostname but cannot extract TLD part" => "Girdi bir DNS host adı gibi görünüyor fakat TLD parçası ayıklanamıyor",
    "The input appears to be a DNS hostname but cannot match TLD against known list" => "Girdi bir DNS host adı gibi görünüyor fakat bilinen TLD listesiyle karşılaştırılamıyor",

    // Zend_Validator_Iban
    "Unknown country within the IBAN" => "IBAN içerisinde bilinmeyen ülke",
    "Countries outside the Single Euro Payments Area (SEPA) are not supported" => "Tekil Euro Ödeme Bölgesi (SEPA) dışındaki ülkeler desteklenmiyor",
    "The input has a false IBAN format" => "Girdi yanlış IBAN formatında",
    "The input has failed the IBAN check" => "Girdinin IBAN denetimi başarısız oldu",

    // Zend_Validator_Identical
    "The two given tokens do not match" => "Verilen iki belirteç eşleşmiyor",
    "No token was provided to match against" => "Hiçbir eşleştirilecek belirteç sağlanmadı",

    // Zend_Validator_InArray
    "The input was not found in the haystack" => "Girdi samanlıkta bulunamadı",

    // Zend_Validator_Ip
    "Invalid type given. String expected" => "Yanlış tür verildi. Dizge bekleniyor",
    "The input does not appear to be a valid IP address" => "Girdi geçerli bir IP adresi gibi görünmüyor",

    // Zend_Validator_Isbn
    "Invalid type given. String or integer expected" => "Geçersiz tür verildi. Dizge veya tamsayı bekleniyor",
    "The input is not a valid ISBN number" => "Girdi geçerli bir ISBN numarası değil",

    // Zend_Validator_LessThan
    "The input is not less than '%max%'" => "Girdi '%max%' değerinden küçük değil",
    "The input is not less or equal than '%max%'" => "Girdi '%max%' değerinden küçük ya da eşit değil",

    // Zend_Validator_NotEmpty
    "Value is required and can't be empty" => "Değer gerekli ve boş olamaz",
    "Invalid type given. String, integer, float, boolean or array expected" => "Geçersiz tür verildi. Dizge, tamsayı, ondalık sayı, mantıksal veya dizi bekleniyor",

    // Zend_Validator_Regex
    "Invalid type given. String, integer or float expected" => "Geçersiz tür verildi, Dizge, tamsayı ya da ondalık sayı bekleniyor",
    "The input does not match against pattern '%pattern%'" => "Girdi '%pattern%' deseniyle eşleşmiyor",
    "There was an internal error while using the pattern '%pattern%'" => "'%pattern%' deseni kullanılırken bir iç hata ile karşılaşıldı",

    // Zend_Validator_Sitemap_Changefreq
    "The input is not a valid sitemap changefreq" => "Girdi geçerli bir site haritası changefreq'i olarak görünmüyor",
    "Invalid type given. String expected" => "Yanlış tür verildi. Dizge bekleniyor",

    // Zend_Validator_Sitemap_Lastmod
    "The input is not a valid sitemap lastmod" => "Girdi geçerli bir site haritası lastmod'u olarak görünmüyor",
    "Invalid type given. String expected" => "Yanlış tür verildi. Dizge bekleniyor",

    // Zend_Validator_Sitemap_Loc
    "The input is not a valid sitemap location" => "Girdi geçerli bir site haritası lokasyonu olarak görünmüyor",
    "Invalid type given. String expected" => "Yanlış tür verildi. Dizge bekleniyor",

    // Zend_Validator_Sitemap_Priority
    "The input is not a valid sitemap priority" => "Girdi geçerli bir site haritası önceliği olarak görünmüyor",
    "Invalid type given. Numeric string, integer or float expected" => "Geçersiz tür verildi. Sayısal dizge, tamsayı ya da ondalık sayı bekleniyor",

    // Zend_Validator_Step
    "Invalid value given. Scalar expected" => "Geçersiz değer verildi. Skalar bekleniyor",
    "The input is not a valid step" => "Girdi geçerli bir aşama değildir",

    // Zend_Validator_StringLength
    "Invalid type given. String expected" => "Yanlış tür verildi. Dizge bekleniyor",
    "The input is less than %min% characters long" => "Girdi %min% karakterden daha az",
    "The input is more than %max% characters long" => "Girdi %max% karakterden daha fazla",

    // Zend_Validator_Uri
    "Invalid type given. String expected" => "Yanlış tür verildi. Dizge bekleniyor",
    "The input does not appear to be a valid Uri" => "Girdi geçerli bir Uri olarak görünmüyor",
);
