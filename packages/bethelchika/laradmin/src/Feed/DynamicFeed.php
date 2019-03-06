<?php
namespace BethelChika\Laradmin\Feed;

class DynamicFeed{
    

    /**
     * The types that can be set for source icon type property
     *
     * @var array
     */
    private $sourceIconTypes=['image','html'];/**
    * The types that can be set for type property
    *
    * @var array
    */
   private static  $types=['normal','info','success','warning','danger','subtle','primary'];

   /**
    * The id of the feed
    *
    * @var string
    */
   public $id=null;

    /**
     * The type of feed. This is index $types property
     *
     * @var int
     */
    public $type=0;

    /**
     * The css class of the feed
     *
     * @var string
     */
    public $cssClass='';


        /**
     * True for dynamic feed
     *
     * @var boolean
     */
    public $isDynamic=true;

    // /**
    //  * The title . It should only be accessed through getter so that it can be well formatted and html cleaned
    //  *
    //  * @var string
    //  */
     private $title=null;

    /**
     * The content. It should only be accessed through getter so that it can be well formatted and html cleaned
     *
     * @var string
     */
    private $content=null;

    /**
     * The type of icon. Default is 'image' @see $sourceIconTypes for possible values
     *
     * @var string
     */
    public $sourceIconType=null;

    /**
     * The icon for the feed srouce
     *
     * @var string
     */
    public $sourceIcon=null;

    /**
     * image
     *
     * @var string
     */
    public $image=null;

    /**
     * The url for the source
     *
     * @var string
     */
    public $sourceUrl='#';

    /**
     * The link for the feed
     *
     * @var string
     */
    public $url=null;

    /**
     * The name of the feed source
     *
     * @var string
     */
    public $sourceName=null;

    /**
     * The summary of feed . It should only be accessed through getter so that it can be well formatted and html cleaned
     *
     * @var string
     */
    private $summary=null;


    /**
     * The url to a share page. The page should contain open graph details and other social sharing meta,This should ideally be the home page of the feed i.e = $this->url.
     * 
     * @var string
     */
    public $shareUrl=null;

    /**
     * HTML to be place somwhat above the feed
     *
     * @var string
     */
    public $beforeHtml=null;

    /**
     * The html to be placed after the feed
     *
     * @var string
     */
    public $afterHtml=null;

    /**
     * Twitter hash tags
     *
     * @var string
     */
    public $twitterHashtags='';
    /**
     * Twitter related screen names
     *
     * @var string
     */
    public $twitterScreenNames='';

    /**
     * The tweet via parameter
     *
     * @var string
     */
    public $twitterVia='';

    /**
     * The language of this feed
     *
     * @var string
     */
    public $lang=null;

    /**
     * Construct a new feed with minimum parameters
     *
     * @param string $title The title of the feed
     * @param string $content The main content of the feed
     * @param string $source_name The name of the feed source
     * @param string $summary The summary of the feed
     * @param boolean $show_once When =true=> a unique id is genarated which makes it impossible for a user to see the same item more than once in a session. When =>false // a random id is generated makes it possible for an item to be show to a user multiple times in a session
     */
    public function __construct($title,$content,$source_name,$summary=null,$show_once=true){
        $this->content=$content;
        $this->title=$title;
        $this->sourceName=$source_name;
        $this->summary=$summary;
        if($show_once){
            $this->id='df-'.$source_name.'-'.preg_replace("/[^a-zA-Z0-9]+/", "",$title);//unique id makes it impossible for a user to see the same item more than once in a session
        }else{
            $this->id='df-'.$source_name.'-'.rand().'-'.time();//Random id makes it possible for an item to be show to a user multiple times
        }

        $this->lang=app()->getLocale();
    }

    /**
     * Magically set inaccessible properties
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

   
     /**
     * Set the properties dynamically using the array key value pair where array key is te property to set
     *
     * @param array $array
     * @return void
     */
    function set(array $array) {

        foreach ($array as $propertyToSet => $value) {
           $this->$propertyToSet =$value;
            
        }
      }


    /**
     * Get display name of the Feed
     *
     * @return string
     */
    public function getTitle(){
        return htmlspecialchars($this->title,ENT_QUOTES);
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function getContent(){
        return htmlspecialchars($this->content,ENT_QUOTES);
    }




    /**
     * Get the short description of the Feed
     *
     * @return string
     */
    public function getSummary(){
        return htmlspecialchars($this->summary,ENT_QUOTES);
    }

     /**
     * Get types
     *
     * @return array
     */
    public static  function getTypes(){
        return self::$types;
    }

   
}