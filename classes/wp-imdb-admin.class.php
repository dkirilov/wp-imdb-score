<?php

class WP_IMDB_Admin{

  public function __construct(){
     $this->init();
  }

  private static function get_option($opt){
     return get_option("wpimdbscore_".$opt);
  }

  public function init(){
     add_action('admin_menu', array($this, 'add_settings_submenu'));
     add_action('admin_init', array($this, 'register_settings'));
  }
  
  public function add_settings_submenu(){
    $hook_name = add_options_page("WordPress IMDb Score", "WordPress IMDb Score", "manage_options", "wp-imdb-score-settings", array($this, 'get_settings_page'));
  }
 
  public function get_settings_page(){
    if(!current_user_can("manage_options")){
       return;
    }

    ?>

    <div class="wrap">
      <h1>WordPress IMDb Score Plugin Settings Page</h1>
      <p>Here you can change some settings according to your needs.</p>
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
     add_settings_section('wpimdbscore_main', 'Main Settings', array($this, 'wpimdbscore_main_section_text'), 'wpimdbscore_options');
  }

  // Sections descriptions
  public function wpimdbscore_main_section_text(){
    // Here you can print some text and/or HTML. It will be shown under Main Settings section in plugin's admin settigns page
    echo "...";
  }

  // Create settings fields and attach them to desired sections 
  public function add_settings_fields(){
     add_settings_field('wpimdbscore_api_key', 'API Key:', array($this, 'wpimdbscore_api_key_field'), 'wpimdbscore_options', 'wpimdbscore_main');
     add_settings_field('wpimdbscore_imdb_icon_url', 'IMDb Icon URL:', array($this, 'wpimdbscore_imdb_icon_url_field'), 'wpimdbscore_options', 'wpimdbscore_main');
     add_settings_field('wpimdbscore_display_imdb_icon', 'Display IMDb Icon:', array($this, 'wpimdbscore_display_imdb_icon_field'), 'wpimdbscore_options', 'wpimdbscore_main');
     add_settings_field('wpimdbscore_icon_width', 'Icon Width:', array($this, 'wpimdbscore_icon_width_field'), 'wpimdbscore_options', 'wpimdbscore_main');
     add_settings_field('wpimdbscore_icon_height', 'Icon Height:', array($this, 'wpimdbscore_icon_height_field'), 'wpimdbscore_options', 'wpimdbscore_main');
     add_settings_field('wpimdbscore_display_as_link', 'Display As Link:', array($this, 'wpimdbscore_display_as_link_field'), 'wpimdbscore_options', 'wpimdbscore_main');
     add_settings_field('wpimdbscore_link_opens_in_new_tab', 'Link Opens In New Tab:', array($this, 'wpimdbscore_link_opens_in_new_tab_field'), 'wpimdbscore_options', 'wpimdbscore_main');
     add_settings_field('wpimdbscore_cache_results', 'Cache Results:', array($this, 'wpimdbscore_cache_results_field'), 'wpimdbscore_options', 'wpimdbscore_main');
     add_settings_field('wpimdbscore_cache_lasts', 'Cache Lasts:', array($this, 'wpimdbscore_cache_lasts_field'), 'wpimdbscore_options', 'wpimdbscore_main');
  }

  /** Settings input fields **/
  // Main Settings section input fields
  public function wpimdbscore_api_key_field(){
     echo "<input id='wpimdbscore_api_key_field' name='wpimdbscore_api_key' size='10' type='text' value='". self::get_option("api_key") ."' required >";
  }

  public function wpimdbscore_imdb_icon_url_field(){
     echo "<input id='wpimdbscore_imdb_icon_url_field' name='wpimdbscore_imdb_icon_url' size='40' type='text' value='". self::get_option("imdb_icon_url") ."'  >";
  }

  public function wpimdbscore_display_imdb_icon_field(){
     $display_imdb_icon = self::get_option("display_imdb_icon");

     echo "<input id='wpimdbscore_display_imdb_icon_field' name='wpimdbscore_display_imdb_icon' type='radio' value='true' ". ($display_imdb_icon?'checked':'') ." > Yes &nbsp;";
     echo "<input id='wpimdbscore_display_imdb_icon_field' name='wpimdbscore_display_imdb_icon' type='radio' value='false' ". (!$display_imdb_icon?'checked':'') ." > No";
  }

  public function wpimdbscore_icon_width_field(){
     echo "<input id='wpimdbscore_icon_width_field' name='wpimdbscore_icon_width' size='5' type='text' value='". self::get_option("icon_width") ."'  >";
  }  

  public function wpimdbscore_icon_height_field(){
     echo "<input id='wpimdbscore_icon_height_field' name='wpimdbscore_icon_height' size='5' type='text' value='". self::get_option("icon_height") ."'  >";
  }

  public function wpimdbscore_display_as_link_field(){
     $display_as_link = self::get_option("display_as_link");

     echo "<input id='wpimdbscore_display_as_link_field' name='wpimdbscore_display_as_link' type='radio' value='true' ". ($display_as_link?'checked':'') ." > Yes &nbsp;";
     echo "<input id='wpimdbscore_display_as_link_field' name='wpimdbscore_display_as_link' type='radio' value='false' ". (!$display_as_link?'checked':'') ." > No";
  }

  public function wpimdbscore_link_opens_in_new_tab_field(){
     $link_opens_in_new_tab = self::get_option("link_opens_in_new_tab");

     echo "<input id='wpimdbscore_link_opens_in_new_tab_field' name='wpimdbscore_link_opens_in_new_tab' type='radio' value='true' ". ($link_opens_in_new_tab?'checked':'') ." > Yes &nbsp;";
     echo "<input id='wpimdbscore_link_opens_in_new_tab_field' name='wpimdbscore_link_opens_in_new_tab' type='radio' value='false' ". (!$link_opens_in_new_tab?'checked':'') ." > No";
  }

  public function wpimdbscore_cache_results_field(){
     $cache_results = self::get_option("cache_results");

     echo "<input id='wpimdbscore_cache_results_field' name='wpimdbscore_cache_results' type='radio' value='true' ". ($cache_results?'checked':'') ." > Yes &nbsp;";
     echo "<input id='wpimdbscore_cache_results_field' name='wpimdbscore_cache_results' type='radio' value='false' ". (!$cache_results?'checked':'') ." > No";
  }

  public function wpimdbscore_cache_lasts_field(){
     echo "<input id='wpimdbscore_cache_lasts_field' name='wpimdbscore_cache_lasts' size='5' type='text' value='". self::get_option("cache_lasts") ."'  >";
  }

 
  /** Input fields validations **/
  public function validate_api_key_input($input){
      return $input;
  }
  public function validate_imdb_icon_url_input($input){
      return $input;
  }
  public function validate_display_imdb_icon_input($input){
      return $input=='true';
  }
  public function validate_icon_width_input($input){
      return $input;
  }
  public function validate_icon_height_input($input){
      return $input;
  }
  public function validate_display_as_link_input($input){
      return $input=='true';
  }
  public function validate_link_opens_in_new_tab_input($input){
      return $input=='true';
  }
  public function validate_cache_results_input($input){
      return $input=='true';
  }
  public function validate_cache_lasts_input($input){
      return $input;
  }

// END OF CLASS
}

// Make an instance of this class
$wpimdbadmin = new WP_IMDB_Admin();

?>
