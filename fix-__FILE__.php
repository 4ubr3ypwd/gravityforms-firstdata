<?php

// Fix the __FILE__ problem with symlinks/WP.
$___GFFDFILE___ = __FILE__;

if ( isset( $plugin ) ) {
	$___GFFDFILE___ = $plugin;
}
else if ( isset( $mu_plugin ) ) {
	$___GFFDFILE___ = $mu_plugin;
}
else if ( isset( $network_plugin ) ) {
	$___GFFDFILE___ = $network_plugin;
}

define( '___GFFDFILE___', $___GFFDFILE___ );

?>