jQuery(document).ready(function($) {
   jQuery( "#accordion" )
      .accordion({
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

        jQuery( "#bp-redirect-settings-submit" ).live( 'click', function(){
        	jQuery( ".bp-redirect-settings-spinner" ).show();
          var sequence = [];
        	var form = jQuery( "#bp-redirect-settings-form" ).serialize(); 
          jQuery( "#bp-redirect-settings-form .group" ).each(function () {
              if(jQuery( this ).attr('id').trim()!= '') {         
                  sequence.push( jQuery( this ).attr('id') );
              }
          }); 
          roleSequence = sequence.join();        
    			jQuery.post(
    	            ajaxurl,
    	            {
    	            'action'        : 'bp_redirect_admin_settings',
    	            'form'      		: form,
                  'sequence'      : roleSequence
    	            },
    	            function () {
    	            	jQuery( ".bp-redirect-settings-spinner" ).hide();
    	          		jQuery( "#bpredirect-settings_updated" ).show();
    	            }
    	        );  
          });
});