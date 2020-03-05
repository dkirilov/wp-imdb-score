<?php

/**
 * @package WP_IMDB_Score
 * @version 1.0.0
 */
/*
Plugin Name: WordPress IMDB Score
Plugin URI: https://github.com/dkirilov/wp-imdb-score
Text Domain: wp-imdb-score
Description: This plugin helps wordpress users to display the IMDB ratings score for a movie. It's lightweight, simple and easy to use.  
Author: Dian Kirilov
Version: 1.0.0
Author URI: http://diankirilov.wordpress.com/
*/


/** Check whether the current file is accessed directly **/
function_exists("do_action") or die('Wrong place dude!');


/** Useful constants **/
define('PLUGIN_DIR', __DIR__ . DIRECTORY_SEPARATOR);


/** Includes **/
include(plugin_dir_path(__FILE__) . 'classes' . DIRECTORY_SEPARATOR . 'wp-imdb-score.class.php');
include(plugin_dir_path(__FILE__) . 'classes' . DIRECTORY_SEPARATOR . 'wp-imdb-score-validator.class.php');
include(plugin_dir_path(__FILE__) . 'classes' . DIRECTORY_SEPARATOR . 'wp-imdb-admin.class.php');


/** Hooks **/
// Activation hook
register_activation_hook(__FILE__, array("WP_IMDB_Score", "activate"));
// Deactivation hook
register_deactivation_hook(__FILE__, array("WP_IMDB_Score", "deactivate"));
// Uninstall hook
register_uninstall_hook(__FILE__, array("WP_IMDB_Score", "uninstall"));
// Display IMDb score hook
add_action("display_imdb_score", "wpimdbscore_display_imdb_score");


/** Actions **/
// Display IMDb score
function wpimdbscore_display_imdb_score($imdb_id){
    $wpimdbscore = new WP_IMDB_Score();

    echo $wpimdbscore->get_score_html($imdb_id);
}


/** Shortcodes **/
// [imdb_score]
function wpimdbscore_shortcode($atts){
   do_action('display_imdb_score', $atts['id']);
}
add_shortcode("imdb_score", "wpimdbscore_shortcode");

?>
