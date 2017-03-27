<?php

/**
 * Page callback for listing tax exemption certificates for the current user.
 */
function avalara_cert_user_page($account) {
  $account_wrapper = entity_metadata_wrapper('user', $account);
  $empty_message = t('No tax certificates could be found.');
  $customer_number = '';

  // Check if the customer code is not empty.
  if (isset($account_wrapper->avalara_customer_number)) {
    $customer_number = $account_wrapper->avalara_customer_number->value();
  }

  // If the customer code is not configured, display an empty message.
  if (empty($customer_number)) {
    return $empty_message;
  }

  return array(
    '#theme' => 'avalara_cert_user_certificates',
    '#customer_number' => $customer_number,
  );
}

/**
 * Page callback: Download a tax certificate.
 */
function avalara_cert_download($certificate_id) {
  try {
    $avalara = avalara_cert_object();
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

/**
 * Page callback: Removes a tax certificate.
 */
function avalara_cert_delete($certificate_id) {
  if (user_is_anonymous()) {
    return MENU_NOT_FOUND;
  }
  try {
    $avalara = avalara_cert_object();
    $result = $avalara->certificatesDelete($certificate_id);
    drupal_set_message(t('The tax certificate was successfully deleted.'));
  }
  catch (Exception $e) {
    drupal_set_message(t('Something went wrong while deleting the tax certificate', 'error'));
  }
  global $user;

  return drupal_goto("user/$user->uid/tax-certificates");
}
