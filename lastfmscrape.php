<?php

/*
    Plugin Name: LastFmScrape
    Plugin URI: https://github.com/acegiak/lastfmscrape
    Description: just scrapes the latest songs from your last.fm and posts them with the listen post kind
    Version: 0.01
    Author: Ashton McAllan
    Author URI: http://www.acegiak.net
    License: GPLv2
*/

/*  Copyright 2011 Ashton McAllan (email : acegiak@machinespirit.net)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

function lastfm_scrape(){

$churl ="http://ws.audioscrobbler.com/2.0/?method=user.getrecenttracks&user=". get_option('lastfmscrape_username')."&api_key=". get_option('lastfmscrape_apikey')."&format=json";
$ch = curl_init($churl);

curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$data = curl_exec($ch);
curl_close($ch);
$ld = json_decode($data);
error_log("lastfmscrape:".$churl."\r\n".print_r($data,true));
$scrobblecategory = get_category_by_slug(get_option('lastfmscrape_categoryslug'));
foreach($ld->recenttracks->track as $track){
	$posttitle = htmlspecialchars($track->artist->{"#text"}." - ".$track->name." @ ".$track->date->{"#text"});
	if(get_page_by_title($posttitle,$nowhere,'post')){

	}else{
		$post = array(
			'post_title' => $posttitle,
			'post_date' => date('Y-m-d H:i:s',$track->date->uts),
			'post_category' => array($scrobblecategory->term_id)
		);

		$id = wp_insert_post($post);
		$responsemeta = array('title'=>$track->name.' ('.$track->album->{"#text"}.")",'url'=>$track->url,'author'=>$track->artist->{"#text"});

		$term_id = term_exists( 'listen' , 'kind'); 
		wp_set_post_terms( $id, $term_id['term_id'], 'kind' );

		update_post_meta($id,"response",$responsemeta);
		wp_publish_post($id);
	}

}
}



add_filter( 'cron_schedules', 'myprefix_add_3min_cron_schedule' );
function myprefix_add_3min_cron_schedule( $schedules ) {
    $schedules['15min'] = array(
        'interval' => 900, // 1 week in seconds
        'display'  => __( 'Quarterhourly' ),
    );
 
    return $schedules;
}



add_action( 'wp', 'prefix_setup_schedule' );
/**
 * On an early action hook, check if the hook is scheduled - if not, schedule it.
 */
function prefix_setup_schedule() {
	if ( ! wp_next_scheduled( 'prefix_hourly_event' ) ) {
		wp_schedule_event( time(), '15min', 'lastfmscrape_regularly');
	}
}


add_action( 'lastfmscrape_regularly', 'lastfm_scrape' );
/**
 * On the scheduled action hook, run a function.
 */





// create custom plugin settings menu
add_action('admin_menu', 'lastfmscrape_create_menu');

function lastfmscrape_create_menu() {

	//create new top-level menu
	//add_menu_page('lastfmscrape Plugin Settings', 'lastfmscrape Settings', 'administrator', __FILE__, 'lastfmscrape_settings_page',plugins_url('/images/icon.png', __FILE__));
	add_options_page('lastfmscrape Plugin Settings', 'lastfmscrape Settings', 'manage_options',  __FILE__, 'lastfmscrape_settings_page');
	//call register settings function
	add_action( 'admin_init', 'register_mysettings' );
}


function register_mysettings() {
	//register our settings
	register_setting( 'lastfmscrape-settings-group', 'lastfmscrape_apikey' );
	register_setting( 'lastfmscrape-settings-group', 'lastfmscrape_username' );
	register_setting( 'lastfmscrape-settings-group', 'lastfmscrape_categoryslug' );
}

function lastfmscrape_settings_page() {
?>
<div class="wrap">
<h2>Your Plugin Name</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'lastfmscrape-settings-group' ); ?>
    <?php do_settings_sections( 'lastfmscrape-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">lastfm <a href="http://www.last.fm/api/account/create">api key</a></th>
        <td><input type="text" name="lastfmscrape_apikey" value="<?php echo esc_attr( get_option('lastfmscrape_apikey') ); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">username</th>
        <td><input type="text" name="lastfmscrape_username" value="<?php echo esc_attr( get_option('lastfmscrape_username') ); ?>" /></td>
        </tr>  
        <tr valign="top">
        <th scope="row">category slug</th>
        <td><input type="text" name="lastfmscrape_categoryslug" value="<?php echo esc_attr( get_option('lastfmscrape_categoryslug') ); ?>" /></td>
        </tr>
     
    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php } ?>