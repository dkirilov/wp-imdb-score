<?php

function_exists("do_action") or die('Wrong place dude!');


if(!class_exists('WP_IMDB_Score')){
   class WP_IMDB_Score{
      private const API_URL = "http://www.omdbapi.com/";
      private const CACHE_DIR = PLUGIN_DIR . "cache" . DIRECTORY_SEPARATOR;

      private $settings = null;
      private $req_params = array(
             'i' => '',
             'plot' => 'short',
             'r' => 'json'
      );

      public function __construct(){
        $this->init();
      }

      private function init(){
         $this->load_settings();
      }

      public static function activate(){
         self::set_default_settings();
      }

      public static function deactivate(){

      }

      public static function uninstall(){
         return self::delete_settings();
      }

      private static function set_default_settings(){
         foreach(self::get_default_settings() as $setting => $value){
             if(empty(get_option('wpimdbscore_'.$setting))){
                update_option('wpimdbscore_'.$setting, $value);
             }
         }
      }

      public static function get_default_settings(){
        return array( // Default settings. Will be applied when plugin activated for first time.
            'api_key' => "1066b476",
            'imdb_icon_url' =>  plugin_dir_url(__FILE__) ."img/imdb-icon.png",
            'display_imdb_icon' => true,
            'icon_width' => "30px",
            'icon_height' => "20px",
            'display_as_link' => true,
            'link_opens_in_new_tab' => true,
            'cache_results' => true,
            'cache_lasts' => "1d"
          );
      }

      private static function delete_settings(){
         $return = false;
         foreach(array_keys(self::get_default_settings()) as $key){
            $return = delete_option('wpimdbscore_'.$key);
         }

         return $return;
      }

      private function load_settings(){
         foreach(self::get_default_settings() as $key => $value){
             $new_setting = get_option('wpimdbscore_'.$key);
             $this->settings[$key] = $new_setting;
         }
      }

      public function get_score_html($imdb_id){
          $outp = "";

          if(empty($this->settings['api_key'])){
             if(current_user_can('manage_options')){
               $outp .= "<b>WP_IMDB_Score:</b> No API Key! Please enter a valid OMDb API Key in plugin's settings page."; 
             }
             return $outp;
          }

          $score = $this->get_cached_score($imdb_id);
          $imdb_icon = $this->settings["display_imdb_icon"]?("<img src='" . $this->settings["imdb_icon_url"] . "' style='width:".$this->settings["icon_width"]."; height:".$this->settings["icon_height"].";'/>&nbsp;"):"";

          if($this->settings["display_as_link"]){
            $blank = $this->settings["link_opens_in_new_tab"]?"target='_blank'":"";

            $outp .= "<a href='https://www.imdb.com/title/$imdb_id' $blank>$imdb_icon<b>$score</b>/10</a>";
          }else{
            $outp .= "$imdb_icon<b>$score</b>/10";
          }

          return "<span class='wp_imdb_score'>$outp</span>";
      }

      private function cache_score($imdb_id, $score){
         $cache_contents = array(
            "score" => $score,
            "expires" => (time() + self::str_to_timestamp($this->settings['cache_lasts']))
         );
         $cache_fn = self::CACHE_DIR . $imdb_id . ".cache";

         if( file_put_contents($cache_fn, serialize($cache_contents)) === FALSE ){
             throw new \Exception("Caching the score for <b>$imdb_id</b> has failed.");
         }
      } 

      public function get_cached_score($imdb_id){
         $cache_fn = self::CACHE_DIR . $imdb_id . ".cache";

         $cached_score = unserialize(file_get_contents($cache_fn));
 
         $score = null;
         if(empty($cached_score) || self::is_outdated($cached_score['expires']) || !$this->cache_enabled()){
            $score = $this->get_score($imdb_id);

            if($this->cache_enabled()){
              $this->cache_score($imdb_id, $score);
            }
         }else{
            $score = $cached_score['score'];
         }

         return $score;
      }

      public function delete_the_cache(){
        return array_map('unlink', glob(self::CACHE_DIR . "*.cache"));
      }

      private function cache_enabled(){
        return $this->settings['cache_results'] == true;
      }

      public function get_score($imdb_id){
         $this->set_id($imdb_id);

         $movie_info  = $this->exec_request();
         $movie_info = json_decode($movie_info, true);

         return $movie_info['imdbRating'];
      }

      private function set_id($imdb_id){
         if(empty($imdb_id)){
            throw new \Exception("Invalid IMDb movie id.");
         }

         $this->req_params['i'] = $imdb_id;
      }


      private function exec_request(){
         $req_str = self::API_URL . "?apikey=" . $this->settings['api_key'];
         foreach($this->req_params as $param => $value){
           $req_str .= "&" . $param . "=" . $value;
         }
         
         return $this->http_get($req_str);
      }

      private function http_get($url){ 
         $data = null;
         if(function_exists('curl_init')){
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $data = curl_exec($ch);
            curl_close($ch);
         }else{
            $data = file_get_contents($url);
         }
 
         if(empty($data)){
             throw new \Exception("Http_get failed.");
         }

         return $data;
      }

      private static function is_outdated($timestamp){
         return time() > $timestamp;
      }

      private static function str_to_timestamp($str){
         $one_minute_seconds = 60;
         $one_hour_seconds = 60 * 60;
         $one_day_seconds = $one_hour_seconds * 24;

         $str_num = substr($str, 0, strlen($str)-1);
         $num_type = substr($str, strlen($str_num), strlen($str));

         switch($num_type){
           case "d":
           case "D":
              return $str_num * $one_day_seconds;
           case "h":
           case "H":
              return $str_num * $one_hour_seconds;
           case "m":
           case "M":
              return $str_num * $one_minute_seconds;
           case "s":
           case "S":
              return $str_num;
           default:
              return false;
         }         
      }
   }
}
// <-- END OF CLASS
 
?>
