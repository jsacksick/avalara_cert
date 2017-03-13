<?php

/**
 * Page callback for listing tax exemption certificates for the current user.
 */
function commerce_avalara_cert_user_page($account) {
  $account_wrapper = entity_metadata_wrapper('user', $account);
  $empty_message = t('No tax certificates could be found.');
  $customer_code = '';

  // Check if the customer code is not empty.
  if (isset($account_wrapper->commerce_avalara_customer_code)) {
    $customer_code = $account_wrapper->commerce_avalara_customer_code->value();
  }

  // If the customer code is not configured, display an empty message.
  if (empty($customer_code)) {
    return $empty_message;
  }

  return array(
    '#theme' => 'commerce_avalara_cert_user_certificates',
    '#customer_code' => $customer_code,
  );
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
