jQuery(document).ready(function($) {
    jQuery( ".bp_redi_login_type" ).on( 'click', function(){
      var id = jQuery( this ). attr('id');
      var type = id.split('_').pop();
    
      if( type == 'referer' ) {
        jQuery( this ).parent().parent().children().find('.bpr-login-custom').removeClass('bpr_show');
        jQuery( this ).parent().parent().children().find('.bpr-login-component').addClass('bpr_show');
      } else {
        jQuery( this ).parent().parent().children().find('.bpr-login-component').removeClass('bpr_show');
        jQuery( this ).parent().parent().children().find('.bpr-login-custom').addClass('bpr_show');
      }
    });

    jQuery( ".bp_redi_logout_type" ).on( 'click', function(){
      var id = jQuery( this ). attr('id');
      var type = id.split('_').pop();      
      if( type == 'referer' ) {
        jQuery( this ).parent().parent().children().find('.bpr-logout-custom').removeClass('bpr_show');
        jQuery( this ).parent().parent().children().find('.bpr-logout-component').addClass('bpr_show');
      } else {
        jQuery( this ).parent().parent().children().find('.bpr-logout-component').removeClass('bpr_show');
        jQuery( this ).parent().parent().children().find('.bpr-logout-custom').addClass('bpr_show');
      }
    });
     
    jQuery( "#bgr-login-accordion,#bgr-logout-accordion" )
      .accordion({
        heightStyle: "content",
  	    collapsible: true,
        option: "icons",
  	    icons: { "header": "ui-icon-plus", "activeHeader": "ui-icon-minus" },
        header: "> div > h3"
      })
      .sortable({
        axis: "y",
        handle: "h3",
        stop: function( event, ui ) {
          // IE doesn't register the blur when sorting
          // so trigger focusout handlers to remove .ui-state-focus
          ui.item.children( "h3" ).triggerHandler( "focusout" );

          // Refresh accordion to handle new order
          jQuery( this ).accordion( "refresh" );
        }
      });

      jQuery( "#bp-redirect-settings-submit" ).on( 'click', function(){
      	
        var loginRoleSequence = [];
        var logoutRoleSequence = [];
      	var login_settings_form = jQuery( "#bpr-login-settings-form" ).serialize();
        var logout_settings_form = jQuery( "#bpr-logout-settings-form" ).serialize(); 
        jQuery( "#bpr-login-settings-form .group" ).each(function () {
            if(jQuery( this ).attr('id').trim()!= '') {         
                loginRoleSequence.push( jQuery( this ).attr('id') );
            }
        }); 
        jQuery( "#bpr-logout-settings-form .group" ).each(function () {
            if(jQuery( this ).attr('id').trim()!= '') {         
                logoutRoleSequence.push( jQuery( this ).attr('id') );
            }
        }); 
        loginRoleSequence = loginRoleSequence.join();  
        logoutRoleSequence = logoutRoleSequence.join();
        jQuery( ".bp-redirect-settings-spinner" ).show();
  			jQuery.post(
  	            ajaxurl,
  	            {
  	            'action'         : 'bp_redirect_admin_settings',
  	            'login_details'  : login_settings_form,
                'logout_details' : logout_settings_form,
                'loginSequence'  : loginRoleSequence,
                'logoutSequence' : logoutRoleSequence
  	            },
  	            function () {
  	            	jQuery( ".bp-redirect-settings-spinner" ).hide();
  	          		jQuery( "#bpredirect-settings_updated" ).show();
  	            }
  	        );  
      });
});