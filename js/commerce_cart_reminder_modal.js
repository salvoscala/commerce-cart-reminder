(function ($, Drupal, drupalSettings) {

  'use strict';

  Drupal.behaviors.commerce_cart_reminder = {
    attach:function (context) {
      //new Date($.now());
      // This show the modal only once.
      $(document, context).once('commerce_cart_reminder').each( function() {

        showCartReminderModal(context);

        updateCookieCount()
      });
    }
  }

  function showCartReminderModal(context) {
    var modal_title = drupalSettings['commerce_cart_reminder']['modal_title'];
    var modal_content = drupalSettings['commerce_cart_reminder']['modal_content'];
    var view_cart_text = drupalSettings['commerce_cart_reminder']['view_cart_text'];
    var close_modal_text = drupalSettings['commerce_cart_reminder']['close_modal_text'];

    var content = $(modal_content).appendTo('body');
    if (modal_content.length > 0) {
      var reminderCartModal = Drupal.dialog(content, {
        title: modal_title,
        dialogClass: 'commerce-cart-reminder-dialog',
        width: 745,
        height: 375,
        maxWidth: '95%',
        autoResize: true,
        resizable: false,

        close: function (event) {
          $(event.target).remove();
        },
        buttons: [
          {
            text: Drupal.t(close_modal_text),
            class: 'btn btn-default',
            click: function () {
              reminderCartModal.close();
            }
          },
          {
            text: Drupal.t(view_cart_text),
            class: 'btn btn-success',
            click: function () {
              window.location.href = '/cart';
              reminderCartModal.close();
            }
          }
        ]
      });
      reminderCartModal.showModal();
    }
  }


  function updateCookieCount() {
    var cookie = getCookie("commerce-cart-reminder");
    console.log(cookie);
    if (cookie != undefined) {
      var jsonCookie = JSON.parse(cookie);
      for(var i = 0; i < jsonCookie.length; i++) {
        jsonCookie[i].created = Math.floor(Date.now() / 1000); // timestamp in seconds
        jsonCookie[i].count = parseInt(jsonCookie[i].count)+1;
      }

      $.cookie("commerce-cart-reminder", JSON.stringify(jsonCookie), { path: '/' })
    }
  }

  function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
  }
})(jQuery, Drupal, drupalSettings);
