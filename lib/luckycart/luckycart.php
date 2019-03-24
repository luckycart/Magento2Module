<?php
/*
 * Luckycart PHP Library V2
 * (c) Lucky Cart 2016
 * Author: Olivier Chappe
 * Version: 2.8
 */

/*
 * LuckyException class
 * Errors thrown by the library
 */
class LuckyException extends Exception {

};

/*
 * LuckyCart class
 * Class used to "summon" Lucky Cart scripts for integration within a page
 * It performs remote calls to the Lucky Cart servers using curl,
 * and therefore requires the curl PHP extension to be installed
 * @see http://php.net/manual/en/book.curl.php
 *
 */
class LuckyCart {

    /*
     * Constants:
     * API   : the remote adress of the API. Change this to hit the test server
     * SCRIPT: the source adress for the script
     */
    const VERSION = "2.8";
    // !!WARNING!! Do not forget the trailing '/'
    const API = 'https://api.luckycart.com/';
    const AUTH_HEADER = 'X-LuckyCart-Auth';

    /*
     * Private data
     * method: GET, PUT, POST or DELETE
     * url    : the url that curl will used for request
     * key    : the API key to be used
     * secret : the API secret (keep it safe)
     * token  : the token to be inserted within the script
     * params : parameters that will be sent with the request
     * sync   : used for time sync with the server
     * host   : the host for the API server
     */
    private $method;
    private $url;
    private $key;
    private $secret;
    private $params;
    private $sync;
    private $host;
    private $debug;

    /*
     * Constructor
     *
     * Create a LuckyCart object
     * @access public
     * @param {string} key
     * @param {string} secret
     */
    public function __construct($key, $secret, $host=self::API) {
        // Initialize data
        $this->key     = $key;
        $this->secret  = $secret;
        $this->timeout = 10; // default timeout
        $this->host    = $host;
        if (preg_match('/\/$/',$this->host)==0)
          $this->host .= '/';

        // use reset
        $this->reset();

    }

    /*
     * log
     *
     * Used to log info into the debug array
     * @access private
     * @param {string} str string to log
     */
    private function log($str) {
        if (!is_null($this->debug))
            array_push($this->debug, $str);
    }

    /*
     * version
     *
     * Display version string
     * @return {string} version
     */
    public function version() {
        return self::VERSION;
    }

    /*
     * getDebug
     *
     * Returns the debug info
     * @return {string} debug information on each line
     */
    public function getDebug($glue = '\n') {
        if (is_null($this->debug))
            return '';
        else
            return implode($glue,$this->debug);
    }

    /*
     * setTimeout
     *
     * set the timeout in seconds
     *
     * @param {int} timeout
     */
    public function setTimeout($timeout) {
       $this->timeout = $timeout;
    }

    /*
     * debug
     *
     * Allow/reset debugging
     *
     */
    public function debug() {
      $this->debug = array();
    }

    /*
     * reset
     *
     * reset the object to perform another call
     *
     */
    private function reset() {
      $this->params  = array();
    }

    /*
     * setData
     *
     * Set data for the token call. Can be called with key/value or an array of
     * associative data (key/value pairs)
     *
     * @params {mixed} name can be the name of the data to set or an array of data to add
     * @params {mixed} value the value to be set if name is a string
     *
     */
    private function setData($key, $value='') {
        if (is_array($key)) {
            $this->params = array_merge($this->params, $key);
        } else {
            $this->params[$key] = $value;
        }
    }

    /*
     * plugin
     * Retrieves the set of tags to insert in the page
     *
     * @param {Boolean} $ajax optional parameter to form ajax script, default to false
     */
    public function plugin($data, $ajax = false) {
      $this->reset();
      $this->setData($data);
      if ($ajax)
        $this->setData('ajax',1);
      return $this->post( "cart/plugin" );
    }

    /*
     * ticket
     * Send data without displaying plugin
     *
     * @param {Boolean} $ajax optional parameter to form ajax script, default to false
     */
    public function ticket($data, $ajax = false) {
      $this->reset();
      $this->setData($data);
      if ($ajax)
        $this->setData('ajax',1);
      return $this->post( "cart/ticket" );
    }

    /*
     * cancel
     *
     * Cancels the specified cart
     *
     * @param: the cart id to cancel
     */
    public function cancel($cartId) {
      $this->reset();
      $this->setData("id", $cartId);
      return $this->post("cart/cancel" );
    }

    /*
     * getPlayers
     *
     * retrieves a list (array) of players for the given key/site
     * @param: campaign id
     * @param: an optionnal from limit (in days)
     * @param: an optionnal cart id
     *
     * @return {array} an array of players
     */
    public function getPlayers($campaignId, $cartId="", $fromdays=0) {
        $this->reset();
        if($fromdays>0){
          $this->setData("from", strtotime("-$fromdays days")*1000);
        }
        if($cartId)
          $this->setData("cartId", $cartId);
        return $this->post("players/$campaignId");
    }

    /*
     * getWinners
     *
     * retrieves a list (array) of winners for the given key/site
     * @param: campaign id
     * @param: an optionnal from limit (in days)
     * @param: an optionnal cart id
     *
     * @return {array} an array of winners
     */
    public function getWinners($campaignId, $cartId="", $fromdays=0) {
        $this->reset();
        if($fromdays>0){
          $this->setData("from", strtotime("-$fromdays days")*1000);
        }
        if($cartId)
          $this->setData("cartId", $cartId);
        return $this->post("winners/$campaignId");
    }
  
    /*
     * authTest
     */
    public function authTest($url,$data=array()) {
      $this->reset();
      $this->setData($data);
      return $this->post("auth/$url",true);
    }

    /*
     * flatten
     * "flatten" the parameter array to be send via GET or POST
     * Array are changed to "object" like indexes:
     * ex: param[0][field] = 1; param[1][field] = 2
     *
     * @param {Array} $input the input array
     * @param {Array} $into  passed as reference to contain flattened array
     * @param {String} $prefix used of recurrence
     */
    private function flatten($input,&$into,$prefix='') {
      if (is_array($input)) {
        foreach($input as $key => $value) {
          $next = (''==$prefix) ? $key : $prefix.'['.$key.']';
          $this->flatten($value,$into,$next);
        }
      } else {
        $into[$prefix] = $input;
      }
    }

    /*
     * is_utf8
     *
     * Test if the passed string is utf8
     */
    private function isUTF8($string) {

	/*	
	 * Alternate version:
	 *
	 * $encoding=mb_detect_encoding($string,array("UTF-8","ISO-8859-1"),true);
	 * if($encoding=="UTF-8") return true;
	 * return false;	
	*/
	
       // From http://w3.org/International/questions/qa-forms-utf-8.html
       return preg_match('%^(?:
             [\x09\x0A\x0D\x20-\x7E]            # ASCII
           | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
           |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
           | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
           |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
           |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
           | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
           |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
       )*$%xs', $string);
    }

    /*
     * forceUTF8
     *
     * if not utf8, force translate
     */
    private function forceUTF8($str)
    {
       return $this->isUTF8($str) ? $str : utf8_encode($str);
    }

    /*
     * timestamp
     *
     * @return {number} timestamp in seconds
     */
    private function timestamp() {
        return time();
    }

    /*
     * get
     *
     * Performs a get request on the server.
     * @param {string} request the request to perform
     * @param {boolean} sign a flag to sign the request or not, signed by default
     * @param {boolean} json a flag to use or not the default format (json) for requests
     * @return {object} the response elements decoded into an associative array
     */
    private function get($request, $sign=true, $json=true) {
        $this->method = 'GET';
        return $this->process($request, $sign, $json);
    }

    /*
     * post
     *
     * Performs a get request on the server.
     * @param {string} request the request to perform
     * @param {boolean} sign a flag to sign the request or not, signed by default
     * @param {boolean} json a flag to use or not the default format (json) for requests
     * @return {object} the response elements decoded into an associative array
     */
    private function post($request, $sign=true, $json=true) {
        $this->method = 'POST';
        return $this->process($request, $sign);
    }

    /*
     * implodeParams
     *
     * Concatenate parameters to fit headers or url query string
     * Parameters are passed as a key value array
     * - for header: k1="v1",k2="v2" ...
     * - for url query: k1=v1&k2=v2 ...
     * @param {array} $params the parameters as a key/value array
     * @param {string} the character used to separate "key=value"  default to '&'
     * @param {string} the character used to "wrap" the value (default to none)
     * @return {string} the concatenated string
     */
    private function implodeParams($params, $sep = '&', $wrapper = '') {
        foreach ($params as $k => $v) {
            $p[] = $k . '=' . $wrapper
                    . rawurlencode($v)
                    . $wrapper;
        }
        return implode($sep, $p);
    }

    /*
     * sign
     *
     * Sign the request, according to the given params
     * The signature is an HMAC of the timestamp with the secret of the client
     *
     * @param {array} the url parameters
     * @return {string} the base64 encoded signature
     */
    private function sign($params) {
        $ts = $params['auth_ts'];
        $this->log("Base used: $ts");
        $this->log("Secret used: $this->secret");

        // Creating the signature according to HMAC SHA256 protocol.
        $signature = hash_hmac('SHA256', $ts, $this->secret, false);
        $this->log("Signature: $signature");
        return $signature;
    }

    /*
     * signHeader
     *
     * Builds the http header line used to sign the request
     * It creates the auth parameters (nonce, timestamp etc..) and sign
     * the final request.
     * @return {string} the Header line
     */
    private function signHeader() {
        $authParams=array(
            'auth_v'     => '2.0',
            'auth_ts'    => $this->timestamp(),
            'auth_key'   => $this->key
        );
        $authParams['auth_sign'] = $this->sign($authParams);
        return self::AUTH_HEADER . ':' . $this->implodeParams($authParams,',','"');
    }

    /*
     * process
     *
     * Process a request to Lucky Cart servers using curl
     * @param {string} the request to make
     * @param {boolean} sign whether or not to sign the request
     * @param {boolean} json a flag to use or not the default format (json) for requests
     * @return {object} an object containing the decoded response
     */
    private function process($request, $sign=true, $json=true) {
        // Create a basic url
        $this->url = $this->host . $request;
        if ($json && !preg_match('/\.json$/',$request))
            $this->url .= '.json';
        // Init curl and set options
        $curling = curl_init();
        $curl_options = array(
            CURLOPT_URL => $this->url,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_SSL_VERIFYPEER => false
        );
        $headers = array();
        $paramStr = '';
        if (count($this->params)>0) {
          $this->flatten($this->params, $flattened);
          $this->params = $flattened;
          $paramStr = $this->implodeParams($this->params);
          $paramStr = $this->forceUTF8($paramStr);
        }
        switch ( $this->method ) {
            case 'GET':
                if ($paramStr) $curl_options[CURLOPT_URL] .= '?'.$paramStr;
                break;
            case 'POST':
                $curl_options[CURLOPT_POST] = 1;
                $curl_options[CURLOPT_POSTFIELDS] = $paramStr;
                $headers[] = 'Content-length:'.strlen($paramStr);
                break;
            case 'PUT':
                $curl_options[CURLOPT_PUT] = 1;
                $curl_options[CURLOPT_POSTFIELDS] = $paramStr;
                $headers[] = 'Content-length: '.strlen($paramStr);
                break;
            case 'DELETE':
                $curl_options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
                break;
        };
        if ($sign) $headers[] = $this->signHeader();
        $curl_options[CURLOPT_HTTPHEADER] = $headers;
        curl_setopt_array($curling, $curl_options);

        // Execute curl, retrieve json response
        $response = curl_exec($curling);
        if (false === $response) {
            throw new LuckyException('Error connecting to API server: '.curl_error($curling));
        }
        // Testing status
        // Test for error and send...
        $decoded = json_decode($response);
        $status = curl_getinfo($curling,CURLINFO_HTTP_CODE);
        if (200 != $status) {
            if (!is_null($decoded) && array_key_exists('error', $decoded)) {
              throw new LuckyException($decoded->error . "\n");
            }
            else
                throw new LuckyException("An error {$status} occured...",$status);
        }
        curl_close($curling);

        return $json ? $decoded : $response;
    }

}
?>
