<?php

/*
Plugin Name: Gravity Forms + First Data Global Gateway e4
Plugin URI: https://wordpress.org/plugins/gravity-forms-first-data-global-gateway-addon/
Description: Gravity Forms + First Data Global Gateway e4 is a plugin and addon for Gravity Forms that will allow you to process products using the First Data Global Gateway e4 API.
Version: 1.2-dev
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

// First Data e4 PHP Wrapper
// https://github.com/VinceG/php-first-data-api
require_once 'php-first-data-api/src/VinceG/FirstDataApi/FirstData.php';

// Fixes the __FILE__ issue with symlinked
// plugins in WP.
require_once 'fix-__FILE__.php';

// Include out terms library and
// keep it as a separate file
// so it's easy to find.
require_once 'gffd-gf-terms.php';

// Include the setup for interrupting
// form data to perform purchases
require_once 'gffd-gf-forms.php';

// The base class that should be loading.
require_once 'class/class-gffd-core.php';

// Setup all the settings pages, etc.
require_once 'class/class-gffd-admin.php';

//Add the Feed stuff
require_once 'class/class-gffd-admin-feeds.php';
