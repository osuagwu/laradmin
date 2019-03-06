<?php
namespace BethelChika\Laradmin\Feed\Models;


use Illuminate\Database\Eloquent\Model;

class Feed extends Model{
    

    /**
     * The types that can be set for source icon type property
     *
     * @var array
     */
    private $sourceIconTypes=['image','html'];

    /**
     * The types that can be set for type property in table
     *
     * @var array
     */
    private static $types=['normal','info','success','warning','danger','subtle','primary',];
    /**
     * True for dynamic feed
     *
     * @var boolean
     */
    public $isDynamic=false;


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
    public function getTitleAttribute($value){
        return htmlspecialchars($value,ENT_QUOTES);
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function getContentAttribute($value){
        return htmlspecialchars($value,ENT_QUOTES);
    }

    /**
     * Get the short description of the Feed
     *
     * @return string
     */
    public function getSummaryAttribute($value){
        return htmlspecialchars($value,ENT_QUOTES);
    }

     /**
     * Get types
     *
     * @return array
     */
    public static function getTypes(){
        return self::$types;
    }

    /**
     * Get the date of the Feed
     *
     * @return integer timestamp
     */
    public function getDate(){
        //print(\Carbon\Carbon::now());
        $date['timestamp'] =null;
        $date['date'] =null;
        $date['timezone']=null;
        $data['timezone_type']=null;
        if($this->created_at){
            $jdate=json_decode(json_encode($this->created_at));
            $date['timestamp'] =$this->created_at->timestamp;
            $date['date'] =$jdate->date;
            $date['timezone']=$jdate->timezone;
            $data['timezone_type']=$jdate->timezone_type;
        }
        return $date;
    }



   
}