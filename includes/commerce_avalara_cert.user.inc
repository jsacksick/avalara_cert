<?php

/**
 * Page callback for listing tax exemption certificates for the current user.
 */
function commerce_avalara_cert_user_page($account) {
  $build = array(
    '#theme' => 'table',
    '#header' => array(
      t('State'),
      t('Signed date'),
      t('Expiration date'),
      t('Status'),
      t('View'),
    ),
    '#rows' => array(),
  );
  $account_wrapper = entity_metadata_wrapper('user', $account);
  $empty_message = t('No tax certificates could be found.');
  $customer_code = '';

  // Check if the customer code is not empty.
  if (isset($account_wrapper->commerce_avalara_customer_code)) {
    $customer_code = $account_wrapper->commerce_avalara_customer_code->value();
  }

  // If the customer code is not configured, display an empty message.
  if (empty($customer_code)) {
    $build['#empty'] = $empty_message;
    return $build;
  }

  try {
    $avalara = commerce_avalara_cert_object();
    $exempt_reasons = $avalara->customersGetExemptReasons($customer_code);

    foreach ($exempt_reasons as $exempt_reason) {
      $tax_code_name = $exempt_reason['actual_tax_code']['name'];

      $status = t('Valid');
      // Logic stolen from the Avalara code sample.
      if (substr($tax_code_name, 0, 8) == 'EXPOSURE' || substr($tax_code_name, 0, 7) == 'TAXABLE') {
        $status = t('Exposure');
      }

      $build['#rows'][] = array(
        $exempt_reason['exposure_zone']['name'],
        $exempt_reason['certificate']['signed_date'],
        $exempt_reason['certificate']['expiration_date'],
        $status,
        l('View', 'avalara-cert/' . $exempt_reason['certificate']['id'] . '/download'),
      );
    }
  }
  catch (Exception $e) {
  }

  if (empty($build['#rows'])) {
    $build['#empty'] = t('No tax certificates could be found.');
  }

  return $build;
}

/**
 * Page callback: Download a tax certificate.
 */
function commerce_avalara_cert_download($certificate_id) {
  try {
    $avalara = commerce_avalara_cert_object();
    $result = $avalara->certificatesDownload($certificate_id);

    if (!isset($result['download_link'])) {
      return MENU_NOT_FOUND;
    }

    return drupal_goto($result['download_link']);
  }
  catch (Exception $e) {
    return MENU_NOT_FOUND;
  }
}
