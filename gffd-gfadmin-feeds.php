<?php

// So we don't have to hard code anything,
// this will give back the admin page baded on $menu['name'].
//
// You can use it like:
//
// gffd_feed_admin_url() to return the url.
// gffd_feed_admin_url(true) to echo the url.
// gffd_feed_admin_url('&subpage=x') to chain value to the url and return it
// gffd_feed_admin_url('&subpage=x', true) to echo the chained url
//
function gffd_feed_admin_url($href_echo=null, $echo=null){
	$url = "admin.php?page=" . gffd_admin_feeds_add_navigation_config('name');

	if(is_bool($href_echo)){
		if($href_echo==true){
			echo $url;
		}else{
			return $url;
		}
	}elseif(is_string($href_echo)){
		if(!$echo){
			return $url . $href_echo;
		}elseif($echo==true){
			echo $url . $href_echo;
		}
	}
}

// Bring in the actual page.
// Also, include scripts that will be needed.
function gffd_admin_feeds_page(){

	gffd_admin_enqueue_css();

	wp_enqueue_style(
		'gffd-admin-feeds-css',
		plugins_url('gffd-gfadmin-feeds.css', ___GFFDFILE___),
		array(),
		'',
		false
	);

	wp_enqueue_script(
		'gffd-admi-feeds-js',
		plugins_url('gffd-gfadmin-feeds.js', ___GFFDFILE___),
		array(),
		'',
		false
	);

	// JS Translate data
	wp_localize_script(
		'gffd-admi-feeds-js',
		'_e',
		array(
			'admin_feeds_button_error'=>__('There were errors, please review and try again.'),
			'admin_feeds_button_save'=>gffd_language_terms('as_object')->term_feed_admin_save
		)
	);	

	if(gffd_admin_feeds_is_subpage('add-new-edit')){
		// include "gffd-gfadmin-feed.html.php"; //here just to show how it's done.
	}else{
		include("gffd-gfadmin-feeds.html.php");
	}
}

//Detect if subpages are loaded for the
//Feeds admin section
function gffd_admin_feeds_is_subpage($asked_subpage=false){
	if(isset($_GET['subpage'])){
		$current_subpage = $_GET['subpage'];
	}else{
		$current_subpage = false;
	}

	if($asked_subpage==$current_subpage){
		return $current_subpage;
	}else{
		return false;
	}
}

function gffd_admin_feeds_add_navigation_config($key=null){
	$menu=array(
		'name'=>'gffd_feeds',
		'label'=>__(gffd_glossary('settings_name')),
		'callback'=>'gffd_admin_feeds_page'
	);

	if($key){
		return $menu[$key];
	}else{
		return $menu;
	}
}

// Add the menu under the Gravity Form menu
// in the Dashboard.
function gffd_admin_feeds_add_navigation($menus){
	$menus[]=gffd_admin_feeds_add_navigation_config();
	return $menus;
}

add_filter(
	'gform_addon_navigation',
	'gffd_admin_feeds_add_navigation'
);

// Save a feed!
function gffd_admin_feeds_save_feed(){
	
	//Don't do if we aren't saving a feed.
	if(!isset($_REQUEST['gffd-feed-admin-edit-submit'])) return;

	//Get the form_id
	$form_id = $_REQUEST['form_id'];

	//Save the feed_active setting
	if($_REQUEST['gffd_feed_active']=='on'){
		update_option('gffd_form_'.$form_id.'_feed_active','active');
	}else{
		delete_option('gffd_form_'.$form_id.'_feed_active');	
	}

	// Save the indexes
	$feed_indexes = $_REQUEST['gffd_form_feed_indexes'];

	if(is_array($feed_indexes)){
		update_option('gffd_form_'.$form_id.'_feed_indexes', $feed_indexes);
		wp_redirect($_SERVER['HTTP_REFERER']);
	}else{
		delete_option('gffd_form_'.$form_id.'_feed_indexes');
	}

}

add_action('admin_init', 'gffd_admin_feeds_save_feed');

?>