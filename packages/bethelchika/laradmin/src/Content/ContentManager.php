<?php
namespace BethelChika\Laradmin\Content;

use BethelChika\Laradmin\Source;

class ContentManager
{
    /**
     *
     * 
     */

    /**
     * A holistic var for any asset string allowing the programmer to include whatever as asset.
     *
     * @var array
     */
    private static $contents = [];

    /**
     * Predefined stacks.
     *
     * @var array
     */
    private static $stacks = [
        'sidebar-top',
        'sidebar-bottom',
        'rightbar-top',
        'rightbar-bottom',
        'mainbar-top',
        'mainbar-bottom',
        'footer-left',//TODO:
        'footer-right',//TODO:
        'footer-center',//TODO:
        'footer-top',//TODO:
        'footer-bottom',//TODO:
        'meta',
    ];

    /**
     * A two element array where [0] is for the name for a current sub application and [1] is for the corresponding url
     *
     * @var string
     */
    private static $subAppName = ['User manager', '/u/profile'];

    /**
     * Predefined admin stacks.TODO: to be used to implement admin stuff
     *
     * @var array
     */
    private static $adminStacks = [
        'admin-tool-bar',
    ];


    

    /**
     * Defines if a page has sidebar or not
     *
     * @var boolean
     */
    public static $hasSidebar = false;

    /**
     * Adds an stack
     *
     * @param string $string The asset eg. <script ...>, <link ...>, <style ...>
     * @param string $stack The stacks (from predefined ones) on the page where the content should be placed
     * @return string
     */
    public static function registerStack($stack, $tag = null, $string)
    {
        if ($tag) {
            self::$contents[$stack][$tag] = ['content' => $string];
        } else {
            self::$contents[$stack][] = ['content' => $string];
        }

    }

    /**
     * Returns contents for a specified stack
     *
     * @param string $stack
     * @return string
     */
    public static function getStackContents($stack)
    {
        $strings = '';
        if (array_key_exists($stack, self::$contents)) {
            foreach (self::$contents[$stack] as $tag) {
                $strings .= $tag['content'];
            }
        }

        return $strings;
    }

    /**
     * Returns a specified content. Also useful for when you want to load from none predefined stacks as those are not loaded automatically
     *
     * @param string $stack
     * @param string $tag
     * @return strings
     */
    public static function getStackContent($stack, $tag)
    {
        return self::$contents[$stack][$tag]['content'];

    }


    /**
     * Returns predefined stacks
     *
     * @return array
     */
    public static function getStacks()
    {
        return self::$stacks;
    }

    /**
     * Insert a string for the sub app name
     *
     * @param string $string
     * @param string $url The Url of the sub app
     * @return void
     */
    public static function registerSubAppName($string, $url)
    {
        self::$subAppName[0] = $string;
        self::$subAppName[1] = $url;
    }

    /**
     * Has the sub app name been set?
     *
     * @return boolean
     */
    public static function hasSubAppName()
    {
        return self::$subAppName[0] ? true : false;
    }
    /**
     * Gets the sub app url
     * @param $url The url to return if none is set
     * @return string
     */
    public static function getSubAppUrl($url)
    {
        if (self::$subAppName[1]) {
            return self::$subAppName[1];
        } else {
            return $url;
        }
    }
    /**
     * Gets the sub app name
     *
     * @return string
     */
    public static function getSubAppName()
    {
        return self::$subAppName[0];
    }

    

    /**
     * Sets the state of the side bar
     *@param $state The state of the sidebar {true=sidebar is on, false=sidebar is off}
     * @return boolean State of the sidebar
     */
    public static function sidebar($state = true)
    {
        $assetmanager=app('laradmin')->assetManager;

        if ($state) {
            self::$hasSidebar = true;
            
            $assetmanager->registerBodyClass('has-sidebar');
            //$assetmanager->setContainerType('fluid',true);
        } else {
            self::$hasSidebar = false;
            $assetmanager->unRegisterBodyClass('has-sidebar');
            //$assetmanager->setContainerType('static',true);//This is not right because we cannot tell the vale of the containerType has not been chnaged by other scripts. 
        }

        return self::$hasSidebar;
    }

    /**
     * Gets the sidebar state i.e what sidebar is on or off. Returen
     * @param mixed $ontrue Variable to return if sidebar is present
     * @param mixed $onfalse Variable to return if there is no sidebar
     * @return boolean|$ontrue|$onfalse
     */
    public static function hasSidebar($ontrue=null,$onfalse=null)
    {
        if($ontrue!==null and self::$hasSidebar){
            return $ontrue;
        }
        if($onfalse!==null and !self::$hasSidebar){
            return $onfalse;
        }
        return self::$hasSidebar;
    }
    /**
     * Loads menu for admin
     *
     * @return void
     */
    public function loadAdminMenu(){
        $navigation=app('laradmin')->navigation;
        
        $navigation->create('Settings','settings','admin.general',[
            'namedRoute'=>'cp-settings',
            'namedRouteParams'=>['cp_settings','general'],
            'iconClass'=>'fas fa-cog',
            ]);
        // $navigation->create('Backup','backup','admin.general',[
        //     'namedRoute'=>'cp-settings',
        //     'iconClass'=>'far fa-hdd',
        //     ]);

        // Source types
        $navigation->create('Types','types','admin.sources',[
            'namedRoute'=>'cp-source-types',
            'iconClass'=>'fas fa-hdd',
            ]);
        
        foreach(Source::$DEFAULT_TYPES as $type=>$label){
            //$type=strtolower($type);
            $route='cp-source-type';
            $route_params=[$type];
            if(in_array($type,['table','route','route_prefix','page'])){
                $route=$route.'-'.$type;
                $route_params=[];
            }
            $navigation->create($label,$type,'admin.sources.types',[
                'namedRoute'=>$route,'namedRouteParams'=>$route_params,
                'activateStartWith'=>true,
                ]);
        }

        //Post install
        $navigation->create('Post installation','post_installation','admin.general',[
            'namedRoute'=>'cp-post-install',
            'iconClass'=>'fas fa-file-export',
            ]);
    }

    /**
     * Loads a predefined menu given by name
     *
     * @param string $name
     * @return void
     */
    public function loadMenu($name){
        $navigation=app('laradmin')->navigation;

        switch($name){

            case 'user_settings':
                //Account
                $navigation->create('Dashboard','dashboard','user_settings',[
                    'url'=>route('user-home'),'iconClass'=>'fas fa-home','order'=>0.0,'comment'=>'Users dashboard with summary of activities.']);
                
                    $navigation->create('Account','account','user_settings',[
                    'namedRoute'=>'user-profile','iconClass'=>'fas fa-user','order'=>1.0,'comment'=>'Manage you account details however you want.']);
                // $navigation->create('Personal information','personal_information','user_settings.account',[
                //     'url'=>route('user-profile'),'urlFragment'=>'PD-personal-information','iconClass'=>'fas fa-info-circle']);
                // $navigation->create('Contact details','contact_details','user_settings.account',[
                //     'url'=>route('user-profile'),'urlFragment'=>'PD-contact-details','iconClass'=>'fas fa-phone']);
                // $navigation->create('Location','location','user_settings.account',[
                //     'url'=>route('user-profile'),'urlFragment'=>'PD-location','iconClass'=>'fas fa-home']);

                

                //Security
                $navigation->create('Security','security','user_settings',[
                    'namedRoute'=>'user-security','iconClass'=>'fas fa-user-lock','order'=>2.0,'comment'=>'Manage your account access and security with a strong password.']);
                $navigation->create('Password','password','user_settings.security',[
                    'namedRoute'=>'user-security','iconClass'=>'fas fa-lock']);
                $navigation->create('Security questions','security_questions','user_settings.security',[
                    'namedRoute'=>'user-security-questions','iconClass'=>'fas fa-lock','activateStartWith'=>true]);
                
                $navigation->create('Login attempts','attempts','user_settings.security',[
                    'namedRoute'=>'user-login-attempts','iconClass'=>'fas fa-lock']);

                //Billing
                $navigation->create('Billing','billing','user_settings',[
                    'namedRoute'=>'user-billing','iconClass'=>'fas fa-file-invoice','order'=>3.0,'comment'=>'Billing information']);
                $navigation->create('Payment methods','payment_methods','user_settings.billing',[
                    'namedRoute'=>'user-billing-methods','iconClass'=>'fas fa-file-invoice','activateStartWith'=>true,]);
                
                //Billing Arbitrary
                $navigation->create('One off-payment','arbitrary_payment','user_settings.billing',[
                    'namedRoute'=>'user-billing-pay-arb','iconClass'=>'fas fa-file-invoice','activateStartWith'=>true,]);
                
                //Billing subscription
                $navigation->create('Subscriptions','subscriptions','user_settings.billing',[
                    'namedRoute'=>'user-billing-subs','iconClass'=>'fas fa-wallet']);

                //Billing subscription plans
                $navigation->create('Plans','plans','user_settings.billing.subscriptions',[
                    'namedRoute'=>'user-billing-sub-plans','iconClass'=>'fas fa-tasks']);
                    
                
                //Billing Invoice
                $navigation->create('Invoices','invoices','user_settings.billing',[
                    'namedRoute'=>'user-billing-invoices','iconClass'=>'fas fa-file-invoice']);
                    

                //External accounts
                $navigation->create('External','external_accounts','user_settings',[
                    'namedRoute'=>'social-user-external' ,'iconClass'=>'fas fa-share','order'=>4.0,'comment'=>'Connect and manage social and email accounts linked to this account']);
                //$navigation->create('External accounts','external_accounts2','user_settings.external_accounts',[
                //    'namedRoute'=>'social-user-external','iconClass'=>'fas fa-external-link-alt']);
                $navigation->create('Social User accounts','social_user_accounts','user_settings.external_accounts',[
                    'namedRoute'=>'social-user','iconClass'=>'fas fa-share-alt']);
                $navigation->create('Emails addresses','emails','user_settings.external_accounts',[
                    'namedRoute'=>'social-user-link-email','iconClass'=>'far fa-envelope']);

                // Control
                $navigation->create('Control','account_control','user_settings',[
                    'namedRoute'=>'user-account-control' ,'iconClass'=>'fas fa-gamepad','order'=>4.1,'comment'=>'Temporarily or permanently disable your account']);
                break;
 
            case 'primary':
            default:
                die('Loading menu '. $name .' with ContentManager is not implemented yet');
        }

    }

}