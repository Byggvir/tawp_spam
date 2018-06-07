<?php
/**
 * @package TAWP_Spam
 * @version 0.1
 */
/*
Plugin Name: TAWP Spam
Plugin URI: https://github.com/Byggvir/tawp_spam/
Description: This is my first plugin to imbed a list of spam comments in an article.
Author: Thomas Arend
Version: 0.2
Author URI: http://byggvir.de/
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function tagetwpspamlist() {

	global $wpdb ;
	$list="";
	
        $spamlist=$wpdb->get_results(
	"
	select distinct comment_author_IP,comment_author_email
	from $wpdb->comments
	where comment_approved = 'spam'	
        order by comment_author_IP, comment_author_email
        ;       

	"
	, 
	ARRAY_A
	);
	
	if ( $spamlist ) {
		foreach ( $spamlist as $row) {
//			$list .= $row["comment_author_IP"] . " - " . $row["comment_author_email"] . "<br />";
			$list .= "<tr><td>$row["comment_author_IP"] . "</td><td>" . preg_replace("(.).*\@","\1...@",$row["comment_author_email"]) . "</td></tr>\n";
		}
	}

	return wptexturize( $list ) ;
}

// This just echoes the chosen line, we'll position it later

function taspam($atts) {
	$chosen=tagetwpspamlist();
	return "
	<!-- Begin shortcode spam // ip-address list -->
	<p id='taspamhead'>Author IP from comments marked as spam:</p>
	<table id='taspamlist'>
    $chosen
    </table>
	<!-- End shortcode spam // ip-address list -->
	";
}

// We need some CSS to format the list

function add_tas_stylesheet() {
            wp_register_style('tasStyleSheets', plugins_url('css/styles.css',__FILE__));
            wp_enqueue_style( 'tasStyleSheets');
}

add_action('wp_print_styles', 'add_tas_stylesheet');

// Add the shortcode
add_shortcode('spam', 'taspam');

?>
