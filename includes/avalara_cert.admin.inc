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

  $fields = array(
    'avalara_cert_username' => array(
      'title' => t('Username'),
      'description' => t('The username that is used to authenticate against the API.'),
    ),
    'avalara_cert_password' => array(
      'title' => t('Password'),
      'description' => t('The password that is used to authenticate against the API.'),
    ),
  );

  $display_warning = FALSE;
  foreach ($fields as $variable_name => $conf) {
    $variable_value = variable_get($variable_name, '');
    // Determine if the settings are in the database; if they are not but have
    // values set, we disable the form fields so the values will not be saved to
    // the database on submission.
    $variable_in_db = unserialize(db_query("SELECT value FROM {variable} WHERE name = :name", array(':name' => $variable_name))->fetchField());

    if (!empty($variable_value) && empty($variable_in_db)) {
      $form['credentials'][$variable_name] = array(
        '#type' => 'item',
        '#title' => $conf['title'],
        '#description' => $conf['description'],
        '#markup' => check_plain($variable_value),
      );
    }
    else {
      $form['credentials'][$variable_name] = array(
        '#type' => 'textfield',
        '#title' => $conf['title'],
        '#description' => $conf['description'],
        '#default_value' => $variable_value,
        '#required' => TRUE,
      );
    }

    // Show a message informing the user of best practices to not store API
    // credentials in the database if necessary.
    if (empty($variable_value) || (!empty($variable_value) && $variable_in_db)) {
      $display_warning = TRUE;
    }
  }

  // Show a message informing the user of best practices to not store API
  // credentials in the database if necessary.
  // Avoid to repeat the message during when the credentials are validated.
  if ($display_warning) {
    $description = t('It is best practice to store API credentials outside of the database and your source code repository.');
    $description .= ' ' . t('Consider setting the credential variables as server environment variables and bringing them into your Drupal configuration via the $conf array in your settings.php file instead.');
    drupal_set_message($description, 'warning');
  }

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
