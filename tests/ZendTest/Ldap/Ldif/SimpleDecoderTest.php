<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Ldap
 */

namespace ZendTest\Ldap\Ldif;

use Zend\Ldap\Ldif;
use ZendTest\Ldap as TestLdap;

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @group      Zend_Ldap
 * @group      Zend_Ldap_Ldif
 */
class SimpleDecoderTest extends TestLdap\AbstractTestCase
{
    public function testDecodeSimpleSingleItem()
    {
        $data =
"version: 1
dn: cn=test3,ou=example,dc=cno
objectclass: oc1
attr3: foo";
        $expected = array(
            'dn'          => 'cn=test3,ou=example,dc=cno',
            'objectclass' => array('oc1'),
            'attr3'       => array('foo'));
        $actual   = Ldif\Encoder::decode($data);
        $this->assertEquals($expected, $actual);
    }

    public function testDecodeSingleItemWithFoldedAttribute()
    {
        $data =
"dn: cn=test blabla,ou=example,dc=cno
objectclass: oc2
attr1: 12345
attr2: 1234
attr2: baz
attr3: foo
attr3: bar
cn: test blabla
verylong: fhu08rhvt7b478vt5hv78h45nfgt45h78t34hhhhhhhhhv5bg8
 h6ttttttttt3489t57nhvgh4788trhg8999vnhtgthgui65hgb
 5789thvngwr789cghm738";
        $expected = array(
            'dn'          => 'cn=test blabla,ou=example,dc=cno',
            'objectclass' => array('oc2'),
            'attr1'       => array('12345'),
            'attr2'       => array('1234', 'baz'),
            'attr3'       => array('foo', 'bar'),
            'cn'          => array('test blabla'),
            'verylong'    => array('fhu08rhvt7b478vt5hv78h45nfgt45h78t34hhhhhhhhhv5bg8'
                                 . 'h6ttttttttt3489t57nhvgh4788trhg8999vnhtgthgui65hgb'
                                 . '5789thvngwr789cghm738'),
        );
        $actual   = Ldif\Encoder::decode($data);
        $this->assertEquals($expected, $actual);
    }

    public function testDecodeSingleItemWithBase64Attributes()
    {
        $data =
"dn:: Y249dGVzdCBibGFibGEsb3U9ZXhhbXBsZSxkYz1jbm8=
objectclass: oc3
attr1: 12345
attr2: 1234
attr2: baz
attr3: foo
attr3: bar
attr4:: w7bDpMO8
attr5:: ZW5kc3BhY2Ug
attr6:: OmJhZGluaXRjaGFy
attr6:: PGJhZGluaXRjaGFy
cn:: dGVzdCDDtsOkw7w=";
        $expected = array(
            'dn'          => 'cn=test blabla,ou=example,dc=cno',
            'objectclass' => array('oc3'),
            'attr1'       => array('12345'),
            'attr2'       => array('1234', 'baz'),
            'attr3'       => array('foo', 'bar'),
            'attr4'       => array('öäü'),
            'attr5'       => array('endspace '),
            'attr6'       => array(':badinitchar', '<badinitchar'),
            'cn'          => array('test öäü'),
        );
        $actual   = Ldif\Encoder::decode($data);
        $this->assertEquals($expected, $actual);
    }

    public function testDecodeSingleItemWithFoldedBase64Attribute()
    {
        $data =
"dn:: Y249dGVzdCBibGFibGEsb
 3U9ZXhhbXBsZSxkYz1jbm8=
objectclass: oc3
attr1: 12345
attr2: 1234
attr2: baz
attr3: foo
attr3: bar";
        $expected = array(
            'dn'          => 'cn=test blabla,ou=example,dc=cno',
            'objectclass' => array('oc3'),
            'attr1'       => array('12345'),
            'attr2'       => array('1234', 'baz'),
            'attr3'       => array('foo', 'bar'),
        );
        $actual   = Ldif\Encoder::decode($data);
        $this->assertEquals($expected, $actual);
    }

    public function testDecodeTwoItems()
    {
        $data =
"version: 1
dn: cn=Barbara Jensen, ou=Product Development, dc=airius, dc=com
objectclass: top
objectclass: person
objectclass: organizationalPerson
cn: Barbara Jensen
cn: Barbara J Jensen
cn: Babs Jensen
sn: Jensen
uid: bjensen
telephonenumber: +1 408 555 1212
description: A big sailing fan.

dn: cn=Bjorn Jensen, ou=Accounting, dc=airius, dc=com
objectclass: top
objectclass: person
objectclass: organizationalPerson
cn: Bjorn Jensen
sn: Jensen
telephonenumber: +1 408 555 1212";
        $expected = array(
            array(
                'dn'              => 'cn=Barbara Jensen, ou=Product Development, dc=airius, dc=com',
                'objectclass'     => array('top', 'person', 'organizationalPerson'),
                'cn'              => array('Barbara Jensen', 'Barbara J Jensen', 'Babs Jensen'),
                'sn'              => array('Jensen'),
                'uid'             => array('bjensen'),
                'telephonenumber' => array('+1 408 555 1212'),
                'description'     => array('A big sailing fan.'),
            ),
            array(
                'dn'              => 'cn=Bjorn Jensen, ou=Accounting, dc=airius, dc=com',
                'objectclass'     => array('top', 'person', 'organizationalPerson'),
                'cn'              => array('Bjorn Jensen'),
                'sn'              => array('Jensen'),
                'telephonenumber' => array('+1 408 555 1212'),
            ),
        );
        $actual   = Ldif\Encoder::decode($data);
        $this->assertEquals($expected, $actual);
    }

    public function testDecodeStringContainingEntryWithFoldedAttributeValue()
    {
        $data =
"version: 1
dn:cn=Barbara Jensen, ou=Product Development, dc=airius, dc=com
objectclass:top
objectclass:person
objectclass:organizationalPerson
cn:Barbara Jensen
cn:Barbara J Jensen
cn:Babs Jensen
sn:Jensen
uid:bjensen
telephonenumber:+1 408 555 1212
description:Babs is a big sailing fan, and travels extensively in sea
 rch of perfect sailing conditions.
title:Product Manager, Rod and Reel Division";
        $expected = array(
            'dn'                => 'cn=Barbara Jensen, ou=Product Development, dc=airius, dc=com',
            'objectclass'       => array('top', 'person', 'organizationalPerson'),
            'cn'                => array('Barbara Jensen', 'Barbara J Jensen', 'Babs Jensen'),
            'sn'                => array('Jensen'),
            'uid'               => array('bjensen'),
            'telephonenumber'   => array('+1 408 555 1212'),
            'description'       => array('Babs is a big sailing fan, and travels extensively'
                                       . ' in search of perfect sailing conditions.'),
            'title'             => array('Product Manager, Rod and Reel Division'),
        );
        $actual   = Ldif\Encoder::decode($data);
        $this->assertEquals($expected, $actual);
    }

    public function testDecodeStringContainingBase64EncodedValue()
    {
        $data =
"version: 1
dn: cn=Gern Jensen, ou=Product Testing, dc=airius, dc=com
objectclass: top
objectclass: person
objectclass: organizationalPerson
cn: Gern Jensen
cn: Gern O Jensen
sn: Jensen
uid: gernj
telephonenumber: +1 408 555 1212
description:: V2hhdCBhIGNhcmVmdWwgcmVhZGVyIHlvdSBhcmUhICBUaGlzIHZhbHVl
 IGlzIGJhc2UtNjQtZW5jb2RlZCBiZWNhdXNlIGl0IGhhcyBhIGNvbnRyb2wgY2hhcmFjdG
 VyIGluIGl0IChhIENSKS4NICBCeSB0aGUgd2F5LCB5b3Ugc2hvdWxkIHJlYWxseSBnZXQg
 b3V0IG1vcmUu";
        $expected = array(
            'dn'              => 'cn=Gern Jensen, ou=Product Testing, dc=airius, dc=com',
            'objectclass'     => array('top', 'person', 'organizationalPerson'),
            'cn'              => array('Gern Jensen', 'Gern O Jensen'),
            'sn'              => array('Jensen'),
            'uid'             => array('gernj'),
            'telephonenumber' => array('+1 408 555 1212'),
            'description'     => array('What a careful reader you are!'
                                     . '  This value is base-64-encoded because it has a '
                                     . 'control character in it (a CR).' . "\r"
                                     . '  By the way, you should really get out more.'),
        );
        $actual   = Ldif\Encoder::decode($data);
        $this->assertEquals($expected, $actual);
    }

    public function testDecodeStringContainingEntriesWithUtf8EncodedAttributeValues()
    {
        $data =
"version: 1
dn:: b3U95Za25qWt6YOoLG89QWlyaXVz
# dn:: ou=営業部,o=Airius
objectclass: top
objectclass: organizationalUnit
ou:: 5Za25qWt6YOo
# ou:: 営業部
ou;lang-ja:: 5Za25qWt6YOo
# ou;lang-ja:: 営業部
ou;lang-ja;phonetic:: 44GI44GE44GO44KH44GG44G2
# ou;lang-ja:: えいぎょうぶ

ou;lang-en: Sales
description: Japanese office

dn:: dWlkPXJvZ2FzYXdhcmEsb3U95Za25qWt6YOoLG89QWlyaXVz
# dn:: uid=rogasawara,ou=営業部,o=Airius
userpassword: {SHA}O3HSv1MusyL4kTjP+HKI5uxuNoM=
objectclass: top
objectclass: person
objectclass: organizationalPerson
objectclass: inetOrgPerson
uid: rogasawara
mail: rogasawara@airius.co.jp
givenname;lang-ja:: 44Ot44OJ44OL44O8
# givenname;lang-ja:: ロドニー
sn;lang-ja:: 5bCP56yg5Y6f
# sn;lang-ja:: 小笠原
cn;lang-ja:: 5bCP56yg5Y6fIOODreODieODi+ODvA==
# cn;lang-ja:: 小笠原 ロドニー
title;lang-ja:: 5Za25qWt6YOoIOmDqOmVtw==
# title;lang-ja:: 営業部 部長
preferredlanguage: ja
givenname:: 44Ot44OJ44OL44O8
# givenname:: ロドニー
sn:: 5bCP56yg5Y6f
# sn:: 小笠原
cn:: 5bCP56yg5Y6fIOODreODieODi+ODvA==
# cn:: 小笠原 ロドニー
title:: 5Za25qWt6YOoIOmDqOmVtw==
# title:: 営業部 部長
givenname;lang-ja;phonetic:: 44KN44Gp44Gr44O8
# givenname;lang-ja;phonetic:: ろどにー
sn;lang-ja;phonetic:: 44GK44GM44GV44KP44KJ
# sn;lang-ja;phonetic:: おがさわら
cn;lang-ja;phonetic:: 44GK44GM44GV44KP44KJIOOCjeOBqeOBq+ODvA==
# cn;lang-ja;phonetic:: おがさわら ろどにー
title;lang-ja;phonetic:: 44GI44GE44GO44KH44GG44G2IOOBtuOBoeOCh+OBhg==
# title;lang-ja;phonetic:: えいぎょうぶ ぶちょう
givenname;lang-en: Rodney
sn;lang-en: Ogasawara
cn;lang-en: Rodney Ogasawara
title;lang-en: Sales, Director";

        $actual = Ldif\Encoder::decode($data);

        $this->assertEquals('ou=営業部,o=Airius', $actual[0]['dn']);
        $this->assertEquals(array('top', 'organizationalUnit'), $actual[0]['objectclass']);
        $this->assertEquals('営業部', $actual[0]['ou'][0]);
        $this->assertEquals('営業部', $actual[0]['ou;lang-ja'][0]);
        $this->assertEquals('えいぎょうぶ', $actual[0]['ou;lang-ja;phonetic'][0]);
        $this->assertEquals('Sales', $actual[0]['ou;lang-en'][0]);
        $this->assertEquals('Japanese office', $actual[0]['description'][0]);

        $this->assertEquals('uid=rogasawara,ou=営業部,o=Airius', $actual[1]['dn']);
        $this->assertEquals('{SHA}O3HSv1MusyL4kTjP+HKI5uxuNoM=', $actual[1]['userpassword'][0]);
        $this->assertEquals(array('top', 'person', 'organizationalPerson', 'inetOrgPerson'),
            $actual[1]['objectclass']
        );
        $this->assertEquals('rogasawara', $actual[1]['uid'][0]);
        $this->assertEquals('rogasawara@airius.co.jp', $actual[1]['mail'][0]);
        $this->assertEquals('ロドニー', $actual[1]['givenname;lang-ja'][0]);
        $this->assertEquals('小笠原', $actual[1]['sn;lang-ja'][0]);
        $this->assertEquals('小笠原 ロドニー', $actual[1]['cn;lang-ja'][0]);
        $this->assertEquals('営業部 部長', $actual[1]['title;lang-ja'][0]);
        $this->assertEquals('ja', $actual[1]['preferredlanguage'][0]);
        $this->assertEquals('ロドニー', $actual[1]['givenname'][0]);
        $this->assertEquals('小笠原', $actual[1]['sn'][0]);
        $this->assertEquals('小笠原 ロドニー', $actual[1]['cn'][0]);
        $this->assertEquals('営業部 部長', $actual[1]['title'][0]);
        $this->assertEquals('ろどにー', $actual[1]['givenname;lang-ja;phonetic'][0]);
        $this->assertEquals('おがさわら', $actual[1]['sn;lang-ja;phonetic'][0]);
        $this->assertEquals('おがさわら ろどにー', $actual[1]['cn;lang-ja;phonetic'][0]);
        $this->assertEquals('えいぎょうぶ ぶちょう', $actual[1]['title;lang-ja;phonetic'][0]);
        $this->assertEquals('Rodney', $actual[1]['givenname;lang-en'][0]);
        $this->assertEquals('Ogasawara', $actual[1]['sn;lang-en'][0]);
        $this->assertEquals('Rodney Ogasawara', $actual[1]['cn;lang-en'][0]);
        $this->assertEquals('Sales, Director', $actual[1]['title;lang-en'][0]);
    }

    public function testDecodeSingleItemWithFoldedAttributesAndEmptyLinesBetween()
    {
        $data =
"dn: cn=test blabla,ou=example,dc=cno

objectclass: top


objectclass: person

objectclass: organizationalPerson

description:: V2hhdCBhIGNhcmVmdWwgcmVhZGVyIHlvdSBhcmUhICBUaGlzIHZhbHVl

 IGlzIGJhc2UtNjQtZW5jb2RlZCBiZWNhdXNlIGl0IGhhcyBhIGNvbnRyb2wgY2hhcmFjdG

 VyIGluIGl0IChhIENSKS4NICBCeSB0aGUgd2F5LCB5b3Ugc2hvdWxkIHJlYWxseSBnZXQg

 b3V0IG1vcmUu


verylong: fhu08rhvt7b478vt5hv78h45nfgt45h78t34hhhhhhhhhv5bg8

 h6ttttttttt3489t57nhvgh4788trhg8999vnhtgthgui65hgb

 5789thvngwr789cghm738";
        $expected = array(
            'dn'          => 'cn=test blabla,ou=example,dc=cno',
            'objectclass' => array('top', 'person', 'organizationalPerson'),
            'description' => array('What a careful reader you are!'
                                 . '  This value is base-64-encoded because it has a '
                                 . 'control character in it (a CR).' . "\r"
                                 . '  By the way, you should really get out more.'),
            'verylong'    => array('fhu08rhvt7b478vt5hv78h45nfgt45h78t34hhhhhhhhhv5bg8'
                                 . 'h6ttttttttt3489t57nhvgh4788trhg8999vnhtgthgui65hgb'
                                 . '5789thvngwr789cghm738'),
        );
        $actual   = Ldif\Encoder::decode($data);
        $this->assertEquals($expected, $actual);
    }

    public function testRoundtripEncoding()
    {
        $node     = $this->createTestNode();
        $ldif     = $node->toLdif();
        $data     = Ldif\Encoder::decode($ldif);
        $expected = array_merge(array('dn' => $node->getDnString()), $node->getData(false));
        $this->assertEquals($expected, $data);
    }
}
