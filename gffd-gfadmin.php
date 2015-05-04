<?php

class GFFD_Admin {

	// Instances:
	public $gffd_core;

	function __construct( $gffd_core ) {
		$this->gffd_core = $gffd_core;

		add_action( 'admin_init', array( $this, 'gffd_admin_scripts' ) );
		add_action( 'admin_init', array( $this, 'gffd_admin_init' ) );
		add_action( 'admin_init', array( $this, 'gffd_save_admin_settings' ) );
	}

	// Include Gravity Forms admin CSS
	// so we can use cool things like tabs, etc.
	function gffd_admin_enqueue_css() {
		wp_enqueue_style( 'gf_css', gffd_plugin_url(), array(), '', false );
		wp_enqueue_style( 'gffd_admin_css', plugins_url( 'gffd-gfadmin.css', ___GFFDFILE___ ), array(), '', false );
	}

	// Check if a request var matches, if so, express $classes.
	// Used when trying to see if &subpage=x
	function gffd_request_match_class_active( $request_var, $var_value, $classes, $echo ) {
		if ( isset( $_REQUEST[ $request_var ] ) ) {
			if ( $var_value == $_REQUEST[ $request_var ] ) {
				if ( true == $echo ) {
					echo $classes;
				} else {
					return $classes;
				}
			} else {
				if( true == $echo ) {
					echo '';
				} else {
					return '';
				}
			}
		} else {
			if ( '' != $var_value ) {
				if ( true == $echo ) {
					echo '';
				} else {
					return '';
				}

			// If the var_value is blank, they
			// must be asking if the request_var
			// is unset.
			//
			// If it's unset, and we're asking if
			// it is by sending '', then express
			// the classes.
			} else {
				if( true == $echo ) {
					echo $classes;
				} else {
					return $classes;
				}
			}
		}
	}

	// Hyper testing if a request var is set and matches.
	//
	// Use like:
	//
	// func('subpage','this-subpage')
	//
	// or
	//
	// func('subpage',array(
	// 		'subpage1',
	// 		'subpage2'
	// ))
	//
	// ... which will test if either is being requested via subpage
	// request var in OR mode.
	function gffd_admin_request_match( $request_var,$request_strings ) {
		if ( is_array( $request_strings ) ) {

			foreach ( $request_strings as $request_string ) {
				if ( isset( $_REQUEST[ $request_var ] ) ) {
					if ( $_REQUEST[ $request_var ] == $request_string ) {
						return true; // one matched
					}
				} else {
					//do nothing
				}
			}

			return false; //none matched.
		} else {

			if ( isset( $_REQUEST[ $request_var ] ) ) {
				if ( $_REQUEST[$request_var] == $request_strings ) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}

		}
	}

	// Test if request variables are set.
	//
	// To test, use:
	//
	// func('form'), which will test if $_REQUEST[form]
	// is set.
	//
	// func(array('form','subpage')), which will test
	// if $_REQUEST[form] and $_REQUEST[subpage] are set.
	function gffd_admin_is_requested( $requested_vars, $match_value = false ) {
		if ( ! is_array( $requested_vars ) ) {
			if ( isset( $_REQUEST[ $requested_vars ] ) ) {
				return true;
			} else {
				return false;
			}
		} else {
			$requests_all_present = true;

			foreach ( $requested_vars as $var_request ) {
				if( ! isset( $_REQUEST[ $var_request ] ) ) {
					$requests_all_present = false;
				}
			}

			return $requests_all_present;
		}
	}

	// Include our WP Admin scripts
	// for the plugin.
	function gffd_admin_scripts() {
		wp_enqueue_script( 'gffd-gf-admin-js', plugins_url( 'gffd-gfadmin.js', ___GFFDFILE___ ), array( 'jquery' ), '', false );
	}

	// Add a settings panel to the Gravity Forms Menu.
	function gffd_admin_init() {
		RGForms::add_settings_page( __( $this->gffd_core->gffd_glossary( 'settings_name' ) ), 'gffd_admin_page', '' );
	}

	// Setup the actual settings pages in
	// wp-admin.
	function gffd_admin_page() {
		include "gffd-gfadmin.html.php";
	}

	// Get a particular setting.
	function gffd_admin_get_setting( $setting_key ) {
		return get_option( $setting_key );
	}

	// Set a particular setting.
	function gffd_admin_set_setting( $setting_key, $value ) {
		return update_option( $setting_key, $value );
	}

	// Save the settings.
	function gffd_save_admin_settings(){

		// Check if our submit button was clicked,
		// if so, keep saving!
		if( ! isset( $_REQUEST['gffd_admin_submit'] ) ) {
			return;
		}

		$gffd_admin_settings = gffd_admin_settings();

		foreach ( $gffd_admin_settings as $setting_key => $setting ) {
			if ( isset( $_REQUEST[ $setting_key ] ) ) {

				// Make sure checks get value
				if( '' == $_REQUEST[ $setting_key ] && 'checkbox' == $setting['html_type'] ) {
					$_REQUEST[ $setting_key ] = 'checked';
				}

				// I like to store each option as it's own
				// separate key in the DB so one can
				// manually hack in and change things.
				gffd_admin_set_setting( $setting_key, $_REQUEST[ $setting_key ] );

			} else {
				delete_option( $setting_key );
			}
		}

		// Go back to the referer page, so we
		// don't get that re-post stuff on refresh.
		wp_redirect( $_SERVER['HTTP_REFERER'] );
	}

	// The settings for gravity forms.
	function gffd_admin_settings() {
		return array(
			'gffd_gateway_id' => array(
				'label'           => 'Gateway ID',
				'description'     => __( 'You can find the Gateway ID at <strong>Administration &rarr; Terminals &rarr; Details &rarr; Gateway ID</strong>.', 'gffd' ),

				// Could be textarea, chekcbox, etc.
				'html_tag'        =>' input',

				// Could be password, date, strongail, etc.
				'html_type'       => 'text',

				// <input>'s should = false.
				// <textarea>'s should = true for </textarea>.
				'html_close'      => false
			),

			'gffd_gateway_password' => array(
				'label'           => __( 'Generated Password', 'gffd' ),
				'description'     => __( 'You can get the Gateway Password at <strong>Administration &rarr; Terminals &rarr; Details &rarr; Password</strong>. You may have to generate a new password to put here.', 'gffd' ),
				'html_tag'        => 'input',
				'html_type'       => 'text',

				//Don't have to set (see below).
				'html_close'      => false
			),
			'gffd_test_mode'=>array(
				'label'           => __( 'Enable Test Mode', 'gffd' ),

				// Used with checkboxes, if true will show
				// label next to the box [x] Label.
				'checkbox_label'  => true,

				'description'     => __( 'Use this option to enable test mode when performing transactions.', 'gffd' ),
				'html_tag'        => 'input',
				'html_type'       => 'checkbox',
			)

		);
	}

	function gffd_get_gf_admin_setting($setting_id){
		$settings = gffd_admin_settings();
		return $settings[$setting_id];
	}

}