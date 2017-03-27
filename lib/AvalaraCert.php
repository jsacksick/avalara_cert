<?php

/**
 * @file
 * Defines a class for consuming the Avalara CertCapture API.
 */

/**
 * Defines the AvalaraCert class.
 *
 * A modern PHP library would namespace its classes under a package name, which
 * in this case would mean using the Avalara namespace and instantiating new
 * objects of this class via:
 *
 * $avalara = new Avalara\AvalaraCert(...);
 *
 * Unfortunately, Drupal 7 does not support namespaces in its autoloader, as it
 * maintains compatibility with previous versions of PHP that did not support
 * namespaces. Thus this library does not currently use a namespace.
 */
class AvalaraCert {

  // Defines the production API url of the REST API V2.
  const BASE_URL = 'https://api.certcapture.com/v2/';

  // Define properties for storing API credentials.
  protected $apiKey;

  // Reference the logger callable.
  protected $logger;

  // Manage a single cURL handle used to submit API requests.
  protected $ch;

  // Stores the HTTP headers.
  protected $headers;

  /**
   * Initializes the API credential properties and cURL handle.
   *
   * @param string $api_key
   *   The API key that is used to authenticate against the API.
   * @param string $logger
   *   A callable used to log API request / response messages. Leave empty if
   *   logging is not needed.
   * @param array $headers
   *   Allow specifying additional HTTP headers that are going to be sent.
   */
  public function __construct($api_key, $logger = NULL, $headers = array()) {
    // Initialize the API credential properties.
    $this->apiKey = $api_key;
    $this->logger = $logger;
    $this->headers = array_merge($headers, array(
      'Authorization' => 'Basic ' . $api_key,
      'Content-Type' => 'application/json',
    ));

    // Initialize the cURL handle.
    $this->ch = curl_init();
    $this->setDefaultCurlOptions();
  }

  /**
   * Returns the HTTP headers.
   *
   * @return array
   *   The HTTP headers used when submitting API requests.
   */
  public function httpHeaders() {
    return $this->headers;
  }

  /**
   * Returns the object's API key.
   *
   * @return string
   *   The API key.
   */
  public function getApiKey() {
    return $this->apiKey;
  }

  /**
   * Closes the cURL handle when the object is destroyed.
   */
  public function __destruct() {
    if (is_resource($this->ch)) {
      curl_close($this->ch);
    }
  }

  /**
   * Sets the default cURL options.
   */
  public function setDefaultCurlOptions() {
    $headers = array();

    foreach ($this->httpHeaders() as $key => $value) {
      $headers[] = "$key: $value";
    }

    curl_setopt($this->ch, CURLOPT_HEADER, FALSE);
    curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, TRUE);
    curl_setopt($this->ch, CURLOPT_VERBOSE, FALSE);
    curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($this->ch, CURLOPT_TIMEOUT, 180);
  }

  /**
   * Send a message to the logger.
   *
   * @param string $message
   *   The message to log.
   * @param $variables
   *   Array of variables to replace in the message on display or
   *   NULL if message is already translated or not possible to
   *   translate.
   * @param int $severity
   *   The severity of the message; one of the following values:
   *   - WATCHDOG_EMERGENCY: Emergency, system is unusable.
   *   - WATCHDOG_ALERT: Alert, action must be taken immediately.
   *   - WATCHDOG_CRITICAL: Critical conditions.
   *   - WATCHDOG_ERROR: Error conditions.
   *   - WATCHDOG_WARNING: Warning conditions.
   *   - WATCHDOG_NOTICE: (default) Normal but significant conditions.
   *   - WATCHDOG_INFO: Informational messages.
   *   - WATCHDOG_DEBUG: Debug-level messages.
   *
   * @see http://www.faqs.org/rfcs/rfc3164.html
   */
  public function logMessage($message, $variables = array(), $severity = WATCHDOG_NOTICE) {
    if (is_callable($this->logger)) {
      call_user_func_array($this->logger, array('avalara_cert', $message, $variables, $severity));
    }
  }

  /**
   * Gets a Token.
   */
  public function getToken() {
    return $this->doRequest('POST', 'auth/get-token');
  }

  /**
   * Refresh a Token.
   */
  public function refreshToken() {
    return $this->doRequest('POST', 'auth/refresh-token');
  }

  /**
   * List all Customers.
   *
   * @param string[] $parameters
   *   An associative array of parameters.
   *
   * @return string[]
   *   The API response JSON converted to an associative array.
   */
  public function customersList($parameters = array()) {
    return $this->doRequest('GET', 'customers', $parameters);
  }

  /**
   * Create a customer.
   *
   * @param string[] $parameters
   *   An associative array of parameters containing at least the customer_code.
   *
   * @return string[]
   *   The API response JSON converted to an associative array.
   */
  public function customersCreate($parameters = array()) {
    return $this->doRequest('POST', 'customers', $parameters);
  }

  /**
   * Retrieve a customer.
   *
   * @param $customer_number
   *   The customer ID or customer_number.
   *
   * @return string[]
   *   The API response JSON converted to an associative array.
   */
  public function customersGet($customer_number) {
    $customer_number = rawurlencode($customer_number);
    return $this->doRequest('GET', 'customers/' . $customer_number);
  }

  /**
   * Update a customer.
   *
   * @param $customer_number
   *   The customer_number.
   * @param string[] $fields
   *   An associative array of fields to update.
   *
   * @return string[]
   *   The API response JSON converted to an associative array.
   */
  public function customersUpdate($customer_number, $fields) {
    $customer_number = rawurlencode($customer_number);
    return $this->doRequest('PUT', 'customers/' . $customer_number, $fields);
  }

  /**
   * Delete a customer.
   *
   * @param $customer_number
   *   The customer_number.
   *
   * @return string[]
   *   The API response JSON converted to an associative array.
   */
  public function customersDelete($customer_number) {
    $customer_number = rawurlencode($customer_number);
    return $this->doRequest('DELETE', 'customers/' . $customer_number);
  }

  /**
   * Retrieve the certificates for a given customer ID|number.
   *
   * @param $customer_number
   *   The customer ID or customer_number.
   *
   * @return string[]
   *   The API response JSON converted to an associative array.
   */
  public function customersGetCertificates($customer_number) {
    $customer_number = rawurlencode($customer_number);
    return $this->doRequest('GET', "customers/$customer_number/certificates");
  }

  /**
   * Retrieve the exempt reasons for a given customer ID|number.
   *
   * @param $customer_number
   *   The customer ID or customer_number.
   *
   * @return string[]
   *   The API response JSON converted to an associative array.
   */
  public function customersGetExemptReasons($customer_number) {
    $customer_number = rawurlencode($customer_number);
    return $this->doRequest('GET', "customers/$customer_number/exempt-reasons");
  }

  /**
   * Generate a customer number.
   *
   * @return string[]
   *   The API response JSON converted to an associative array.
   */
  public function customersGenerateNumber() {
    return $this->doRequest('POST', "customers/generate-customer-number");
  }

  /**
   * List all Certificates.
   *
   * @param string[] $parameters
   *   An associative array of parameters.
   *
   * @return string[]
   *   The API response JSON converted to an associative array.
   */
  public function certificatesList($parameters = array()) {
    return $this->doRequest('GET', 'certificates', $parameters);
  }

  /**
   * Create a Certificate.
   *
   * @param string[] $parameters
   *   An associative array of parameters containing at least the 'filename' or
   *   'pdf' (urlencoded base64 pdf) or 'pages' (an array of urlencoded base64
   *   images).
   *
   * @return string[]
   *   The API response JSON converted to an associative array.
   */
  public function certificatesCreate($parameters = array()) {
    return $this->doRequest('POST', 'certificates', $parameters);
  }

  /**
   * Retrieve a certificate.
   *
   * @param $certificate_id
   *   The number certificate id.
   *
   * @return string[]
   *   The API response JSON converted to an associative array.
   */
  public function certificatesGet($certificate_id) {
    return $this->doRequest('GET', 'certificates/' . $certificate_id);
  }

  /**
   * Retrieve the customers associated to a customer.
   *
   * @param $certificate_id
   *   The number certificate id.
   *
   * @return string[]
   *   The API response JSON converted to an associative array.
   */
  public function certificatesGetCustomers($certificate_id) {
    return $this->doRequest('GET', "certificates/$certificate_id/customers");
  }

  /**
   * Generate a PDF of a certificate.
   *
   * @param string $certificate_id
   *   Numeric certificate id.
   *
   * @return string[]
   *   The API response JSON converted to an associative array.
   */
  public function certificatesDownload($certificate_id) {
    return $this->doRequest('GET', "certificates/$certificate_id/download");
  }

  /**
   * Removes a certificate.
   *
   * @param string $certificate_id
   *   Numeric certificate id.
   *
   * @return string[]
   *   The API response JSON converted to an associative array.
   */
  public function certificatesDelete($certificate_id) {
    return $this->doRequest('DELETE', "certificates/$certificate_id");
  }

  /**
   * Upload a certificate pdf in urlencoded base64 format to create/update
   * current certificate file.
   *
   * @param string $certificate_id
   *   Numeric certificate id.
   * @param string[] $parameters
   *   An associative array containing the 'pdf' key (urlencoded base64 pdf).
   *
   * @return string[]
   *   The API response JSON converted to an associative array.
   */
  public function certificatesUploadPdf($certificate_id, $parameters) {
    return $this->doRequest('PUT', "certificates/$certificate_id/upload-pdf", $parameters);
  }

  /**
   * Upload certificate images by page in urlencoded base64 format to
   * create/update current certificate images on file.
   * The following image types are currently supported: JPEG, TIFF, PNG.
   *
   * @param string $certificate_id
   *   Numeric certificate id.
   * @param string[] $parameters
   *   An associative array containing the 'pages' key (urlencoded base64).
   *
   * @return string[]
   *   The API response JSON converted to an associative array.
   */
  public function certificatesUploadImages($certificate_id, $parameters) {
    return $this->doRequest('PUT', "certificates/$certificate_id/upload-images", $parameters);
  }

  /**
   * List all Exempt Reasons.
   *
   * @param string[] $parameters
   *   An associative array of parameters.
   *
   * @return string[]
   *   The API response JSON converted to an associative array.
   */
  public function exemptReasonsList($parameters = array()) {
    return $this->doRequest('GET', 'exempt-reasons', $parameters);
  }

  /**
   * Create an Exempt Reason.
   *
   * @param string[] $fields
   *   An associative array of $fields.
   *
   * @return string[]
   *   The API response JSON converted to an associative array.
   */
  public function exemptReasonsCreate($fields = array()) {
    return $this->doRequest('POST', 'exempt-reasons', $fields);
  }

  /**
   * Retrieve an Exempt Reason.
   *
   * @param $id
   *   The exempt reason id.
   *
   * @return string[]
   *   The API response JSON converted to an associative array.
   */
  public function exemptReasonsGet($id) {
    return $this->doRequest('GET', 'exempt-reasons/' . $id);
  }

  /**
   * Update an Exempt Reason.
   *
   * @param $id
   *   The exempt reason id.
   * @param string[] $fields
   *   An associative array of fields.
   *
   * @return string[]
   *   The API response JSON converted to an associative array.
   */
  public function exemptReasonsUpdate($id, $fields = array()) {
    return $this->doRequest('PUT', 'exempt-reasons/' . $id, $fields);
  }

  /**
   * Delete an Exempt Reason.
   *
   * @param $id
   *   The exempt reason id.
   *
   * @return string[]
   *   The API response JSON converted to an associative array.
   */
  public function exemptReasonsDelete($id) {
    return $this->doRequest('DELETE', 'exempt-reasons/' . $id);
  }

  /**
   * Converts an array of query parameters into a string for use in a URL.
   *
   * @param string[] $parameters
   *   An associative array of query parameters to convert to a string.
   * @param string $parent
   *    Internal use only. Used to build the $pairs array key for nested items.
   *
   * @return string
   *   The query string ready for use in a URL.
   *
   * @see https://api.drupal.org/api/drupal/includes%21common.inc/function/drupal_http_build_query/7
   */
  protected function buildQueryString($parameters, $parent = '') {
    $pairs= array();

    foreach ($parameters as $key => $value) {
      $key = ($parent ? $parent . '[' . rawurlencode($key) . ']' : rawurlencode($key));

      // Recurse into children.
      if (is_array($value)) {
        $pairs[] = $this->buildQueryString($value, $key);
      }
      // If a query parameter value is NULL, only append its key.
      elseif (!isset($value)) {
        $pairs[] = $key;
      }
      else {
        // For better readability of paths in query strings, we decode slashes.
        $pairs[] = $key . '=' . str_replace('%2F', '/', rawurlencode($value));
      }
    }

    return implode('&', $pairs);
  }

  /**
   * Performs a request.
   *
   * @param string $method
   *   The HTTP method to use. One of: 'GET', 'POST', 'PUT', 'DELETE'.
   * @param string $path
   *   The remote path. The base URL will be automatically appended.
   * @param string[] $parameters
   *   An array of fields to include with the request. Optional.
   *
   * @throws AvalaraCertAuthenticationException if the request fails authentication.
   * @throws AvalaraCertHttpServerErrorException if the response status code is 5xx.
   * @throws AvalaraCertHttpClientErrorException if the response status code is 4xx.
   * @throws AvalaraCertHttpRedirectionException if the response status code is 3xx.
   * @throws AvalaraCertHttpInvalidResponseJsonException if the response is not valid JSON.
   *
   * @return string[]
   *   The API response JSON converted to an associative array.
   */
  protected function doRequest($method, $path, array $parameters = array()) {
    $url = self::BASE_URL . $path;

    if (!empty($parameters)) {
      // In case of a GET request, append the parameters to the query string.
      if ($method == 'GET') {
        $url .= (strpos($url, '?') !== FALSE ? '&' : '?') . $this->buildQueryString($parameters);
      }
      else {
        // JSON encode the fields and set them to the request body.
        $parameters = json_encode($parameters);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $parameters);
      }
    }

    curl_setopt($this->ch, CURLOPT_URL, $url);
    curl_setopt($this->ch, CURLINFO_HEADER_OUT, TRUE);
    curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $method);
    $response = curl_exec($this->ch);
    $response_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);

    // Log information about the request.
    $this->logMessage('Request info: !url !headers !response !meta', array(
      '!url' => "<pre>URL : $method $url</pre>",
      '!headers' => "<pre>Request Headers:\n" . var_export(curl_getinfo($this->ch, CURLINFO_HEADER_OUT), TRUE) . '</pre>',
      '!response' => "<pre>Response:\n" . var_export($response, TRUE) . '</pre>',
      '!meta' => "<pre>Response Meta:\n" . var_export(curl_getinfo($this->ch), TRUE) . '</pre>',
    ));

    // The CertCapture API does uses a 401 HTTP status code when authentication
    // fails.
    if ($response_code == 401) {
      // Throw an exception indicating authentication failed.
      $message = 'Authentication failed with API key ' . $this->getApiKey() . '.';
      throw new AvalaraCertAuthenticationException($message);
    }
    elseif ($response_code >= 500) {
      // Throw an exception indicating a server error.
      throw new AvalaraCertHttpServerErrorException('', $response_code);
    }
    elseif ($response_code >= 400) {
      // Throw an exception indicating a client error.
      throw new AvalaraCertHttpClientErrorException('', $response_code);
    }
    elseif ($response_code >= 300) {
      // Throw an exception indicating a redirection that this library is not
      // going to automatically follow.
      throw new AvalaraCertHttpRedirectionException('', $response_code);
    }

    // Attempt to convert the response body to an associative array.
    try {
      $json = json_decode($response, TRUE);
    }
    catch (\Exception $e) {
      throw new AvalaraCertHttpInvalidResponseJsonException('The API response string could not be parsed as JSON.');
    }

    return $json;
  }

}
