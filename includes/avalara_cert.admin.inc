<?php

/**
 * Builds the Avalara CertCapture settings form.
 */
function avalara_cert_settings_form($form, &$form_state) {
  form_load_include($form_state, 'inc', 'avalara_cert', 'includes/avalara_cert.admin');

  $form['credentials'] = array(
    '#title' => t('API Credentials'),
    '#type' => 'fieldset',
  );

  $form['credentials']['avalara_cert_username'] = array(
    '#title' => t('Username'),
    '#type' => 'textfield',
    '#default_value' => variable_get('avalara_cert_username', ''),
    '#required' => TRUE,
  );

  $form['credentials']['avalara_cert_password'] = array(
    '#title' => t('Password'),
    '#type' => 'textfield',
    '#default_value' => variable_get('avalara_cert_password', ''),
    '#required' => TRUE,
  );

  $form['avalara_cert_client_id'] = array(
    '#title' => t('Client ID'),
    '#type' => 'textfield',
    '#default_value' => variable_get('avalara_cert_client_id', ''),
    '#required' => TRUE,
  );

  $form['avalara_cert_client_id'] = array(
    '#title' => t('Client ID'),
    '#description' => t('Your CertCapture Client ID, which can be found in your CertCapture account under Company Settings > Company Details >  Client Information.'),
    '#type' => 'textfield',
    '#default_value' => variable_get('avalara_cert_client_id', ''),
    '#required' => TRUE,
  );

  $form['avalara_cert_registration_key'] = array(
    '#title' => t('Registration Key'),
    '#description' => t('Your CertCapture Registration Key, which can be found in your CertCapture account under Company Settings > Company Details > Webportal Registration.'),
    '#type' => 'textfield',
    '#default_value' => variable_get('avalara_cert_registration_key', ''),
  );

  $form['avalara_cert_logging'] = array(
    '#title' => t('Enable logging?'),
    '#description' => t('Enables detailed logging.'),
    '#type' => 'checkbox',
    '#default_value' => variable_get('avalara_cert_logging', FALSE),
  );

  return system_settings_form($form);
}
