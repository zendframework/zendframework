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
    "Invalid type given. String, integer or float expected" => "不正な形式です。文字列、整数、もしくは小数が期待されています",
    "The input contains characters which are non alphabetic and no digits" => "入力値にアルファベットと数字以外の文字が含まれています",
    "The input is an empty string" => "入力値は空の文字列です",

    // Zend\I18n\Validator\Alpha
    "Invalid type given. String expected" => "不正な形式です。文字列が期待されています",
    "The input contains non alphabetic characters" => "入力値にアルファベット以外の文字が含まれています",
    "The input is an empty string" => "入力値は空の文字列です",

    // Zend\I18n\Validator\DateTime
    "Invalid type given. String expected" => "不正な形式です。文字列が期待されています",
    "The input does not appear to be a valid datetime" => "入力値は正しい日時ではないようです",

    // Zend\I18n\Validator\Float
    "Invalid type given. String, integer or float expected" => "不正な形式です。文字列、整数、もしくは小数が期待されています",
    "The input does not appear to be a float" => " 入力値は小数ではないようです",

    // Zend\I18n\Validator\Int
    "Invalid type given. String or integer expected" => "不正な形式です。文字列もしくは整数が期待されています",
    "The input does not appear to be an integer" => " 入力値は整数ではないようです",

    // Zend\I18n\Validator\PhoneNumber
    "The input does not match a phone number format" => "入力値は電話番号形式に一致しません",
    "The country provided is currently unsupported" => "条件の国は現在サポートされていません",
    "Invalid type given. String expected" => "不正な形式です。文字列が期待されています",

    // Zend\I18n\Validator\PostCode
    "Invalid type given. String or integer expected" => "不正な形式です。文字列もしくは整数が期待されています",
    "The input does not appear to be a postal code" => " 入力値は郵便番号でないようです",
    "An exception has been raised while validating the input" => "入力値を検証中に例外が発生しました",

    // Zend\Validator\Barcode
    "The input failed checksum validation" => "入力値はチェックサムが一致していません",
    "The input contains invalid characters" => "入力値は不正な文字を含んでいます",
    "The input should have a length of %length% characters" => "入力値は %length% 文字である必要があります",
    "Invalid type given. String expected" => "不正な形式です。文字列が期待されています",

    // Zend\Validator\Between
    "The input is not between '%min%' and '%max%', inclusively" => "入力値は '%min%' 以上 '%max%' 以下ではありません",
    "The input is not strictly between '%min%' and '%max%'" => "入力値は '%min%' 以下か '%max%' 以上です",

    // Zend\Validator\Callback
    "The input is not valid" => "入力値は正しくありません",
    "An exception has been raised within the callback" => "コールバック内で例外が発生しました",

    // Zend\Validator\CreditCard
    "The input seems to contain an invalid checksum" => "入力値は無効なチェックサムを含んでいるようです",
    "The input must contain only digits" => "入力値は数値だけで構成される必要があります",
    "Invalid type given. String expected" => "不正な形式です。文字列が期待されています",
    "The input contains an invalid amount of digits" => "入力値は不正な桁数です",
    "The input is not from an allowed institute" => "入力値は認可機関から許可されていません",
    "The input seems to be an invalid credit card number" => "入力値は不正なクレジットカード番号のようです",
    "An exception has been raised while validating the input" => "入力値を検証中に例外が発生しました",

    // Zend\Validator\Csrf
    "The form submitted did not originate from the expected site" => "期待されるサイトからフォーム送信がなされていません",

    // Zend\Validator\Date
    "Invalid type given. String, integer, array or DateTime expected" => "無効な型が与えられています。文字列、数値型、配列もしくはDateTimeが期待されます",
    "The input does not appear to be a valid date" => "入力値は有効な日付ではないようです",
    "The input does not fit the date format '%format%'" => "入力値は '%format%' フォーマットに一致していません",

    // Zend\Validator\DateStep
    "The input is not a valid step" => "入力値は有効な間隔ではありません",

    // Zend\Validator\Db\AbstractDb
    "No record matching the input was found" => "入力値に一致するレコードが見つかりませんでした",
    "A record matching the input was found" => "入力値に一致するレコードが見つかりました",

    // Zend\Validator\Digits
    "The input must contain only digits" => "入力値は数値だけで構成される必要があります",
    "The input is an empty string" => "入力値は空の文字列です",
    "Invalid type given. String, integer or float expected" => "不正な形式です。文字列、整数、もしくは小数が期待されています",

    // Zend\Validator\EmailAddress
    "Invalid type given. String expected" => "不正な形式です。文字列が期待されています",
    "The input is not a valid email address. Use the basic format local-part@hostname" => "入力値は有効なEmailアドレスではありません。 基本的なフォーマット local-part@hostname を使ってください",
    "'%hostname%' is not a valid hostname for the email address" => "Emailアドレスの '%hostname%' は有効なホスト名ではありません",
    "'%hostname%' does not appear to have any valid MX or A records for the email address" => "Emailアドレスの '%hostname%' は有効なMXやAレコードを持ってないようです",
    "'%hostname%' is not in a routable network segment. The email address should not be resolved from public network" => "'%hostname%' はルーティング可能なネットワークセグメントではありません。Emailアドレスはパブリックネットワークから解決できません",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' はドットアトム形式ではありません",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' は引用文字列形式ではありません",
    "'%localPart%' is not a valid local part for the email address" => "Emailアドレスの '%localPart%' は有効なローカルパートではありません",
    "The input exceeds the allowed length" => "入力値は許された長さを超えています",

    // Zend\Validator\Explode
    "Invalid type given" => "Invalid type given",

    // Zend\Validator\File\Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "ファイル数が多すぎます。最大 '%max%' まで許されていますが、 '%count%' 個指定ました",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "ファイル数が少なすぎます。最小 '%min%' 以上の必要がありますが、 '%count%' 個指定されていません",

    // Zend\Validator\File\Crc32
    "File does not match the given crc32 hashes" => "ファイルは crc32 ハッシュ値と一致しませんでした",
    "A crc32 hash could not be evaluated for the given file" => "ファイルに crc32 ハッシュ値が見つかりませんでした",
    "File is not readable or does not exist" => "ファイルは読み込めないかもしくは存在しません",

    // Zend\Validator\File\ExcludeExtension
    "File has an incorrect extension" => "ファイルの拡張子が正しくありません",
    "File is not readable or does not exist" => "ファイルは読み込めないかもしくは存在しません",

    // Zend\Validator\File\Exists
    "File does not exist" => "ファイルは存在しません",

    // Zend\Validator\File\Extension
    "File has an incorrect extension" => "ファイルの拡張子が正しくありません",
    "File is not readable or does not exist" => "ファイルは読み込めないかもしくは存在しません",

    // Zend\Validator\File\FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "全てのファイルの合計は最大 '%max%' より小さい必要があります。しかしファイルサイズは '%size%' でした",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "全てのファイルの合計は最小 '%min%' より大きい必要があります。しかしファイルサイズは '%size%' でした",
    "One or more files can not be read" => "ファイルを読み込めませんでした",

    // Zend\Validator\File\Hash
    "File does not match the given hashes" => "ファイルは設定されたハッシュ値と一致しませんでした",
    "A hash could not be evaluated for the given file" => "渡されたファイルのハッシュ値を評価できませんでした",
    "File is not readable or does not exist" => "ファイルは読み込めないかもしくは存在しません",

    // Zend\Validator\File\ImageSize
    "Maximum allowed width for image should be '%maxwidth%' but '%width%' detected" => "画像の横幅は '%width%' でした。横幅は最大 '%maxwidth%' まで許されています",
    "Minimum expected width for image should be '%minwidth%' but '%width%' detected" => "画像の横幅は '%width%' でした。横幅は最小 '%minwidth%' 以上である必要があります",
    "Maximum allowed height for image should be '%maxheight%' but '%height%' detected" => "画像の高さは '%height%' でした。高さは最大 '%maxheight%' まで許されています",
    "Minimum expected height for image should be '%minheight%' but '%height%' detected" => "画像の高さは '%height%' でした。高さは最小 '%minheight%' 以上である必要があります",
    "The size of image could not be detected" => "画像の大きさを取得できませんでした",
    "File is not readable or does not exist" => "ファイルは読み込めないかもしくは存在しません",

    // Zend\Validator\File\IsCompressed
    "File is not compressed, '%type%' detected" => " '%type%' が見つかりました。ファイルは圧縮されていません",
    "The mimetype could not be detected from the file" => "ファイルからMimeTypeが検出できませんでした",
    "File is not readable or does not exist" => "ファイルは読み込めないかもしくは存在しません",

    // Zend\Validator\File\IsImage
    "File is no image, '%type%' detected" => "ファイルは画像ではありません。 '%type%' です",
    "The mimetype could not be detected from the file" => "ファイルからMimeTypeが検出できませんでした",
    "File is not readable or does not exist" => "ファイルは読み込めないかもしくは存在しません",

    // Zend\Validator\File\Md5
    "File does not match the given md5 hashes" => "ファイルは md5 ハッシュ値と一致していません",
    "An md5 hash could not be evaluated for the given file" => "与えられたファイルからはmd5 hash値が求めることができませんでした",
    "File is not readable or does not exist" => "ファイルは読み込めないかもしくは存在しません",

    // Zend\Validator\File\MimeType
    "File has an incorrect mimetype of '%type%'" => "ファイルは不正な mimetype 形式の '%type%' を含んでいます",
    "The mimetype could not be detected from the file" => "ファイルからMimeTypeが検出できませんでした",
    "File is not readable or does not exist" => "ファイルは読み込めないかもしくは存在しません",

    // Zend\Validator\File\NotExists
    "File exists" => "ファイルは存在しています",

    // Zend\Validator\File\Sha1
    "File does not match the given sha1 hashes" => "ファイルは sha1 ハッシュ値と一致していません",
    "A sha1 hash could not be evaluated for the given file" => "渡されたファイルの sha1 ハッシュ値を評価できませんでした",
    "File is not readable or does not exist" => "ファイルは読み込めないかもしくは存在しません",

    // Zend\Validator\File\Size
    "Maximum allowed size for file is '%max%' but '%size%' detected" => "ファイルサイズは '%size%' です。ファイルのサイズは最大 '%max%' まで許されています",
    "Minimum expected size for file is '%min%' but '%size%' detected" => "ファイルサイズは '%size%' です。ファイルのサイズは最小 '%min%' 以上必要です",
    "File is not readable or does not exist" => "ファイルは読み込めないかもしくは存在しません",

    // Zend\Validator\File\Upload
    "File '%value%' exceeds the defined ini size" => "ファイル '%value%' は定義されたiniサイズを越えています",
    "File '%value%' exceeds the defined form size" => "ファイル '%value%' は定義されたフォームサイズを越えています",
    "File '%value%' was only partially uploaded" => "ファイル '%value%' は一部のみアップロードされました",
    "File '%value%' was not uploaded" => "ファイル '%value%' がアップロードされていません",
    "No temporary directory was found for file '%value%'" => "ファイル '%value%' 用の一時ディレクトリが見つかりませんでした",
    "File '%value%' can't be written" => "ファイル '%value%' に書き込めませんでした",
    "A PHP extension returned an error while uploading the file '%value%'" => "ファイル '%value%' をアップロード中にPHPの拡張がモジュールがエラーを返しました",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "ファイル '%value%' は不当にアップロードされました。攻撃の可能性があります",
    "File '%value%' was not found" => "ファイル '%value%' は見つかりませんでした",
    "Unknown error while uploading file '%value%'" => "ファイル '%value%' をアップロード中に不明なエラーです",

    // Zend\Validator\File\UploadFile
    "File exceeds the defined ini size" => "ファイルは ini で定義されたサイズを超えています",
    "File exceeds the defined form size" => "ファイルはフォームで定義されたサイズを超えています",
    "File was only partially uploaded" => "ファイルは一部のみしかアップロードされていません",
    "File was not uploaded" => "ファイルはアップロードされませんでした",
    "No temporary directory was found for file" => "ファイルをアップロードする一時ディレクトリが見つかりませんでした",
    "File can't be written" => "ファイルは書き込めませんでした",
    "A PHP extension returned an error while uploading the file" => "ファイルをアップロード中に拡張モジュールがエラーを応答しました",
    "File was illegally uploaded. This could be a possible attack" => "ファイルは不正なアップロードでした。攻撃の可能性があります",
    "File was not found" => "ファイルは見つかりませんでした",
    "Unknown error while uploading file" => "ファイルをアップロード中に未知のエラーが発生しました",

    // Zend\Validator\File\WordCount
    "Too many words, maximum '%max%' are allowed but '%count%' were counted" => "単語数が多すぎます。最大で '%max%' までですが '%count%' 個カウントされました",
    "Too few words, minimum '%min%' are expected but '%count%' were counted" => "単語数 '%count%' が少な過ぎます。少なくとも '%min%' 個必要です",
    "File is not readable or does not exist" => "ファイルは読み込めないかもしくは存在しません",

    // Zend\Validator\GreaterThan
    "The input is not greater than '%min%'" => " 入力値は '%min%' より大きくありません",
    "The input is not greater or equal than '%min%'" => "入力値は '%min%' 以上ではありません",

    // Zend\Validator\Hex
    "Invalid type given. String expected" => "不正な形式です。文字列が期待されています",
    "The input contains non-hexadecimal characters" => "入力値は16進数ではない文字を含んでいます",

    // Zend\Validator\Hostname
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

    // Zend\Validator\Iban
    "Unknown country within the IBAN" => "IBAN 内の既知の国ではありません",
    "Countries outside the Single Euro Payments Area (SEPA) are not supported" => "Single Euro Payments Area (SEPA) 外の国々はサポート外です",
    "The input has a false IBAN format" => " 入力値は誤った IBAN 書式です",
    "The input has failed the IBAN check" => " 入力値は IBAN コードチェックに失敗しました",

    // Zend\Validator\Identical
    "The two given tokens do not match" => "2 つのトークンは一致しませんでした",
    "No token was provided to match against" => "チェックを行うためのトークンがありませんでした",

    // Zend\Validator\InArray
    "The input was not found in the haystack" => " 入力値が haystack の中に見つかりませんでした",

    // Zend\Validator\Ip
    "Invalid type given. String expected" => "不正な形式です。文字列が期待されています",
    "The input does not appear to be a valid IP address" => " 入力値は IP アドレスではないようです",

    // Zend\Validator\IsInstanceOf
    "The input is not an instance of '%className%'" => "入力は '%className%' のインスタンスではありません",

    // Zend\Validator\Isbn
    "Invalid type given. String or integer expected" => "不正な形式です。文字列もしくは整数が期待されています",
    "The input is not a valid ISBN number" => " 入力値は ISBN 番号ではありません",

    // Zend\Validator\LessThan
    "The input is not less than '%max%'" => " 入力値は '%max%' 未満ではありません",
    "The input is not less or equal than '%max%'" => "入力値は '%max%' 以下ではありません",

    // Zend\Validator\NotEmpty
    "Value is required and can't be empty" => "値は必須です。空値は許可されていません",
    "Invalid type given. String, integer, float, boolean or array expected" => "不正な形式です。文字列、整数、小数、真偽値もしくは配列が期待されています",

    // Zend\Validator\Regex
    "Invalid type given. String, integer or float expected" => "不正な形式です。文字列、整数、もしくは小数が期待されています",
    "The input does not match against pattern '%pattern%'" => " 入力値はパターン '%pattern%' と一致していません",
    "There was an internal error while using the pattern '%pattern%'" => "正規表現パターン '%pattern%' を使用中に内部エラーが発生しました。",

    // Zend\Validator\Sitemap\Changefreq
    "The input is not a valid sitemap changefreq" => " 入力値は正しいサイトマップの更新頻度ではありません",
    "Invalid type given. String expected" => "不正な形式です。文字列が期待されています",

    // Zend\Validator\Sitemap\Lastmod
    "The input is not a valid sitemap lastmod" => " 入力値は正しいサイトマップの最終更新日ではありません",
    "Invalid type given. String expected" => "不正な形式です。文字列が期待されています",

    // Zend\Validator\Sitemap\Loc
    "The input is not a valid sitemap location" => " 入力値は正しいサイトマップの位置ではありません",
    "Invalid type given. String expected" => "不正な形式です。文字列が期待されています",

    // Zend\Validator\Sitemap\Priority
    "The input is not a valid sitemap priority" => " 入力値は正しいサイトマップの優先度ではありません",
    "Invalid type given. Numeric string, integer or float expected" => "不正な形式です。数字、整数もしくは小数が期待されています",

    // Zend\Validator\Step
    "Invalid value given. Scalar expected" => "無効な値が与えられています。スカラーが期待されます",
    "The input is not a valid step" => "入力値は有効な間隔ではありません",

    // Zend\Validator\StringLength
    "Invalid type given. String expected" => "不正な形式です。文字列が期待されています",
    "The input is less than %min% characters long" => " 入力値は %min% 文字より短いです",
    "The input is more than %max% characters long" => " 入力値は %max% 文字より長いです",

    // Zend\Validator\Uri
    "Invalid type given. String expected" => "不正な形式です。文字列が期待されています",
    "The input does not appear to be a valid Uri" => "入力値は有効なUriではないようです",
);
