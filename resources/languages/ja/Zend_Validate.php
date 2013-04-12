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
 * EN-Revision: 09.Sept.2012
 */
return array(
    // Zend_I18n_Validator_Alnum
    "Invalid type given. String, integer or float expected" => "不正な形式です。文字列、整数、もしくは小数が期待されています",
    "The input contains characters which are non alphabetic and no digits" => "入力値にアルファベットと数字以外の文字が含まれています",
    "The input is an empty string" => "入力値は空の文字列です",

    // Zend_I18n_Validator_Alpha
    "Invalid type given. String expected" => "不正な形式です。文字列が期待されています",
    "The input contains non alphabetic characters" => "入力値にアルファベット以外の文字が含まれています",
    "The input is an empty string" => "入力値は空の文字列です",

    // Zend_I18n_Validator_Float
    "Invalid type given. String, integer or float expected" => "不正な形式です。文字列、整数、もしくは小数が期待されています",
    "The input does not appear to be a float" => " 入力値は小数ではないようです",

    // Zend_I18n_Validator_Int
    "Invalid type given. String or integer expected" => "不正な形式です。文字列もしくは整数が期待されています",
    "The input does not appear to be an integer" => " 入力値は整数ではないようです",

    // Zend_I18n_Validator_PostCode
    "Invalid type given. String or integer expected" => "不正な形式です。文字列もしくは整数が期待されています",
    "The input does not appear to be a postal code" => " 入力値は郵便番号でないようです",
    "An exception has been raised while validating the input" => "入力値を検証中に例外が発生しました",

    // Zend_Validator_Barcode
    "The input failed checksum validation" => "入力値はチェックサムが一致していません",
    "The input contains invalid characters" => "入力値は不正な文字を含んでいます",
    "The input should have a length of %length% characters" => "入力値は %length% 文字である必要があります",
    "Invalid type given. String expected" => "不正な形式です。文字列が期待されています",

    // Zend_Validator_Between
    "The input is not between '%min%' and '%max%', inclusively" => "入力値は '%min%' 以上 '%max%' 以下ではありません",
    "The input is not strictly between '%min%' and '%max%'" => "入力値は '%min%' 以下か '%max%' 以上です",

    // Zend_Validator_Callback
    "The input is not valid" => "入力値は正しくありません",
    "An exception has been raised within the callback" => "コールバック内で例外が発生しました",

    // Zend_Validator_CreditCard
    "The input seems to contain an invalid checksum" => "入力値は無効なチェックサムを含んでいるようです",
    "The input must contain only digits" => "入力値は数値だけで構成される必要があります",
    "Invalid type given. String expected" => "不正な形式です。文字列が期待されています",
    "The input contains an invalid amount of digits" => "入力値は不正な桁数です",
    "The input is not from an allowed institute" => "入力値は認可機関から許可されていません",
    "The input seems to be an invalid creditcard number" => "入力値は無効なクレジットカード番号のようです",
    "An exception has been raised while validating the input" => "入力値を検証中に例外が発生しました",

    // Zend_Validator_Csrf
    "The form submitted did not originate from the expected site" => "期待されるサイトからフォーム送信がなされていません",

    // Zend_Validator_Date
    "Invalid type given. String, integer, array or DateTime expected" => "無効な型が与えられています。文字列、数値型、配列もしくはDateTimeが期待されます",
    "The input does not appear to be a valid date" => "入力値は正しい日付ではないようです",
    "The input does not fit the date format '%format%'" => "入力値は '%format%' フォーマットに一致していません",

    // Zend_Validator_DateStep
    "Invalid type given. String, integer, array or DateTime expected" => "無効な型が与えられています。文字列、数値型、配列もしくはDateTimeが期待されます",
    "The input is not a valid step" => "入力値は有効な間隔ではありません",
    "The input does not appear to be a valid date" => "入力値は有効な日付ではないようです",

    // Zend_Validator_Db_AbstractDb
    "No record matching the input was found" => "入力値に一致するレコードが見つかりませんでした",
    "A record matching the input was found" => "入力値に一致するレコードが見つかりました",

    // Zend_Validator_Digits
    "The input must contain only digits" => "入力値は数値だけで構成される必要があります",
    "The input is an empty string" => "入力値は空の文字列です",
    "Invalid type given. String, integer or float expected" => "不正な形式です。文字列、整数、もしくは小数が期待されています",

    // Zend_Validator_EmailAddress
    "Invalid type given. String expected" => "不正な形式です。文字列が期待されています",
    "The input is not a valid email address. Use the basic format local-part@hostname" => "入力値は有効なEmailアドレスではありません。 基本的なフォーマット local-part@hostname を使ってください",
    "'%hostname%' is not a valid hostname for the email address" => "Emailアドレスの '%hostname%' は有効なホスト名ではありません",
    "'%hostname%' does not appear to have any valid MX or A records for the email address" => "Emailアドレスの '%hostname%' は有効なMXやAレコードを持ってないようです",
    "'%hostname%' is not in a routable network segment. The email address should not be resolved from public network" => "'%hostname%' はルーティング可能なネットワークセグメントではありません。Emailアドレスはパブリックネットワークから解決できません",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' はドットアトム形式ではありません",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' は引用文字列形式ではありません",
    "'%localPart%' is not a valid local part for the email address" => "Emailアドレスの '%localPart%' は有効なローカルパートではありません",
    "The input exceeds the allowed length" => "入力値は許された長さを超えています",

    // Zend_Validator_Explode
    "Invalid type given. String expected" => "不正な形式です。文字列が期待されています",

    // Zend_Validator_File_Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "ファイル数が多すぎます。最大 '%max%' まで許されていますが、 '%count%' 個指定ました",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "ファイル数が少なすぎます。最小 '%min%' 以上の必要がありますが、 '%count%' 個指定されていません",

    // Zend_Validator_File_Crc32
    "File '%value%' does not match the given crc32 hashes" => "ファイル '%value%' は crc32 ハッシュ値と一致しませんでした",
    "A crc32 hash could not be evaluated for the given file" => "ファイルに crc32 ハッシュ値が見つかりませんでした",
    "File '%value%' is not readable or does not exist" => "ファイル '%value%' は読み込めないかもしくは存在しません",

    // Zend_Validator_File_ExcludeExtension
    "File '%value%' has a false extension" => "ファイル '%value%' は誤った拡張子です",
    "File '%value%' is not readable or does not exist" => "ファイル '%value%' は読み込めないかもしくは存在しません",

    // Zend_Validator_File_Exists
    "File '%value%' does not exist" => "ファイル '%value%' は存在しません",

    // Zend_Validator_File_Extension
    "File '%value%' has a false extension" => "ファイル '%value%' は誤った拡張子です",
    "File '%value%' is not readable or does not exist" => "ファイル '%value%' は読み込めないかもしくは存在しません",

    // Zend_Validator_File_FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "全てのファイルの合計は最大 '%max%' より小さい必要があります。しかしファイルサイズは '%size%' でした",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "全てのファイルの合計は最小 '%min%' より大きい必要があります。しかしファイルサイズは '%size%' でした",
    "One or more files can not be read" => "ファイルを読み込めませんでした",

    // Zend_Validator_File_Hash
    "File '%value%' does not match the given hashes" => "ファイル '%value%' は設定されたハッシュ値と一致しませんでした",
    "A hash could not be evaluated for the given file" => "渡されたファイルのハッシュ値を評価できませんでした",
    "File '%value%' is not readable or does not exist" => "ファイル '%value%' は読み込めないかもしくは存在しません",

    // Zend_Validator_File_ImageSize
    "Maximum allowed width for image '%value%' should be '%maxwidth%' but '%width%' detected" => "画像 '%value%' の横幅は '%width%' でした。横幅は最大 '%maxwidth%' まで許されています",
    "Minimum expected width for image '%value%' should be '%minwidth%' but '%width%' detected" => "画像 '%value%' の横幅は '%width%' でした。横幅は最小 '%minwidth%' 以上である必要があります",
    "Maximum allowed height for image '%value%' should be '%maxheight%' but '%height%' detected" => "画像 '%value%' の高さは '%height%' でした。高さは最大 '%maxheight%' まで許されています",
    "Minimum expected height for image '%value%' should be '%minheight%' but '%height%' detected" => "画像 '%value%' の高さは '%height%' でした。高さは最小 '%minheight%' 以上である必要があります",
    "The size of image '%value%' could not be detected" => "画像 '%value%' の大きさを取得できませんでした",
    "File '%value%' is not readable or does not exist" => "ファイル '%value%' は読み込めないかもしくは存在しません",

    // Zend_Validator_File_IsCompressed
    "File '%value%' is not compressed, '%type%' detected" => " '%type%' が見つかりました。ファイル '%value%' は圧縮されていません",
    "The mimetype of file '%value%' could not be detected" => "ファイル '%value%' の Mimetype は見つかりませんでした",
    "File '%value%' is not readable or does not exist" => "ファイル '%value%' は読み込めないかもしくは存在しません",

    // Zend_Validator_File_IsImage
    "File '%value%' is no image, '%type%' detected" => "ファイル '%value%' は画像ではありません。 '%type%' です",
    "The mimetype of file '%value%' could not be detected" => "ファイル '%value%' の Mimetype は見つかりませんでした",
    "File '%value%' is not readable or does not exist" => "ファイル '%value%' は読み込めないかもしくは存在しません",

    // Zend_Validator_File_Md5
    "File '%value%' does not match the given md5 hashes" => "ファイル '%value%' は md5 ハッシュ値と一致していません",
    "A md5 hash could not be evaluated for the given file" => "渡されたファイルの md5 ハッシュ値を評価できませんでした",
    "File '%value%' is not readable or does not exist" => "ファイル '%value%' は読み込めないかもしくは存在しません",

    // Zend_Validator_File_MimeType
    "File '%value%' has a false mimetype of '%type%'" => "ファイル '%value%' は誤った MimeType '%type%' です",
    "The mimetype of file '%value%' could not be detected" => "ファイル '%value%' の Mimetype は見つかりませんでした",
    "File '%value%' is not readable or does not exist" => "ファイル '%value%' は読み込めないかもしくは存在しません",

    // Zend_Validator_File_NotExists
    "File '%value%' exists" => "ファイル '%value%' は存在しています",

    // Zend_Validator_File_Sha1
    "File '%value%' does not match the given sha1 hashes" => "ファイル '%value%' は sha1 ハッシュ値と一致していません",
    "A sha1 hash could not be evaluated for the given file" => "渡されたファイルの sha1 ハッシュ値を評価できませんでした",
    "File '%value%' is not readable or does not exist" => "ファイル '%value%' は読み込めないかもしくは存在しません",

    // Zend_Validator_File_Size
    "Maximum allowed size for file '%value%' is '%max%' but '%size%' detected" => "ファイルサイズは '%size%' です。ファイル '%value%' のサイズは最大 '%max%' まで許されています",
    "Minimum expected size for file '%value%' is '%min%' but '%size%' detected" => "ファイルサイズは '%size%' です。ファイル '%value%' のサイズは最小 '%min%' 以上必要です",
    "File '%value%' is not readable or does not exist" => "ファイル '%value%' は読み込めないかもしくは存在しません",

    // Zend_Validator_File_Upload
    "File '%value%' exceeds the defined ini size" => "ファイル '%value%' は ini で定義されたサイズを超えています",
    "File '%value%' exceeds the defined form size" => "ファイル '%value%' はフォームで定義されたサイズを超えています",
    "File '%value%' was only partially uploaded" => "ファイル '%value%' は一部のみしかアップロードされていません",
    "File '%value%' was not uploaded" => "ファイル '%value%' はアップロードされませんでした",
    "No temporary directory was found for file '%value%'" => "ファイル '%value%' をアップロードする一時ディレクトリが見つかりませんでした",
    "File '%value%' can't be written" => "ファイル '%value%' は書き込めませんでした",
    "A PHP extension returned an error while uploading the file '%value%'" => "ファイル '%value%' をアップロード中に拡張モジュールがエラーを応答しました",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "ファイル '%value%' は不正なアップロードでした。攻撃の可能性があります",
    "File '%value%' was not found" => "ファイル '%value%' は見つかりませんでした",
    "Unknown error while uploading file '%value%'" => "ファイル '%value%' をアップロード中に未知のエラーが発生しました",

    // Zend_Validator_File_WordCount
    "Too much words, maximum '%max%' are allowed but '%count%' were counted" => "単語数 '%count%' が多過ぎます。最大で '%max%' 個が許されます",
    "Too few words, minimum '%min%' are expected but '%count%' were counted" => "単語数 '%count%' が少な過ぎます。少なくとも '%min%' 個必要です",
    "File '%value%' is not readable or does not exist" => "ファイル '%value%' は読み込めないかもしくは存在しません",

    // Zend_Validator_GreaterThan
    "The input is not greater than '%min%'" => " 入力値は '%min%' より大きくありません",
    "The input is not greater or equal than '%min%'" => "入力値は '%min%' 以上ではありません",

    // Zend_Validator_Hex
    "Invalid type given. String expected" => "不正な形式です。文字列が期待されています",
    "The input contains non-hexadecimal characters" => "入力値は16進数ではない文字を含んでいます",

    // Zend_Validator_Hostname
    "The input appears to be a DNS hostname but the given punycode notation cannot be decoded" => " 入力値は DNS ホスト名のようですが、 punycode 変換ができませんでした",
    "Invalid type given. String expected" => "不正な形式です。文字列が期待されています",
    "The input appears to be a DNS hostname but contains a dash in an invalid position" => " 入力値は DNS ホスト名のようですが不正な位置にダッシュがあります",
    "The input does not match the expected structure for a DNS hostname" => " 入力値は DNS ホスト名の構造に一致していません",
    "The input appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => " 入力値は DNS ホスト名のようですが TLD '%tld%' のホスト名スキーマと一致していません",
    "The input does not appear to be a valid local network name" => " 入力値は有効なローカルネットワーク名ではないようです",
    "The input does not appear to be a valid URI hostname" => "入力値は有効なURIホスト名ではないようです",
    "The input appears to be an IP address, but IP addresses are not allowed" => " 入力値は IP アドレスのようですが、 IP アドレスは許されていません",
    "The input appears to be a local network name but local network names are not allowed" => " 入力値はローカルネットワーク名のようですがローカルネットワーク名は許されていません",
    "The input appears to be a DNS hostname but cannot extract TLD part" => " 入力値は DNS ホスト名のようですが TLD 部を展開できません",
    "The input appears to be a DNS hostname but cannot match TLD against known list" => " 入力値は DNS ホスト名のようですが、 TLD が一覧に見つかりません",

    // Zend_Validator_Iban
    "Unknown country within the IBAN" => "IBAN 内の既知の国ではありません",
    "Countries outside the Single Euro Payments Area (SEPA) are not supported" => "Single Euro Payments Area (SEPA) 外の国々はサポート外です",
    "The input has a false IBAN format" => " 入力値は誤った IBAN 書式です",
    "The input has failed the IBAN check" => " 入力値は IBAN コードチェックに失敗しました",

    // Zend_Validator_Identical
    "The two given tokens do not match" => "2 つのトークンは一致しませんでした",
    "No token was provided to match against" => "チェックを行うためのトークンがありませんでした",

    // Zend_Validator_InArray
    "The input was not found in the haystack" => " 入力値が haystack の中に見つかりませんでした",

    // Zend_Validator_Ip
    "Invalid type given. String expected" => "不正な形式です。文字列が期待されています",
    "The input does not appear to be a valid IP address" => " 入力値は IP アドレスではないようです",

    // Zend_Validator_Isbn
    "Invalid type given. String or integer expected" => "不正な形式です。文字列もしくは整数が期待されています",
    "The input is not a valid ISBN number" => " 入力値は ISBN 番号ではありません",

    // Zend_Validator_LessThan
    "The input is not less than '%max%'" => " 入力値は '%max%' 未満ではありません",
    "The input is not less or equal than '%max%'" => "入力値は '%max%' 以下ではありません",

    // Zend_Validator_NotEmpty
    "Value is required and can't be empty" => "値は必須です。空値は許可されていません",
    "Invalid type given. String, integer, float, boolean or array expected" => "不正な形式です。文字列、整数、小数、真偽値もしくは配列が期待されています",

    // Zend_Validator_Regex
    "Invalid type given. String, integer or float expected" => "不正な形式です。文字列、整数、もしくは小数が期待されています",
    "The input does not match against pattern '%pattern%'" => " 入力値はパターン '%pattern%' と一致していません",
    "There was an internal error while using the pattern '%pattern%'" => "正規表現パターン '%pattern%' を使用中に内部エラーが発生しました。",

    // Zend_Validator_Sitemap_Changefreq
    "The input is not a valid sitemap changefreq" => " 入力値は正しいサイトマップの更新頻度ではありません",
    "Invalid type given. String expected" => "不正な形式です。文字列が期待されています",

    // Zend_Validator_Sitemap_Lastmod
    "The input is not a valid sitemap lastmod" => " 入力値は正しいサイトマップの最終更新日ではありません",
    "Invalid type given. String expected" => "不正な形式です。文字列が期待されています",

    // Zend_Validator_Sitemap_Loc
    "The input is not a valid sitemap location" => " 入力値は正しいサイトマップの位置ではありません",
    "Invalid type given. String expected" => "不正な形式です。文字列が期待されています",

    // Zend_Validator_Sitemap_Priority
    "The input is not a valid sitemap priority" => " 入力値は正しいサイトマップの優先度ではありません",
    "Invalid type given. Numeric string, integer or float expected" => "不正な形式です。数字、整数もしくは小数が期待されています",

    // Zend_Validator_Step
    "Invalid value given. Scalar expected" => "無効な値が与えられています。スカラーが期待されます",
    "The input is not a valid step" => "入力値は有効な間隔ではありません",

    // Zend_Validator_StringLength
    "Invalid type given. String expected" => "不正な形式です。文字列が期待されています",
    "The input is less than %min% characters long" => " 入力値は %min% 文字より短いです",
    "The input is more than %max% characters long" => " 入力値は %max% 文字より長いです",

    // Zend_Validator_Uri
    "Invalid type given. String expected" => "不正な形式です。文字列が期待されています",
    "The input does not appear to be a valid Uri" => "入力値は有効なUriではないようです",
);
