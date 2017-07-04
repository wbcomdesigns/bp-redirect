<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

//Class to add custom scripts and styles
if( !class_exists( 'BPRedirectScriptsStyles' ) ) {
	class BPRedirectScriptsStyles{

		/*Constructor
		 *  @since   1.0.0
		 *  @author  Wbcom Designs
		 */
		 
		function __construct() {

			add_action( 'admin_enqueue_scripts', array( $this, 'bp_redirect_scripts_enqueue' ) );

		}
		
		/* Actions performed for enqueuing scripts and styles for admin page
		*  @since   1.0.0
		*  @author  Wbcom Designs
		*/                 
		
		function bp_redirect_scripts_enqueue() {
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-accordion' );
			wp_enqueue_script( 'bp-redirect-js', BP_REDIRECT_PLUGIN_URL.'admin/assets/js/bp-redirect-js.js', array('jquery') );
			wp_enqueue_style( 'bp-redirect-css', BP_REDIRECT_PLUGIN_URL.'admin/assets/css/bp-redirect-css.css' );
			if ( !wp_script_is( 'jquery-ui-sortable', 'enqueued' ) ) {
				wp_enqueue_script( 'jquery-ui-sortable' );
			}
			
		}
	}
	new BPRedirectScriptsStyles();
}