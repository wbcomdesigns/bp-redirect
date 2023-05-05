jQuery(document).ready(function ($) {
  jQuery(".bp_redi_login_type").on("click", function () {
    var id = jQuery(this).attr("id");
    var type = id.split("_").pop();

    if (type == "referer") {
      jQuery(this)
        .parent()
        .parent()
        .children()
        .find(".bpr-login-custom")
        .removeClass("bpr_show");
      jQuery(this)
        .parent()
        .parent()
        .children()
        .find(".bpr-login-component")
        .addClass("bpr_show");
      jQuery(this)
        .parent()
        .parent()
        .children()
        .find(".bpr-login-cutom-component")
        .addClass("bpr_show");
    } else if (type == "none") {
      jQuery(this)
        .parent()
        .parent()
        .children()
        .find(".bpr-login-custom")
        .removeClass("bpr_show");
      jQuery(this)
        .parent()
        .parent()
        .children()
        .find(".bpr-login-component")
        .removeClass("bpr_show");
      jQuery(this)
        .parent()
        .parent()
        .children()
        .find(".bpr-login-cutom-component")
        .removeClass("bpr_show");
    } else {
      jQuery(this)
        .parent()
        .parent()
        .children()
        .find(".bpr-login-component")
        .removeClass("bpr_show");
      jQuery(this)
        .parent()
        .parent()
        .children()
        .find(".bpr-login-custom")
        .addClass("bpr_show");
      jQuery(this)
        .parent()
        .parent()
        .children()
        .find(".bpr-login-cutom-component")
        .addClass("bpr_show");
    }
  });

  jQuery(".bp_redi_logout_type").on("click", function () {
    var id = jQuery(this).attr("id");
    var type = id.split("_").pop();
    if (type == "referer") {
      jQuery(this)
        .parent()
        .parent()
        .children()
        .find(".bpr-logout-custom")
        .removeClass("bpr_show");
      jQuery(this)
        .parent()
        .parent()
        .children()
        .find(".bpr-logout-component")
        .addClass("bpr_show");
    } else if (type == "none") {
      jQuery(this)
        .parent()
        .parent()
        .children()
        .find(".bpr-logout-custom")
        .removeClass("bpr_show");
      jQuery(this)
        .parent()
        .parent()
        .children()
        .find(".bpr-logout-component")
        .removeClass("bpr_show");
    } else {
      jQuery(this)
        .parent()
        .parent()
        .children()
        .find(".bpr-logout-component")
        .removeClass("bpr_show");
      jQuery(this)
        .parent()
        .parent()
        .children()
        .find(".bpr-logout-custom")
        .addClass("bpr_show");
    }
  });

  jQuery("#bgr-login-accordion,#bgr-logout-accordion,#bgr-global-accordion")
    .accordion({
      heightStyle: "content",
      collapsible: true,
      option: "icons",
      icons: {
        header: "ui-icon-plus",
        activeHeader: "ui-icon-minus",
      },
      header: "> div > h3",
    })
    .sortable({
      axis: "y",
      handle: "h3",
      stop: function (event, ui) {
        // IE doesn't register the blur when sorting
        // so trigger focusout handlers to remove .ui-state-focus
        ui.item.children("h3").triggerHandler("focusout");

        // Refresh accordion to handle new order
        jQuery(this).accordion("refresh");
      },
    });

  jQuery("#bp-redirect-settings-submit").on("click", function () {
    var loginRoleSequence = [];
    var logoutRoleSequence = [];
    var global_settings_form = jQuery("#bpr-global-settings-form").serialize();
    var login_settings_form = jQuery("#bpr-login-settings-form").serialize();
    var logout_settings_form = jQuery("#bpr-logout-settings-form").serialize();
    var enable_disable_setting = "";
    var enable_disable_role_setting = "";
    if (jQuery('input[name="bp_enable_disable_member_checkbox"]').length) {
      enable_disable_setting = jQuery(
        'input[name="bp_enable_disable_member_checkbox"]'
      ).val();
    }
    if (jQuery('input[name="bp_enable_disable_role_checkbox"]').length) {
      enable_disable_role_setting = jQuery(
        'input[name="bp_enable_disable_role_checkbox"]'
      ).val();
    }
    jQuery("#bpr-login-settings-form .group").each(function () {
      if (jQuery(this).attr("id").trim() != "") {
        loginRoleSequence.push(jQuery(this).attr("id"));
      }
    });
    jQuery("#bpr-logout-settings-form .group").each(function () {
      if (jQuery(this).attr("id").trim() != "") {
        logoutRoleSequence.push(jQuery(this).attr("id"));
      }
    });
    loginRoleSequence = loginRoleSequence.join();
    logoutRoleSequence = logoutRoleSequence.join();
    jQuery(".bp-redirect-settings-spinner").show();
    jQuery.post(
      ajaxurl,
      {
        action: "bp_redirect_admin_settings",
        nonce: bp_redirect_ajax_nonce.nonce,
        global_details: global_settings_form,
        enable_disable_setting: enable_disable_setting,
        enable_disable_role_setting: enable_disable_role_setting,
        login_details: login_settings_form,
        logout_details: logout_settings_form,
        loginSequence: loginRoleSequence,
        logoutSequence: logoutRoleSequence,
      },
      function () {
        jQuery(".bp-redirect-settings-spinner").hide();
        jQuery("#bpredirect-settings_updated").show();
        jQuery("#bpredirect-settings_updated-footer").show();
        jQuery("#bpredirect-settings_updated-footer").addClass(
          "updated settings-error notice "
        );
      }
    );
  });


  jQuery("#bp-redirect-globel-settings-submit").on("click", function () {
    var loginRoleSequence = [];
    var logoutRoleSequence = [];
    var global_settings_form = jQuery("#bpr-global-settings-form").serialize();
    var login_settings_form = jQuery("#bpr-login-settings-form-global").serialize();
    var logout_settings_form = jQuery("#bpr-logout-settings-form").serialize();
    var enable_disable_setting = "";
    var enable_disable_role_setting = "";
    if (jQuery('input[name="bp_enable_disable_member_checkbox"]').length) {
      enable_disable_setting = jQuery(
        'input[name="bp_enable_disable_member_checkbox"]'
      ).val();
    }
    if (jQuery('input[name="bp_enable_disable_role_checkbox"]').length) {
      enable_disable_role_setting = jQuery(
        'input[name="bp_enable_disable_role_checkbox"]'
      ).val();
    }
    jQuery("#bpr-login-settings-form-global .group").each(function () {
      if (jQuery(this).attr("id").trim() != "") {
        loginRoleSequence.push(jQuery(this).attr("id"));
      }
    });
    jQuery("#bpr-logout-settings-form .group").each(function () {
      if (jQuery(this).attr("id").trim() != "") {
        logoutRoleSequence.push(jQuery(this).attr("id"));
      }
    });
    loginRoleSequence = loginRoleSequence.join();
    logoutRoleSequence = logoutRoleSequence.join();
    jQuery(".bp-redirect-settings-spinner").show();
    jQuery.post(
      ajaxurl,
      {
        action: "bp_redirect_admin_settings_global",
        nonce: bp_redirect_ajax_nonce.nonce,
        global_details: global_settings_form,
        enable_disable_setting: enable_disable_setting,
        enable_disable_role_setting: enable_disable_role_setting,
        login_details: login_settings_form,
        logout_details: logout_settings_form,
        loginSequence: loginRoleSequence,
        logoutSequence: logoutRoleSequence,
      },
      function () {
        jQuery(".bp-redirect-settings-spinner").hide();
        jQuery("#bpredirect-settings_updated").show();
        jQuery("#bpredirect-settings_updated-footer").show();
        jQuery("#bpredirect-settings_updated-footer").addClass(
          "updated settings-error notice "
        );
      }
    );
  });

});

jQuery(document).ready(function () {
  jQuery("#bp_red_enable_disable").on("click", function () {
    if (jQuery(this).prop("checked") == true) {
      jQuery(".bpr-row").show(500);
      jQuery('input[name="bp_enable_disable_member_checkbox"]').val("yes");
      jQuery('input[name="bp_enable_disable_role_checkbox"]').val("yes");
    } else {
      jQuery(this).val("no");
      jQuery(".bpr-row").hide(500);
      jQuery('input[name="bp_enable_disable_member_checkbox"]').val("no");
      jQuery('input[name="bp_enable_disable_role_checkbox"]').val("no");
    }
  });
});
jQuery(document).ready(function () {
  jQuery("#bp_role_enable_disable").on("click", function () {
    if (jQuery(this).prop("checked") == true) {
      jQuery(".bpr-row").show(500);
      jQuery('input[name="bp_enable_disable_member_checkbox"]').val("yes");
      jQuery('input[name="bp_enable_disable_role_checkbox"]').val("yes");
    } else {
      jQuery(".bpr-row").hide(500);
      jQuery('input[name="bp_enable_disable_member_checkbox"]').val("no");
      jQuery('input[name="bp_enable_disable_role_checkbox"]').val("no");
    }
  });
});

(function ($) {
  "use strict";

  // Support tab
  $(document).ready(function () {
    var acc = document.getElementsByClassName("bpr-accordion");
    var i;
    for (i = 0; i < acc.length; i++) {
      acc[i].onclick = function () {
        this.classList.toggle("active");
        var panel = this.nextElementSibling;
        if (panel.style.maxHeight) {
          panel.style.maxHeight = null;
        } else {
          panel.style.maxHeight = panel.scrollHeight + "px";
        }
      };
    }
    $(document).on("click", ".bpr-accordion", function () {
      return false;
    });
  });
})(jQuery);
