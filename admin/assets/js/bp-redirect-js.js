jQuery(document).ready(function($) {
   jQuery( "#accordion" )
      .accordion({
  	    collapsible: true,
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

        jQuery( "#bp-redirect-settings-submit" ).live( 'click', function(){
        	jQuery( ".bp-redirect-settings-spinner" ).show();
        	var form = jQuery( "#bp-redirect-settings-form" ).serialize();          
			jQuery.post(
	            ajaxurl,
	            {
	            'action'            : 'bp_redirect_admin_settings',
	            'form'      		: form
	            },
	            function () {
	            	jQuery( ".bp-redirect-settings-spinner" ).hide();
	          		jQuery( "#bpredirect-settings_updated" ).show();
	            }
	        );  
        });
});