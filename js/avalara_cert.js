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
    if ($(ajax.element).data('ship-zone')) {
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
        ship_zone: $(ajax.element).data('ship-zone'),
        upload: true
      };
      $.extend(initSettings, response.initSettings);
      GenCert.init($(response.selector).get(0), initSettings);
      GenCert.show();
      $(response.selector).dialog('open');
    }
  }

  Drupal.AvalaraCert.Modal.modal_dismiss = function(ajax, response, status) {
    $(response.selector).dialog.close();
  }

  $(function() {
    Drupal.ajax.prototype.commands.avalara_cert_modal_display = Drupal.AvalaraCert.Modal.modal_display;
    Drupal.ajax.prototype.commands.avalara_cert_modal_dismiss = Drupal.AvalaraCert.Modal.modal_dismiss;
  });

}(jQuery));
