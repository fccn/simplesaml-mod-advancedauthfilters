<?php

/**
 * Filter that makes a REST call to an API and saves the result in a SAML attribute
 *
 * For the moment it only makes GET and POST requests, defaults to POST when no type is defined
 *
 * @author Paulo Costa FCT|FCCN
 * @package advancedauthfilters
 * @version $Id$
 */
class sspmod_advancedauthfilters_Auth_Process_CallRestApi extends SimpleSAML_Auth_ProcessingFilter
{

    /*
      The URL of the API
    */
    private $api_url;

    /*
      The action to call on the API
    */
    private $action;

    /*
      The data type of the API request, defaults to JSON
    */
    private $data_type = 'JSON';

    /*
      The request type of the API call, defaults to POST
    */
    private $request_type = 'POST';

    /*
      The name of the SAML attribute with the response from the API call
    */
    private $attribute_name;

    /*
    * List of saml attributes to add to the REST call
    */
    private $saml_attributes;

    /*
    *  List of additional parameters to add to REST call
    */
    private $params;

    /*
    * Debug flag, if true shows additional information on saml attributes
    */
    private $debug = false;

    /**
     * Initialize this filter.
     *
     * @param array $config  Configuration information about this filter.
     * @param mixed $reserved  For future use.
     */
    public function __construct($config, $reserved)
    {
        parent::__construct($config, $reserved);

        assert('is_array($config)');
        //set REST API URL
        if (empty($config['api_url'])) {
            throw new SimpleSAML_Error_Exception($this->authId .': missing required \'api_url\' option.');
        }
        $this->api_url = $config['api_url'];
        //set REST action to call
        if (empty($config['action'])) {
            throw new SimpleSAML_Error_Exception($this->authId .': missing required \'action\' option.');
        }
        $this->action = $config['action'];
        //set request type of the API call, default to POST
        if (!empty($config['type'])) {
            $this->request_type = $config['type'];
        } else {
            $this->request_type = 'POST';
        }
        //set SAML attribute name, default to rest:response
        if (!empty($config['attribute_name'])) {
            $this->attribute_name = $config['attribute_name'];
        } else {
            $this->attribute_name = 'rest:response';
        }
        //set SAML attributes to add to request
        if (array_key_exists('include_attributes', $config)) {
            $this->saml_attributes = $config['include_attributes'];
            if (!is_array($this->saml_attributes)) {
                throw new Exception('CallRestAPI configuration error: \'include_attributes\' should be an array.');
            }
        } else {
            $this->saml_attributes = array();
        }
        // set REST call parameters
        if (array_key_exists('params', $config)) {
            $this->params = $config['params'];
            if (!is_array($this->params)) {
                throw new Exception('CallRestAPI configuration error: \'params\' should be an array.');
            }
        } else {
            $this->params = array();
        }
        //set debug flag
        if (!empty($config['debug'])) {
            $this->debug = true;
        }
    }


    /**
     * Apply filter to add idp attribute.
     * @param array &$request  The current request
     */
    public function process(&$request)
    {
        SimpleSAML_Logger::debug('[CallRestAPI] calling process...');
        assert('is_array($request)');
        assert('array_key_exists("Attributes", $request)');

        $name = $this->attribute_name;

        // Get attributes from request
        $attributes =& $request['Attributes'];

        //add saml attributes to request params
        if (!empty($this->saml_attributes)) {
            foreach ($this->saml_attributes as $attr_name) {
                //check if attribute exists
                if (array_key_exists($attr_name, $attributes)) {
                    //add it to params
                    $value = $attributes[$attr_name];
                    if (!array_key_exists($attr_name, $this->params)) {
                        $this->params[$attr_name] = $value;
                    } else {
                        $this->params[$attr_name] = array_merge($this->params[$attr_name], $value);
                    }
                }
            }
        }

        $response = json_encode($this->makeApiRequest());
        $response = array($response);

        if (!array_key_exists($name, $attributes)) {
            $attributes[$name] = $response;
        } else {
            $attributes[$name] = array_merge($attributes[$name], $response);
        }

        //additional info
        if ($this->debug) {
            $attributes[$name.':fullurl'] = array($this->api_url.$this->action);
        }
    }

    /* Function to send HTTP POST Requests to defined API
    * and return response from server */
    public function makeApiRequest()
    {

        //$data = $this->request_params;

        /*Creates the endpoint URL*/
        $request_url = $this->api_url.$this->action;
        SimpleSAML_Logger::debug('[CallRestAPI] making API request to '.$request_url);

        /* create post fields from parameters */
        $request_fields = http_build_query($this->params);
        /*Check to see queried fields*/
        /*Used for troubleshooting/debugging*/
        //SimpleSAML_Logger::debug('[CallRestAPI] - API request with fields: '.json_encode($request_fields));

        /*Preparing Query...*/
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $request_url);
        if ($this->request_type == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request_fields);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        /*Check for any errors*/
        $errorMessage = curl_error($ch);
        curl_close($ch);

        /*Will print back the response from the call*/
        /*Used for troubleshooting/debugging		*/
        //SimpleSAML_Logger::debug('[CallRestAPI] - API request url: '. $request_url);
        //SimpleSAML_Logger::debug('[CallRestAPI] - API request with data: '.json_encode($data));
        //SimpleSAML_Logger::debug('[CallRestAPI] - API response: '.json_encode($response));
        //SimpleSAML_Logger::debug('[CallRestAPI] - API error message: '.json_encode($errorMessage));
        if (!empty($errorMessage)) {
            SimpleSAML_Logger::error("[CallRestAPI] - API call returned error: ".json_encode($errorMessage));
            return $errorMessage;
        }
        if (empty($response)) {
            SimpleSAML_Logger::error("[CallRestAPI] - API call returned empty response");
            return array("error" => array(
                "code" => 500,
                "message" => "Unable to get response from REST API."
            ));
        }
        /*Return the response */
        return $response;
    }
}
