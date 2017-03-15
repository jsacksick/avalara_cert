<?php

/**
 * @file
 * This file contains no working PHP code; it exists to provide additional
 * documentation for doxygen as well as to document hooks in the standard
 * Drupal manner.
 */

/**
 * Allows modules to alter the ajax commands returned by the submit certificate
 * ajax callback.
 *
 * @param array $commands
 *   The ajax commands array returned by avalara_cert_submit_certificate_ajax().
 * @param object $account
 *   The user object.
 *
 * @see avalara_cert_checkout_ajax().
 */
function avalara_cert_submit_certificate_ajax_alter(&$commands, $account) {

}
