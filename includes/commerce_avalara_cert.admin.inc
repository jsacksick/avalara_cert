<?php

/**
 * Builds the Avalara CertCapture settings form.
 */
function commerce_avalara_cert_settings_form($form, &$form_state) {
  form_load_include($form_state, 'inc', 'commerce_avalara_cert', 'includes/commerce_avalara_cert.admin');

  $form['credentials'] = array(
    '#title' => t('API Credentials'),
    '#type' => 'fieldset',
  );

  $form['credentials']['commerce_avalara_cert_username'] = array(
    '#title' => t('Username'),
    '#type' => 'textfield',
    '#default_value' => variable_get('commerce_avalara_cert_username', ''),
    '#required' => TRUE,
  );

  $form['credentials']['commerce_avalara_cert_password'] = array(
    '#title' => t('Password'),
    '#type' => 'textfield',
    '#default_value' => variable_get('commerce_avalara_cert_password', ''),
    '#required' => TRUE,
  );

  $form['commerce_avalara_cert_client_id'] = array(
    '#title' => t('Client ID'),
    '#type' => 'textfield',
    '#default_value' => variable_get('commerce_avalara_cert_client_id', ''),
    '#required' => TRUE,
  );

  $form['commerce_avalara_cert_logging'] = array(
    '#title' => t('Enable logging?'),
    '#description' => t('Enables detailed logging.'),
    '#type' => 'checkbox',
    '#default_value' => variable_get('commerce_avalara_cert_logging', FALSE),
  );

  return system_settings_form($form);
}
