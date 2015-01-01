<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * ZH-Revision: 26.Apr.2013
 */
return array(
    // Zend_I18n_Validator_Alnum
    "Invalid type given. String, integer or float expected" => "請輸入一個整數或小數",
    "The input contains characters which are non alphabetic and no digits" => "輸入不能為字母數字以外的字符",
    "The input is an empty string" => "輸入不能為空",

    // Zend_I18n_Validator_Alpha
    "Invalid type given. String expected" => "輸入無效，請輸入一個字符串",
    "The input contains non alphabetic characters" => "輸入不能為字母以外的字符",
    "The input is an empty string" => "輸入不能為空",

    // Zend_I18n_Validator_Float
    "Invalid type given. String, integer or float expected" => "請輸入與一個整數或小數",
    "The input does not appear to be a float" => "輸入無效，請輸入一個小數",

    // Zend_I18n_Validator_Int
    "Invalid type given. String or integer expected" => "輸入無效，請輸入字符或數字",
    "The input does not appear to be an integer" => "請輸入一個整數",

    // Zend_I18n_Validator_PostCode
    "Invalid type given. String or integer expected" => "輸入無效，請輸入一個字符或數字",
    "The input does not appear to be a postal code" => "無效的郵政編碼格式",
    "An exception has been raised while validating the input" => "驗證輸入時有異常發生",

    // Zend_Validator_Barcode
    "The input failed checksum validation" => "輸入的條碼無法通過校驗",
    "The input contains invalid characters" => "輸入的條碼包含無效的字符",
    "The input should have a length of %length% characters" => "輸入的條碼長度應為%length%個字符",
    "Invalid type given. String expected" => "輸入無效，請輸入一個字符串",

    // Zend_Validator_Between
    "The input is not between '%min%' and '%max%', inclusively" => "請輸入大於等於'%min%'並小於等於'%max%'的值",
    "The input is not strictly between '%min%' and '%max%'" => "請輸入大於'%min%'並小於'%max%'的值",

    // Zend_Validator_Callback
    "The input is not valid" => "輸入無效",
    "An exception has been raised within the callback" => "回調中有異常發生",

    // Zend_Validator_CreditCard
    "The input seems to contain an invalid checksum" => "輸入的卡號格式有誤",
    "The input must contain only digits" => "卡號應為數字",
    "Invalid type given. String expected" => "輸入無效，請輸入一個字符串",
    "The input contains an invalid amount of digits" => "輸入的卡號長度有誤",
    "The input is not from an allowed institute" => "輸入的卡號沒有找到對應的發行機構",
    "The input seems to be an invalid creditcard number" => "輸入的卡號無法通過校驗",
    "An exception has been raised while validating the input" => "驗證輸入時有異常發生",

    // Zend_Validator_Csrf
    "The form submitted did not originate from the expected site" => "表單提交來源網站未經過許可",

    // Zend_Validator_Date
    "Invalid type given. String, integer, array or DateTime expected" => "輸入無效，請輸入字符數字或日期",
    "The input does not appear to be a valid date" => "輸入的日期格式無效",
    "The input does not fit the date format '%format%'" => "請按照日期格式'%format%'輸入一個日期",

    // Zend_Validator_DateStep
    "Invalid type given. String, integer, array or DateTime expected" => "輸入無效，請輸入字符數字或日期",
    "The input does not appear to be a valid date" => "輸入的日期格式無效",
    "The input is not a valid step" => "The input is not a valid step",

    // Zend_Validator_Db_AbstractDb
    "No record matching the input was found" => "沒有找到匹配輸入的記錄",
    "A record matching the input was found" => "輸入已經被占用",

    // Zend_Validator_Digits
    "The input must contain only digits" => "輸入不能為數字以外的字符",
    "The input is an empty string" => "輸入不能為空",
    "Invalid type given. String, integer or float expected" => "輸入無效，請輸入字符整數或小數",

    // Zend_Validator_EmailAddress
    "Invalid type given. String expected" => "輸入無效，請輸入一個字符串",
    "The input is not a valid email address. Use the basic format local-part@hostname" => "輸入郵件地址格式有誤，請檢查格式是否為local-part@hostname",
    "'%hostname%' is not a valid hostname for the email address" => "'%hostname%'不是一個可用的郵件域名",
    "'%hostname%' does not appear to have any valid MX or A records for the email address" => "'%hostname%'域名下沒有找到可用的MX或A記錄，郵件無法投遞",
    "'%hostname%' is not in a routable network segment. The email address should not be resolved from public network" => "'%hostname%'域名所在網段無法被路由，郵件地址應位於公共網絡",
    "'%localPart%' can not be matched against dot-atom format" => "郵件用戶名部分'%localPart%'格式無法匹配dot-atom格式",
    "'%localPart%' can not be matched against quoted-string format" => "郵件用戶名部分'%localPart%'格式無法匹配quoted-string格式",
    "'%localPart%' is not a valid local part for the email address" => "'%localPart%'不是一個有效的郵件用戶名",
    "The input exceeds the allowed length" => "輸入超出允許長度",

    // Zend_Validator_Explode
    "Invalid type given. String expected" => "輸入無效，請輸入一個字符串",

    // Zend_Validator_File_Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "文件過多，最多允許'%max%'個文件，找到'%count%'個",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "文件過少，至少需要'%min%'個文件，找到'%count%'個",

    // Zend_Validator_File_Crc32
    "File '%value%' does not match the given crc32 hashes" => "文件'%value%'無法通過CRC32校驗",
    "A crc32 hash could not be evaluated for the given file" => "文件無法生成CRC32校驗碼",
    "File '%value%' is not readable or does not exist" => "文件'%value%'無法讀取或不存在",

    // Zend_Validator_File_ExcludeExtension
    "File '%value%' has a false extension" => "文件'%value%'擴展名不允許",
    "File '%value%' is not readable or does not exist" => "文件'%value%'無法讀取或不存在",

    // Zend_Validator_File_Exists
    "File '%value%' does not exist" => "文件'%value%'不存在",

    // Zend_Validator_File_Extension
    "File '%value%' has a false extension" => "文件'%value%'擴展名不允許",
    "File '%value%' is not readable or does not exist" => "文件'%value%'無法讀取或不存在",

    // Zend_Validator_File_FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "所有文件總大小'%size%'超出，最大允許'%max%'",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "所有文件總大小'%size%'不足，至少需要'%min%'",
    "One or more files can not be read" => "一個或多個文件無法讀取",

    // Zend_Validator_File_Hash
    "File '%value%' does not match the given hashes" => "文件'%value%'無法通過哈希校驗",
    "A hash could not be evaluated for the given file" => "文件無法生成哈希校驗碼",
    "File '%value%' is not readable or does not exist" => "文件'%value%'無法讀取或不存在",

    // Zend_Validator_File_ImageSize
    "Maximum allowed width for image '%value%' should be '%maxwidth%' but '%width%' detected" => "圖片'%value%'的寬度'%width%'超出，最大允許'%maxwidth%'",
    "Minimum expected width for image '%value%' should be '%minwidth%' but '%width%' detected" => "圖片'%value%'的寬度'%width%'不足，至少應為'%minwidth%'",
    "Maximum allowed height for image '%value%' should be '%maxheight%' but '%height%' detected" => "圖片'%value%'的高度'%height%'超出，最大允許'%maxheight%'",
    "Minimum expected height for image '%value%' should be '%minheight%' but '%height%' detected" => "圖片'%value%'的高度'%height%'不足，至少應為'%minheight%'",
    "The size of image '%value%' could not be detected" => "圖片'%value%'的尺寸無法讀取",
    "File '%value%' is not readable or does not exist" => "文件'%value%'無法讀取或不存在",

    // Zend_Validator_File_IsCompressed
    "File '%value%' is not compressed, '%type%' detected" => "文件'%value%'沒有被壓縮，檢測到文件的媒體類型為'%type%'",
    "The mimetype of file '%value%' could not be detected" => "文件'%value%'的媒體類型無法檢測",
    "File '%value%' is not readable or does not exist" => "文件'%value%'無法讀取或不存在",

    // Zend_Validator_File_IsImage
    "File '%value%' is no image, '%type%' detected" => "文件'%value%'不是圖片，檢測到文件的媒體類型為'%type%'",
    "The mimetype of file '%value%' could not be detected" => "文件'%value%'的媒體類型無法檢測",
    "File '%value%' is not readable or does not exist" => "文件'%value%'無法讀取或不存在",

    // Zend_Validator_File_Md5
    "File '%value%' does not match the given md5 hashes" => "文件'%value%'無法通過MD5校驗",
    "A md5 hash could not be evaluated for the given file" => "文件無法生成MD5校驗碼",
    "File '%value%' is not readable or does not exist" => "文件'%value%'無法讀取或不存在",

    // Zend_Validator_File_MimeType
    "File '%value%' has a false mimetype of '%type%'" => "文件'%value%'的媒體類型'%type%'不允許",
    "The mimetype of file '%value%' could not be detected" => "文件'%value%'的媒體類型無法檢測",
    "File '%value%' is not readable or does not exist" => "文件'%value%'無法讀取或不存在",

    // Zend_Validator_File_NotExists
    "File '%value%' exists" => "文件'%value%'已經存在",

    // Zend_Validator_File_Sha1
    "File '%value%' does not match the given sha1 hashes" => "文件'%value%'無法通過SHA1校驗",
    "A sha1 hash could not be evaluated for the given file" => "文件無法生成SHA1校驗碼",
    "File '%value%' is not readable or does not exist" => "文件'%value%'無法讀取或不存在",

    // Zend_Validator_File_Size
    "Maximum allowed size for file '%value%' is '%max%' but '%size%' detected" => "文件'%value%'的大小'%size%'超出，最大允許'%max%'",
    "Minimum expected size for file '%value%' is '%min%' but '%size%' detected" => "文件'%value%'的大小'%size%'不足，至少需要'%min%'",
    "File '%value%' is not readable or does not exist" => "文件'%value%'無法讀取或不存在",

    // Zend_Validator_File_Upload
    "File '%value%' exceeds the defined ini size" => "文件'%value%'大小超出系統允許範圍",
    "File '%value%' exceeds the defined form size" => "文件'%value%'大小超出表單允許範圍",
    "File '%value%' was only partially uploaded" => "文件'%value%'上傳不完整",
    "File '%value%' was not uploaded" => "文件'%value%'沒有被上傳",
    "No temporary directory was found for file '%value%'" => "沒有找到臨時文件夾存放文件'%value%'",
    "File '%value%' can't be written" => "文件'%value%'無法被寫入",
    "A PHP extension returned an error while uploading the file '%value%'" => "文件'%value%'上傳時發生了一個PHP擴展錯誤",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "文件'%value%'被非法上傳，這可能被判定為一次入侵",
    "File '%value%' was not found" => "文件'%value%'不存在",
    "Unknown error while uploading file '%value%'" => "文件'%value%'上傳時發生了一個未知錯誤",

    // Zend_Validator_File_WordCount
    "Too much words, maximum '%max%' are allowed but '%count%' were counted" => "輸入的單詞過多，最多允許'%max%'個單詞，輸入了'%count%'個",
    "Too few words, minimum '%min%' are expected but '%count%' were counted" => "輸入的單詞過少，至少需要'%min%'個單詞，輸入了'%count%'個",
    "File '%value%' is not readable or does not exist" => "文件'%value%'無法讀取或不存在",

    // Zend_Validator_GreaterThan
    "The input is not greater than '%min%'" => "輸入應大於'%min%'",
    "The input is not greater or equal than '%min%'" => "輸入應大於等於'%min%'",

    // Zend_Validator_Hex
    "Invalid type given. String expected" => "輸入無效，請輸入一個字符串",
    "The input contains non-hexadecimal characters" => "請輸入十六進制允許的字符",

    // Zend_Validator_Hostname
    "The input appears to be a DNS hostname but the given punycode notation cannot be decoded" => "輸入的DNS域名在解析中無法用給定的punycode正確解碼",
    "Invalid type given. String expected" => "輸入無效，請輸入一個字符串",
    "The input appears to be a DNS hostname but contains a dash in an invalid position" => "輸入的DNS域名中連接符位置不符合規定",
    "The input does not match the expected structure for a DNS hostname" => "輸入的DNS域名結構組成有誤",
    "The input appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "輸入的DNS域名的頂級域名'%tld%'無法被解析",
    "The input does not appear to be a valid local network name" => "輸入域名不是一個本地域名",
    "The input does not appear to be a valid URI hostname" => "域名格式有誤",
    "The input appears to be an IP address, but IP addresses are not allowed" => "不允許輸入IP地址作為域名",
    "The input appears to be a local network name but local network names are not allowed" => "不允許輸入本地或局域網內域名",
    "The input appears to be a DNS hostname but cannot extract TLD part" => "在輸入的DNS域名中無法找到頂級域名部分",
    "The input appears to be a DNS hostname but cannot match TLD against known list" => "在輸入的DNS域名中，頂級域名部分無法匹配已知列表",

    // Zend_Validator_Iban
    "Unknown country within the IBAN" => "輸入的IBAN帳號無法找到對應的國家",
    "Countries outside the Single Euro Payments Area (SEPA) are not supported" => "不支持單一歐元支付區(SEPA)以外的帳號",
    "The input has a false IBAN format" => "輸入的IBAN帳號格式有誤",
    "The input has failed the IBAN check" => "輸入的IBAN帳號校驗失敗",

    // Zend_Validator_Identical
    "The two given tokens do not match" => "兩個驗證令牌不匹配",
    "No token was provided to match against" => "沒有令牌輸入，無法匹配",

    // Zend_Validator_InArray
    "The input was not found in the haystack" => "輸入沒有在指定的允許範圍內",

    // Zend_Validator_Ip
    "Invalid type given. String expected" => "輸入無效，請輸入一個字符串",
    "The input does not appear to be a valid IP address" => "輸入的IP地址格式不正確",

    // Zend_Validator_Isbn
    "Invalid type given. String or integer expected" => "輸入無效，請輸入字符或整數",
    "The input is not a valid ISBN number" => "輸入的ISBN編號格式不正確",

    // Zend_Validator_LessThan
    "The input is not less than '%max%'" => "輸入應小於'%max%'",
    "The input is not less or equal than '%max%'" => "輸入應小於等於'%max%'",

    // Zend_Validator_NotEmpty
    "Value is required and can't be empty" => "輸入不能為空",
    "Invalid type given. String, integer, float, boolean or array expected" => "輸入無效，只允許字符、整數、小數、布爾值、數組類型",

    // Zend_Validator_Regex
    "Invalid type given. String, integer or float expected" => "輸入無效，請輸入字符、整數或小數",
    "The input does not match against pattern '%pattern%'" => "輸入不匹配指定的模式'%pattern%'",
    "There was an internal error while using the pattern '%pattern%'" => "匹配指定模式'%pattern%'時有內部錯誤發生",

    // Zend_Validator_Sitemap_Changefreq
    "The input is not a valid sitemap changefreq" => "輸入不符合網站地圖的changefreq格式",
    "Invalid type given. String expected" => "輸入無效，請輸入一個字符串",

    // Zend_Validator_Sitemap_Lastmod
    "The input is not a valid sitemap lastmod" => "輸入不符合網站地圖的lastmod格式",
    "Invalid type given. String expected" => "輸入無效，請輸入一個字符串",

    // Zend_Validator_Sitemap_Loc
    "The input is not a valid sitemap location" => "輸入不符合網站地圖的location格式",
    "Invalid type given. String expected" => "輸入無效，請輸入一個字符串",

    // Zend_Validator_Sitemap_Priority
    "The input is not a valid sitemap priority" => "輸入不符合網站地圖的priority格式",
    "Invalid type given. Numeric string, integer or float expected" => "輸入無效，請輸入一個數字",

    // Zend_Validator_Step
    "Invalid value given. Scalar expected" => "輸入無效，請輸入一個數字",
    "The input is not a valid step" => "輸入不在階梯計算的結果範圍內",

    // Zend_Validator_StringLength
    "Invalid type given. String expected" => "輸入無效，請輸入一個字符串",
    "The input is less than %min% characters long" => "輸入字符個數應大於%min%",
    "The input is more than %max% characters long" => "輸入字符個數應小於%max%",

    // Zend_Validator_Uri
    "Invalid type given. String expected" => "輸入無效，請輸入一個字符串",
    "The input does not appear to be a valid Uri" => "輸入的Uri格式有誤",
);
