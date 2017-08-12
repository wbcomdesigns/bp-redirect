<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Class to add custom hooks for this plugin
 */

if( !class_exists( 'BP_Redirect_Hooks' ) ) {
	class BP_Redirect_Hooks{

		/**
		*  Constructor for custom hooks
		*  @since   1.0.0
		*  @author  Wbcom Designs
		*/
		
		function __construct() {
			add_action( 'wp_footer', array( $this, 'bp_redirect_footer_tooltip' ) );
		}

		/**
		*  Actions performed to add tooltip in admin settings
		*  @since   1.0.0
		*  @access public
		*  @author  Wbcom Designs
		*/

		public function bp_redirect_footer_tooltip() { ?>
			<div id="bp_redirect_tooltip_holder" style="max-width: 200px; margin: 519px 0px 0px 313px; display: block;" class="bp_redirect_tooltip_bottom">
				<div id="bp_redirect_tooltip_arrow" style="margin-left: 79.5px; margin-top: -12px;">
					<div id="bp_redirect_tooltip_arrow_inner"></div>
				</div>
				<div id="bp_redirect_tooltip_content">This is the base location for your business. Tax rates will be based on this country.</div>
			</div>
		<?php }

	}
	new BP_Redirect_Hooks();
}