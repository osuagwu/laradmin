<?php 
namespace BethelChika\IsThisFake;

use BethelChika\Laradmin\Plugin\Contracts\Plugable;

class IsthisFakePlugable implements Plugable{

    public $displayName;
    public $logo;
    public $summary;
    
    public function __construct(){
            
            $this->displayName='Is this Fake?';

            $this->logo='/vendor/isthisfake/img/logo.jpg';
            $this->summary='Battling fake news!';
        
    }

    public function getDisplayName(){
        return $this->displayName;
    }

    /**
     * Get Version of plugin
     *
     * @return string
     */
    public function getVersion()
    {
        return '1.1';

    }

    /**
     * Get Description test
     *
     * @return string
     */
    public function getDescription()
    {
        return 'None Description';
    }






    /**
     * The Icon of the pluagble
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Get the short description of the plugable
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }


    /**
     * Installation script
     *
     * @return boolean
     */
    public function install()
    {

    }

    /**
     * Unnstallation script
     *
     * @return boolean
     */
    public function uninstall()
    {

    }

    /**
     * Update script
     *
     * @return boolean
     */
    public function update()
    {

    }

    /**
     * Disable script
     *
     * @return boolean
     */
    public function disable()
    {

    }
     /**
     * Enale script
     *
     * @return boolean
     */
    public function enable()
    {

    }
  
}