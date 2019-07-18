<?php
namespace BethelChika\Laradmin\Tools;
class Color{
/**
     * Find the resulting colour by blending 2 colours
     * and setting an opacity level for the foreground colour.
     * 
     * @author J de Silva
     * @link http://www.gidnetwork.com/b-135.html
     * @param string $foreground Hexadecimal colour value of the foreground colour.
     * @param integer $opacity Opacity percentage (of foreground colour). A number between 0 and 100.
     * @param string $background Optional. Hexadecimal colour value of the background colour. Default is: <code>FFFFFF</code> aka white.
     * @return string Hexadecimal colour value which will be preceded by hash if the the $foreground is also preceded by hash. <code>false</code> on errors.
     */
    public static function  colorBlendByOpacity( $foreground, $opacity, $background=null )
    {
        static $colors_rgb=array(); // stores colour values already passed through the hexdec() functions below.
        
        $replace_count=0;
        $foreground=str_replace('#','',$foreground,$replace_count);
        
        

        if( is_null($background) ){
            $background = 'FFFFFF'; // default background.
        }else{
            $background=str_replace('#','',$background);
        }

            

        $pattern = '~^[a-f0-9]{6,6}$~i'; // accept only valid hexadecimal colour values.
        if( !@preg_match($pattern, $foreground)  or  !@preg_match($pattern, $background) )
        {
            trigger_error( "Invalid hexadecimal colour value(s) found", E_USER_WARNING );
            return false;
        }
            
        $opacity = intval( $opacity ); // validate opacity data/number.
        if( $opacity>100  || $opacity<0 )
        {
            trigger_error( "Opacity percentage error, valid numbers are between 0 - 100", E_USER_WARNING );
            return false;
        }

        if( $opacity==100 )    // $transparency == 0
            return strtoupper( $foreground );
        if( $opacity==0 )    // $transparency == 100
            return strtoupper( $background );
        // calculate $transparency value.
        $transparency = 100-$opacity;

        if( !isset($colors_rgb[$foreground]) )
        { // do this only ONCE per script, for each unique colour.
            $f = array(  'r'=>hexdec($foreground[0].$foreground[1]),
                        'g'=>hexdec($foreground[2].$foreground[3]),
                        'b'=>hexdec($foreground[4].$foreground[5])    );
            $colors_rgb[$foreground] = $f;
        }
        else
        { // if this function is used 100 times in a script, this block is run 99 times.  Efficient.
            $f = $colors_rgb[$foreground];
        }
        
        if( !isset($colors_rgb[$background]) )
        { // do this only ONCE per script, for each unique colour.
            $b = array(  'r'=>hexdec($background[0].$background[1]),
                        'g'=>hexdec($background[2].$background[3]),
                        'b'=>hexdec($background[4].$background[5])    );
            $colors_rgb[$background] = $b;
        }
        else
        { // if this FUNCTION is used 100 times in a SCRIPT, this block will run 99 times.  Efficient.
            $b = $colors_rgb[$background];
        }
        
        $add = array(    'r'=>( $b['r']-$f['r'] ) / 100,
                        'g'=>( $b['g']-$f['g'] ) / 100,
                        'b'=>( $b['b']-$f['b'] ) / 100    );
                        
        $f['r'] += intval( $add['r'] * $transparency );
        $f['g'] += intval( $add['g'] * $transparency );
        $f['b'] += intval( $add['b'] * $transparency );
        
        $precede_hash='';
        if($replace_count){
            $precede_hash='#';
        }
        return sprintf( '%s%02X%02X%02X',$precede_hash, $f['r'], $f['g'], $f['b'] );
    }

        /**
     * Lightens/darkens a given colour (hex format), returning the altered colour in hex format.7
     * Source: https://gist.github.com/stephenharris/5532899
     * @param str $hex Colour as hexadecimal (with or without hash);
     * @percent float $percent Decimal ( 0.2 = lighten by 20%(), -0.4 = darken by 40%() )
     * @return str Lightened/Darkend colour as hexadecimal (with hash);
     */
    public static function colorLuminance($hex, $percent)
    {

        // validate hex string

        $hex = preg_replace('/[^0-9a-f]/i', '', $hex);
        $new_hex = '#';

        if (strlen($hex) < 6) {
            $hex = $hex[0] + $hex[0] + $hex[1] + $hex[1] + $hex[2] + $hex[2];
        }

        // convert to decimal and change luminosity
        for ($i = 0; $i < 3; $i++) {
            $dec = hexdec(substr($hex, $i * 2, 2));
            $dec = min(max(0, $dec + $dec * $percent), 255);
            $new_hex .= str_pad(dechex($dec), 2, 0, STR_PAD_LEFT);
        }

        return $new_hex;
    }
}