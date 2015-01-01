<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * ZH-Revision: 09.Nov.2012
 */
return array(
    // Zend_I18n_Validator_Alnum
    "Invalid type given. String, integer or float expected" => "请输入一个整数或小数",
    "The input contains characters which are non alphabetic and no digits" => "输入不能为字母数字以外的字符",
    "The input is an empty string" => "输入不能为空",

    // Zend_I18n_Validator_Alpha
    "Invalid type given. String expected" => "输入无效，请输入一个字符串",
    "The input contains non alphabetic characters" => "输入不能为字母以外的字符",
    "The input is an empty string" => "输入不能为空",

    // Zend_I18n_Validator_Float
    "Invalid type given. String, integer or float expected" => "请输入与一个整数或小数",
    "The input does not appear to be a float" => "输入无效，请输入一个小数",

    // Zend_I18n_Validator_Int
    "Invalid type given. String or integer expected" => "输入无效，请输入字符或数字",
    "The input does not appear to be an integer" => "请输入一个整数",

    // Zend_I18n_Validator_PostCode
    "Invalid type given. String or integer expected" => "输入无效，请输入一个字符或数字",
    "The input does not appear to be a postal code" => "无效的邮政编码格式",
    "An exception has been raised while validating the input" => "验证输入时有异常发生",

    // Zend_Validator_Barcode
    "The input failed checksum validation" => "输入的条码无法通过校验",
    "The input contains invalid characters" => "输入的条码包含无效的字符",
    "The input should have a length of %length% characters" => "输入的条码长度应为%length%个字符",
    "Invalid type given. String expected" => "输入无效，请输入一个字符串",

    // Zend_Validator_Between
    "The input is not between '%min%' and '%max%', inclusively" => "请输入大于等于'%min%'并小于等于'%max%'的值",
    "The input is not strictly between '%min%' and '%max%'" => "请输入大于'%min%'并小于'%max%'的值",

    // Zend_Validator_Callback
    "The input is not valid" => "输入无效",
    "An exception has been raised within the callback" => "回调中有异常发生",

    // Zend_Validator_CreditCard
    "The input seems to contain an invalid checksum" => "输入的卡号格式有误",
    "The input must contain only digits" => "卡号应为数字",
    "Invalid type given. String expected" => "输入无效，请输入一个字符串",
    "The input contains an invalid amount of digits" => "输入的卡号长度有误",
    "The input is not from an allowed institute" => "输入的卡号没有找到对应的发行机构",
    "The input seems to be an invalid creditcard number" => "输入的卡号无法通过校验",
    "An exception has been raised while validating the input" => "验证输入时有异常发生",

    // Zend_Validator_Csrf
    "The form submitted did not originate from the expected site" => "表单提交来源网站未经过许可",

    // Zend_Validator_Date
    "Invalid type given. String, integer, array or DateTime expected" => "输入无效，请输入字符数字或日期",
    "The input does not appear to be a valid date" => "输入的日期格式无效",
    "The input does not fit the date format '%format%'" => "请按照日期格式'%format%'输入一个日期",

    // Zend_Validator_DateStep
    "Invalid type given. String, integer, array or DateTime expected" => "输入无效，请输入字符数字或日期",
    "The input does not appear to be a valid date" => "输入的日期格式无效",
    "The input is not a valid step" => "The input is not a valid step",

    // Zend_Validator_Db_AbstractDb
    "No record matching the input was found" => "没有找到匹配输入的记录",
    "A record matching the input was found" => "输入已经被占用",

    // Zend_Validator_Digits
    "The input must contain only digits" => "输入不能为数字以外的字符",
    "The input is an empty string" => "输入不能为空",
    "Invalid type given. String, integer or float expected" => "输入无效，请输入字符整数或小数",

    // Zend_Validator_EmailAddress
    "Invalid type given. String expected" => "输入无效，请输入一个字符串",
    "The input is not a valid email address. Use the basic format local-part@hostname" => "输入邮件地址格式有误，请检查格式是否为local-part@hostname",
    "'%hostname%' is not a valid hostname for the email address" => "'%hostname%'不是一个可用的邮件域名",
    "'%hostname%' does not appear to have any valid MX or A records for the email address" => "'%hostname%'域名下没有找到可用的MX或A记录，邮件无法投递",
    "'%hostname%' is not in a routable network segment. The email address should not be resolved from public network" => "'%hostname%'域名所在网段无法被路由，邮件地址应位于公共网络",
    "'%localPart%' can not be matched against dot-atom format" => "邮件用户名部分'%localPart%'格式无法匹配dot-atom格式",
    "'%localPart%' can not be matched against quoted-string format" => "邮件用户名部分'%localPart%'格式无法匹配quoted-string格式",
    "'%localPart%' is not a valid local part for the email address" => "'%localPart%'不是一个有效的邮件用户名",
    "The input exceeds the allowed length" => "输入超出允许长度",

    // Zend_Validator_Explode
    "Invalid type given. String expected" => "输入无效，请输入一个字符串",

    // Zend_Validator_File_Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "文件过多，最多允许'%max%'个文件，找到'%count%'个",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "文件过少，至少需要'%min%'个文件，找到'%count%'个",

    // Zend_Validator_File_Crc32
    "File '%value%' does not match the given crc32 hashes" => "文件'%value%'无法通过CRC32校验",
    "A crc32 hash could not be evaluated for the given file" => "文件无法生成CRC32校验码",
    "File '%value%' is not readable or does not exist" => "文件'%value%'无法读取或不存在",

    // Zend_Validator_File_ExcludeExtension
    "File '%value%' has a false extension" => "文件'%value%'扩展名不允许",
    "File '%value%' is not readable or does not exist" => "文件'%value%'无法读取或不存在",

    // Zend_Validator_File_Exists
    "File '%value%' does not exist" => "文件'%value%'不存在",

    // Zend_Validator_File_Extension
    "File '%value%' has a false extension" => "文件'%value%'扩展名不允许",
    "File '%value%' is not readable or does not exist" => "文件'%value%'无法读取或不存在",

    // Zend_Validator_File_FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "所有文件总大小'%size%'超出，最大允许'%max%'",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "所有文件总大小'%size%'不足，至少需要'%min%'",
    "One or more files can not be read" => "一个或多个文件无法读取",

    // Zend_Validator_File_Hash
    "File '%value%' does not match the given hashes" => "文件'%value%'无法通过哈希校验",
    "A hash could not be evaluated for the given file" => "文件无法生成哈希校验码",
    "File '%value%' is not readable or does not exist" => "文件'%value%'无法读取或不存在",

    // Zend_Validator_File_ImageSize
    "Maximum allowed width for image '%value%' should be '%maxwidth%' but '%width%' detected" => "图片'%value%'的宽度'%width%'超出，最大允许'%maxwidth%'",
    "Minimum expected width for image '%value%' should be '%minwidth%' but '%width%' detected" => "图片'%value%'的宽度'%width%'不足，至少应为'%minwidth%'",
    "Maximum allowed height for image '%value%' should be '%maxheight%' but '%height%' detected" => "图片'%value%'的高度'%height%'超出，最大允许'%maxheight%'",
    "Minimum expected height for image '%value%' should be '%minheight%' but '%height%' detected" => "图片'%value%'的高度'%height%'不足，至少应为'%minheight%'",
    "The size of image '%value%' could not be detected" => "图片'%value%'的尺寸无法读取",
    "File '%value%' is not readable or does not exist" => "文件'%value%'无法读取或不存在",

    // Zend_Validator_File_IsCompressed
    "File '%value%' is not compressed, '%type%' detected" => "文件'%value%'没有被压缩，检测到文件的媒体类型为'%type%'",
    "The mimetype of file '%value%' could not be detected" => "文件'%value%'的媒体类型无法检测",
    "File '%value%' is not readable or does not exist" => "文件'%value%'无法读取或不存在",

    // Zend_Validator_File_IsImage
    "File '%value%' is no image, '%type%' detected" => "文件'%value%'不是图片，检测到文件的媒体类型为'%type%'",
    "The mimetype of file '%value%' could not be detected" => "文件'%value%'的媒体类型无法检测",
    "File '%value%' is not readable or does not exist" => "文件'%value%'无法读取或不存在",

    // Zend_Validator_File_Md5
    "File '%value%' does not match the given md5 hashes" => "文件'%value%'无法通过MD5校验",
    "A md5 hash could not be evaluated for the given file" => "文件无法生成MD5校验码",
    "File '%value%' is not readable or does not exist" => "文件'%value%'无法读取或不存在",

    // Zend_Validator_File_MimeType
    "File '%value%' has a false mimetype of '%type%'" => "文件'%value%'的媒体类型'%type%'不允许",
    "The mimetype of file '%value%' could not be detected" => "文件'%value%'的媒体类型无法检测",
    "File '%value%' is not readable or does not exist" => "文件'%value%'无法读取或不存在",

    // Zend_Validator_File_NotExists
    "File '%value%' exists" => "文件'%value%'已经存在",

    // Zend_Validator_File_Sha1
    "File '%value%' does not match the given sha1 hashes" => "文件'%value%'无法通过SHA1校验",
    "A sha1 hash could not be evaluated for the given file" => "文件无法生成SHA1校验码",
    "File '%value%' is not readable or does not exist" => "文件'%value%'无法读取或不存在",

    // Zend_Validator_File_Size
    "Maximum allowed size for file '%value%' is '%max%' but '%size%' detected" => "文件'%value%'的大小'%size%'超出，最大允许'%max%'",
    "Minimum expected size for file '%value%' is '%min%' but '%size%' detected" => "文件'%value%'的大小'%size%'不足，至少需要'%min%'",
    "File '%value%' is not readable or does not exist" => "文件'%value%'无法读取或不存在",

    // Zend_Validator_File_Upload
    "File '%value%' exceeds the defined ini size" => "文件'%value%'大小超出系统允许范围",
    "File '%value%' exceeds the defined form size" => "文件'%value%'大小超出表单允许范围",
    "File '%value%' was only partially uploaded" => "文件'%value%'上传不完整",
    "File '%value%' was not uploaded" => "文件'%value%'没有被上传",
    "No temporary directory was found for file '%value%'" => "没有找到临时文件夹存放文件'%value%'",
    "File '%value%' can't be written" => "文件'%value%'无法被写入",
    "A PHP extension returned an error while uploading the file '%value%'" => "文件'%value%'上传时发生了一个PHP扩展错误",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "文件'%value%'被非法上传，这可能被判定为一次入侵",
    "File '%value%' was not found" => "文件'%value%'不存在",
    "Unknown error while uploading file '%value%'" => "文件'%value%'上传时发生了一个未知错误",

    // Zend_Validator_File_WordCount
    "Too much words, maximum '%max%' are allowed but '%count%' were counted" => "输入的单词过多，最多允许'%max%'个单词，输入了'%count%'个",
    "Too few words, minimum '%min%' are expected but '%count%' were counted" => "输入的单词过少，至少需要'%min%'个单词，输入了'%count%'个",
    "File '%value%' is not readable or does not exist" => "文件'%value%'无法读取或不存在",

    // Zend_Validator_GreaterThan
    "The input is not greater than '%min%'" => "输入应大于'%min%'",
    "The input is not greater or equal than '%min%'" => "输入应大于等于'%min%'",

    // Zend_Validator_Hex
    "Invalid type given. String expected" => "输入无效，请输入一个字符串",
    "The input contains non-hexadecimal characters" => "请输入十六进制允许的字符",

    // Zend_Validator_Hostname
    "The input appears to be a DNS hostname but the given punycode notation cannot be decoded" => "输入的DNS域名在解析中无法用给定的punycode正确解码",
    "Invalid type given. String expected" => "输入无效，请输入一个字符串",
    "The input appears to be a DNS hostname but contains a dash in an invalid position" => "输入的DNS域名中连接符位置不符合规定",
    "The input does not match the expected structure for a DNS hostname" => "输入的DNS域名结构组成有误",
    "The input appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "输入的DNS域名的顶级域名'%tld%'无法被解析",
    "The input does not appear to be a valid local network name" => "输入域名不是一个本地域名",
    "The input does not appear to be a valid URI hostname" => "域名格式有误",
    "The input appears to be an IP address, but IP addresses are not allowed" => "不允许输入IP地址作为域名",
    "The input appears to be a local network name but local network names are not allowed" => "不允许输入本地或局域网内域名",
    "The input appears to be a DNS hostname but cannot extract TLD part" => "在输入的DNS域名中无法找到顶级域名部分",
    "The input appears to be a DNS hostname but cannot match TLD against known list" => "在输入的DNS域名中，顶级域名部分无法匹配已知列表",

    // Zend_Validator_Iban
    "Unknown country within the IBAN" => "输入的IBAN帐号无法找到对应的国家",
    "Countries outside the Single Euro Payments Area (SEPA) are not supported" => "不支持单一欧元支付区(SEPA)以外的帐号",
    "The input has a false IBAN format" => "输入的IBAN帐号格式有误",
    "The input has failed the IBAN check" => "输入的IBAN帐号校验失败",

    // Zend_Validator_Identical
    "The two given tokens do not match" => "两个验证令牌不匹配",
    "No token was provided to match against" => "没有令牌输入，无法匹配",

    // Zend_Validator_InArray
    "The input was not found in the haystack" => "输入没有在指定的允许范围内",

    // Zend_Validator_Ip
    "Invalid type given. String expected" => "输入无效，请输入一个字符串",
    "The input does not appear to be a valid IP address" => "输入的IP地址格式不正确",

    // Zend_Validator_Isbn
    "Invalid type given. String or integer expected" => "输入无效，请输入字符或整数",
    "The input is not a valid ISBN number" => "输入的ISBN编号格式不正确",

    // Zend_Validator_LessThan
    "The input is not less than '%max%'" => "输入应小于'%max%'",
    "The input is not less or equal than '%max%'" => "输入应小于等于'%max%'",

    // Zend_Validator_NotEmpty
    "Value is required and can't be empty" => "输入不能为空",
    "Invalid type given. String, integer, float, boolean or array expected" => "输入无效，只允许字符、整数、小数、布尔值、数组类型",

    // Zend_Validator_Regex
    "Invalid type given. String, integer or float expected" => "输入无效，请输入字符、整数或小数",
    "The input does not match against pattern '%pattern%'" => "输入不匹配指定的模式'%pattern%'",
    "There was an internal error while using the pattern '%pattern%'" => "匹配指定模式'%pattern%'时有内部错误发生",

    // Zend_Validator_Sitemap_Changefreq
    "The input is not a valid sitemap changefreq" => "输入不符合网站地图的changefreq格式",
    "Invalid type given. String expected" => "输入无效，请输入一个字符串",

    // Zend_Validator_Sitemap_Lastmod
    "The input is not a valid sitemap lastmod" => "输入不符合网站地图的lastmod格式",
    "Invalid type given. String expected" => "输入无效，请输入一个字符串",

    // Zend_Validator_Sitemap_Loc
    "The input is not a valid sitemap location" => "输入不符合网站地图的location格式",
    "Invalid type given. String expected" => "输入无效，请输入一个字符串",

    // Zend_Validator_Sitemap_Priority
    "The input is not a valid sitemap priority" => "输入不符合网站地图的priority格式",
    "Invalid type given. Numeric string, integer or float expected" => "输入无效，请输入一个数字",

    // Zend_Validator_Step
    "Invalid value given. Scalar expected" => "输入无效，请输入一个数字",
    "The input is not a valid step" => "输入不在阶梯计算的结果范围内",

    // Zend_Validator_StringLength
    "Invalid type given. String expected" => "输入无效，请输入一个字符串",
    "The input is less than %min% characters long" => "输入字符个数应大于%min%",
    "The input is more than %max% characters long" => "输入字符个数应小于%max%",

    // Zend_Validator_Uri
    "Invalid type given. String expected" => "输入无效，请输入一个字符串",
    "The input does not appear to be a valid Uri" => "输入的Uri格式有误",
);
