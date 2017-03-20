/**
 * @file
 * Opens the submit a certificate link into a JQuery UI dialog.
 */

(function($) {
  // Make sure our objects are defined.
  Drupal.AvalaraCert = Drupal.AvalaraCert || {};
  Drupal.AvalaraCert.Modal = Drupal.AvalaraCert.Modal || {};

  /**
   * AJAX responder command to place HTML within the modal.
   */
  Drupal.AvalaraCert.Modal.modal_display = function(ajax, response, status) {
    // Check if the data-ship-zone attribute is set.
    $('body:not(:has(' + response.selector + '))').prepend('<div id="' + response.dialogID + '"></div>');

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
      upload: true
    };
    $.extend(initSettings, response.initSettings);
    // If the ship_zone is not present in the initSettings.
    if (initSettings.ship_zone == '' && $(ajax.element).data('ship-zone')) {
      initSettings.ship_zone = $(ajax.element).data('ship-zone');
    }
    GenCert.init($(response.selector).get(0), initSettings);
    GenCert.show();
    $(response.selector).dialog('open');
  }

  Drupal.AvalaraCert.Modal.modal_dismiss = function(ajax, response, status) {
    $(response.selector).dialog.close();
  }

  $(function() {
    Drupal.ajax.prototype.commands.avalara_cert_modal_display = Drupal.AvalaraCert.Modal.modal_display;
    Drupal.ajax.prototype.commands.avalara_cert_modal_dismiss = Drupal.AvalaraCert.Modal.modal_dismiss;
  });

}(jQuery));
