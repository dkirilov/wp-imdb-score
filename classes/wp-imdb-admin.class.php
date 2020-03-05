<?php

function_exists("do_action") or die('Wrong place dude!');


if(!class_exists('WP_IMDB_Admin')){

    class WP_IMDB_Admin{

      public function __construct(){
         $this->init();
      }

      public function __destruct(){
      }

      private static function get_option($opt){
         return get_option("wpimdbscore_".$opt);
      }

      public function init(){
         add_action('admin_menu', array($this, 'add_settings_submenu'));
         add_action('admin_init', array($this, 'register_settings'));
      }
      
      public function add_settings_submenu(){
        /* $hook_name = */ add_options_page(__('WordPress IMDb Score', 'wp-imdb-score'), __('WordPress IMDb Score', 'wp-imdb-score'), "manage_options", "wp-imdb-score-settings", array($this, 'get_settings_page'));
      }
     
      public function get_settings_page(){
        if(!current_user_can("manage_options")){
           return;
        }

        ?>

        <div class="wrap">
          <h1><?php _e('WordPress IMDb Score Plugin Settings Page', 'wp-imdb-score'); ?></h1>
          <p><?php _e('Here you can change some settings according to your needs.', 'wp-imdb-score') ?></p>
          <form method="POST" action="options.php">
             <?php settings_fields("wpimdbscore_options"); ?>
             <?php do_settings_sections("wpimdbscore_options"); ?>
             <?php submit_button(); ?>
          </form>
        </div>

        <?php
      }

      public function register_settings(){
         register_setting("wpimdbscore_options", "wpimdbscore_api_key", array(
              'sanitize_callback' => array($this, 'validate_api_key_input')
          ));
         register_setting("wpimdbscore_options", "wpimdbscore_imdb_icon_url", array(
              'sanitize_callback' => array($this, 'validate_imdb_icon_url_input')
          ));
         register_setting("wpimdbscore_options", "wpimdbscore_display_imdb_icon", array(
              'sanitize_callback' => array($this, 'validate_display_imdb_icon_input')
          ));
         register_setting("wpimdbscore_options", "wpimdbscore_icon_width", array(
              'sanitize_callback' => array($this, 'validate_icon_width_input')
          ));
         register_setting("wpimdbscore_options", "wpimdbscore_icon_height", array(
              'sanitize_callback' => array($this, 'validate_icon_height_input')
          ));
         register_setting("wpimdbscore_options", "wpimdbscore_display_as_link", array(
              'sanitize_callback' => array($this, 'validate_display_as_link_input')
          ));
         register_setting("wpimdbscore_options", "wpimdbscore_link_opens_in_new_tab", array(
              'sanitize_callback' => array($this, 'validate_link_opens_in_new_tab_input')
          ));
         register_setting("wpimdbscore_options", "wpimdbscore_cache_results", array(
              'sanitize_callback' => array($this, 'validate_cache_results_input')
          ));
         register_setting("wpimdbscore_options", "wpimdbscore_cache_lasts", array(
              'sanitize_callback' => array($this, 'validate_cache_lasts_input')
          ));

          $this->add_settings_sections();
          $this->add_settings_fields();
      }


      // Create sections
      public function add_settings_sections(){
         // OMDb API Settings
         add_settings_section('wpimdbscore_omdbapi_section', '<ins>'. __('OMDb API Settings', 'wp-imdb-score') .'</ins>', array($this, 'wpimdbscore_omdbapi_section_descr'), 'wpimdbscore_options');

         // IMDb Icon Settings
         add_settings_section('wpimdbscore_imdbicon_section', '<ins>'. __('IMDb Icon Settings', 'wp-imdb-score') .'</ins>', array($this, 'wpimdbscore_imdbicon_section_descr'), 'wpimdbscore_options');

         // Link Settings
         add_settings_section('wpimdbscore_link_section', '<ins>'. __('Link Settings', 'wp-imdb-score') .'</ins>', array($this, 'wpimdbscore_link_section_descr'), 'wpimdbscore_options');
      
         // Cache Settings
         add_settings_section('wpimdbscore_cache_section', '<ins>'. __('Cache Settings', 'wp-imdb-score') .'</ins>', array($this, 'wpimdbscore_cache_section_descr'), 'wpimdbscore_options');
      }

      public function wpimdbscore_omdbapi_section_descr(){
         $api_key_url = 'http://www.omdbapi.com/apikey.aspx';
         $link_target = '_blank';

         printf( wp_kses( __('You can get OMDb API Key by visiting <a href="%s" target="%s">this page</a>.', 'wp-imdb-score'), array('a'=>array('href'=>array(), 'target'=>array())) ), esc_url($api_key_url), $link_target );
      }

      public function wpimdbscore_imdbicon_section_descr(){
         _e('Please note that <strong>icon URL should finish in one of the following extentions: <ins>*.png</ins>, <ins>*.jpg</ins>, <ins>*.jpeg</ins>, <ins>*.gif</ins></strong>! Everything else will be rejected and marked as error. In addition <strong>icon sizes have to be in pixels</strong>!', 'wp-imdb-score');
      }

      public function wpimdbscore_link_section_descr(){
         
      }

      public function wpimdbscore_cache_section_descr(){
         _e("'Cache lasts' field does accept values in the following format: {number}{[d,D,h,H,m,M,s,S]}.", 'wp-imdb-score');
      }

      // Create settings fields and attach them to desired sections 
      public function add_settings_fields(){
         add_settings_field('wpimdbscore_api_key', '&nbsp; &nbsp;'. __('API Key', 'wp-imdb-score') .':', array($this, 'wpimdbscore_api_key_field'), 'wpimdbscore_options', 'wpimdbscore_omdbapi_section');
         add_settings_field('wpimdbscore_imdb_icon_url', '&nbsp; &nbsp;'. __('IMDb Icon URL', 'wp-imdb-score') .':', array($this, 'wpimdbscore_imdb_icon_url_field'), 'wpimdbscore_options', 'wpimdbscore_imdbicon_section');
         add_settings_field('wpimdbscore_display_imdb_icon', '&nbsp; &nbsp;'. __('Display IMDb Icon', 'wp-imdb-score') .':', array($this, 'wpimdbscore_display_imdb_icon_field'), 'wpimdbscore_options', 'wpimdbscore_imdbicon_section');
         add_settings_field('wpimdbscore_icon_width', '&nbsp; &nbsp;'. __('Icon width', 'wp-imdb-score') .':', array($this, 'wpimdbscore_icon_width_field'), 'wpimdbscore_options', 'wpimdbscore_imdbicon_section');
         add_settings_field('wpimdbscore_icon_height', '&nbsp; &nbsp;'. __('Icon height', 'wp-imdb-score') .':', array($this, 'wpimdbscore_icon_height_field'), 'wpimdbscore_options', 'wpimdbscore_imdbicon_section');
         add_settings_field('wpimdbscore_display_as_link', '&nbsp; &nbsp;'. __('Display as link', 'wp-imdb-score') .':', array($this, 'wpimdbscore_display_as_link_field'), 'wpimdbscore_options', 'wpimdbscore_link_section');
         add_settings_field('wpimdbscore_link_opens_in_new_tab', '&nbsp; &nbsp;'. __('Link opens in new tab', 'wp-imdb-score') .':', array($this, 'wpimdbscore_link_opens_in_new_tab_field'), 'wpimdbscore_options', 'wpimdbscore_link_section');
         add_settings_field('wpimdbscore_cache_results', '&nbsp; &nbsp;'. __('Cache results', 'wp-imdb-score') .':', array($this, 'wpimdbscore_cache_results_field'), 'wpimdbscore_options', 'wpimdbscore_cache_section');
         add_settings_field('wpimdbscore_cache_lasts', '&nbsp; &nbsp;'. __('Cache lasts', 'wp-imdb-score') .':', array($this, 'wpimdbscore_cache_lasts_field'), 'wpimdbscore_options', 'wpimdbscore_cache_section');
      }

      /** Settings input fields **/
      // Main Settings section input fields
      public function wpimdbscore_api_key_field(){
         echo "<input id='wpimdbscore_api_key_field' name='wpimdbscore_api_key' size='10' type='text' value='". self::get_option("api_key") ."' required ><sup>*</sup>";
      }

      public function wpimdbscore_imdb_icon_url_field(){
         echo "<input id='wpimdbscore_imdb_icon_url_field' name='wpimdbscore_imdb_icon_url' size='40' type='text' value='". self::get_option("imdb_icon_url") ."'  >";
      }

      public function wpimdbscore_display_imdb_icon_field(){
         $display_imdb_icon = self::get_option("display_imdb_icon");

         echo "<input id='wpimdbscore_display_imdb_icon_field' name='wpimdbscore_display_imdb_icon' type='radio' value='true' ". ($display_imdb_icon?'checked':'') ." > ". __('Yes', 'wp-imdb-score') ." &nbsp;";
         echo "<input id='wpimdbscore_display_imdb_icon_field' name='wpimdbscore_display_imdb_icon' type='radio' value='false' ". (!$display_imdb_icon?'checked':'') ." > " . __('No', 'wp-imdb-score');
      }

      public function wpimdbscore_icon_width_field(){
         echo "<input id='wpimdbscore_icon_width_field' name='wpimdbscore_icon_width' size='5' type='text' value='". self::get_option("icon_width") ."'  >";
      }  

      public function wpimdbscore_icon_height_field(){
         echo "<input id='wpimdbscore_icon_height_field' name='wpimdbscore_icon_height' size='5' type='text' value='". self::get_option("icon_height") ."'  >";
      }

      public function wpimdbscore_display_as_link_field(){
         $display_as_link = self::get_option("display_as_link");

         echo "<input id='wpimdbscore_display_as_link_field' name='wpimdbscore_display_as_link' type='radio' value='true' ". ($display_as_link?'checked':'') ." > ". __('Yes', 'wp-imdb-score') ." &nbsp;";
         echo "<input id='wpimdbscore_display_as_link_field' name='wpimdbscore_display_as_link' type='radio' value='false' ". (!$display_as_link?'checked':'') ." > " . __('No', 'wp-imdb-score');
      }

      public function wpimdbscore_link_opens_in_new_tab_field(){
         $link_opens_in_new_tab = self::get_option("link_opens_in_new_tab");

         echo "<input id='wpimdbscore_link_opens_in_new_tab_field' name='wpimdbscore_link_opens_in_new_tab' type='radio' value='true' ". ($link_opens_in_new_tab?'checked':'') ." > ". __('Yes', 'wp-imdb-score') ." &nbsp;";
         echo "<input id='wpimdbscore_link_opens_in_new_tab_field' name='wpimdbscore_link_opens_in_new_tab' type='radio' value='false' ". (!$link_opens_in_new_tab?'checked':'') ." > " . __('No', 'wp-imdb-score');
      }

      public function wpimdbscore_cache_results_field(){
         $cache_results = self::get_option("cache_results");

         echo "<input id='wpimdbscore_cache_results_field' name='wpimdbscore_cache_results' type='radio' value='true' ". ($cache_results?'checked':'') ." > ". __('Yes', 'wp-imdb-score') ." &nbsp;";
         echo "<input id='wpimdbscore_cache_results_field' name='wpimdbscore_cache_results' type='radio' value='false' ". (!$cache_results?'checked':'') ." > " . __('No', 'wp-imdb-score');
      }

      public function wpimdbscore_cache_lasts_field(){
         echo "<input id='wpimdbscore_cache_lasts_field' name='wpimdbscore_cache_lasts' size='5' type='text' value='". self::get_option("cache_lasts") ."'  >";
      }

     
      /** Input fields validations **/
      public function validate_api_key_input($input){
          $input = trim($input);

          if(!WP_IMDB_Score_Validator::is_api_key($input)){
             $api_key_url = 'http://www.omdbapi.com/apikey.aspx';
             $link_target = '_blank';
             $error_str = sprintf( wp_kses(__('Invalid API Key! Visit <a href="%1$s" target="%2$s">OMDb API\'s website</a> to get a valid API Key.', 'wp-imdb-score'), array('a'=>array('href'=>array(), 'target'=>array()))) , esc_url($api_key_url), $link_target );

             add_settings_error("wpimdbscore_api_key", "invalid-api-key", $error_str);

             return '';
          }

          return $input;
      }

      public function validate_imdb_icon_url_input($input){
          $input = trim($input);

          if(!WP_IMDB_Score_Validator::is_imdb_icon_url($input)){
             add_settings_error("wpimdbscore_imdb_icon_url", "invalid-imdb-icon-url", __('Invalid <ins>IMDb Icon URL</ins>!', 'wp-imdb-score'));

             return '';
          }

          return $input;
      }

      public function validate_display_imdb_icon_input($input){
          return trim($input) == 'true';
      }

      public function validate_icon_width_input($input){
          $input = trim($input);

          if(!WP_IMDB_Score_Validator::is_icon_size($input)){
             add_settings_error("wpimdbscore_imdb_icon_width", "invalid-imdb-icon-width", __('Invalid <ins>Icon Width</ins>!', 'wp-imdb-score'));

             return '';
          }

          return $input;
      }

      public function validate_icon_height_input($input){
          $input = trim($input);

          if(!WP_IMDB_Score_Validator::is_icon_size($input)){
             add_settings_error("wpimdbscore_imdb_icon_height", "invalid-imdb-icon-height", __('Invalid <ins>Icon Height</ins>!', 'wp-imdb-score'));

             return '';
          }

          return $input;
      }

      public function validate_display_as_link_input($input){
          return trim($input) == 'true';
      }

      public function validate_link_opens_in_new_tab_input($input){
          return trim($input) == 'true';
      }

      public function validate_cache_results_input($input){
          return trim($input) == 'true';
      }

      public function validate_cache_lasts_input($input){
          $input = trim($input);

          if(!WP_IMDB_Score_Validator::is_cache_lasts($input)){
             add_settings_error("wpimdbscore_cache_lasts", "invalid-cache-lasts", __('Invalid value for <ins>Cache lasts</ins>!', 'wp-imdb-score'));

             return '';
          }

          return $input;
      }

    }
    // <--- END OF CLASS
}

// Make an instance of this class
$wpimdbadmin = new WP_IMDB_Admin();
 

?>
