<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Class to add custom hooks for this plugin
 */
if( !class_exists( 'BP_Redirect_admin' ) ) {
	class BP_Redirect_admin{
		/**
		 * Holds options key
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var string
		 */

		protected $options_key = 'bp_redirect_setting';

		/*  Constructor for custom hooks
		*  @since   1.0.0
		*  @author  Wbcom Designs
		*/
		
		function __construct() {
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 8 );
		}

		/**
		 * Builds plugin admin menu and pages
		 *
		 * @since 1.0.0
		 * @author  Wbcom Designs
		 * @access public
		 */

	 	public function admin_menu() {

			add_menu_page(__('BP Redirect Setting Page', BP_REDIRECT_DOMAIN ), __('BP Redirect', BP_REDIRECT_DOMAIN ), 'manage_options', 'bp_redirect_settings', array( $this, 'bp_redirect_settings_page' ), 'dashicons-admin-generic' );

			add_submenu_page( 'bp_redirect_settings', __('General', BP_REDIRECT_DOMAIN ), __(' General ', BP_REDIRECT_DOMAIN ), 'manage_options', 'bp_redirect_settings');
		} 

		/**
		 * Builds plugin admin setting page
		 *
		 * @since 1.0.0
		 * @author  Wbcom Designs
		 * @access public
		 */

		public function bp_redirect_settings_page() {
			global $bp;
			$spinner_src = includes_url().'images/spinner.gif';
			$saved_setting = get_option('bp_redirect_admin_settings');	
			$bp_pages = get_option( 'bp-pages' );
			$wp_pages_ids = get_all_page_ids();
			$bp_pages_ids = array_values($bp_pages);
			
			if( !empty( $wp_pages_ids ) && !empty( $bp_pages_ids ) ) {
				$wp_pages_ids = array_diff( $wp_pages_ids, $bp_pages_ids );
			}			
		 ?>
			<h1><?php _e('BP Redirect Settings', BP_REDIRECT_DOMAIN ); ?></h1>
			<div id="bpredirect-settings_updated" class="updated settings-error notice is-dismissible"> 
				<p><strong><?php _e('Settings saved.', BP_REDIRECT_DOMAIN ); ?></strong></p>
				<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e( 'Dismiss this notice.', BP_REDIRECT_DOMAIN ); ?></span></button>
			</div>
			<form method="post" id="bp-redirect-settings-form">			
				<div id="accordion">
				<?php $roles = get_editable_roles();  
				if( !empty( $saved_setting )) {
					if( array_key_exists( 'sequence', $saved_setting )) {
						$seq = explode( ',', $saved_setting['sequence'] );					 
						if( !empty( $seq ) ) {								
							uksort($roles, function($key1, $key2) use ( $seq ) {
								return (array_search($key1, $seq ) > array_search($key2, $seq ));
							});												
						}					
					}
				}
				?>
				<!--<ul>
					<li>
					<input type="radio" id="f-option" name="selector">
					<label for="f-option">Pizza</label>

					<div class="check"></div>
					</li>

					<li>
					<input type="radio" id="s-option" name="selector">
					<label for="s-option">Bacon</label>

					<div class="check"><div class="inside"></div></div>
					</li>

					<li>
					<input type="radio" id="t-option" name="selector">
					<label for="t-option">Cats</label>

					<div class="check">
							<div class="inside"></div></div>
					</li>
				</ul> -->
				<?php
				foreach( $roles as $key => $val ) {
				  ?>
				    <div class="group" id="<?php echo $key; ?>" >
					 	<h3><?php _e( $key, BP_REDIRECT_DOMAIN ); ?></h3>					  	
					  	<div>
						    <p>
						    	<table class="form-table">
									<tbody>
										<tr valign="top">
											<th scope="row"><?php _e('Log in', BP_REDIRECT_DOMAIN ); ?></th>
											<td>
											<?php 
												$login_component = '';
												$login_url = '';
												if( !empty( $saved_setting )) {													
													$setting  = $saved_setting['bp_redirect_redirection'];
														if( array_key_exists( $key, $setting )) {
														 	if( array_key_exists('login_type', $setting[$key])){
																$login_type_val = $setting[$key]['login_type'];								
															}
															if(!empty($setting[$key]['login_component'])){
																$login_component = $setting[$key]['login_component'];	
															}	
															$login_url= $setting[$key]['login_url'];
														}	
												} 
												if ( in_array('buddypress/bp-loader.php', apply_filters('active_plugins', get_option('active_plugins')))) { 
												?>																		
													<input name='<?php echo "bp_redirect_redirection[$key][login_type]"; ?>' id= '<?php echo "bp_redirect_redirection_".$key."_login_type_referer"; ?>' value="referer" type="radio" class="bp_redi_login_type" <?php if( isset( $login_type_val) &&  $login_type_val == "referer" ) { echo "checked = 'checked'"; } ?>> 
													<label for="bp_redirect_redirection_administrator_login_type_referer"><?php _e('BuddyPress Component', BP_REDIRECT_DOMAIN ); ?></label>													
													<div class="check">
														<div class="inside"></div>
													</div>
													<div class="bpr_tooltip">
  														<span class="bpr_tooltiptext">bpr tooltip text</span>
													</div>
													<select name='<?php echo "bp_redirect_redirection[$key][login_component]"; ?>'>
													<?php 	if( bp_is_active( 'members' )) { ?>
																<option value= "<?php echo 'profile'; ?>" <?php if( $login_component == 'profile' ) { echo "selected = 'selected'"; } ?> ><?php _e( 'Profile', BP_REDIRECT_DOMAIN ); ?>            			
																</option>
													<?php	}
														$bp_pages = bp_core_get_directory_page_ids();
														$pages = get_pages( array('include' => $bp_pages)); 
															foreach ( $pages as $page ) {
																$option = '<option value="' . get_page_link( $page->ID ) . '">';
																$option .= $page->post_title;
																$option .= '</option>';
																echo $option; 
															}																									    
												  	?> 
													</select>
													<p class="description"><?php _e( "Check this option to send the user to the component page when they will visit after log in.", BP_REDIRECT_DOMAIN ); ?></p>
													<br> 
												<?php } ?>
												<input name='<?php echo "bp_redirect_redirection[$key][login_type]"; ?>' id= '<?php echo "bp_redirect_redirection_".$key."_login_type_wp_pages"; ?>' value="wp_pages" type="radio" class="bp_redi_login_type" <?php if( isset( $login_type_val) &&  $login_type_val == "wp_pages" ) { echo "checked = 'checked'"; } ?>> 
												<label for="bp_redirect_redirection_administrator_login_type_wp_pages"><?php _e('WP Pages', BP_REDIRECT_DOMAIN ); ?></label>							
												<select name='<?php echo "bp_redirect_redirection[$key][login_wp_pages]"; ?>'>	
										        <?php   $pages = get_pages( array('include' => $wp_pages_ids)); 
												 	foreach ( $pages as $page ) {
													    $option = '<option value="' . get_page_link( $page->ID ) . '">';
													    $option .= $page->post_title;
													    $option .= '</option>';
													    echo $option; 
													} ?>
								           		</select>
												<p class="description"><?php _e( "Check this option to send the user to the WordPress page when they will visit after log in.", BP_REDIRECT_DOMAIN ); ?></p>
									           	<br>								           		
												<input name='<?php echo "bp_redirect_redirection[$key][login_type]"; ?>' id='<?php echo "bp_redirect_redirection_".$key."login_type_custom"; ?>' value="custom" type="radio" class="bp_redi_login_type" <?php if( isset( $login_type_val) &&  $login_type_val == "custom" ) { echo "checked = 'checked'"; } ?>>
												<label for="bp_redirect_redirection_login_type_custom"><?php _e('Custom URL', BP_REDIRECT_DOMAIN ); ?></label>
												<input name='<?php echo "bp_redirect_redirection[$key][login_url]"; ?>' id='<?php echo "bp_redirect_redirection_".$key."_login_url"; ?>' value="<?php if( !empty( $login_url ) ){ _e( $login_url , BP_REDIRECT_DOMAIN ); } ?>" class="regular-text" type="text">			
												<p class="description"><?php _e( 'Check this option to send the user to a custom location after login.', BP_REDIRECT_DOMAIN ); ?></p>
											</td>
										</tr>
									</tbody>
								</table>
						    </p>
					  	</div>	
					</div>  			
				<?php } ?>
				</div> 				
			</form>
			<p>
				<button id="bp-redirect-settings-submit" class="button-primary" name="bp-redirect-settings-submit"><?php _e('Save Settings', BP_REDIRECT_DOMAIN ); ?></button><img src="<?php _e( $spinner_src, BP_REDIRECT_DOMAIN );?>" class="bp-redirect-settings-spinner" />
			</p>
		<?php }

		/**
		 * Registers BP Redirect settings
		 *
		 * This function is used for user roles.
		 *
		 * @since 1.0.0
		 * @author  Wbcom Designs
		 * @access public
		 */

		public function get_editable_roles() {
		    global $wp_roles;

		    $all_roles = $wp_roles->roles;
		    $editable_roles = apply_filters('editable_roles', $all_roles);

		    return $editable_roles;
		}

		/**
		 * Registers BP Redirect settings
		 *
		 * This is used because register_setting() isn't available until the "admin_init" hook.
		 *
		 * @since 1.0.0
		 * @author  Wbcom Designs
		 * @access public
		 */

		public function admin_init() {

			// Register setting
			register_setting( 'bp_redirect_settings', 'bp_redirect_settings',  array( $this, 'save_settings' ) );

			// Add sections
			add_settings_section( 'general',    __( 'General', BP_REDIRECT_DOMAIN ), '__return_false', $this->options_key );
		}
		
	}
	new BP_Redirect_admin();
}	
