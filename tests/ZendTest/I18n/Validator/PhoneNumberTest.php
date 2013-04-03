<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace Zend\I18nTest\Validator;

use Zend\I18n\Validator\PhoneNumber;

class PhoneNumberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PhoneNumber
     */
    protected $validator;

    /**
     * @var array
     */
    protected $phone = array(
        'AC' => array(
            'code' => '247',
            'patterns' => array(
                'example' => array(
                    'fixed' => '6889',
                    'emergency' => '911',
                ),
            ),
        ),
        'AD' => array(
            'code' => '376',
            'patterns' => array(
                'example' => array(
                    'fixed' => '712345',
                    'mobile' => '312345',
                    'tollfree' => '18001234',
                    'premium' => '912345',
                    'emergency' => '112',
                ),
            ),
        ),
        'AE' => array(
            'code' => '971',
            'patterns' => array(
                'example' => array(
                    'fixed' => '22345678',
                    'mobile' => '501234567',
                    'tollfree' => '800123456',
                    'premium' => '900234567',
                    'shared' => '700012345',
                    'uan' => '600212345',
                    'emergency' => '112',
                ),
            ),
        ),
        'AF' => array(
            'code' => '93',
            'patterns' => array(
                'example' => array(
                    'fixed' => '234567890',
                    'mobile' => '701234567',
                    'emergency' => '119',
                ),
            ),
        ),
        'AG' => array(
            'code' => '1',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2684601234',
                    'mobile' => '2684641234',
                    'pager' => '2684061234',
                    'tollfree' => '8002123456',
                    'premium' => '9002123456',
                    'personal' => '5002345678',
                    'voip' => '2684801234',
                    'emergency' => '911',
                ),
            ),
        ),
        'AI' => array(
            'code' => '1',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2644612345',
                    'mobile' => '2642351234',
                    'tollfree' => '8002123456',
                    'premium' => '9002123456',
                    'personal' => '5002345678',
                    'emergency' => '911',
                ),
            ),
        ),
        'AL' => array(
            'code' => '355',
            'patterns' => array(
                'example' => array(
                    'fixed' => '22345678',
                    'mobile' => '661234567',
                    'tollfree' => '8001234',
                    'premium' => '900123',
                    'shared' => '808123',
                    'personal' => '70012345',
                    'emergency' => '129',
                ),
            ),
        ),
        'AM' => array(
            'code' => '374',
            'patterns' => array(
                'example' => array(
                    'fixed' => '10123456',
                    'mobile' => '77123456',
                    'tollfree' => '80012345',
                    'premium' => '90012345',
                    'shared' => '80112345',
                    'voip' => '60271234',
                    'shortcode' => '8711',
                    'emergency' => '102',
                ),
            ),
        ),
        'AO' => array(
            'code' => '244',
            'patterns' => array(
                'example' => array(
                    'fixed' => '222123456',
                    'mobile' => '923123456',
                    'emergency' => '113',
                ),
            ),
        ),
        'AR' => array(
            'code' => '54',
            'patterns' => array(
                'example' => array(
                    'fixed' => '1123456789',
                    'mobile' => '91123456789',
                    'tollfree' => '8001234567',
                    'premium' => '6001234567',
                    'uan' => '8101234567',
                    'shortcode' => '121',
                    'emergency' => '101',
                ),
            ),
        ),
        'AS' => array(
            'code' => '1',
            'patterns' => array(
                'example' => array(
                    'fixed' => '6846221234',
                    'mobile' => '6847331234',
                    'tollfree' => '8002123456',
                    'premium' => '9002123456',
                    'personal' => '5002345678',
                    'emergency' => '911',
                ),
            ),
        ),
        'AT' => array(
            'code' => '43',
            'patterns' => array(
                'example' => array(
                    'fixed' => '1234567890',
                    'mobile' => '644123456',
                    'tollfree' => '800123456',
                    'premium' => '900123456',
                    'shared' => '810123456',
                    'voip' => '780123456',
                    'uan' => '50123',
                    'emergency' => '112',
                ),
            ),
        ),
        'AU' => array(
            'code' => '61',
            'patterns' => array(
                'example' => array(
                    'fixed' => '212345678',
                    'mobile' => '412345678',
                    'pager' => '1612345',
                    'tollfree' => '1800123456',
                    'premium' => '1900123456',
                    'shared' => '1300123456',
                    'personal' => '500123456',
                    'voip' => '550123456',
                    'emergency' => '112',
                ),
            ),
        ),
        'AW' => array(
            'code' => '297',
            'patterns' => array(
                'example' => array(
                    'fixed' => '5212345',
                    'mobile' => '5601234',
                    'tollfree' => '8001234',
                    'premium' => '9001234',
                    'voip' => '5011234',
                    'emergency' => '911',
                ),
            ),
        ),
        'AX' => array(
            'code' => '358',
            'patterns' => array(
                'example' => array(
                    'fixed' => '1812345678',
                    'mobile' => '412345678',
                    'tollfree' => '8001234567',
                    'premium' => '600123456',
                    'uan' => '10112345',
                    'emergency' => '112',
                ),
            ),
        ),
        'AZ' => array(
            'code' => '994',
            'patterns' => array(
                'example' => array(
                    'fixed' => '123123456',
                    'mobile' => '401234567',
                    'tollfree' => '881234567',
                    'premium' => '900200123',
                    'emergency' => '101',
                ),
            ),
        ),
        'BA' => array(
            'code' => '387',
            'patterns' => array(
                'example' => array(
                    'fixed' => '30123456',
                    'mobile' => '61123456',
                    'tollfree' => '80123456',
                    'premium' => '90123456',
                    'shared' => '82123456',
                    'uan' => '70223456',
                    'emergency' => '122',
                ),
            ),
        ),
        'BB' => array(
            'code' => '1',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2462345678',
                    'mobile' => '2462501234',
                    'tollfree' => '8002123456',
                    'premium' => '9002123456',
                    'personal' => '5002345678',
                    'emergency' => '211',
                ),
            ),
        ),
        'BD' => array(
            'code' => '880',
            'patterns' => array(
                'example' => array(
                    'fixed' => '27111234',
                    'mobile' => '1812345678',
                    'tollfree' => '8001234567',
                    'voip' => '9604123456',
                    'shortcode' => '103',
                    'emergency' => '999',
                ),
            ),
        ),
        'BE' => array(
            'code' => '32',
            'patterns' => array(
                'example' => array(
                    'fixed' => '12345678',
                    'mobile' => '470123456',
                    'tollfree' => '80012345',
                    'premium' => '90123456',
                    'uan' => '78123456',
                    'emergency' => '112',
                ),
            ),
        ),
        'BF' => array(
            'code' => '226',
            'patterns' => array(
                'example' => array(
                    'fixed' => '20491234',
                    'mobile' => '70123456',
                    'emergency' => '17',
                ),
            ),
        ),
        'BG' => array(
            'code' => '359',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2123456',
                    'mobile' => '48123456',
                    'tollfree' => '80012345',
                    'premium' => '90123456',
                    'personal' => '70012345',
                    'emergency' => '112',
                ),
            ),
        ),
        'BH' => array(
            'code' => '973',
            'patterns' => array(
                'example' => array(
                    'fixed' => '17001234',
                    'mobile' => '36001234',
                    'tollfree' => '80123456',
                    'premium' => '90123456',
                    'shared' => '84123456',
                    'emergency' => '999',
                ),
            ),
        ),
        'BI' => array(
            'code' => '257',
            'patterns' => array(
                'example' => array(
                    'fixed' => '22201234',
                    'mobile' => '79561234',
                    'emergency' => '117',
                ),
            ),
        ),
        'BJ' => array(
            'code' => '229',
            'patterns' => array(
                'example' => array(
                    'fixed' => '20211234',
                    'mobile' => '90011234',
                    'tollfree' => '7312',
                    'voip' => '85751234',
                    'uan' => '81123456',
                    'emergency' => '117',
                ),
            ),
        ),
        'BL' => array(
            'code' => '590',
            'patterns' => array(
                'example' => array(
                    'fixed' => '590271234',
                    'mobile' => '690221234',
                    'emergency' => '18',
                ),
            ),
        ),
        'BM' => array(
            'code' => '1',
            'patterns' => array(
                'example' => array(
                    'fixed' => '4412345678',
                    'mobile' => '4413701234',
                    'tollfree' => '8002123456',
                    'premium' => '9002123456',
                    'personal' => '5002345678',
                    'emergency' => '911',
                ),
            ),
        ),
        'BN' => array(
            'code' => '673',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2345678',
                    'mobile' => '7123456',
                    'emergency' => '991',
                ),
            ),
        ),
        'BO' => array(
            'code' => '591',
            'patterns' => array(
                'example' => array(
                    'fixed' => '22123456',
                    'mobile' => '71234567',
                    'emergency' => '110',
                ),
            ),
        ),
        'BQ' => array(
            'code' => '599',
            'patterns' => array(
                'example' => array(
                    'fixed' => '7151234',
                    'mobile' => '3181234',
                    'emergency' => '112',
                ),
            ),
        ),
        'BR' => array(
            'code' => '55',
            'patterns' => array(
                'example' => array(
                    'fixed' => '1123456789',
                    'mobile' => '1161234567',
                    'tollfree' => '800123456',
                    'premium' => '300123456',
                    'shared' => '40041234',
                    'emergency' => '190',
                ),
            ),
        ),
        'BS' => array(
            'code' => '1',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2423456789',
                    'mobile' => '2423591234',
                    'tollfree' => '8002123456',
                    'premium' => '9002123456',
                    'personal' => '5002345678',
                    'emergency' => '911',
                ),
            ),
        ),
        'BT' => array(
            'code' => '975',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2345678',
                    'mobile' => '17123456',
                    'emergency' => '113',
                ),
            ),
        ),
        'BW' => array(
            'code' => '267',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2401234',
                    'mobile' => '71123456',
                    'premium' => '9012345',
                    'voip' => '79101234',
                    'emergency' => '999',
                ),
            ),
        ),
        'BY' => array(
            'code' => '375',
            'patterns' => array(
                'example' => array(
                    'fixed' => '152450911',
                    'mobile' => '294911911',
                    'tollfree' => '8011234567',
                    'premium' => '9021234567',
                    'emergency' => '112',
                ),
            ),
        ),
        'BZ' => array(
            'code' => '501',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2221234',
                    'mobile' => '6221234',
                    'tollfree' => '08001234123',
                    'emergency' => '911',
                ),
            ),
        ),
        'CA' => array(
            'code' => '1',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2042345678',
                    'mobile' => '2042345678',
                    'tollfree' => '8002123456',
                    'premium' => '9002123456',
                    'personal' => '5002345678',
                    'emergency' => '911',
                ),
            ),
        ),
        'CC' => array(
            'code' => '61',
            'patterns' => array(
                'example' => array(
                    'fixed' => '891621234',
                    'mobile' => '412345678',
                    'tollfree' => '1800123456',
                    'premium' => '1900123456',
                    'personal' => '500123456',
                    'voip' => '550123456',
                    'emergency' => '112',
                ),
            ),
        ),
        'CD' => array(
            'code' => '243',
            'patterns' => array(
                'example' => array(
                    'fixed' => '1234567',
                    'mobile' => '991234567',
                ),
            ),
        ),
        'CF' => array(
            'code' => '236',
            'patterns' => array(
                'example' => array(
                    'fixed' => '21612345',
                    'mobile' => '70012345',
                    'premium' => '87761234',
                ),
            ),
        ),
        'CG' => array(
            'code' => '242',
            'patterns' => array(
                'example' => array(
                    'fixed' => '222123456',
                    'mobile' => '061234567',
                    'tollfree' => '800123456',
                ),
            ),
        ),
        'CH' => array(
            'code' => '41',
            'patterns' => array(
                'example' => array(
                    'fixed' => '212345678',
                    'mobile' => '741234567',
                    'tollfree' => '800123456',
                    'premium' => '900123456',
                    'shared' => '840123456',
                    'personal' => '878123456',
                    'voicemail' => '860123456789',
                    'emergency' => '112',
                ),
            ),
        ),
        'CI' => array(
            'code' => '225',
            'patterns' => array(
                'example' => array(
                    'fixed' => '21234567',
                    'mobile' => '01234567',
                    'emergency' => '110',
                ),
            ),
        ),
        'CK' => array(
            'code' => '682',
            'patterns' => array(
                'example' => array(
                    'fixed' => '21234',
                    'mobile' => '71234',
                    'emergency' => '998',
                ),
            ),
        ),
        'CL' => array(
            'code' => '56',
            'patterns' => array(
                'example' => array(
                    'fixed' => '221234567',
                    'mobile' => '961234567',
                    'tollfree' => '800123456',
                    'shared' => '6001234567',
                    'voip' => '441234567',
                    'emergency' => '133',
                ),
            ),
        ),
        'CM' => array(
            'code' => '237',
            'patterns' => array(
                'example' => array(
                    'fixed' => '22123456',
                    'mobile' => '71234567',
                    'tollfree' => '80012345',
                    'premium' => '88012345',
                    'emergency' => '113',
                ),
            ),
        ),
        'CN' => array(
            'code' => '86',
            'patterns' => array(
                'example' => array(
                    'fixed' => '1012345678',
                    'mobile' => '13123456789',
                    'tollfree' => '8001234567',
                    'premium' => '16812345',
                    'shared' => '4001234567',
                    'emergency' => '119',
                ),
            ),
        ),
        'CO' => array(
            'code' => '57',
            'patterns' => array(
                'example' => array(
                    'fixed' => '12345678',
                    'mobile' => '3211234567',
                    'tollfree' => '18001234567',
                    'premium' => '19001234567',
                    'emergency' => '112',
                ),
            ),
        ),
        'CR' => array(
            'code' => '506',
            'patterns' => array(
                'example' => array(
                    'fixed' => '22123456',
                    'mobile' => '83123456',
                    'tollfree' => '8001234567',
                    'premium' => '9001234567',
                    'voip' => '40001234',
                    'shortcode' => '1022',
                    'emergency' => '911',
                ),
            ),
        ),
        'CU' => array(
            'code' => '53',
            'patterns' => array(
                'example' => array(
                    'fixed' => '71234567',
                    'mobile' => '51234567',
                    'shortcode' => '140',
                    'emergency' => '106',
                ),
            ),
        ),
        'CV' => array(
            'code' => '238',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2211234',
                    'mobile' => '9911234',
                    'emergency' => '132',
                ),
            ),
        ),
        'CW' => array(
            'code' => '599',
            'patterns' => array(
                'example' => array(
                    'fixed' => '94151234',
                    'mobile' => '95181234',
                    'pager' => '95581234',
                    'shared' => '1011234',
                    'emergency' => '112',
                ),
            ),
        ),
        'CX' => array(
            'code' => '61',
            'patterns' => array(
                'example' => array(
                    'fixed' => '891641234',
                    'mobile' => '412345678',
                    'tollfree' => '1800123456',
                    'premium' => '1900123456',
                    'personal' => '500123456',
                    'voip' => '550123456',
                    'emergency' => '112',
                ),
            ),
        ),
        'CY' => array(
            'code' => '357',
            'patterns' => array(
                'example' => array(
                    'fixed' => '22345678',
                    'mobile' => '96123456',
                    'tollfree' => '80001234',
                    'premium' => '90012345',
                    'shared' => '80112345',
                    'personal' => '70012345',
                    'uan' => '77123456',
                    'emergency' => '112',
                ),
            ),
        ),
        'CZ' => array(
            'code' => '420',
            'patterns' => array(
                'example' => array(
                    'fixed' => '212345678',
                    'mobile' => '601123456',
                    'tollfree' => '800123456',
                    'premium' => '900123456',
                    'shared' => '811234567',
                    'personal' => '700123456',
                    'voip' => '910123456',
                    'uan' => '972123456',
                    'voicemail' => '93123456789',
                    'shortcode' => '116123',
                    'emergency' => '112',
                ),
            ),
        ),
        'DE' => array(
            'code' => '49',
            'patterns' => array(
                'example' => array(
                    'fixed' => '30123456',
                    'mobile' => '15123456789',
                    'pager' => '16412345',
                    'tollfree' => '8001234567890',
                    'premium' => '9001234567',
                    'shared' => '18012345',
                    'personal' => '70012345678',
                    'uan' => '18500123456',
                    'voicemail' => '177991234567',
                    'shortcode' => '115',
                    'emergency' => '112',
                ),
            ),
        ),
        'DJ' => array(
            'code' => '253',
            'patterns' => array(
                'example' => array(
                    'fixed' => '21360003',
                    'mobile' => '77831001',
                    'emergency' => '17',
                ),
            ),
        ),
        'DK' => array(
            'code' => '45',
            'patterns' => array(
                'example' => array(
                    'fixed' => '32123456',
                    'mobile' => '20123456',
                    'tollfree' => '80123456',
                    'premium' => '90123456',
                    'emergency' => '112',
                ),
            ),
        ),
        'DM' => array(
            'code' => '1',
            'patterns' => array(
                'example' => array(
                    'fixed' => '7674201234',
                    'mobile' => '7672251234',
                    'tollfree' => '8002123456',
                    'premium' => '9002123456',
                    'personal' => '5002345678',
                    'emergency' => '999',
                ),
            ),
        ),
        'DO' => array(
            'code' => '1',
            'patterns' => array(
                'example' => array(
                    'fixed' => '8092345678',
                    'mobile' => '8092345678',
                    'tollfree' => '8002123456',
                    'premium' => '9002123456',
                    'personal' => '5002345678',
                    'emergency' => '911',
                ),
            ),
        ),
        'DZ' => array(
            'code' => '213',
            'patterns' => array(
                'example' => array(
                    'fixed' => '12345678',
                    'mobile' => '551234567',
                    'tollfree' => '800123456',
                    'premium' => '808123456',
                    'shared' => '801123456',
                    'voip' => '983123456',
                    'emergency' => '17',
                ),
            ),
        ),
        'EC' => array(
            'code' => '593',
            'patterns' => array(
                'example' => array(
                    'fixed' => '22123456',
                    'mobile' => '991234567',
                    'tollfree' => '18001234567',
                    'voip' => '28901234',
                    'emergency' => '911',
                ),
            ),
        ),
        'EE' => array(
            'code' => '372',
            'patterns' => array(
                'example' => array(
                    'fixed' => '3212345',
                    'mobile' => '51234567',
                    'tollfree' => '80012345',
                    'premium' => '9001234',
                    'personal' => '70012345',
                    'uan' => '12123',
                    'shortcode' => '116',
                    'emergency' => '112',
                ),
            ),
        ),
        'EG' => array(
            'code' => '20',
            'patterns' => array(
                'example' => array(
                    'fixed' => '234567890',
                    'mobile' => '1001234567',
                    'tollfree' => '8001234567',
                    'premium' => '9001234567',
                    'emergency' => '122',
                ),
            ),
        ),
        'EH' => array(
            'code' => '212',
            'patterns' => array(
                'example' => array(
                    'fixed' => '528812345',
                    'mobile' => '650123456',
                    'tollfree' => '801234567',
                    'premium' => '891234567',
                    'emergency' => '15',
                ),
            ),
        ),
        'ER' => array(
            'code' => '291',
            'patterns' => array(
                'example' => array(
                    'fixed' => '8370362',
                    'mobile' => '7123456',
                ),
            ),
        ),
        'ES' => array(
            'code' => '34',
            'patterns' => array(
                'example' => array(
                    'fixed' => '810123456',
                    'mobile' => '612345678',
                    'tollfree' => '800123456',
                    'premium' => '803123456',
                    'shared' => '901123456',
                    'personal' => '701234567',
                    'uan' => '511234567',
                    'emergency' => '112',
                ),
            ),
        ),
        'ET' => array(
            'code' => '251',
            'patterns' => array(
                'example' => array(
                    'fixed' => '111112345',
                    'mobile' => '911234567',
                    'emergency' => '991',
                ),
            ),
        ),
        'FI' => array(
            'code' => '358',
            'patterns' => array(
                'example' => array(
                    'fixed' => '1312345678',
                    'mobile' => '412345678',
                    'tollfree' => '8001234567',
                    'premium' => '600123456',
                    'uan' => '10112345',
                    'emergency' => '112',
                ),
            ),
        ),
        'FJ' => array(
            'code' => '679',
            'patterns' => array(
                'example' => array(
                    'fixed' => '3212345',
                    'mobile' => '7012345',
                    'tollfree' => '08001234567',
                    'shortcode' => '22',
                    'emergency' => '911',
                ),
            ),
        ),
        'FK' => array(
            'code' => '500',
            'patterns' => array(
                'example' => array(
                    'fixed' => '31234',
                    'mobile' => '51234',
                    'shortcode' => '123',
                    'emergency' => '999',
                ),
            ),
        ),
        'FM' => array(
            'code' => '691',
            'patterns' => array(
                'example' => array(
                    'fixed' => '3201234',
                    'mobile' => '3501234',
                    'emergency' => '911',
                ),
            ),
        ),
        'FO' => array(
            'code' => '298',
            'patterns' => array(
                'example' => array(
                    'fixed' => '201234',
                    'mobile' => '211234',
                    'tollfree' => '802123',
                    'premium' => '901123',
                    'voip' => '601234',
                    'shortcode' => '114',
                    'emergency' => '112',
                ),
            ),
        ),
        'FR' => array(
            'code' => '33',
            'patterns' => array(
                'example' => array(
                    'fixed' => '123456789',
                    'mobile' => '612345678',
                    'tollfree' => '801234567',
                    'premium' => '891123456',
                    'shared' => '810123456',
                    'voip' => '912345678',
                    'emergency' => '112',
                ),
            ),
        ),
        'GA' => array(
            'code' => '241',
            'patterns' => array(
                'example' => array(
                    'fixed' => '1441234',
                    'mobile' => '06031234',
                    'emergency' => '1730',
                ),
            ),
        ),
        'GB' => array(
            'code' => '44',
            'patterns' => array(
                'example' => array(
                    'fixed' => '1212345678',
                    'mobile' => '7400123456',
                    'pager' => '7640123456',
                    'tollfree' => '8001234567',
                    'premium' => '9012345678',
                    'shared' => '8431234567',
                    'personal' => '7012345678',
                    'voip' => '5612345678',
                    'uan' => '5512345678',
                    'shortcode' => '150',
                    'emergency' => '112',
                ),
            ),
        ),
        'GD' => array(
            'code' => '1',
            'patterns' => array(
                'example' => array(
                    'fixed' => '4732691234',
                    'mobile' => '4734031234',
                    'tollfree' => '8002123456',
                    'premium' => '9002123456',
                    'personal' => '5002345678',
                    'emergency' => '911',
                ),
            ),
        ),
        'GE' => array(
            'code' => '995',
            'patterns' => array(
                'example' => array(
                    'fixed' => '322123456',
                    'mobile' => '555123456',
                    'tollfree' => '800123456',
                    'emergency' => '112',
                ),
            ),
        ),
        'GF' => array(
            'code' => '594',
            'patterns' => array(
                'example' => array(
                    'fixed' => '594101234',
                    'mobile' => '694201234',
                    'emergency' => '15',
                ),
            ),
        ),
        'GG' => array(
            'code' => '44',
            'patterns' => array(
                'example' => array(
                    'fixed' => '1481456789',
                    'mobile' => '7781123456',
                    'pager' => '7640123456',
                    'tollfree' => '8001234567',
                    'premium' => '9012345678',
                    'shared' => '8431234567',
                    'personal' => '7012345678',
                    'voip' => '5612345678',
                    'uan' => '5512345678',
                    'shortcode' => '155',
                    'emergency' => '999',
                ),
            ),
        ),
        'GH' => array(
            'code' => '233',
            'patterns' => array(
                'example' => array(
                    'fixed' => '302345678',
                    'mobile' => '231234567',
                    'tollfree' => '80012345',
                    'emergency' => '999',
                ),
            ),
        ),
        'GI' => array(
            'code' => '350',
            'patterns' => array(
                'example' => array(
                    'fixed' => '20012345',
                    'mobile' => '57123456',
                    'tollfree' => '80123456',
                    'premium' => '88123456',
                    'shared' => '87123456',
                    // wrong: 'shortcode' => '116123',
                    'emergency' => '112',
                ),
            ),
        ),
        'GL' => array(
            'code' => '299',
            'patterns' => array(
                'example' => array(
                    'fixed' => '321000',
                    'mobile' => '221234',
                    'tollfree' => '801234',
                    'voip' => '381234',
                    'emergency' => '112',
                ),
            ),
        ),
        'GM' => array(
            'code' => '220',
            'patterns' => array(
                'example' => array(
                    'fixed' => '5661234',
                    'mobile' => '3012345',
                    'emergency' => '117',
                ),
            ),
        ),
        'GN' => array(
            'code' => '224',
            'patterns' => array(
                'example' => array(
                    'fixed' => '30241234',
                    'mobile' => '60201234',
                    'voip' => '78123456',
                ),
            ),
        ),
        'GP' => array(
            'code' => '590',
            'patterns' => array(
                'example' => array(
                    'fixed' => '590201234',
                    'mobile' => '690301234',
                    'emergency' => '18',
                ),
            ),
        ),
        'GQ' => array(
            'code' => '240',
            'patterns' => array(
                'example' => array(
                    'fixed' => '333091234',
                    'mobile' => '222123456',
                    'tollfree' => '800123456',
                    'premium' => '900123456',
                ),
            ),
        ),
        'GR' => array(
            'code' => '30',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2123456789',
                    'mobile' => '6912345678',
                    'tollfree' => '8001234567',
                    'premium' => '9091234567',
                    'shared' => '8011234567',
                    'personal' => '7012345678',
                    'emergency' => '112',
                ),
            ),
        ),
        'GT' => array(
            'code' => '502',
            'patterns' => array(
                'example' => array(
                    'fixed' => '22456789',
                    'mobile' => '51234567',
                    'tollfree' => '18001112222',
                    'premium' => '19001112222',
                    'shortcode' => '124',
                    'emergency' => '110',
                ),
            ),
        ),
        'GU' => array(
            'code' => '1',
            'patterns' => array(
                'example' => array(
                    'fixed' => '6713001234',
                    'mobile' => '6713001234',
                    'tollfree' => '8002123456',
                    'premium' => '9002123456',
                    'personal' => '5002345678',
                    'emergency' => '911',
                ),
            ),
        ),
        'GW' => array(
            'code' => '245',
            'patterns' => array(
                'example' => array(
                    'fixed' => '3201234',
                    'mobile' => '5012345',
                    'emergency' => '113',
                ),
            ),
        ),
        'GY' => array(
            'code' => '592',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2201234',
                    'mobile' => '6091234',
                    'tollfree' => '2891234',
                    'premium' => '9008123',
                    'shortcode' => '0801',
                    'emergency' => '911',
                ),
            ),
        ),
        'HK' => array(
            'code' => '852',
            'patterns' => array(
                'example' => array(
                    'fixed' => '21234567',
                    'mobile' => '51234567',
                    'pager' => '71234567',
                    'tollfree' => '800123456',
                    'premium' => '90012345678',
                    'personal' => '81123456',
                    'emergency' => '999',
                ),
            ),
        ),
        'HN' => array(
            'code' => '504',
            'patterns' => array(
                'example' => array(
                    'fixed' => '22123456',
                    'mobile' => '91234567',
                    'emergency' => '199',
                ),
            ),
        ),
        'HR' => array(
            'code' => '385',
            'patterns' => array(
                'example' => array(
                    'fixed' => '12345678',
                    'uan' => '62123456',
                    'mobile' => '912345678',
                    'tollfree' => '8001234567',
                    'premium' => '611234',
                    'personal' => '741234567',
                    'emergency' => '112',
                ),
            ),
        ),
        'HT' => array(
            'code' => '509',
            'patterns' => array(
                'example' => array(
                    'fixed' => '22453300',
                    'mobile' => '34101234',
                    'tollfree' => '80012345',
                    'voip' => '98901234',
                    'shortcode' => '114',
                    'emergency' => '118',
                ),
            ),
        ),
        'HU' => array(
            'code' => '36',
            'patterns' => array(
                'example' => array(
                    'fixed' => '12345678',
                    'mobile' => '201234567',
                    'tollfree' => '80123456',
                    'premium' => '90123456',
                    'shared' => '40123456',
                    'emergency' => '112',
                ),
            ),
        ),
        'ID' => array(
            'code' => '62',
            'patterns' => array(
                'example' => array(
                    'fixed' => '612345678',
                    'mobile' => '812345678',
                    'tollfree' => '8001234567',
                    'premium' => '8091234567',
                    'emergency' => '112',
                ),
            ),
        ),
        'IE' => array(
            'code' => '353',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2212345',
                    'mobile' => '850123456',
                    'tollfree' => '1800123456',
                    'premium' => '1520123456',
                    'shared' => '1850123456',
                    'personal' => '700123456',
                    'voip' => '761234567',
                    'uan' => '818123456',
                    'voicemail' => '8501234567',
                    'emergency' => '112',
                ),
            ),
        ),
        'IL' => array(
            'code' => '972',
            'patterns' => array(
                'example' => array(
                    'fixed' => '21234567',
                    'mobile' => '501234567',
                    'tollfree' => '1800123456',
                    'premium' => '1919123456',
                    'shared' => '1700123456',
                    'voip' => '771234567',
                    'uan' => '2250',
                    'voicemail' => '1599123456',
                    'shortcode' => '1455',
                    'emergency' => '112',
                ),
            ),
        ),
        'IM' => array(
            'code' => '44',
            'patterns' => array(
                'example' => array(
                    'fixed' => '1624456789',
                    'mobile' => '7924123456',
                    'tollfree' => '8081624567',
                    'premium' => '9016247890',
                    'shared' => '8456247890',
                    'personal' => '7012345678',
                    'voip' => '5612345678',
                    'uan' => '5512345678',
                    'shortcode' => '150',
                    'emergency' => '999',
                ),
            ),
        ),
        'IN' => array(
            'code' => '91',
            'patterns' => array(
                'example' => array(
                    'fixed' => '1123456789',
                    'mobile' => '9123456789',
                    'tollfree' => '1800123456',
                    'premium' => '1861123456789',
                    'uan' => '18603451234',
                    'emergency' => '108',
                ),
            ),
        ),
        'IO' => array(
            'code' => '246',
            'patterns' => array(
                'example' => array(
                    'fixed' => '3709100',
                    'mobile' => '3801234',
                ),
            ),
        ),
        'IQ' => array(
            'code' => '964',
            'patterns' => array(
                'example' => array(
                    'fixed' => '12345678',
                    'mobile' => '7912345678',
                ),
            ),
        ),
        'IR' => array(
            'code' => '98',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2123456789',
                    'mobile' => '9123456789',
                    'pager' => '9432123456',
                    'voip' => '9932123456',
                    'uan' => '9990123456',
                    'emergency' => '112',
                ),
            ),
        ),
        'IS' => array(
            'code' => '354',
            'patterns' => array(
                'example' => array(
                    'fixed' => '4101234',
                    'mobile' => '6101234',
                    'tollfree' => '8001234',
                    'premium' => '9011234',
                    'voip' => '4921234',
                    'voicemail' => '388123456',
                    'emergency' => '112',
                ),
            ),
        ),
        'IT' => array(
            'code' => '39',
            'patterns' => array(
                'example' => array(
                    'fixed' => '0212345678',
                    'mobile' => '3123456789',
                    'tollfree' => '800123456',
                    'premium' => '899123456',
                    'shared' => '848123456',
                    'personal' => '1781234567',
                    'voip' => '5512345678',
                    'shortcode' => '114',
                    'emergency' => '112',
                ),
            ),
        ),
        'JE' => array(
            'code' => '44',
            'patterns' => array(
                'example' => array(
                    'fixed' => '1534456789',
                    'mobile' => '7797123456',
                    'pager' => '7640123456',
                    'tollfree' => '8007354567',
                    'premium' => '9018105678',
                    'shared' => '8447034567',
                    'personal' => '7015115678',
                    'voip' => '5612345678',
                    'uan' => '5512345678',
                    'shortcode' => '150',
                    'emergency' => '999',
                ),
            ),
        ),
        'JM' => array(
            'code' => '1',
            'patterns' => array(
                'example' => array(
                    'fixed' => '8765123456',
                    'mobile' => '8762101234',
                    'tollfree' => '8002123456',
                    'premium' => '9002123456',
                    'personal' => '5002345678',
                    'emergency' => '119',
                ),
            ),
        ),
        'JO' => array(
            'code' => '962',
            'patterns' => array(
                'example' => array(
                    'fixed' => '62001234',
                    'mobile' => '790123456',
                    'pager' => '746612345',
                    'tollfree' => '80012345',
                    'premium' => '90012345',
                    'shared' => '85012345',
                    'personal' => '700123456',
                    'uan' => '88101234',
                    'shortcode' => '111',
                    'emergency' => '112',
                ),
            ),
        ),
        'JP' => array(
            'code' => '81',
            'patterns' => array(
                'example' => array(
                    'fixed' => '312345678',
                    'mobile' => '7012345678',
                    'pager' => '2012345678',
                    'tollfree' => '120123456',
                    'premium' => '990123456',
                    'personal' => '601234567',
                    'voip' => '5012345678',
                    'uan' => '570123456',
                    'emergency' => '110',
                ),
            ),
        ),
        'KE' => array(
            'code' => '254',
            'patterns' => array(
                'example' => array(
                    'fixed' => '202012345',
                    'mobile' => '712123456',
                    'tollfree' => '800223456',
                    'premium' => '900223456',
                    'shortcode' => '116',
                    'emergency' => '999',
                ),
            ),
        ),
        'KG' => array(
            'code' => '996',
            'patterns' => array(
                'example' => array(
                    'fixed' => '312123456',
                    'mobile' => '700123456',
                    'tollfree' => '800123456',
                    'emergency' => '101',
                ),
            ),
        ),
        'KH' => array(
            'code' => '855',
            'patterns' => array(
                'example' => array(
                    'fixed' => '23456789',
                    'mobile' => '91234567',
                    'tollfree' => '1800123456',
                    'premium' => '1900123456',
                    'emergency' => '117',
                ),
            ),
        ),
        'KI' => array(
            'code' => '686',
            'patterns' => array(
                'example' => array(
                    'fixed' => '31234',
                    'mobile' => '61234',
                    'shortcode' => '100',
                    'emergency' => '999',
                ),
            ),
        ),
        'KM' => array(
            'code' => '269',
            'patterns' => array(
                'example' => array(
                    'fixed' => '7712345',
                    'mobile' => '3212345',
                    'premium' => '9001234',
                    'emergency' => '17',
                ),
            ),
        ),
        'KN' => array(
            'code' => '1',
            'patterns' => array(
                'example' => array(
                    'fixed' => '8692361234',
                    'mobile' => '8695561234',
                    'tollfree' => '8002123456',
                    'premium' => '9002123456',
                    'personal' => '5002345678',
                    'emergency' => '999',
                ),
            ),
        ),
        'KP' => array(
            'code' => '850',
            'patterns' => array(
                'example' => array(
                    'fixed' => '21234567',
                    'mobile' => '1921234567',
                ),
            ),
        ),
        'KR' => array(
            'code' => '82',
            'patterns' => array(
                'example' => array(
                    'fixed' => '22123456',
                    'mobile' => '1023456789',
                    'tollfree' => '801234567',
                    'premium' => '602345678',
                    'personal' => '5012345678',
                    'voip' => '7012345678',
                    'uan' => '15441234',
                    'emergency' => '112',
                ),
            ),
        ),
        'KW' => array(
            'code' => '965',
            'patterns' => array(
                'example' => array(
                    'fixed' => '22345678',
                    'mobile' => '50012345',
                    'shortcode' => '177',
                    'emergency' => '112',
                ),
            ),
        ),
        'KY' => array(
            'code' => '1',
            'patterns' => array(
                'example' => array(
                    'fixed' => '3452221234',
                    'mobile' => '3453231234',
                    'pager' => '3458491234',
                    'tollfree' => '8002345678',
                    'premium' => '9002345678',
                    'personal' => '5002345678',
                    'emergency' => '911',
                ),
            ),
        ),
        'KZ' => array(
            'code' => '7',
            'patterns' => array(
                'example' => array(
                    'fixed' => '7123456789',
                    'mobile' => '7710009998',
                    'tollfree' => '8001234567',
                    'premium' => '8091234567',
                    'voip' => '7511234567',
                    'emergency' => '112',
                ),
            ),
        ),
        'LA' => array(
            'code' => '856',
            'patterns' => array(
                'example' => array(
                    'fixed' => '21212862',
                    'mobile' => '2023123456',
                    'emergency' => '190',
                ),
            ),
        ),
        'LB' => array(
            'code' => '961',
            'patterns' => array(
                'example' => array(
                    'fixed' => '1123456',
                    'mobile' => '71123456',
                    'premium' => '90123456',
                    'shared' => '80123456',
                    'emergency' => '112',
                ),
            ),
        ),
        'LC' => array(
            'code' => '1',
            'patterns' => array(
                'example' => array(
                    'fixed' => '7582345678',
                    'mobile' => '7582845678',
                    'tollfree' => '8002123456',
                    'premium' => '9002123456',
                    'personal' => '5002345678',
                    'emergency' => '911',
                ),
            ),
        ),
        'LI' => array(
            'code' => '423',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2345678',
                    'mobile' => '661234567',
                    'tollfree' => '8002222',
                    'premium' => '9002222',
                    'uan' => '8770123',
                    'voicemail' => '697361234',
                    'personal' => '7011234',
                    'shortcode' => '1600',
                    'emergency' => '112',
                ),
            ),
        ),
        'LK' => array(
            'code' => '94',
            'patterns' => array(
                'example' => array(
                    'fixed' => '112345678',
                    'mobile' => '712345678',
                    'emergency' => '119',
                ),
            ),
        ),
        'LR' => array(
            'code' => '231',
            'patterns' => array(
                'example' => array(
                    'fixed' => '21234567',
                    'mobile' => '4612345',
                    'premium' => '90123456',
                    'voip' => '332001234',
                    'emergency' => '911',
                ),
            ),
        ),
        'LS' => array(
            'code' => '266',
            'patterns' => array(
                'example' => array(
                    'fixed' => '22123456',
                    'mobile' => '50123456',
                    'tollfree' => '80021234',
                    'emergency' => '112',
                ),
            ),
        ),
        'LT' => array(
            'code' => '370',
            'patterns' => array(
                'example' => array(
                    'fixed' => '31234567',
                    'mobile' => '61234567',
                    'tollfree' => '80012345',
                    'premium' => '90012345',
                    'personal' => '70012345',
                    'shared' => '80812345',
                    'uan' => '70712345',
                    'emergency' => '112',
                ),
            ),
        ),
        'LU' => array(
            'code' => '352',
            'patterns' => array(
                'example' => array(
                    'fixed' => '27123456',
                    'mobile' => '628123456',
                    'tollfree' => '80012345',
                    'premium' => '90012345',
                    'shared' => '80112345',
                    'personal' => '70123456',
                    'voip' => '2012345',
                    'shortcode' => '12123',
                    'emergency' => '112',
                ),
            ),
        ),
        'LV' => array(
            'code' => '371',
            'patterns' => array(
                'example' => array(
                    'fixed' => '63123456',
                    'mobile' => '21234567',
                    'tollfree' => '80123456',
                    'premium' => '90123456',
                    'shared' => '81123456',
                    'emergency' => '112',
                ),
            ),
        ),
        'LY' => array(
            'code' => '218',
            'patterns' => array(
                'example' => array(
                    'fixed' => '212345678',
                    'mobile' => '912345678',
                    'emergency' => '193',
                ),
            ),
        ),
        'MA' => array(
            'code' => '212',
            'patterns' => array(
                'example' => array(
                    'fixed' => '520123456',
                    'mobile' => '650123456',
                    'tollfree' => '801234567',
                    'premium' => '891234567',
                    'emergency' => '15',
                ),
            ),
        ),
        'MC' => array(
            'code' => '377',
            'patterns' => array(
                'example' => array(
                    'fixed' => '99123456',
                    'mobile' => '612345678',
                    'tollfree' => '90123456',
                    'emergency' => '112',
                ),
            ),
        ),
        'MD' => array(
            'code' => '373',
            'patterns' => array(
                'example' => array(
                    'fixed' => '22212345',
                    'mobile' => '65012345',
                    'tollfree' => '80012345',
                    'premium' => '90012345',
                    'shared' => '80812345',
                    'uan' => '80312345',
                    'voip' => '30123456',
                    'shortcode' => '116000',
                    'emergency' => '112',
                ),
            ),
        ),
        'ME' => array(
            'code' => '382',
            'patterns' => array(
                'example' => array(
                    'fixed' => '30234567',
                    'mobile' => '67622901',
                    'tollfree' => '80080002',
                    'premium' => '94515151',
                    'voip' => '78108780',
                    'uan' => '77273012',
                    'shortcode' => '1011',
                    'emergency' => '112',
                ),
            ),
        ),
        'MF' => array(
            'code' => '590',
            'patterns' => array(
                'example' => array(
                    'fixed' => '590271234',
                    'mobile' => '690221234',
                    'emergency' => '18',
                ),
            ),
        ),
        'MG' => array(
            'code' => '261',
            'patterns' => array(
                'example' => array(
                    'fixed' => '202123456',
                    'mobile' => '301234567',
                    'emergency' => '117',
                ),
            ),
        ),
        'MH' => array(
            'code' => '692',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2471234',
                    'mobile' => '2351234',
                    'voip' => '6351234',
                ),
            ),
        ),
        'MK' => array(
            'code' => '389',
            'patterns' => array(
                'example' => array(
                    'fixed' => '22212345',
                    'mobile' => '72345678',
                    'tollfree' => '80012345',
                    'premium' => '50012345',
                    'shared' => '80123456',
                    'emergency' => '112',
                ),
            ),
        ),
        'ML' => array(
            'code' => '223',
            'patterns' => array(
                'example' => array(
                    'fixed' => '20212345',
                    'mobile' => '65012345',
                    'tollfree' => '80012345',
                    'emergency' => '17',
                ),
            ),
        ),
        'MM' => array(
            'code' => '95',
            'patterns' => array(
                'example' => array(
                    'fixed' => '1234567',
                    'mobile' => '92123456',
                    'voip' => '13331234',
                    'emergency' => '199',
                ),
            ),
        ),
        'MN' => array(
            'code' => '976',
            'patterns' => array(
                'example' => array(
                    'fixed' => '50123456',
                    'mobile' => '88123456',
                    'voip' => '75123456',
                    'emergency' => '102',
                ),
            ),
        ),
        'MO' => array(
            'code' => '853',
            'patterns' => array(
                'example' => array(
                    'fixed' => '28212345',
                    'mobile' => '66123456',
                    'emergency' => '999',
                ),
            ),
        ),
        'MP' => array(
            'code' => '1',
            'patterns' => array(
                'example' => array(
                    'fixed' => '6702345678',
                    'mobile' => '6702345678',
                    'tollfree' => '8002123456',
                    'premium' => '9002123456',
                    'personal' => '5002345678',
                    'emergency' => '911',
                ),
            ),
        ),
        'MQ' => array(
            'code' => '596',
            'patterns' => array(
                'example' => array(
                    'fixed' => '596301234',
                    'mobile' => '696201234',
                    'emergency' => '15',
                ),
            ),
        ),
        'MR' => array(
            'code' => '222',
            'patterns' => array(
                'example' => array(
                    'fixed' => '35123456',
                    'mobile' => '22123456',
                    'tollfree' => '80012345',
                    'emergency' => '17',
                ),
            ),
        ),
        'MS' => array(
            'code' => '1',
            'patterns' => array(
                'example' => array(
                    'fixed' => '6644912345',
                    'mobile' => '6644923456',
                    'tollfree' => '8002123456',
                    'premium' => '9002123456',
                    'personal' => '5002345678',
                    'emergency' => '911',
                ),
            ),
        ),
        'MT' => array(
            'code' => '356',
            'patterns' => array(
                'example' => array(
                    'fixed' => '21001234',
                    'mobile' => '96961234',
                    'pager' => '71171234',
                    'premium' => '50031234',
                    'emergency' => '112',
                ),
            ),
        ),
        'MU' => array(
            'code' => '230',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2012345',
                    'mobile' => '2512345',
                    'pager' => '2181234',
                    'tollfree' => '8001234',
                    'premium' => '3012345',
                    'voip' => '3201234',
                    'shortcode' => '195',
                    'emergency' => '999',
                ),
            ),
        ),
        'MV' => array(
            'code' => '960',
            'patterns' => array(
                'example' => array(
                    'fixed' => '6701234',
                    'mobile' => '7712345',
                    'pager' => '7812345',
                    'premium' => '9001234567',
                    'shortcode' => '123',
                    'emergency' => '102',
                ),
            ),
        ),
        'MW' => array(
            'code' => '265',
            'patterns' => array(
                'example' => array(
                    'fixed' => '1234567',
                    'mobile' => '991234567',
                    'emergency' => '997',
                ),
            ),
        ),
        'MX' => array(
            'code' => '52',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2221234567',
                    'mobile' => '12221234567',
                    'tollfree' => '8001234567',
                    'premium' => '9001234567',
                    'emergency' => '066',
                ),
            ),
        ),
        'MY' => array(
            'code' => '60',
            'patterns' => array(
                'example' => array(
                    'fixed' => '323456789',
                    'mobile' => '123456789',
                    'tollfree' => '1300123456',
                    'premium' => '1600123456',
                    'personal' => '1700123456',
                    'voip' => '1541234567',
                    'emergency' => '999',
                ),
            ),
        ),
        'MZ' => array(
            'code' => '258',
            'patterns' => array(
                'example' => array(
                    'fixed' => '21123456',
                    'mobile' => '821234567',
                    'tollfree' => '800123456',
                    'shortcode' => '101',
                    'emergency' => '119',
                ),
            ),
        ),
        'NA' => array(
            'code' => '264',
            'patterns' => array(
                'example' => array(
                    'fixed' => '612012345',
                    'mobile' => '811234567',
                    'premium' => '870123456',
                    'voip' => '88612345',
                    'shortcode' => '93111',
                    'emergency' => '10111',
                ),
            ),
        ),
        'NC' => array(
            'code' => '687',
            'patterns' => array(
                'example' => array(
                    'fixed' => '201234',
                    'mobile' => '751234',
                    'premium' => '366711',
                    'shortcode' => '1000',
                    'emergency' => '15',
                ),
            ),
        ),
        'NE' => array(
            'code' => '227',
            'patterns' => array(
                'example' => array(
                    'fixed' => '20201234',
                    'mobile' => '93123456',
                    'tollfree' => '08123456',
                    'premium' => '09123456',
                ),
            ),
        ),
        'NF' => array(
            'code' => '672',
            'patterns' => array(
                'example' => array(
                    'fixed' => '106609',
                    'mobile' => '381234',
                    'emergency' => '911',
                ),
            ),
        ),
        'NG' => array(
            'code' => '234',
            'patterns' => array(
                'example' => array(
                    'fixed' => '12345678',
                    'mobile' => '8021234567',
                    'tollfree' => '80017591759',
                    'uan' => '7001234567',
                    'emergency' => '199',
                ),
            ),
        ),
        'NI' => array(
            'code' => '505',
            'patterns' => array(
                'example' => array(
                    'fixed' => '21234567',
                    'mobile' => '81234567',
                    'tollfree' => '18001234',
                    'emergency' => '118',
                ),
            ),
        ),
        'NL' => array(
            'code' => '31',
            'patterns' => array(
                'example' => array(
                    'fixed' => '101234567',
                    'mobile' => '612345678',
                    'pager' => '662345678',
                    'tollfree' => '8001234',
                    'premium' => '9001234',
                    'voip' => '851234567',
                    'uan' => '14020',
                    'shortcode' => '1833',
                    'emergency' => '112',
                ),
            ),
        ),
        'NO' => array(
            'code' => '47',
            'patterns' => array(
                'example' => array(
                    'fixed' => '21234567',
                    'mobile' => '41234567',
                    'tollfree' => '80012345',
                    'premium' => '82012345',
                    'shared' => '81021234',
                    'personal' => '88012345',
                    'voip' => '85012345',
                    'uan' => '01234',
                    'voicemail' => '81212345',
                    'emergency' => '112',
                ),
            ),
        ),
        'NP' => array(
            'code' => '977',
            'patterns' => array(
                'example' => array(
                    'fixed' => '14567890',
                    'mobile' => '9841234567',
                    'emergency' => '112',
                ),
            ),
        ),
        'NR' => array(
            'code' => '674',
            'patterns' => array(
                'example' => array(
                    'fixed' => '4441234',
                    'mobile' => '5551234',
                    'shortcode' => '123',
                    'emergency' => '110',
                ),
            ),
        ),
        'NU' => array(
            'code' => '683',
            'patterns' => array(
                'example' => array(
                    'fixed' => '4002',
                    'mobile' => '1234',
                    'emergency' => '999',
                ),
            ),
        ),
        'NZ' => array(
            'code' => '64',
            'patterns' => array(
                'example' => array(
                    'fixed' => '32345678',
                    'mobile' => '211234567',
                    'pager' => '26123456',
                    'tollfree' => '800123456',
                    'premium' => '900123456',
                    'emergency' => '111',
                ),
            ),
        ),
        'OM' => array(
            'code' => '968',
            'patterns' => array(
                'example' => array(
                    'fixed' => '23123456',
                    'mobile' => '92123456',
                    'tollfree' => '80071234',
                    'emergency' => '9999',
                ),
            ),
        ),
        'PA' => array(
            'code' => '507',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2001234',
                    'mobile' => '60012345',
                    'tollfree' => '8001234',
                    'premium' => '8601234',
                    'shortcode' => '102',
                    'emergency' => '911',
                ),
            ),
        ),
        'PE' => array(
            'code' => '51',
            'patterns' => array(
                'example' => array(
                    'fixed' => '11234567',
                    'mobile' => '912345678',
                    'tollfree' => '80012345',
                    'premium' => '80512345',
                    'shared' => '80112345',
                    'personal' => '80212345',
                    'emergency' => '105',
                ),
            ),
        ),
        'PF' => array(
            'code' => '689',
            'patterns' => array(
                'example' => array(
                    'fixed' => '401234',
                    'mobile' => '212345',
                    'emergency' => '15',
                ),
            ),
        ),
        'PG' => array(
            'code' => '675',
            'patterns' => array(
                'example' => array(
                    'fixed' => '3123456',
                    'mobile' => '6812345',
                    'tollfree' => '1801234',
                    'voip' => '2751234',
                    'emergency' => '000',
                ),
            ),
        ),
        'PH' => array(
            'code' => '63',
            'patterns' => array(
                'example' => array(
                    'fixed' => '21234567',
                    'mobile' => '9051234567',
                    'tollfree' => '180012345678',
                    'emergency' => '117',
                ),
            ),
        ),
        'PK' => array(
            'code' => '92',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2123456789',
                    'mobile' => '3012345678',
                    'tollfree' => '80012345',
                    'premium' => '90012345',
                    'personal' => '122044444',
                    'uan' => '21111825888',
                    'emergency' => '112',
                ),
            ),
        ),
        'PL' => array(
            'code' => '48',
            'patterns' => array(
                'example' => array(
                    'fixed' => '123456789',
                    'mobile' => '512345678',
                    'pager' => '642123456',
                    'tollfree' => '800123456',
                    'premium' => '701234567',
                    'shared' => '801234567',
                    'voip' => '391234567',
                    'emergency' => '112',
                ),
            ),
        ),
        'PM' => array(
            'code' => '508',
            'patterns' => array(
                'example' => array(
                    'fixed' => '411234',
                    'mobile' => '551234',
                    'emergency' => '17',
                ),
            ),
        ),
        'PR' => array(
            'code' => '1',
            'patterns' => array(
                'example' => array(
                    'fixed' => '7872345678',
                    'mobile' => '7872345678',
                    'tollfree' => '8002345678',
                    'premium' => '9002345678',
                    'personal' => '5002345678',
                    'emergency' => '911',
                ),
            ),
        ),
        'PS' => array(
            'code' => '970',
            'patterns' => array(
                'example' => array(
                    'fixed' => '22234567',
                    'mobile' => '599123456',
                    'tollfree' => '1800123456',
                    'premium' => '19123',
                    'shared' => '1700123456',
                ),
            ),
        ),
        'PT' => array(
            'code' => '351',
            'patterns' => array(
                'example' => array(
                    'fixed' => '212345678',
                    'mobile' => '912345678',
                    'tollfree' => '800123456',
                    'premium' => '760123456',
                    'shared' => '808123456',
                    'personal' => '884123456',
                    'voip' => '301234567',
                    'uan' => '707123456',
                    'emergency' => '112',
                ),
            ),
        ),
        'PW' => array(
            'code' => '680',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2771234',
                    'mobile' => '6201234',
                    'emergency' => '911',
                ),
            ),
        ),
        'PY' => array(
            'code' => '595',
            'patterns' => array(
                'example' => array(
                    'fixed' => '212345678',
                    'mobile' => '961456789',
                    'voip' => '870012345',
                    'uan' => '201234567',
                    'shortcode' => '123',
                    'emergency' => '911',
                ),
            ),
        ),
        'QA' => array(
            'code' => '974',
            'patterns' => array(
                'example' => array(
                    'fixed' => '44123456',
                    'mobile' => '33123456',
                    'pager' => '2123456',
                    'tollfree' => '8001234',
                    'shortcode' => '2012',
                    'emergency' => '999',
                ),
            ),
        ),
        'RE' => array(
            'code' => '262',
            'patterns' => array(
                'example' => array(
                    'fixed' => '262161234',
                    'mobile' => '692123456',
                    'tollfree' => '801234567',
                    'premium' => '891123456',
                    'shared' => '810123456',
                    'emergency' => '15',
                ),
            ),
        ),
        'RO' => array(
            'code' => '40',
            'patterns' => array(
                'example' => array(
                    'fixed' => '211234567',
                    'mobile' => '712345678',
                    'tollfree' => '800123456',
                    'premium' => '900123456',
                    'shared' => '801123456',
                    'personal' => '802123456',
                    'uan' => '372123456',
                    'emergency' => '112',
                ),
            ),
        ),
        'RS' => array(
            'code' => '381',
            'patterns' => array(
                'example' => array(
                    'fixed' => '10234567',
                    'mobile' => '601234567',
                    'tollfree' => '80012345',
                    'premium' => '90012345',
                    'uan' => '700123456',
                    'shortcode' => '18923',
                    'emergency' => '112',
                ),
            ),
        ),
        'RU' => array(
            'code' => '7',
            'patterns' => array(
                'example' => array(
                    'fixed' => '3011234567',
                    'mobile' => '9123456789',
                    'tollfree' => '8001234567',
                    'premium' => '8091234567',
                    'emergency' => '112',
                ),
            ),
        ),
        'RW' => array(
            'code' => '250',
            'patterns' => array(
                'example' => array(
                    'fixed' => '250123456',
                    'mobile' => '720123456',
                    'tollfree' => '800123456',
                    'premium' => '900123456',
                    'emergency' => '112',
                ),
            ),
        ),
        'SA' => array(
            'code' => '966',
            'patterns' => array(
                'example' => array(
                    'fixed' => '12345678',
                    'mobile' => '512345678',
                    'tollfree' => '8001234567',
                    'uan' => '920012345',
                    'shortcode' => '902',
                    'emergency' => '999',
                ),
            ),
        ),
        'SB' => array(
            'code' => '677',
            'patterns' => array(
                'example' => array(
                    'fixed' => '40123',
                    'mobile' => '7421234',
                    'tollfree' => '18123',
                    'voip' => '51123',
                    'shortcode' => '100',
                    'emergency' => '999',
                ),
            ),
        ),
        'SC' => array(
            'code' => '248',
            'patterns' => array(
                'example' => array(
                    'fixed' => '4217123',
                    'mobile' => '2510123',
                    'tollfree' => '800000',
                    'premium' => '981234',
                    'voip' => '6412345',
                    'shortcode' => '100',
                    'emergency' => '999',
                ),
            ),
        ),
        'SD' => array(
            'code' => '249',
            'patterns' => array(
                'example' => array(
                    'fixed' => '121231234',
                    'mobile' => '911231234',
                    'emergency' => '999',
                ),
            ),
        ),
        'SE' => array(
            'code' => '46',
            'patterns' => array(
                'example' => array(
                    'fixed' => '8123456',
                    'mobile' => '701234567',
                    'pager' => '741234567',
                    'tollfree' => '201234567',
                    'premium' => '9001234567',
                    'shared' => '771234567',
                    'personal' => '751234567',
                    'emergency' => '112',
                ),
            ),
        ),
        'SG' => array(
            'code' => '65',
            'patterns' => array(
                'example' => array(
                    'fixed' => '61234567',
                    'mobile' => '81234567',
                    'tollfree' => '18001234567',
                    'premium' => '19001234567',
                    'voip' => '31234567',
                    'uan' => '70001234567',
                    'shortcode' => '1312',
                    'emergency' => '999',
                ),
            ),
        ),
        'SH' => array(
            'code' => '290',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2158',
                    'premium' => '5012',
                    'shortcode' => '1234',
                    'emergency' => '999',
                ),
            ),
        ),
        'SI' => array(
            'code' => '386',
            'patterns' => array(
                'example' => array(
                    'fixed' => '11234567',
                    'mobile' => '31234567',
                    'tollfree' => '80123456',
                    'premium' => '90123456',
                    'voip' => '59012345',
                    'emergency' => '112',
                ),
            ),
        ),
        'SJ' => array(
            'code' => '47',
            'patterns' => array(
                'example' => array(
                    'fixed' => '79123456',
                    'mobile' => '41234567',
                    'tollfree' => '80012345',
                    'premium' => '82012345',
                    'shared' => '81021234',
                    'personal' => '88012345',
                    'voip' => '85012345',
                    'uan' => '01234',
                    'voicemail' => '81212345',
                    'emergency' => '112',
                ),
            ),
        ),
        'SK' => array(
            'code' => '421',
            'patterns' => array(
                'example' => array(
                    'fixed' => '212345678',
                    'mobile' => '912123456',
                    'tollfree' => '800123456',
                    'premium' => '900123456',
                    'shared' => '850123456',
                    'voip' => '690123456',
                    'uan' => '961234567',
                    'emergency' => '112',
                ),
            ),
        ),
        'SL' => array(
            'code' => '232',
            'patterns' => array(
                'example' => array(
                    'fixed' => '22221234',
                    'mobile' => '25123456',
                    'emergency' => '999',
                ),
            ),
        ),
        'SM' => array(
            'code' => '378',
            'patterns' => array(
                'example' => array(
                    'fixed' => '0549886377',
                    'mobile' => '66661212',
                    'premium' => '71123456',
                    'voip' => '58001110',
                    'emergency' => '113',
                ),
            ),
        ),
        'SN' => array(
            'code' => '221',
            'patterns' => array(
                'example' => array(
                    'fixed' => '301012345',
                    'mobile' => '701012345',
                    'voip' => '333011234',
                ),
            ),
        ),
        'SO' => array(
            'code' => '252',
            'patterns' => array(
                'example' => array(
                    'fixed' => '5522010',
                    'mobile' => '90792024',
                ),
            ),
        ),
        'SR' => array(
            'code' => '597',
            'patterns' => array(
                'example' => array(
                    'fixed' => '211234',
                    'mobile' => '7412345',
                    'voip' => '561234',
                    'shortcode' => '1234',
                    'emergency' => '115',
                ),
            ),
        ),
        'SS' => array(
            'code' => '211',
            'patterns' => array(
                'example' => array(
                    'fixed' => '181234567',
                    'mobile' => '977123456',
                ),
            ),
        ),
        'ST' => array(
            'code' => '239',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2221234',
                    'mobile' => '9812345',
                    'emergency' => '112',
                ),
            ),
        ),
        'SV' => array(
            'code' => '503',
            'patterns' => array(
                'example' => array(
                    'fixed' => '21234567',
                    'mobile' => '70123456',
                    'tollfree' => '8001234',
                    'premium' => '9001234',
                    'emergency' => '911',
                ),
            ),
        ),
        'SX' => array(
            'code' => '1',
            'patterns' => array(
                'example' => array(
                    'fixed' => '7215425678',
                    'mobile' => '7215205678',
                    'tollfree' => '8002123456',
                    'premium' => '9002123456',
                    'personal' => '5002345678',
                    'emergency' => '919',
                ),
            ),
        ),
        'SY' => array(
            'code' => '963',
            'patterns' => array(
                'example' => array(
                    'fixed' => '112345678',
                    'mobile' => '944567890',
                    'emergency' => '112',
                ),
            ),
        ),
        'SZ' => array(
            'code' => '268',
            'patterns' => array(
                'example' => array(
                    'fixed' => '22171234',
                    'mobile' => '76123456',
                    'tollfree' => '08001234',
                    'emergency' => '999',
                ),
            ),
        ),
        'TC' => array(
            'code' => '1',
            'patterns' => array(
                'example' => array(
                    'fixed' => '6497121234',
                    'mobile' => '6492311234',
                    'tollfree' => '8002345678',
                    'premium' => '9002345678',
                    'personal' => '5002345678',
                    'voip' => '6497101234',
                    'emergency' => '911',
                ),
            ),
        ),
        'TD' => array(
            'code' => '235',
            'patterns' => array(
                'example' => array(
                    'fixed' => '22501234',
                    'mobile' => '63012345',
                    'emergency' => '17',
                ),
            ),
        ),
        'TG' => array(
            'code' => '228',
            'patterns' => array(
                'example' => array(
                    'fixed' => '22212345',
                    'mobile' => '90112345',
                    'emergency' => '117',
                ),
            ),
        ),
        'TH' => array(
            'code' => '66',
            'patterns' => array(
                'example' => array(
                    'fixed' => '21234567',
                    'mobile' => '812345678',
                    'tollfree' => '1800123456',
                    'premium' => '1900123456',
                    'voip' => '601234567',
                    'uan' => '1100',
                    'emergency' => '191',
                ),
            ),
        ),
        'TJ' => array(
            'code' => '992',
            'patterns' => array(
                'example' => array(
                    'fixed' => '372123456',
                    'mobile' => '917123456',
                    'emergency' => '112',
                ),
            ),
        ),
        'TK' => array(
            'code' => '690',
            'patterns' => array(
                'example' => array(
                    'fixed' => '3010',
                    'mobile' => '5190',
                ),
            ),
        ),
        'TL' => array(
            'code' => '670',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2112345',
                    'mobile' => '77212345',
                    'tollfree' => '8012345',
                    'premium' => '9012345',
                    'personal' => '7012345',
                    'shortcode' => '102',
                    'emergency' => '112',
                ),
            ),
        ),
        'TM' => array(
            'code' => '993',
            'patterns' => array(
                'example' => array(
                    'fixed' => '12345678',
                    'mobile' => '66123456',
                    'emergency' => '03',
                ),
            ),
        ),
        'TN' => array(
            'code' => '216',
            'patterns' => array(
                'example' => array(
                    'fixed' => '71234567',
                    'mobile' => '20123456',
                    'premium' => '80123456',
                    'emergency' => '197',
                ),
            ),
        ),
        'TO' => array(
            'code' => '676',
            'patterns' => array(
                'example' => array(
                    'fixed' => '20123',
                    'mobile' => '7715123',
                    'tollfree' => '0800222',
                    'emergency' => '911',
                ),
            ),
        ),
        'TR' => array(
            'code' => '90',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2123456789',
                    'mobile' => '5012345678',
                    'pager' => '5123456789',
                    'tollfree' => '8001234567',
                    'premium' => '9001234567',
                    'uan' => '4441444',
                    'emergency' => '112',
                ),
            ),
        ),
        'TT' => array(
            'code' => '1',
            'patterns' => array(
                'example' => array(
                    'fixed' => '8682211234',
                    'mobile' => '8682911234',
                    'tollfree' => '8002345678',
                    'premium' => '9002345678',
                    'personal' => '5002345678',
                    'emergency' => '999',
                ),
            ),
        ),
        'TV' => array(
            'code' => '688',
            'patterns' => array(
                'example' => array(
                    'fixed' => '20123',
                    'mobile' => '901234',
                    'emergency' => '911',
                ),
            ),
        ),
        'TW' => array(
            'code' => '886',
            'patterns' => array(
                'example' => array(
                    'fixed' => '21234567',
                    'mobile' => '912345678',
                    'tollfree' => '800123456',
                    'premium' => '900123456',
                    'emergency' => '110',
                ),
            ),
        ),
        'TZ' => array(
            'code' => '255',
            'patterns' => array(
                'example' => array(
                    'fixed' => '222345678',
                    'mobile' => '612345678',
                    'tollfree' => '800123456',
                    'premium' => '900123456',
                    'shared' => '840123456',
                    'voip' => '412345678',
                    'emergency' => '111',
                ),
            ),
        ),
        'UA' => array(
            'code' => '380',
            'patterns' => array(
                'example' => array(
                    'fixed' => '311234567',
                    'mobile' => '391234567',
                    'tollfree' => '800123456',
                    'premium' => '900123456',
                    'emergency' => '112',
                ),
            ),
        ),
        'UG' => array(
            'code' => '256',
            'patterns' => array(
                'example' => array(
                    'fixed' => '312345678',
                    'mobile' => '712345678',
                    'tollfree' => '800123456',
                    'premium' => '901123456',
                    'emergency' => '999',
                ),
            ),
        ),
        'US' => array(
            'code' => '1',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2015550123',
                    'mobile' => '2015550123',
                    'tollfree' => '8002345678',
                    'premium' => '9002345678',
                    'personal' => '5002345678',
                    'emergency' => '911',
                ),
            ),
        ),
        'UY' => array(
            'code' => '598',
            'patterns' => array(
                'example' => array(
                    'fixed' => '21231234',
                    'mobile' => '94231234',
                    'tollfree' => '8001234',
                    'premium' => '9001234',
                    'shortcode' => '104',
                    'emergency' => '911',
                ),
            ),
        ),
        'UZ' => array(
            'code' => '998',
            'patterns' => array(
                'example' => array(
                    'fixed' => '662345678',
                    'mobile' => '912345678',
                    'emergency' => '01',
                ),
            ),
        ),
        'VA' => array(
            'code' => '379',
            'patterns' => array(
                'example' => array(
                    'fixed' => '0669812345',
                    'emergency' => '113',
                ),
            ),
        ),
        'VC' => array(
            'code' => '1',
            'patterns' => array(
                'example' => array(
                    'fixed' => '7842661234',
                    'mobile' => '7844301234',
                    'tollfree' => '8002345678',
                    'premium' => '9002345678',
                    'personal' => '5002345678',
                    'emergency' => '911',
                ),
            ),
        ),
        'VE' => array(
            'code' => '58',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2121234567',
                    'mobile' => '4121234567',
                    'tollfree' => '8001234567',
                    'premium' => '9001234567',
                    'emergency' => '171',
                ),
            ),
        ),
        'VG' => array(
            'code' => '1',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2842291234',
                    'mobile' => '2843001234',
                    'tollfree' => '8002345678',
                    'premium' => '9002345678',
                    'personal' => '5002345678',
                    'emergency' => '911',
                ),
            ),
        ),
        'VI' => array(
            'code' => '1',
            'patterns' => array(
                'example' => array(
                    'fixed' => '3406421234',
                    'mobile' => '3406421234',
                    'tollfree' => '8002345678',
                    'premium' => '9002345678',
                    'personal' => '5002345678',
                    'emergency' => '911',
                ),
            ),
        ),
        'VN' => array(
            'code' => '84',
            'patterns' => array(
                'example' => array(
                    'fixed' => '2101234567',
                    'mobile' => '912345678',
                    'tollfree' => '1800123456',
                    'premium' => '1900123456',
                    'uan' => '1992000',
                    'emergency' => '113',
                ),
            ),
        ),
        'VU' => array(
            'code' => '678',
            'patterns' => array(
                'example' => array(
                    'fixed' => '22123',
                    'mobile' => '5912345',
                    'uan' => '30123',
                    'emergency' => '112',
                ),
            ),
        ),
        'WF' => array(
            'code' => '681',
            'patterns' => array(
                'example' => array(
                    'fixed' => '501234',
                    'mobile' => '501234',
                    'emergency' => '15',
                ),
            ),
        ),
        'WS' => array(
            'code' => '685',
            'patterns' => array(
                'example' => array(
                    'fixed' => '22123',
                    'mobile' => '601234',
                    'tollfree' => '800123',
                    'emergency' => '994',
                ),
            ),
        ),
        'YE' => array(
            'code' => '967',
            'patterns' => array(
                'example' => array(
                    'fixed' => '1234567',
                    'mobile' => '712345678',
                    'emergency' => '191',
                ),
            ),
        ),
        'YT' => array(
            'code' => '262',
            'patterns' => array(
                'example' => array(
                    'fixed' => '269601234',
                    'mobile' => '639123456',
                    'tollfree' => '801234567',
                    'emergency' => '15',
                ),
            ),
        ),
        'ZA' => array(
            'code' => '27',
            'patterns' => array(
                'example' => array(
                    'fixed' => '101234567',
                    'mobile' => '711234567',
                    'tollfree' => '801234567',
                    'premium' => '862345678',
                    'shared' => '860123456',
                    'voip' => '871234567',
                    'uan' => '861123456',
                    'emergency' => '10111',
                ),
            ),
        ),
        'ZM' => array(
            'code' => '260',
            'patterns' => array(
                'example' => array(
                    'fixed' => '211234567',
                    'mobile' => '955123456',
                    'tollfree' => '800123456',
                    'emergency' => '999',
                ),
            ),
        ),
        'ZW' => array(
            'code' => '263',
            'patterns' => array(
                'example' => array(
                    'fixed' => '1312345',
                    'mobile' => '711234567',
                    'voip' => '8686123456',
                    'emergency' => '999',
                ),
            ),
        ),
    );

    public function setUp()
    {
        $this->validator = new PhoneNumber();
    }

    public function testExampleNumbers()
    {
        foreach ($this->phone as $country => $parameters) {
            $this->validator->setCountry($country);
            foreach ($parameters['patterns']['example'] as $type => $value) {
                $this->validator->allowedTypes(array($type));
                $this->assertTrue($this->validator->isValid($value));
                // check with country code:
                $value = $parameters['code'] . $value;
                $this->assertTrue($this->validator->isValid($value));
            }
        }
    }

    public function testExampleNumbersAgainstPossible()
    {
        $this->validator->allowPossible(true);
        foreach ($this->phone as $country => $parameters) {
            $this->validator->setCountry($country);
            foreach ($parameters['patterns']['example'] as $type => $value) {
                $this->validator->allowedTypes(array($type));
                $this->assertTrue($this->validator->isValid($value));
                // check with country code:
                $value = $parameters['code'] . $value;
                $this->assertTrue($this->validator->isValid($value));
            }
        }
    }

    public function testAllowPossibleSetterGetter()
    {
        $this->assertFalse($this->validator->allowPossible());
        $this->validator->allowPossible(true);
        $this->assertTrue($this->validator->allowPossible());
    }

    public function testInvalidTypes()
    {
        $values = array(
            array(),
            new \stdClass,
        );

        foreach ($values as $value) {
            $this->assertFalse($this->validator->isValid($value));
        }
    }
}
