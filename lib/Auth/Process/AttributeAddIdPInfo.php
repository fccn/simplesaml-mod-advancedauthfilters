<?php

/**
 * Filter to add idp information to an attribute.
 *
 * This filter allows you to add information about the the entityID of the IdP the user is authenticated against.
 * It can be used on briged connections to identify the source IdP
 *
 * @author Paulo Costa FCT|FCCN
 * @package advancedauthfilters
 * @version $Id$
 */
class sspmod_advancedauthfilters_Auth_Process_AttributeAddIdPInfo extends SimpleSAML_Auth_ProcessingFilter
{

    /*
     the default name for the attribute
    */
    private $name;

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
        if (empty($config['name'])) {
            SimpleSAML_Logger::warning('AttributeAddIdPInfo: Configuration error. There is no name for the idp entityID attribute, using default name idp.');
            $this->name = 'idp';
        }
        $this->name = $config['name'];
    }


    /**
     * Apply filter to add idp attribute.
     * @param array &$request  The current request
     */
    public function process(&$request)
    {
        assert('is_array($request)');
        assert('array_key_exists("Attributes", $request)');
        assert('array_key_exists("saml:sp:IdP", $request)');

        $attributes =& $request['Attributes'];
        //add the idp attribute
        $attributes[$this->name] = array($request["saml:sp:IdP"]);
    }
}
