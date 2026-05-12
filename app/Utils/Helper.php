<?php

namespace App\Utils;

use App\Models\Country;
use App\Modules\v0\Customers\Models\Customer;
use App\Models\PaymentChannel;
use App\Models\PaymentMethod;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Propaganistas\LaravelPhone\PhoneNumber;
use SimpleXMLElement;
use function Sentry\captureException;

class Helper
{
    /**
     * check if item is null or empty string
     *
     * @param mixed $str
     * @return bool
     */
    public static function IsNullOrEmptyString($str): bool
    {
        if (!isset($str) || trim($str) === '') {
            return false;
        }

        return true;
    }

    /**
     * format phone numbers
     *
     * @param string $country_code
     * @param string $phone
     * @return string
     */
    public static function format_phone(string $country_code, string $phone): string
    {
        $countries = config('countries');

        $dial_code = '';
        foreach ($countries as $country) {
            if ($country['code'] == $country_code) {
                $dial_code = $country['dial_code'];
                break;
            }
        }

        if ($phone[0] == '0') {
            $phone = substr($phone, 1, strlen($phone));
        }

        return $dial_code . '' . $phone;
    }

    /**
     * get dial code
     *
     * @param string $country_code
     * @return string|null
     */
    public static function get_dial_code(string $country_code) : ?string
    {
        $countries = config('countries');

        $dial_code = null;
        foreach ($countries as $country) {
            if ($country['code'] == $country_code) {
                $dial_code = $country['dial_code'];
                break;
            }
        }

        return $dial_code;
    }

    /**
     * parse a phone number
     *
     * @param string $number
     * @param string $country
     * @return string|null
     * @throws NumberParseException
     */
    public static function parse_phone(string $number, string $country) : ?string
    {
        try{
            if(strlen($number) > 1 && $number[0] === '0'){
                $number = substr($number, 1, strlen($number));
            }

            $phone_util = PhoneNumberUtil::getInstance();
            $phone_number = $phone_util->parse($number, $country);

            return $phone_util->format($phone_number, PhoneNumberFormat::E164);
        } catch (NumberParseException $e) {
            self::logError($e, __CLASS__);
        }

        return null;
    }

    public static function parse_phone_number(string $number, string $country) : ?string
    {
        try{
       
            $phone =new PhoneNumber($number, $country);
            $phone->formatE164();
            return $phone;
        } catch (NumberParseException $e) {
            self::logError($e, __CLASS__);
        }

        return null;
    }

    /**
     * get active countries code
     *
     * @param bool $isSending true if sending country code
     * @return string
     */
    public static function active_countries(bool $isSending = true) : string
    {
        $countries = '';
        $dbCountries = Country::when($isSending,function($query, $isSending){
                return $query->where('is_sending', $isSending);
                })
                ->active()
                ->select('code')
                ->get();
        foreach($dbCountries as $country){
            if(empty($countries)){
                $countries = $country['code'];
            } else {
                $countries = $countries.','.$country['code'];
            }
        }

        return $countries;
    }

    /**
     * get customer
     *
     * @param int $id
     * @return Customer|null
     */
    public static function get_customer_by_auth(string $id): ?Customer
    {
        return Customer::whereKey($id)->first();
    }

    /**
     * generate transaction code
     * @param string $curency
     * @param string $rec
     * @return string
     */
    public static function generate_transaction_code(string $currency = 'GH', string $type = 'Individual') : string
    {
        //TODO: Move to the transaction model and generate directly from there
        $type = Str::upper($type[0]);
        $country = self::findCountry($currency);
        $uuid = self::convertMD5HashToInteger();
        return "FP{$country}{$type}{$uuid}";
    }


    /**
     * find the country using the currency code
     *
     * @param string $currency
     * @return string
     */
    public static function findCountry(string $currency) : string
    {
        $countries = config('currency');
        foreach($countries as $country){
            if($country['currencyCode'] === $currency){
                return $country['countryCode'];
            }
        }
        return 'GH';
    }


    /**
     * get  integer val
     * @return string
     * @throws Exception
     */
    public static function convertMD5HashToInteger() : string
    {
        $arr = str_split(md5(Str::uuid()),4);
        foreach($arr as $grp){
            $dec[] = str_pad(hexdec($grp),5, '0',STR_PAD_LEFT);
        }

        $str_val =  implode('',$dec);
        //randomly pick 6 characters
        $len = strlen($str_val) -1;
        $picked = '';
        for($i = 0; $i <= 6; $i++){
            $to_pick = random_int(0,$len);
            $picked .= $str_val[$to_pick];
        }

        return $picked;
    }

    public static function encode_image($path) : array
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $file = file_get_contents($path);

        $base64 = base64_encode($path);

        return [
            'base64'=> $base64,
            'type'=> $type
        ];
    }

     /**
     * encode images
     *
     * @param array $images
     * @return array
     */
    public static function getEncodeImages(array $images ) : array
    {
        $encodedImages = [];

        foreach($images as $image){
            //$encodedImage = Helper::encode_image($image);
            array_push($encodedImages,[
                'format'=> 'jpg',
                'base_64_data'=> $image
            ]);
        }

        return $encodedImages;
    }

    /**
     * check if there is internet connection
     *
     * @return bool
     */
    public static function is_connected() : bool
    {
        $connected = @fsockopen('www.google.com',80);
        if($connected){
            //action when connected
            fclose($connected);
            return true;
        }

        return false;
    }

    /**
     * generate referral code
     *
     * @param string $char1
     * @param string $char2
     * @return string
     */
    public static function generate_referral_code(string $char1, string $char2) : string
    {
        return "{$char1}{$char2}0".Str::random(4);
    }

    /**
     * replace first character of phone number
     *
     * @param string $number
     * @return string
     */
    public static function formatPhoneNumber(string $number) : string
    {
        if((int)$number[0] != 0){
            return "0{$number}";
        }

        return $number;
    }


    /**
     * parse the settlement date
     *
     * @param string $settlementType
     * @param int $daysToSettle
     * @return string
     */
    public static function parseSettlementDate(string $settlementType, int $daysToSettle = 0) : string
    {
        $date = '';
        $today = now();
        switch ($settlementType) {
            case 'daily':
                $date = self::parseDate($today);
                break;
            case 'weekly':
                $date = self::parseDate($today->addDays(7));
                break;
            case 'monthly':
                $date = self::parseDate($today->addMonths(1));
                break;
            case 'quarterly':
                $date = self::parseDate($today->addMonths(3));
                break;
            case 'custom':
                $date = self::parseDate($today, $daysToSettle);
                break;
            default:
                $date = self::parseDate($today,2); // default is T+2
                break;
        }

        return $date;
    }

    /**
     * parse the date
     *
     * @param Carbon $date
     * @param int $numOfDays
     * @return Carbon
     */
    public static function parseDate(Carbon $date, int $numOfDays = 0) : string
    {
        if($numOfDays > 0){
            $date = $date->addDays($numOfDays);
        }

        $nonWeekend = self::checkWeekend($date);
        $pastDate = self::isPastEndOfDay($nonWeekend);


        return $pastDate->toDateString(). ' 09:30:00';
    }

    /**
     * check if date is a weekend and move to weekdays
     *
     * @param Carbon $date
     * @return Carbon
     */
    public static  function checkWeekend(Carbon $date) : Carbon
    {
        while($date->isWeekend()){
            $date->addDay();
        }

        return $date;
    }

    /**
     * check if the date is passed end of day or not
     * and moves to next day if it is
     *
     * @param Carbon $date
     * @return Carbon
     */
    public static  function isPastEndOfDay(Carbon $date) :Carbon
    {
        $start = $date->createFromTimeString('08:00:00');
        $end = $date->createFromTimeString('17:00:00');

        if(!$date->between($start,$end)){
            $date->addDay();
            while($date->isWeekend()){
                $date->addDay();
            }
        }

        return $date;
    }

    	/**
	* mask account number
	* @param $account account number to mask
	* @return string
	*/
	public static  function maskAccount(string $account) : string
	{
		return str_repeat('*', strlen($account) - 4) . substr($account, -4);
	}

    /**
     * convert  array to xml object
     *
     * @param SimpleXMLElement $object
     * @param array $data
     * @return void
     */
    public static function  to_xml(SimpleXMLElement $object, array $data)
    {
        foreach($data as $key => $value){
            if(is_array($value)){
                $new_object = $object->addChild($key);
                self::to_xml($new_object, $value);
            } else {
                //if key is an integer then append key to it
                if($key != 0 && $key == (int) $key){
                    $key = 'key_'.$key;
                }

                $object->addChild($key, $value);
            }
        }
    }

    public static function from_xml_to_array(SimpleXMLElement $xml) : array
    {
        // foreach ( (array) $xmlObject as $index => $node )
        // $out[$index] = ( is_object ( $node ) ) ? xml2array ( $node ) : $node;

        $array = [];
        foreach($xml as $key => $value){
            if(is_object($value)){
                $array[$key] = self::from_xml_to_array($value);
            } else {
                $array[$key] = $value;
            }
        }
        return $array;
    }

    /**
     * get forbidden words
     *
     * @return array
     */
    public static function getForbiddenUsernames() : array
    {
        $usernames = config('utils.forbidden_usernames','');

        return explode(',',$usernames);
    }

    /**
     * log error to file and sentry and any other channel
     *
     * @param Exception $ex
     * @param string|null $_class
     * @return void
     */
    final public static function logError(Exception $ex, string $_class = null) : void
    {
        Log::error($_class ?? __CLASS__, [$ex]);
        captureException($ex);
    }

    /**
     * get payout method
     *
     * @param mixed $id
     * @return string|null
     */
    final public static function get_payout_type($id) : ?string
    {
        $channel = PaymentChannel::where('id',$id)->orWhere('code',$id)
                                ->select(['id','code','payment_method_id'])
                                ->with('paymentMethod:id,code,processor')
                                ->first();
        if(!$channel){
            return null;
        }

        $code = $channel->paymentMethod->code;
        switch($code){
            case str_contains($code,'bank'):
                return PaymentMethod::BANK;
            case str_contains($code,'mobile') || str_contains($code,'wallet') || str_contains($code,'money'):
                return PaymentMethod::MOBILE_MONEY;
            case str_contains($code, 'cash') || str_contains($code, 'pickup') || str_contains($code, 'otc'):
                return PaymentMethod::OVER_THE_COUNTER;
                default:
                    return null;
        }
    }

    /**
     * Clean account number by removing all symbols and spaces
     *
     * This method removes all non-alphanumeric characters from an account number,
     * including spaces, dashes, parentheses, slashes, underscores, and other symbols.
     *
     * @param string|int|null $accountNumber The account number to clean
     * @return string The cleaned account number, or empty string if input is null/empty
     *
     * @example
     * Helper::cleanAccountNumber('123-456-789');      // returns '123456789'
     * Helper::cleanAccountNumber('123 456 789');      // returns '123456789'
     * Helper::cleanAccountNumber('(123) 456-7890');   // returns '1234567890'
     * Helper::cleanAccountNumber('123/456/789');      // returns '123456789'
     * Helper::cleanAccountNumber('123_456_789');      // returns '123456789'
     */
    public static function cleanAccountNumber(string|int|null $accountNumber): string
    {
        if ($accountNumber === null || $accountNumber === '') {
            return '';
        }

        // Convert to string if it's an integer
        $accountNumber = (string) $accountNumber;

        // Remove all non-alphanumeric characters (symbols and spaces)
        // This removes: spaces, dashes, parentheses, slashes, underscores, dots, commas, etc.
        $cleaned = preg_replace('/[^a-zA-Z0-9]/', '', $accountNumber);

        return $cleaned;
    }
}
