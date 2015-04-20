<?php

/*
Plugin Name: Gravity Forms + First Data Global Gateway e4
Plugin URI: https://wordpress.org/plugins/gravity-forms-first-data-global-gateway-addon/
Description: Gravity Forms + First Data Global Gateway e4 is a plugin and addon for Gravity Forms that will allow you to process products using the First Data Global Gateway e4 API.
Version: 1.1
Author: Aubrey Portwood of Excion
Author URI: http://excion.co
License: GPL2
 */

/*  Copyright 2013  Aubrey Portwood  (email : aubreypwd@gmail.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Load up the basic setup, which will allow others
// to hack it's use without Gravity Forms using the
// request-method.
//
// Gravity forms integration should load only
// if the class exists.

// == First Data Integration ==

// First Data e4 PHP Wrapper
// https://github.com/VinceG/php-first-data-api
require_once "php-first-data-api/src/VinceG/FirstDataApi/FirstData.php";

// Primary API functions for FirstData.
// Can be used w/out GF
require_once "gffd-fd.php";

// Include the request method.
// Can be used with or w/out GF.
require_once "gffd-fd-request-method.php";

// Include debugging.
require_once "gffd-fd-debugging.php";

// == Gravity Forms Integration ==

// Fixes the __FILE__ issue with symlinked
// plugins in WP.
require_once('fix-__FILE__.php');

// First, check that we have the requirements,
// if not stop (this way we don't throw an error).
function gffd_check_requirements(){
	if(

		// These are the Gravity Forms classes we use
		// to do things.
		class_exists("RGForms")
		&& class_exists("RGFormsModel")
		&& class_exists("GFCommon")
	){

		// If we have the requirements,
		// let's prepare everything:

		// Integrate with Gravity Forms
		function gffd_load(){
			require_once "gffd-gf.php";
		}

		function gffd_check_n_load(){
			gffd_load();
		}

		add_action('init','gffd_check_n_load');

	}else{
		// Right now, just do nothing if all
		// the requirements aren't met.
	}
}

add_action('plugins_loaded','gffd_check_requirements');

?>
