<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * EN-Revision: 25.Jul.2011
 */
return array(
    // Zend_Validate_Alnum
    "Invalid type given. String, integer or float expected" => "خطأ في المدخلة. يجب ادخال أرقام أو حروف فقط",
    "'%value%' contains characters which are non alphabetic and no digits" => "'%value%' تحتوي على رموز ليست حروف أو أرقام",
    "'%value%' is an empty string" => "'%value%' فارغ",

    // Zend_Validate_Alpha
    "Invalid type given. String expected" => "خطأ في المدخلة. يجب ادخال حروف فقط",
    "'%value%' contains non alphabetic characters" => "'%value%' تحتوي على رموز ليست حروف",
    "'%value%' is an empty string" => "'%value%' فارغ",

    // Zend_Validate_Barcode
    "'%value%' failed checksum validation" => "'%value%' لم يجتز فحص الصحة",
    "'%value%' contains invalid characters" => "'%value%' يحتوي على رموز خاطئة",
    "'%value%' should have a length of %length% characters" => "طول '%value%' يجب أن يكون %length% حرف",
    "Invalid type given. String expected" => "خطأ في المدخلة. يجب ادخال نص",

    // Zend_Validate_Between
    "'%value%' is not between '%min%' and '%max%', inclusively" => "قيمة '%value%' يجب أن تكون بين '%min%' و '%max%'",
    "'%value%' is not strictly between '%min%' and '%max%'" => "قيمة '%value%' ليست بين '%min%' و '%max%'",

    // Zend_Validate_Callback
    "'%value%' is not valid" => "'%value%' خاطئ",
    "An exception has been raised within the callback" => "حصل خطأ داخلي أثناء تنفيذ العملية",

    // Zend_Validate_Ccnum
    "'%value%' must contain between 13 and 19 digits" => "'%value%' يجب أن تحتوي على ما بين 13 إلى 19 رقم",
    "Luhn algorithm (mod-10 checksum) failed on '%value%'" => "فشل اختبار Luhn algorithm (mod-10 checksum) على '%value%'",

    // Zend_Validate_CreditCard
    "'%value%' seems to contain an invalid checksum" => "'%value%' لم يجتز فحص الصحة",
    "'%value%' must contain only digits" => "'%value%' يجب أن تحتوي على أرقام فقط",
    "Invalid type given. String expected" => "خطأ في المدخلة. يجب ادخال نص",
    "'%value%' contains an invalid amount of digits" => "'%value%' تحتوي على عدد خاطئ من الأرقام",
    "'%value%' is not from an allowed institute" => "قيمة '%value%' ليست من مؤسسة مسموحة أو مقبولة",
    "'%value%' seems to be an invalid creditcard number" => "'%value%' ليس رقم بطاقة إئتمان",
    "An exception has been raised while validating '%value%'" => "حصل خطأ أثناء التحقق من صحة '%value%'",

    // Zend_Validate_Date
    "Invalid type given. String, integer, array or Zend_Date expected" => "خطأ في المدخلة. يجب ادخال حروف، أرقام، متسلسلات، أو Zend_Date",
    "'%value%' does not appear to be a valid date" => "'%value%' ليس تاريخ صحيح",
    "'%value%' does not fit the date format '%format%'" => "'%value%' لا يطابق شكل التاريخ '%format%'",

    // Zend_Validate_Db_Abstract
    "No record matching '%value%' was found" => "لم يتم العثور على سجل مطابق لـ '%value%'",
    "A record matching '%value%' was found" => "السجل '%value%' موجود",

    // Zend_Validate_Digits
    "Invalid type given. String, integer or float expected" => "خطأ في المدخلة. يجب ادخال أرقام أو حروف فقط",
    "'%value%' must contain only digits" => "'%value%' يجب أن يحتوي على أرقام فقط",
    "'%value%' is an empty string" => "'%value%' فارغ",

    // Zend_Validate_EmailAddress
    "Invalid type given. String expected" => "خطأ في المدخلة. يجب ادخال نص",
    "'%value%' is not a valid email address in the basic format local-part@hostname" => "قيمة '%value%' ليست بريد الكتروني صحيح يطابق نمط local-part@hostname",
    "'%hostname%' is not a valid hostname for email address '%value%'" => "قيمة '%hostname%' للبريد الإلكتروني '%value%' ليس صحيح",
    "'%hostname%' does not appear to have a valid MX record for the email address '%value%'" => "لا يوجد مدخلة MX صحيحة لـ'%hostname%' للبريد الإلكتروني '%value%'",
    "'%hostname%' is not in a routable network segment. The email address '%value%' should not be resolved from public network" => "'%hostname%' غير موجود في مكان يمكن الوصول إليه. البريد الإلكتروني '%value%' لا يمكن الوصول إليه من شبكة عامة",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' لا يمكن مطابقته مع شكل dot-atom",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' لا يمكن مطابقته مع شكل quoted-string",
    "'%localPart%' is not a valid local part for email address '%value%'" => "'%localPart%' ليس بريد الكتروني صحيح لقيمة '%value%'",
    "'%value%' exceeds the allowed length" => "طول '%value%' تعدى الطول المسموح",

    // Zend_Validate_File_Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "'%count%' ملف/ملفات هو عدد أكبر من العدد المسموح به وهو '%max%'",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "'%count%' ملف/ملفات هو عدد أقل من العدد المطلوب وهو '%min%'",

    // Zend_Validate_File_Crc32
    "File '%value%' does not match the given crc32 hashes" => "لم يطابق تشفير crc32 للملف '%value%' التشفير المعطى",
    "A crc32 hash could not be evaluated for the given file" => "لا يمكن معرفة قيمة تشفير crc32 للملف",
    "File '%value%' is not readable or does not exist" => "الملف '%value%' لا يمكن قراءته أو أنه غير موجود",

    // Zend_Validate_File_ExcludeExtension
    "File '%value%' has a false extension" => "امتداد الملف '%value%' خاطئ",
    "File '%value%' is not readable or does not exist" => "الملف '%value%' لا يمكن قراءته أو أنه غير موجود",

    // Zend_Validate_File_ExcludeMimeType
    "File '%value%' has a false mimetype of '%type%'" => "الملف '%value%' له نوع خاطئ وهو '%type%'",
    "The mimetype of file '%value%' could not be detected" => "لم يتم التعرف على نوع الملف '%value%'",
    "File '%value%' is not readable or does not exist" => "الملف '%value%' لا يمكن قراءته أو أنه غير موجود",

    // Zend_Validate_File_Exists
    "File '%value%' does not exist" => "الملف '%value%' غير موجود",

    // Zend_Validate_File_Extension
    "File '%value%' has a false extension" => "صيغة الملف '%value%' خاطئة",
    "File '%value%' is not readable or does not exist" => "'%value%' لا يمكن قراءة محتوى الملف أو أنه غير موجود",

    // Zend_Validate_File_FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "'%size%' هو حجم أكبر من الحد الأقصى المسموح به للملفات وهو '%max%'",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "'%size%' هو حجم أصغر من الحد الأدنى المسموح به للملفات وهو '%min%'",
    "One or more files can not be read" => "لا يمكن قراءة محتوى ملف أو أكثر",

    // Zend_Validate_File_Hash
    "File '%value%' does not match the given hashes" => "لم يطابق تشفير الملف '%value%' التشفير المعطى",
    "A hash could not be evaluated for the given file" => "لا يمكن معرفة قيمة التشفير للملف",
    "File '%value%' is not readable or does not exist" => "الملف '%value%' لا يمكن قراءته أو أنه غير موجود",

    // Zend_Validate_File_ImageSize
    "Maximum allowed width for image '%value%' should be '%maxwidth%' but '%width%' detected" => "أكبر عرض مسموح به للصورة '%value%' هو '%maxwidth%' ولكن العرض الحالي هو '%width%'",
    "Minimum expected width for image '%value%' should be '%minwidth%' but '%width%' detected" => "أقل عرض مسموح به للصورة '%value%' هو '%minwidth%' ولكن العرض الحالي هو '%width%'",
    "Maximum allowed height for image '%value%' should be '%maxheight%' but '%height%' detected" => "أكبر ارتفاع مسموح به للصورة '%value%' هو '%maxheight%' ولكن الطول الحالي هو '%height%'",
    "Minimum expected height for image '%value%' should be '%minheight%' but '%height%' detected" => "أقل ارتفاع مسموح به للصورة '%value%' should be '%minheight%' ولكن الطول الحالي هو '%height%'",
    "The size of image '%value%' could not be detected" => "لا يمكن أبعاد الصورة '%value%'",
    "File '%value%' is not readable or does not exist" => "الملف '%value%' لا يمكن قراءته أو أنه غير موجود",

    // Zend_Validate_File_IsCompressed
    "File '%value%' is not compressed, '%type%' detected" => "الملف '%value%' ليس ملف مضغوط، بل هو ملف '%type%'",
    "The mimetype of file '%value%' could not be detected" => "لم يتم التعرف على نوع الملف '%value%'",
    "File '%value%' is not readable or does not exist" => "الملف '%value%' لا يمكن قراءته أو أنه غير موجود",

    // Zend_Validate_File_IsImage
    "File '%value%' is no image, '%type%' detected" => "الملف '%value%' ليس صورة، بل هو ملف '%type%'",
    "The mimetype of file '%value%' could not be detected" => "لم يتم التعرف على نوع الملف '%value%'",
    "File '%value%' is not readable or does not exist" => "الملف '%value%' لا يمكن قراءته أو أنه غير موجود",

    // Zend_Validate_File_Md5
    "File '%value%' does not match the given md5 hashes" => "لم يطابق تشفير md5 للملف '%value%' التشفير المعطى",
    "A md5 hash could not be evaluated for the given file" => "لا يمكن معرفة قيمة تشفير md5 للملف",
    "File '%value%' is not readable or does not exist" => "الملف '%value%' لا يمكن قراءته أو أنه غير موجود",

    // Zend_Validate_File_MimeType
    "File '%value%' has a false mimetype of '%type%'" => "الملف '%value%' له نوع خاطئ وهو '%type%'",
    "The mimetype of file '%value%' could not be detected" => "لم يتم التعرف على نوع الملف '%value%'",
    "File '%value%' is not readable or does not exist" => "الملف '%value%' لا يمكن قراءته أو أنه غير موجود",

    // Zend_Validate_File_NotExists
    "File '%value%' exists" => "الملف '%value%' موجود",

    // Zend_Validate_File_Sha1
    "File '%value%' does not match the given sha1 hashes" => "لم يطابق تشفير sha1 للملف '%value%' التشفير المعطى",
    "A sha1 hash could not be evaluated for the given file" => "لا يمكن معرفة قيمة تشفير sha1 للملف",
    "File '%value%' is not readable or does not exist" => "الملف '%value%' لا يمكن قراءته أو أنه غير موجود",

    // Zend_Validate_File_Size
    "Maximum allowed size for file '%value%' is '%max%' but '%size%' detected" => "حجم الملف '%value%' هو '%size%' وهذا الحجم أكبر من الحد الأقصى المسموح به وهو '%max%'",
    "Minimum expected size for file '%value%' is '%min%' but '%size%' detected" => "حجم الملف '%value%' هو '%size%' وهذا الحجم أقل من الحد الأدنى المسموح به وهو '%min%'",
    "File '%value%' is not readable or does not exist" => "الملف '%value%' لا يمكن قراءته أو أنه غير موجود",

    // Zend_Validate_File_Upload
    "File '%value%' exceeds the defined ini size" => "الملف '%value%' تعدى الحجم المسموح به حسب التعريف في ini",
    "File '%value%' exceeds the defined form size" => "الملف '%value%' تعدى الحجم المسموح به حسب التعريف في النموذج",
    "File '%value%' was only partially uploaded" => "الملف '%value%' تم تحميل جزء منه",
    "File '%value%' was not uploaded" => "الملف '%value%' لم يتم تحميله",
    "No temporary directory was found for file '%value%'" => "لم يتم العثور على مكان مؤقت للملف '%value%'",
    "File '%value%' can't be written" => "الملف '%value%' لا يمكن كتابته وتخزينه",
    "A PHP extension returned an error while uploading the file '%value%'" => "لقد حصل خطأ من إضافة PHP في عملية تحميل الملف '%value%'",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "لقد تم تحميل الملف '%value%' بطريقة غير مشروعة. وهذا يمكن أن يكون محاولة هجوم",
    "File '%value%' was not found" => "لم يتم العثور على الملف '%value%'",
    "Unknown error while uploading file '%value%'" => "حصل خطأ غير معروف في عملية تحميل الملف '%value%'",

    // Zend_Validate_File_WordCount
    "Too much words, maximum '%max%' are allowed but '%count%' were counted" => "'%count%' كلمات أكثر من العدد الأقصى المسموح به وهو '%max%' كلمات",
    "Too few words, minimum '%min%' are expected but '%count%' were counted" => "'%count%' كلمات أقل من العدد الأدنى المسموح وهو '%min%' كلمات",
    "File '%value%' is not readable or does not exist" => "الملف '%value%' لا يمكن قراءته أو أنه غير موجود",

    // Zend_Validate_Float
    "Invalid type given. String, integer or float expected" => "خطأ في المدخلة. يجب ادخال أرقام أو حروف فقط",
    "'%value%' does not appear to be a float" => "'%value%' ليس رقم",

    // Zend_Validate_GreaterThan
    "'%value%' is not greater than '%min%'" => "'%value%' ليس أكبر من '%min%'",

    // Zend_Validate_Hex
    "Invalid type given. String expected" => "خطأ في المدخلة. يجب ادخال نص",
    "'%value%' has not only hexadecimal digit characters" => "'%value%' يحتوي على حروف أو رموز ليست من النظام الست عشري (hexadecimal)",

    // Zend_Validate_Hostname
    "Invalid type given. String expected" => "خطأ في المدخلة. يجب ادخال نص",
    "'%value%' appears to be an IP address, but IP addresses are not allowed" => "قيمة '%value%' تبدو أنها عنوان بروتوكول انترنت (IP) وهذا غير مسموح به",
    "'%value%' appears to be a DNS hostname but cannot match TLD against known list" => "'%value%' هو اسم نظام أسماء المجالات (DNS)، ولكن اسم المجال ذو المستوى العال (TLD) غير معروف",
    "'%value%' appears to be a DNS hostname but contains a dash in an invalid position" => "'%value%' هو اسم نظام أسماء المجالات (DNS)، ولكنه يحتوي على (-) في مكان خاطئ",
    "'%value%' appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "'%value%' هو اسم نظام أسماء المجالات (DNS)، ولكن لا يمكن مطابقته مع اسم مخطط لاسم المجال ذو المستوى العال (TLD) '%tld%'",
    "'%value%' appears to be a DNS hostname but cannot extract TLD part" => "'%value%' هو اسم نظام أسماء المجالات (DNS)، ولكن لا يمكن معرفة جزء اسم مجال ذو مستوى عال (TLD) منه",
    "'%value%' does not match the expected structure for a DNS hostname" => "'%value%' لا يطابق شكل نظام أسماء المجالات (DNS)",
    "'%value%' does not appear to be a valid local network name" => "'%value%' ليس اسم صحيح لشبكة محلية",
    "'%value%' appears to be a local network name but local network names are not allowed" => "'%value%' هو اسم لشبكة محلية، والشبكة المحلية غير مسموح بها",
    "'%value%' appears to be a DNS hostname but the given punycode notation cannot be decoded" => "'%value%' هو اسم نظام أسماء المجالات (DNS)،  ولكن الرموز في الاسم لا يمكن تفكيكها وتحويلها لصيغة أبسط",
    "'%value%' does not appear to be a valid URI hostname" => "'%value%' ليس عنوان اسم صحيح لمضيف",

    // Zend_Validate_Iban
    "Unknown country within the IBAN '%value%'" => "لم يتم التعرف على الدولة في الرقم الدولي للحساب البنكي (IBAN) '%value%'",
    "'%value%' has a false IBAN format" => "'%value%' ليس صيفة صحيحة لالرقم الدولي للحساب البنكي (IBAN)",
    "'%value%' has failed the IBAN check" => "'%value%' لم يجتز فحص الرقم الدولي للحساب البنكي (IBAN)",

    // Zend_Validate_Identical
    "The two given tokens do not match" => "الرمزان غير متطابقان",
    "No token was provided to match against" => "لا يوجد رمز للمقارنة به",

    // Zend_Validate_InArray
    "'%value%' was not found in the haystack" => "لم يتم العثور على '%value%' في المتسلسلة",

    // Zend_Validate_Int
    "Invalid type given. String or integer expected" => "خطأ في المدخلة. يجب ادخال أرقام",
    "'%value%' does not appear to be an integer" => "'%value%' ليس رقم",

    // Zend_Validate_Ip
    "Invalid type given. String expected" => "خطأ في المدخلة. يجب ادخال نص",
    "'%value%' does not appear to be a valid IP address" => "'%value%' ليس عنوان بروتوكول انترنت (IP) صحيح",

    // Zend_Validate_Isbn
    "Invalid type given. String or integer expected" => "خطأ في المدخلة. يجب ادخال أرقام أو حروف",
    "'%value%' is not a valid ISBN number" => "'%value%' ليس قيمة صحيحة لالرقم الدولي الموحد للكتاب (ISBN)",

    // Zend_Validate_LessThan
    "'%value%' is not less than '%max%'" => "'%value%' ليس أقل من '%max%'",

    // Zend_Validate_NotEmpty
    "Invalid type given. String, integer, float, boolean or array expected" => "خطأ في المدخلة. يجب ادخال أرقام، حروف، صح أو خطأ، أو متسلسلة",
    "Value is required and can't be empty" => "لا يمكن ترك هذا الحقل فارغ",

    // Zend_Validate_PostCode
    "Invalid type given. String or integer expected" => "خطأ في المدخلة. يجب ادخال أرقام أو حروف",
    "'%value%' does not appear to be a postal code" => "'%value%' ليس رمز بريدي صحيح",

    // Zend_Validate_Regex
    "Invalid type given. String, integer or float expected" => "خطأ في المدخلة. يجب ادخال أرقام أو حروف فقط",
    "'%value%' does not match against pattern '%pattern%'" => "'%value%' لا يطابق النمط '%pattern%'",
    "There was an internal error while using the pattern '%pattern%'" => "حصل خطأ داخلي أثناء استخدام النمط '%pattern%'",

    // Zend_Validate_Sitemap_Changefreq
    "'%value%' is not a valid sitemap changefreq" => "'%value%' ليست قيمة صحيحة لوتيرة التغيير لخريطة الموقع",
    "Invalid type given. String expected" => "خطأ في المدخلة. يجب ادخال نص",

    // Zend_Validate_Sitemap_Lastmod
    "'%value%' is not a valid sitemap lastmod" => "'%value%' ليست قيمة صحيحة لتاريخ آخر تعديل لخريطة الموقع",
    "Invalid type given. String expected" => "خطأ في المدخلة. يجب ادخال نص",

    // Zend_Validate_Sitemap_Loc
    "'%value%' is not a valid sitemap location" => "'%value%' ليس عنوان صحيح لخريطة الموقع",
    "Invalid type given. String expected" => "خطأ في المدخلة. يجب ادخال نص",

    // Zend_Validate_Sitemap_Priority
    "'%value%' is not a valid sitemap priority" => "'%value%' ليست أولوية صحيحة لعنوان خريطة الموقع",
    "Invalid type given. Numeric string, integer or float expected" => "خطأ في المدخلة. يجب ادخال أرقام أو حروف فقط",

    // Zend_Validate_StringLength
    "Invalid type given. String expected" => "خطأ في المدخلة. يجب ادخال نص",
    "'%value%' is less than %min% characters long" => "طول '%value%' يجب أن يكون %min% حرف على الأقل",
    "'%value%' is more than %max% characters long" => "طول '%value%' يجب أن يكون %max% كحد أقصى",
);
