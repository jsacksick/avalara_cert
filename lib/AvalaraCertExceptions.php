<?php

/**
 * @file
 * Defines exception classes for use by the AvalaraCert class.
 */

class AvalaraCertAuthenticationException extends \Exception {}

/**
 * Defines a base class for HTTP response related exceptions.
 *
 * If an HTTP exception is thrown, the exception's code SHOULD correspond to the
 * status code of the response that generated the exception.
 */
class AvalaraCertHttpException extends \Exception {
  /**
   * Supplies a default message if none was supplied.
   */
  public function __construct($message = '', $code = 0, \Exception $previous = NULL) {
    if (empty($message)) {
      $message = '';

      if (!empty($code)) {
        $message .= $code . ' ';
      }

      $message .= 'HTTP response status code not OK.';
    }

    parent::__construct($message, $code, $previous);
  }
}

class AvalaraCertHttpServerErrorException extends AvalaraCertHttpException {}
class AvalaraCertHttpClientErrorException extends AvalaraCertHttpException {}
class AvalaraCertHttpRedirectionException extends AvalaraCertHttpException {}

class AvalaraCertHttpInvalidResponseJsonException extends \Exception {}
