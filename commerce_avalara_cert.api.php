<?php

/**
 * @file
 * This file contains no working PHP code; it exists to provide additional
 * documentation for doxygen as well as to document hooks in the standard
 * Drupal manner.
 */

/**
 * Allows modules to alter the ajax commands returned by the checkout ajax
 * callback.
 *
 * @param array $commands
 *   The ajax commands array returned by commerce_avalara_cert_checkout_ajax().
 * @param object $account
 *   The user object.
 *
 * @see commerce_avalara_cert_checkout_ajax().
 */
function hook_commerce_avalara_cert_checkout_ajax_commands_alter(&$commands, $account) {

}
