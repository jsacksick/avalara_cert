/**
 * @file
 * Opens the submit a certificate link into a JQuery UI dialog.
 */

(function($) {
  // Make sure our objects are defined.
  Drupal.CommerceAvalaraCert = Drupal.CommerceAvalaraCert || {};
  Drupal.CommerceAvalaraCert.Modal = Drupal.CommerceAvalaraCert.Modal || {};

  /**
   * AJAX responder command to place HTML within the modal.
   */
  Drupal.CommerceAvalaraCert.Modal.modal_display = function(ajax, response, status) {
    var settings = Drupal.settings.commerce_avalara_cert;
    $(response.selector).dialog({
      height: 500,
      width: 800,
      modal: true,
      title: Drupal.t('Submit a certificate'),
      resizable: false,
      draggable: false,
      dialogClass: 'no-close',
      closeOnEscape: false
    });
    var initSettings = {
      ship_zone: settings.shipZone,
      upload: true
    };
    $.extend(initSettings, response.initSettings);
    GenCert.init($(response.selector).get(0), initSettings);
    GenCert.show();
    $(response.selector).dialog('open');
  }

  Drupal.CommerceAvalaraCert.Modalmodal_dismiss = function(ajax, response, status) {
    $(response.selector).dialog.close();
  }

  $(function() {
    Drupal.ajax.prototype.commands.commerce_avalara_cert_checkout_modal_display = Drupal.CommerceAvalaraCert.Modal.modal_display;
    Drupal.ajax.prototype.commands.commerce_avalara_cert_checkout_modal_dismiss = Drupal.CommerceAvalaraCert.Modal.modal_dismiss;
  });

}(jQuery));
