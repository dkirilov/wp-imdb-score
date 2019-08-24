<?php

function_exists("do_action") or die('Wrong place dude!');


if(!class_exists('WP_IMDB_Score_Validator')){

   class WP_IMDB_Score_Validator{

       public static function is_api_key($string){
          $regex = '/[a-zA-Z0-9]+/m';

          return ( strlen($string) == 8 ) && self::regex_matches($regex, $string);
       }

       public static function is_imdb_icon_url($string){
          $regex = '/^(https|http){1}:\/\/.+(png|jpg|jpeg|gif)$/m';
 
          return self::regex_matches($regex, $string);
       }

       public static function is_icon_size($string){
          $regex = '/^[0-9]+(px){1}$/m';
 
          return self::regex_matches($regex, $string);
       }

       public static function is_cache_lasts($string){
          $regex = '/^[0-9]+(d|D|h|H|m|M|s|S){1}$/m';
 
          return self::regex_matches($regex, $string);
       }

       public static function regex_matches($regex, $string){
          $return = preg_match_all($regex, $string);

          return ( $return !== FALSE ) && ( $return > 0 ) ;       
       }

   }

}

?>
