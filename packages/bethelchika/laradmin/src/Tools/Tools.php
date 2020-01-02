<?php
namespace BethelChika\Laradmin\Tools;

use Money\Money;
use Money\Currency;

// NOTE either depend on Cashier for the following of install:: composer require moneyphp/money
use NumberFormatter;
use Corcel\Model\Option;
use Illuminate\Support\Facades\Log;
use Money\Currencies\ISOCurrencies;


use Money\Formatter\IntlMoneyFormatter;

use Money\Formatter\DecimalMoneyFormatter;
use Money\Parser\IntlLocalizedDecimalParser;

class Tools
{
    /**
     * Used internally to catch the base path to avoid recomputing it.
     *
     * @var string
     */
    private static $BASE_PATH=null;


    /**
     * Internally stores recently address translated from ips. The ips are keys to the address.
     *
     * @var array
     */
    private static $RECENT_IP2ADDRESSES=[];
     



    /**
     * Return the base path of laradmin
     *
     * @return string
     */
    public static function basePath()
    {
        if(!self::$BASE_PATH){
            $reflector = new \ReflectionClass(\BethelChika\Laradmin\LaradminServiceProvider::class);
            self::$BASE_PATH= dirname(dirname($reflector->getFileName()));
        }
        return self::$BASE_PATH;
    }

    /**
     * Copy the Wordpress plugin
     *
     * @param boolean $force
     * @return integer The return value is -1=> if plugin is already installed and not reinstalled; 1=> for successfully installed and 0=>error.
     */
    public static function installWpPlugin($force = false)
    {
        if(!config('laradmin.wp_enable')){
            Log::error(__CLASS__.':'.__METHOD__.': msg=>WordPress is not enabled');
            return null;
        }
        $wp_plugins_rpath = '/wp-content/plugins'; //TODO: (perhaps set in config in future) CAUTION: We are assuming that wp plugin relative path is not changed from its default value.
        //$wp_tpls_rpath='/wp-contents/themes/'.trim(config('laradmin.wp_theme'),'\/').'/page_templates';//TODO: (perhaps set in config in future) CAUTION: We are assuming that wp theme relative path is not changed from its default value.


        $wp_plugins_path = (public_path() . '/' . trim(config('laradmin.wp_rpath'), '\/') . $wp_plugins_rpath);
        //$wp_tpls_path=(public_path().'/'.trim(config('laradmin.wp_rpath'),'\/').$wp_tpls_rpath);


        if (!$force and file_exists($wp_plugins_path . '/laradmin')) {
            return -1;
        } 
        else {
            try{
                
                self::rcopy(self::basePath() . '/wp_plugins/laradmin',  $wp_plugins_path.'/laradmin');
            }catch (\Exception $ex) {
                Log::error(__CLASS__.':'.__METHOD__.': msg=>'.$ex->getMessage());
                return 0;
            }

            return 1;
        }
    }

    /**
     * Create Wordpress templates
     *
     * @param boolean $force
     * @return integer The return value is -1=> if plugin is already installed and not reinstalled; 1=> for successfully installed and 0=>error.
     */
    public static function installWpTemplates($force = false)
    {
        if(!config('laradmin.wp_enable')){
            Log::error(__CLASS__.':'.__METHOD__.': msg=>WordPress is not enabled');
            return null;
        }
        $wp_theme=Option::get('template');
        if(!$wp_theme){
            Log::error(__CLASS__.':'.__METHOD__.': msg=>Could not obtain WordPress theme');
            return 0;
        }

        $wp_tpls_rpath = '/wp-content/themes/' . $wp_theme; //TODO: (perhaps set in config in future) CAUTION: We are assuming that wp theme relative path is not changed from its default value.


        $wp_tpls_path = (public_path() . '/' . trim(config('laradmin.wp_rpath'), '\/') . $wp_tpls_rpath);


        if (!$force and file_exists($wp_tpls_path . '/page_templates')) {
            return -1;
        } 
        else {

            // If the template folder is not created, make one.
            if (!file_exists($wp_tpls_path . '/page_templates')){
                mkdir($wp_tpls_path . '/page_templates');
            }
            
            $theme=config('laradmin.theme','default');

            $tpl_path = self::basePath() . '/resources/views/themes/'.$theme.'/wp/page_templates';
            try {
                foreach (scandir($tpl_path) as $tpl) {
                    
                    if(str_is(['.','..'],$tpl)){
                        continue;
                    }
                    $name = str_ireplace('.blade.php', '', $tpl);
                    $filename = $wp_tpls_path . '/page_templates/' . $tpl;
                    $content = '<?php
                    /**
                     * Template Name: '.ucfirst(str_replace('_',' ',$name)).'
                     *
                     * @package Laradmin
                     * @subpackage WP
                     * @since  1.0
                     */';
                     //dd($filename);
                    file_put_contents($filename, $content);
                    
                }
            } catch (\Exception $ex) {
                Log::error(__CLASS__.':'.__METHOD__.': msg=>'.$ex->getMessage());
                return 0;
            }

            return 1;
        }
    }

    /**
     * Copy a directory to a destination recursively
     *
     * @param string $src
     * @param string $dst
     * @return void
     */
    public static function rcopy($src,$dst) { //dd($src);
        $dir = opendir($src); 
        @mkdir($dst); 
        while(false !== ( $file = readdir($dir)) ) { 
            if (( $file != '.' ) && ( $file != '..' )) { 
                if ( is_dir($src . '/' . $file) ) { 
                    self::rcopy($src . '/' . $file,$dst . '/' . $file); 
                } 
                else { 
                    copy($src . '/' . $file,$dst . '/' . $file); 
                } 
            } 
        } 
        closedir($dir); 
    } 

    
    /**
     * Fetch content of a URL
     *
     * @param string $URL The URL to fetch
     * @return string
     */
    public static function urlGetContent($URL){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $URL);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
  }
  
  
  /**
   * Returns the address info in array
   *
   * @param string $ip IP address
   * @return array The array indexes include latitude,longitude, city_name, country_name,etc
   */
  public static function ip2Address($ip){

    //Could we return from recently fetched ones?
    if(array_key_exists($ip,self::$RECENT_IP2ADDRESSES)){
        return self::$RECENT_IP2ADDRESSES[$ip];
    }

    
    //$ip_database_path=//self::basePath().'/database/geoip2';
    
    $city_database=base_path().'/'.ltrim(config('laradmin.geoip.db_city_filename','\\/'));//$ip_database_path.'/GeoLite2-City.mmdb';
    //$country_database=$ip_database_path.'/GeoLite2-Country.mmdb';

    // This creates the Reader object, which should be reused across
    // lookups.
    $reader = new \GeoIp2\Database\Reader($city_database);

    // Replace "city" with the appropriate method for your database, e.g.,
    // "country".

    $record = $reader->city($ip);
    $address=[];

    $address['latitude']=$record->location->latitude;
    $address['longitude']=$record->location->longitude;
    $address['city_name']=$record->city->name; 
    $address['country_name']=$record->country->name;
    //$address['country_iso_code']=$record->country->isoCode; NOTE: OPEN THIS IF NEEDED


    // Save some recent address: TODO: Change this to proper catching so that it can be used across sessions.
    self::$RECENT_IP2ADDRESSES[$ip]=$address;
    if(count(self::$RECENT_IP2ADDRESSES)>3){
        array_shift(self::$RECENT_IP2ADDRESSES);
    }

    return $address;
    
  }


  /**
   * Mask a given email address
   *
   * @param string $email
   * @return string Masked email
   */
    public static function maskEmail($email){
        $em   = explode("@",$email);
        $name = $em[0];
        $len  = floor(strlen($name)/2);

        $len_=strlen($name)%2 +$len;

        return substr($name,0, $len) . str_repeat('*', $len_) . "@" . end($em);   
    }

    /**
     * Returns an assoc array of timezones
     *
     * @return array
     */
    public static function getTimezones(){
        $tzs_=\DateTimeZone::listIdentifiers(\DateTimeZone::ALL);//TODO: How can we localise this to other languages
        $tzs=[];
        foreach($tzs_ as $tz_){
            $tzs[$tz_]=$tz_;
        }
        return $tzs;
    }
 
    /**
     * Format the given amount into a displayable currency.
     *
     * @param  int  $amount
     * @param  string|null  $currency
     * @param string|null $currency_local The local of the currency.
     * @return string
     */
    public static function formatAmount($amount, $currency = null,$currency_local=null)
    {
        if (!$currency_local) {
            $currency_local=config('app.currency_locale', 'en');
        }

        if(!$currency){
            $currency=config('app.currency','GBP');
        }
        $currency=strtoupper($currency);

        $money = new Money($amount, new Currency($currency));

        $numberFormatter = new NumberFormatter($currency_local, NumberFormatter::CURRENCY);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, new ISOCurrencies());//CAUTION: The 'intl' that this depends on could lead to different results in different environments (http://moneyphp.org/en/stable/features/parsing.html)

        return $moneyFormatter->format($money);
    }


    /**
     * Parse a human friendly decimal money to the lowest subdivision.
     * Useful when trying to make calculation with amount entered by user.
     * Also useful for saving an amount entered by user to a database in an integer column.
     *
    * @param  int  $amount
     * @param  string|null  $currency
     * @param string|null $currency_local The local of the currency.
     * @return string
     */
    public static function parseDecimalAmount($amount,$currency=null,$currency_local=null){

        if (!$currency_local) {
            $currency_local=config('app.currency_locale', 'en');
        }

        if(!$currency){
            $currency=config('app.currency','GBP');
        }
        $currency=strtoupper($currency);


        $currencies = new ISOCurrencies();

        $numberFormatter = new \NumberFormatter($currency_local, \NumberFormatter::DECIMAL);
        $moneyParser = new IntlLocalizedDecimalParser($numberFormatter, $currencies);//CAUTION: The 'intl' that this depends on could lead to different results in different environments (http://moneyphp.org/en/stable/features/parsing.html)

        $money = $moneyParser->parse($amount, new Currency($currency));

        return $money->getAmount(); 
    }

      /**
     * Format the lowest subdivision into a human friendly decimal. The output is not localised.
     * Useful whe displaying an amount for a user to edit
     *
    * @param  int  $amount Amount in lowest subdivision of currency
     * @param  string|null  $currency
     * @return string
     */
    public static function decimalAmountForHumans($amount,$currency=null){

        if(!$currency){
            $currency=config('app.currency','GBP');
        }
        $currency=strtoupper($currency);

        $money = new Money($amount, new Currency($currency));
        $currencies = new ISOCurrencies();

        $moneyFormatter = new DecimalMoneyFormatter($currencies);

        return $moneyFormatter->format($money); 
    }
    
}
