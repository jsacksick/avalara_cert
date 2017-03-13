/**
 * @file
 * Handles
 */

(function($) {
  var dialogSelector = '#commerce-avalara-cert-user-dialog';

  Drupal.behaviors.commerceAvalaraCertUser = {
    attach: function(context, settings) {
      $(dialogSelector).dialog({
        autoOpen: false,
        height: 600,
        width: 800
      });
      $('.commerce-avalara-cert-renew').click(function (e) {
        e.preventDefault();
        GenCert.init($(dialogSelector).get(0), {
          ship_zone: $(this).data('state'),
          customer_number: settings.commerce_avalara_cert.customer_number,
          upload: true
        });
        GenCert.show();
        $(dialogSelector).dialog('open');
      });
    }
  };

}(jQuery));
